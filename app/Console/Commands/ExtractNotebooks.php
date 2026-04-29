<?php

namespace App\Console\Commands;

use App\Services\ParserService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExtractNotebooks extends Command
{
    protected $signature = 'app:extract-notebooks';

    protected $description = 'Command description';

    public function handle(ParserService $parser)
    {
        DB::disableQueryLog();

        \App\Models\PageCache::where('url', 'like', '%.html')
            ->where('url', 'not like', '%reviews.html')
            ->chunk(50, function ($pages) use ($parser) {

                $this->info('Найдено страниц для обработки: '.$pages->count());
                $count = 0;

                foreach ($pages as $page) {
                    try {

                        $data = $parser->extractMainInfo($page->html);

                        $brand = \App\Models\Brand::firstOrCreate(
                            ['name' => $data['brand']],
                            ['slug' => \Illuminate\Support\Str::slug($data['brand'])]
                        );

                        $slug = pathinfo($page->url, PATHINFO_FILENAME);

                        $product = \App\Models\Product::updateOrCreate(
                            ['external_id' => $data['external_id']],
                            [
                                'brand_id' => $brand->id,
                                'name' => $data['name'],
                                'slug' => $slug,
                                'price' => $data['price'],
                                'rating' => $data['rating'],
                                'reviews_count' => $data['reviews_count'],
                                'url' => $page->url,
                                'is_popular' => rand(0, 1),
                            ]
                        );

                        $gallery = $parser->extractGalleryLinks($page->html);
                        if (! empty($gallery)) {
                            $product->update(['image' => $gallery[0]]);

                            foreach ($gallery as $url) {
                                \App\Models\ProductImage::updateOrCreate([
                                    'product_id' => $product->id,
                                    'path' => $url,
                                ]);
                            }
                        }

                        $specsByGroups = $parser->extractSpecifications($page->html);

                        foreach ($specsByGroups as $groupName => $attributes) {
                            $group = \App\Models\AttributeGroup::firstOrCreate([
                                'name' => $groupName,
                            ]);

                            foreach ($attributes as $specName => $specValue) {
                                $attribute = \App\Models\Attribute::firstOrCreate(
                                    ['name' => $specName],
                                    ['attribute_group_id' => $group->id]
                                );

                                if (is_null($attribute->attribute_group_id)) {
                                    $attribute->update(['attribute_group_id' => $group->id]);
                                }

                                \App\Models\AttributeProduct::updateOrCreate(
                                    [
                                        'product_id' => $product->id,
                                        'attribute_id' => $attribute->id,
                                    ],
                                    ['value' => $specValue]
                                );
                            }
                        }

                        $count++;
                        if (str_starts_with($data['external_id'], 'temp_')) {
                            $this->warn('! Товар без артикула: '.$page->url);
                        } else {
                            $this->info("№ $count Сохранен вместе с фото: ".$data['name']);
                        }

                        if (ob_get_level() > 0) {
                            ob_flush();
                        }
                        flush();
                    } catch (\Exception $e) {
                        $this->error("Ошибка на странице {$page->url}: ".$e->getMessage());
                    }
                    $this->info('Обработано: '.$page->url);
                }
            });
        $this->info('Переработка завершена!');
    }
}
