<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\ParserService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ParseReviews extends Command
{
    protected $signature = 'app:parse-reviews';

    protected $description = 'Command description';

    public function handle(ParserService $parser)
    {
        $products = Product::take(900)->get();

        $this->info('Начинаю сбор отзывов для '.$products->count().' товаров...');

        foreach ($products as $product) {

            if ($product->reviews()->exists()) {
                $this->info("Уже есть отзывы на {$product->name}. Перехожу к следующему товару. ");

                continue;
            }

            $reviewsUrl = str_replace('.html', '/reviews.html', $product->url);

            $this->warn("-> Проверяю отзывы: $reviewsUrl");

            $html = $parser->getFromCache($reviewsUrl);

            if ($html === null) {
                $this->line('   Качаю из сети страницу отзывов');
                $html = $parser->download($reviewsUrl);

                if ($html === null) {
                    $parser->saveToCache($reviewsUrl, 'NOT_FOUND_404');
                    $this->info('   Страница 404. Пропускаю.Спим от 8 до 10 секунд');
                    usleep(random_int(8000, 10000) * 1000);

                    continue;
                }

                $parser->saveToCache($reviewsUrl, $html);
                $this->info('Спим от 10 до 14 секунд после загрузки страницы отзывов');

                usleep(random_int(10000, 14000) * 1000);
            } else {
                $this->line('   Взята из кэша главная страница отзывов.');
            }

            if ($html === 'NOT_FOUND_404') {
                $this->info('   Уже проверено (404). Пропускаю.');

                continue;
            }

            $allProductReviews = [];
            $reviewsData = $parser->extractReviewsFromHtml($html);
            $allProductReviews = array_merge($allProductReviews, $reviewsData);

            preg_match('/\/([^\/]+)\.html$/', $product->url, $matches);
            $slug = $matches[1] ?? null;

            if ($slug) {
                $offset = 20;
                $hasMore = true;

                while ($hasMore) {
                    $apiUrl = "https://gate.21vek.by/reviews/product/{$slug}/reviews?limit=10&offset={$offset}";

                    $this->line("   Качаю по сети AJAX (offset $offset)...");

                    $json = $parser->getFromCache($apiUrl);
                    if (! $json) {
                        $json = $parser->download($apiUrl, [
                            'Accept' => 'application/json',
                            'Content-Type' => 'application/json',
                            'Origin' => 'https://www.21vek.by',
                        ]);

                        if (! $json) {
                            break;
                        }
                        $parser->saveToCache($apiUrl, $json);

                        $this->info('Спим от 9 до 13 секунд после загрузки отзывов по ajax...');

                        usleep(random_int(9000, 13000) * 1000);
                    } else {
                        $this->line("   Из кеша AJAX (offset $offset)...");
                    }

                    $ajaxReviews = $parser->extractReviewsFromJson($json);

                    if (empty($ajaxReviews)) {
                        $hasMore = false;
                    } else {

                        $allProductReviews = array_merge($allProductReviews, $ajaxReviews);
                        $offset += 10;
                    }
                }
            }

            if (! empty($allProductReviews)) {

                DB::transaction(function () use ($product, $allProductReviews) {
                    foreach ($allProductReviews as $reviewData) {
                        $product->reviews()->create($reviewData);
                    }
                });
                $this->info('   Успешно сохранено '.count($allProductReviews).' отзывов.');
            }
        }

        $this->info('Сбор отзывов завершен!');
    }
}
