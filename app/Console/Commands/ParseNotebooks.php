<?php

namespace App\Console\Commands;

use App\Services\ParserService;
use Illuminate\Console\Command;

class ParseNotebooks extends Command
{
    protected $signature = 'parse:21vek';

    protected $description = 'Парсинг ноутбуков с 21vek.by (Пагинация + Кэш)';

    private string $domain = 'https://www.21vek.by';

    private string $baseUrl = 'https://www.21vek.by/notebooks/';

    public function handle(ParserService $parser)
    {
        set_time_limit(0);
        $maxPages = 20; // Maximum number of pages to parse (set manually, as determining via pagination can be unreliable)

        try {
            for ($page = 1; $page <= $maxPages; $page++) {
                $pageUrl = $this->baseUrl.($page > 1 ? "page:{$page}/" : '');

                $this->info("--- [Страница пагинации #{$page}] ---");
                $this->line("Проверяю URL: $pageUrl");
                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();
                $html = $parser->getFromCache($pageUrl);

                if (! $html) {
                    $this->warn('В кэше пусто. Скачиваем страницу списка из сети...');
                    $html = $parser->download($pageUrl);
                    $parser->saveToCache($pageUrl, $html);

                    $this->info('Спим от 8 до 10 секунд после загрузки списка...');
                    if (ob_get_level() > 0) {
                        ob_flush();
                    }
                    flush();
                    usleep(random_int(8000, 10000) * 1000);
                } else {
                    $this->comment('Страница списка получена из кэша.');
                }

                $links = $parser->extractProductLinks($html, $this->domain);

                if (empty($links)) {
                    $this->comment("На странице #{$page} ссылок нет. Пагинация завершена.");
                    break;
                }

                $this->info('Найдено уникальных ссылок: '.count($links));
                $this->info('Начинаем обход товаров (тестовый режим)...');
                $testLinks = array_slice($links, 0, 70);
                foreach ($testLinks as $index => $link) {
                    $this->line('-> Товар '.($index + 1).": $link");
                    if (ob_get_level() > 0) {
                        ob_flush();
                    }
                    flush();
                    $productHtml = $parser->getFromCache($link);

                    if (! $productHtml) {
                        $this->warn('В кэше пусто. Качаем страницу ТОВАРА...');
                        try {
                            $productHtml = $parser->download($link);
                            $parser->saveToCache($link, $productHtml);

                            $this->info('Спим от 8 до 10 секунд !');
                            if (ob_get_level() > 0) {
                                ob_flush();
                            }
                            flush();
                            usleep(random_int(8000, 10000) * 1000);
                        } catch (\Exception $e) {
                            $this->error('Не удалось скачать товар: '.$e->getMessage());

                            continue;
                        }
                    } else {
                        $this->comment('Страница товара уже в кэше.');
                        if (ob_get_level() > 0) {
                            ob_flush();
                        }
                        flush();
                    }
                }
            }
        } catch (\Exception $e) {
            $this->error('Произошла критическая ошибка: '.$e->getMessage());
        }

        $this->info('Парсинг всех доступных страниц завершен.');
    }
}
