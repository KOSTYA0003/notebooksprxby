<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class UpdateProductReviews extends Command
{
    protected $signature = 'app:update-reviews';

    protected $description = 'Пересчет количества отзывов для всех товаров через чанки(по 100 товаров за раз)';

    public function handle()
    {
        $this->info('🚀 Запуск процесса обновления...');

        $total = Product::count();
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        Product::query()
            ->withCount('reviews as actual_count')
            ->chunkById(100, function ($products) use ($bar) {
                foreach ($products as $product) {

                    $product->update([
                        'reviews_count' => $product->actual_count,
                    ]);

                    $bar->advance();
                }
            });

        $bar->finish();
        $this->newLine();
        $this->info('✅ Все товары успешно обновлены!');
    }
}
