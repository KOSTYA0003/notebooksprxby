<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AttributeUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $attributes = [
            ['name' => 'Вид', 'pos_sidebar' => 50, 'pos_modal' => 10],
            ['name' => 'Дата выхода на рынок', 'pos_sidebar' => 230, 'pos_modal' => 190],
            ['name' => 'Линейка', 'pos_sidebar' => 190, 'pos_modal' => 150],
            ['name' => 'Операционная система', 'pos_sidebar' => 110, 'pos_modal' => 60],
            ['name' => 'Серия процессора', 'pos_sidebar' => 60, 'pos_modal' => 20],
            ['name' => 'Модель процессора', 'pos_sidebar' => 70, 'pos_modal' => 30],
            ['name' => 'Количество ядер', 'pos_sidebar' => 220, 'pos_modal' => 180],
            ['name' => 'Тактовая частота', 'pos_sidebar' => 250, 'pos_modal' => 220],
            ['name' => 'Диагональ экрана', 'pos_photo' => 1, 'pos_sidebar' => 150, 'pos_modal' => 110],
            ['name' => 'Разрешение экрана', 'pos_photo' => 2, 'pos_sidebar' => 170, 'pos_modal' => 130],
            ['name' => 'Частота обновления экрана', 'pos_photo' => 3, 'pos_sidebar' => 180, 'pos_modal' => 140],
            ['name' => 'Технология экрана', 'pos_sidebar' => 160, 'pos_modal' => 120],
            ['name' => 'Тип оперативной памяти', 'pos_sidebar' => 120, 'pos_modal' => 70],
            ['name' => 'Объем оперативной памяти', 'pos_sidebar' => 130, 'pos_modal' => 80],
            ['name' => 'Тип видеокарты', 'pos_photo' => 4, 'pos_sidebar' => 80, 'pos_modal' => 40],
            ['name' => 'Модель видеокарты', 'pos_photo' => 5, 'pos_sidebar' => 100, 'pos_modal' => 55],
            ['name' => 'Конфигурация дисков', 'pos_sidebar' => 130, 'pos_modal' => 90],
            ['name' => 'Емкость SSD', 'pos_sidebar' => 140, 'pos_modal' => 100],
            ['name' => 'Ethernet (LAN)', 'pos_sidebar' => 240, 'pos_modal' => 200],
            ['name' => 'Всего USB-портов', 'pos_sidebar' => 280, 'pos_modal' => 260],
            ['name' => 'Thunderbolt', 'pos_sidebar' => 290, 'pos_modal' => 270],
            ['name' => 'Трансформер', 'pos_sidebar' => 260, 'pos_modal' => 230],
            ['name' => 'Материал корпуса', 'pos_sidebar' => 200, 'pos_modal' => 160],
            ['name' => 'Цвет корпуса', 'pos_sidebar' => 210, 'pos_modal' => 170],
            ['name' => 'Кириллица на клавиатуре', 'pos_sidebar' => 300, 'pos_modal' => 270],
            ['name' => 'Запас энергии', 'pos_sidebar' => 270, 'pos_modal' => 240],
            ['name' => 'Сумка или чехол', 'pos_sidebar' => 310, 'pos_modal' => 280],
            ['name' => 'Объем видеопамяти', 'pos_sidebar' => 250, 'pos_modal' => 210],

            ['name' => 'Популярные параметры', 'filter_type' => 'boolean', 'is_visible' => false, 'pos_sidebar' => 90, 'pos_modal' => 45],
            ['name' => 'Сенсорный экран', 'parent_name' => 'Популярные параметры', 'filter_type' => 'boolean', 'pos_sidebar' => 93, 'pos_modal' => 48],
            ['name' => 'HDMI', 'parent_name' => 'Популярные параметры', 'filter_type' => 'boolean', 'pos_sidebar' => 95, 'pos_modal' => 50],
            ['name' => 'DisplayPort', 'parent_name' => 'Популярные параметры', 'filter_type' => 'boolean', 'pos_sidebar' => 96, 'pos_modal' => 51],
            ['name' => 'Подсветка клавиатуры', 'parent_name' => 'Популярные параметры', 'filter_type' => 'boolean', 'pos_sidebar' => 91, 'pos_modal' => 46],
            ['name' => 'Цифровое поле (Numpad)', 'parent_name' => 'Популярные параметры', 'filter_type' => 'boolean', 'pos_sidebar' => 92, 'pos_modal' => 47],
            ['name' => 'Сканер отпечатков пальцев', 'parent_name' => 'Популярные параметры', 'filter_type' => 'boolean', 'pos_sidebar' => 97, 'pos_modal' => 52],
            ['name' => 'Камера для идентификации пользователя', 'parent_name' => 'Популярные параметры', 'filter_type' => 'boolean', 'pos_sidebar' => 94, 'pos_modal' => 49],

            ['name' => 'Срок доставки', 'filter_type' => 'boolean', 'is_visible' => false, 'pos_sidebar' => 20, 'pos_modal' => 2],
            ['name' => 'Не важно', 'parent_name' => 'Срок доставки', 'filter_type' => 'boolean', 'is_visible' => false, 'pos_sidebar' => 21, 'pos_modal' => 3],
            ['name' => 'Сегодня или завтра', 'parent_name' => 'Срок доставки', 'filter_type' => 'boolean', 'is_visible' => false, 'pos_sidebar' => 22, 'pos_modal' => 4],
            ['name' => 'До 5 дней', 'parent_name' => 'Срок доставки', 'filter_type' => 'boolean', 'is_visible' => false, 'pos_sidebar' => 23, 'pos_modal' => 5],

            ['name' => 'Спецпредложения', 'filter_type' => 'boolean', 'is_visible' => false, 'pos_sidebar' => 40],
            ['name' => 'Товары со скидкой', 'parent_name' => 'Спецпредложения', 'is_visible' => false, 'pos_sidebar' => 41],
            ['name' => 'ШОП ЦЕНА', 'parent_name' => 'Спецпредложения', 'is_visible' => false, 'pos_sidebar' => 42],
            ['name' => 'Уценённые товары', 'parent_name' => 'Спецпредложения', 'is_visible' => false, 'pos_sidebar' => 43],
            ['name' => 'БОЛЬШАЯ РАСПРОДАЖА', 'parent_name' => 'Спецпредложения', 'is_visible' => false, 'pos_sidebar' => 44],
            ['name' => 'Кредит 4% на 36 мес.', 'parent_name' => 'Спецпредложения', 'is_visible' => false, 'pos_sidebar' => 45],

            ['name' => 'Оплата частями', 'filter_type' => 'boolean', 'is_visible' => false, 'pos_sidebar' => 10],
            ['name' => '3 месяца', 'parent_name' => 'Оплата частями', 'filter_type' => 'boolean', 'is_visible' => false, 'pos_sidebar' => 16, 'pos_modal' => 53],
            ['name' => '6 месяцев', 'parent_name' => 'Оплата частями', 'filter_type' => 'boolean', 'is_visible' => false, 'pos_sidebar' => 17],
            ['name' => '9 месяцев', 'parent_name' => 'Оплата частями', 'filter_type' => 'boolean', 'is_visible' => false, 'pos_sidebar' => 18],
            ['name' => '12 месяцев', 'parent_name' => 'Оплата частями', 'filter_type' => 'boolean', 'is_visible' => false, 'pos_sidebar' => 19],

            ['name' => 'Цена', 'filter_type' => 'number', 'is_visible' => false, 'pos_modal' => 1],
            ['name' => 'Производители', 'filter_type' => 'brand_list', 'is_visible' => false, 'pos_modal' => 6],

        ];

        foreach ($attributes as $data) {
            $parentId = null;

            if (! empty($data['parent_name'])) {
                $parentId = \App\Models\Attribute::where('name', $data['parent_name'])->value('id');
                unset($data['parent_name']);
            }

            \App\Models\Attribute::updateOrCreate(
                ['name' => $data['name']],
                array_merge($data, ['parent_id' => $parentId])
            );
        }
    }
}
