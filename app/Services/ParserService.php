<?php

namespace App\Services;

use App\Models\PageCache;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class ParserService
{
    private string $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

    public function download(string $url, array $headers = []): ?string
    {
        $defaultHeaders = [
            'User-Agent' => $this->userAgent,
            'Referer' => 'https://www.21vek.by',
        ];
        $finalHeaders = array_merge($defaultHeaders, $headers);

        $response = Http::withHeaders($finalHeaders)
            ->timeout(30)
            ->withoutVerifying()
            ->get($url);

        if ($response->failed()) {
            $status = $response->status();

            if ($status === 403 || $status === 429) {
                throw new \Exception("БАН или БЛОКИРОВКА! Код: $status. Срочно останови парсер.");
            }

            if ($status === 404) {
                return null;
            }

            throw new \Exception("Ошибка загрузки: $url. Код: $status");
        }

        $html = $response->body();
        if (str_contains($html, 'Access Denied') || str_contains($html, 'captcha')) {
            throw new \Exception('Обнаружена капча или текст блокировки в HTML!');
        }

        return $html;
    }

    public function getFromCache(string $url): ?string
    {
        return PageCache::where('url', $url)->value('html');
    }

    public function saveToCache(string $url, string $html): void
    {
        PageCache::updateOrCreate(
            ['url' => $url],
            ['html' => $html]
        );
    }

    public function extractProductLinks(string $html, string $domain = 'https://www.21vek.by'): array
    {
        $crawler = new Crawler($html);
        $links = [];

        $crawler->filter('div.ListingProduct_product__WBPsd div.ListingProduct_middlePanel__t7tPV a.CardInfo_info__zKUou.ListingProduct_info__aS1Kt')
            ->each(function (Crawler $node) use (&$links, $domain) {
                $link = $node->attr('href');
                if ($link) {
                    $links[] = $this->normalizeUrl($link, $domain, false);
                }
            });

        return array_unique($links);
    }

    public function extractMainInfo(string $html): array
    {
        $crawler = new \Symfony\Component\DomCrawler\Crawler($html);

        $brandNode = $crawler->filter('ul.Breadcrumbs-module__breadcrumbs li.Breadcrumbs-module__crumb a');
        $brand = ($brandNode->count() > 0) ? trim($brandNode->first()->text()) : 'Unknown';

        $nameNode = $crawler->filter('h1.ProductCardScreen_title__1vng6');
        if ($nameNode->count() === 0) {
            $nameNode = $crawler->filter('h1');
        }
        $name = ($nameNode->count() > 0) ? trim($nameNode->text()) : 'Без названия';

        $codeNode = $crawler->filter('.ProductCode_code__bD1_B');
        if ($codeNode->count() > 0) {
            $rawCode = $codeNode->text();
            $externalId = trim(str_replace('код', '', $rawCode));
        } else {
            $externalId = 'temp_'.md5($name);
        }

        $priceNode = $crawler->filter('.ProductPrice_productPrice__thjM7');
        $price = 0.0;
        if ($priceNode->count() > 0) {
            $priceRaw = $priceNode->text();
            $priceClean = str_replace([',', ' '], ['.', ''], $priceRaw);
            $price = (float) preg_replace('/[^\d.]/', '', $priceClean);
        }

        $ratingNode = $crawler->filter('[data-testid="area-rating-value"]');
        $rating = $ratingNode->count() > 0 ? (float) $ratingNode->text() : 0.0;

        $reviewsCountNode = $crawler->filter('[data-testid="area-review-count"]');
        $reviewsCount = 0;
        if ($reviewsCountNode->count() > 0) {
            $reviewsCount = (int) preg_replace('/\D/', '', $reviewsCountNode->text());
        }

        return [
            'brand' => $brand,
            'name' => $name,
            'external_id' => $externalId,
            'price' => $price,
            'rating' => $rating,
            'reviews_count' => $reviewsCount,
        ];
    }

    public function extractGalleryLinks(string $html): array
    {
        $crawler = new \Symfony\Component\DomCrawler\Crawler($html);
        $images = [];

        $crawler->filter('div.ProductCardGallery_previews__K2D2k div.swiper-wrapper div.swiper-slide img')
            ->each(function ($node) use (&$images) {

                $url = $node->attr('src');
                if ($url) {
                    if (str_contains($url, '/preview_b/')) {
                        $url = str_replace('/preview_b/', '/large/', $url);
                    } elseif (str_contains($url, '/preview_s/')) {
                        $url = str_replace('/preview_s/', '/large/', $url);
                    }

                    $images[] = $this->normalizeUrl($url, 'https://cdn21vek.by', false);
                }
            });

        return array_unique($images);
    }

    public function extractSpecifications(string $html): array
    {
        $crawler = new \Symfony\Component\DomCrawler\Crawler($html);
        $groups = [];

        $crawler->filter('.Attributes_group__G89wX')->each(function ($groupNode) use (&$groups) {
            $groupName = trim($groupNode->filter('h2')->text());

            $groupNode->filter('dl.Attribute_attribute__2uE4Q')->each(function ($node) use (&$groups, $groupName) {
                $name = trim($node->filter('dt')->text());
                $value = trim($node->filter('dd')->text());

                if ($name && $value) {
                    $groups[$groupName][$name] = $value;
                }
            });
        });

        return $groups;
    }

    public function parseRussianDate(string $dateString): ?string
    {
        $months = [
            'Янв' => '01',
            'Фев' => '02',
            'Мар' => '03',
            'Апр' => '04',
            'Мая' => '05',
            'Июн' => '06',
            'Июл' => '07',
            'Авг' => '08',
            'Сен' => '09',
            'Окт' => '10',
            'Ноя' => '11',
            'Дек' => '12',
        ];

        $dateString = str_replace(',', '', $dateString);
        foreach ($months as $ru => $num) {
            if (str_contains($dateString, $ru)) {
                $dateString = str_replace($ru, $num, $dateString);
                break;
            }
        }

        try {
            return \Carbon\Carbon::createFromFormat('d m Y', $dateString)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function extractReviewsFromHtml(string $html): array
    {
        $crawler = new \Symfony\Component\DomCrawler\Crawler($html);
        $reviews = [];

        $crawler->filter('div.Review_wrapper__g6wWt')->each(function ($node) use (&$reviews) {

            $nameNode = $node->filter('.Comment_nameWrapper__6CL3x .Text-module__bold');
            $userName = $nameNode->count() > 0 ? $nameNode->text() : 'Покупатель 21vek.by';

            $dateNode = $node->filter('.Comment_date__c8E_i');
            $rawDate = $dateNode->count() > 0 ? $dateNode->text() : null;

            $rating = 0;
            $starNodes = $node->filter('.Rating_star___awk_');
            foreach ($starNodes as $starNode) {
                $style = $starNode->getAttribute('style');
                if (preg_match('/width:\s*100%/', $style)) {
                    $rating++;
                }
            }

            $pros = null;
            $cons = null;
            $comment = null;

            $bodyNode = $node->filter('.Comment_body__KfQZa');

            if ($bodyNode->count() > 0) {
                $currentSection = null;

                $bodyNode->children()->each(function ($item) use (&$pros, &$cons, &$comment, &$currentSection) {
                    $class = $item->attr('class') ?? '';
                    $text = trim($item->text());

                    if (empty($text)) {
                        return;
                    }

                    if (str_contains($class, 'CommentBodyBlock_title__WxJ6o')) {
                        $currentSection = $text;
                    } elseif (str_contains($class, 'CommentBodyBlock_content__GdKRv') && ! str_contains($class, 'Review_showCommentsButton__ChvYN')) {
                        if ($currentSection === 'Достоинства') {
                            $pros = $text;
                        } elseif ($currentSection === 'Недостатки') {
                            $cons = $text;
                        } elseif ($currentSection === 'Резюме') {
                            $comment = $text;
                        }
                    }
                });
            }

            $replyText = null;
            $replyNode = $node->filter('.Review_moderatorContainer__NVGGG .CommentBodyBlock_content__GdKRv');
            if ($replyNode->count() > 0) {
                $replyText = trim($replyNode->text());
            }

            $reviews[] = [
                'user_name' => trim($userName),
                'pros' => $pros,
                'cons' => $cons,
                'comment' => $comment,
                'rating' => $rating,
                'reply_text' => $replyText,
                'publish_date' => $rawDate ? $this->parseRussianDate($rawDate) : null,
            ];
        });

        return $reviews;
    }

    public function extractReviewsFromJson(string $json): array
    {
        $data = json_decode($json, true);
        $reviews = [];

        if (! isset($data['data']['reviews'])) {
            return [];
        }

        foreach ($data['data']['reviews'] as $item) {
            $reviews[] = [
                'user_name' => $item['name'] ?? 'Покупатель 21vek.by',
                'pros' => ! empty($item['positives']) ? $item['positives'] : null,
                'cons' => ! empty($item['negatives']) ? $item['negatives'] : null,
                'comment' => ! empty($item['summary']) ? $item['summary'] : null,
                'rating' => (int) $item['rating'],
                'reply_text' => $item['moderatorComment']['text'] ?? null,
                'publish_date' => isset($item['dateTimestamp'])
                    ? substr($item['dateTimestamp'], 0, 10)
                    : null,
            ];
        }

        return $reviews;
    }

    public function normalizeUrl($path, $baseUrl, $isHtml = true)
    {
        $normalize_url_internal = function ($url, $baseUrl) {

            if (strpos($url, '//') === 0) {
                $baseParts = parse_url($baseUrl);
                $scheme = $baseParts['scheme'] ?? 'http';

                return $scheme.':'.$url;
            }

            if (preg_match('#^https?://#i', $url)) {
                return $url;
            }

            $baseParts = parse_url($baseUrl);
            $scheme = $baseParts['scheme'] ?? 'http';
            $host = $baseParts['host'] ?? '';

            if (strpos($url, '/') === 0) {
                return $scheme.'://'.$host.$url;
            }

            $basePath = $baseParts['path'] ?? '/';
            $isBaseFile = false;
            $basePathTrimmed = rtrim($basePath, '/');

            if ($basePathTrimmed !== '' && $basePathTrimmed !== '/') {
                $lastSegment = basename($basePathTrimmed);
                if (strpos($lastSegment, '.') !== false && $lastSegment !== '.' && $lastSegment !== '..') {
                    $isBaseFile = true;
                }
            }

            if ($isBaseFile) {
                $baseDir = dirname($basePath === '/' ? '/dummy' : $basePath);
                $baseDir = ($baseDir === '.') ? '/' : $baseDir.'/';
            } else {
                $baseDir = $basePath;
                if (substr($baseDir, -1) !== '/') {
                    $baseDir .= '/';
                }
            }

            $fullPath = $baseDir.$url;
            $path = str_replace('\\', '/', $fullPath);
            $parts = explode('/', $path);
            $result = [];

            foreach ($parts as $part) {
                if ($part === '' || $part === '.') {
                    continue;
                } elseif ($part === '..') {
                    if (! empty($result)) {
                        array_pop($result);
                    }
                } else {
                    $result[] = $part;
                }
            }

            $normalized = implode('/', $result);
            $wasDirectory = substr($path, -1) === '/';
            $normalized = '/'.ltrim($normalized, '/');

            if ($wasDirectory && substr($normalized, -1) !== '/') {
                $normalized .= '/';
            }

            return $scheme.'://'.$host.$normalized;
        };

        if ($isHtml) {
            return preg_replace_callback(
                '#(href|src)=["\'](.+?)["\']#i',
                function ($matches) use ($baseUrl, $normalize_url_internal) {
                    $url = $matches[2];
                    $normalized = $normalize_url_internal($url, $baseUrl);

                    return $matches[1].'="'.$normalized.'"';
                },
                $path
            );
        }

        return $normalize_url_internal($path, $baseUrl);
    }
}
