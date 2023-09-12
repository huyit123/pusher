<?php

namespace Tests\Browser;

use App\Helper\MyEvent;
use App\Models\CrawlStep;
use App\Models\Good;
use Exception;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Collection;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExampleTest extends DuskTestCase
{

    public function testBasicExample(): void
    {
        new MyEvent('hello world');
        dd('done');
        $arrayPage = array(
            ['id' => 0, 'page' => 'https://warehouse-asia.com/vi/red-wine.html', 'name' => 'Rượu Vang Đỏ'],
            ['id' => 1, 'page' => 'https://warehouse-asia.com/dn-haichau/vi/white-wine.html', 'name' => 'Rượu Vang Trắng'],
            ['id' => 2, 'page' => 'https://warehouse-asia.com/dn-haichau/vi/rose-wine.html', 'name' => 'Rượu Vang Hồng'],
            ['id' => 3, 'page' => 'https://warehouse-asia.com/dn-haichau/vi/champagne-sparkling.html', 'name' => 'SÂM BANH & VANG SỦI'],
            ['id' => 4, 'page' => 'https://warehouse-asia.com/dn-haichau/vi/beverages.html', 'name' => 'NƯỚC UỐNG'],
            ['id' => 5, 'page' => 'https://warehouse-asia.com/dn-haichau/vi/spirits-2.html', 'name' => 'Rượu Mạnh'],
        );

        foreach ($arrayPage as $pager) {
            $this->browse(function (Browser $browser) use ($pager) {
                $originalUrl = $pager['page'];
                $browser->visit($originalUrl)
                    ->waitFor('#category-products-grid');
                if ($browser->element('.modals-wrapper')) {
                    $browser->script('document.querySelector(".modals-wrapper").remove();');
                    $browser->script('document.querySelector("body").classList.remove("_has-modal");');
                }
                $pageNumber = 1;

                // if ($browser->elements('.pages')) {

                //     $pageNumber =  $browser->text('.pages .pages-items');
                //     dd($pageNumber);
                // }
                // dd($pageNumber);
                do {
                    $links = $browser->elements('.products ol.products li');
                    $countLink = 0;

                    while ($countLink < count($links)) {
                        $link = $links[$countLink];
                        try {

                            $link->click('a');
                            $browser->waitFor('.page-title-wrapper h1 span');

                            if ($browser->element('.modals-wrapper')) {
                                $browser->script('document.querySelector(".modals-wrapper").remove();');
                            }

                            $browser->pause(1000); // Tạm dừng 2 giây
                            // Lấy dữ liệu và xử lý dữ liệu ở đây
                            $productTitle = $browser->text('.page-title-wrapper h1 span');
                            $imageSrc = $browser->attribute('.fotorama__img', 'src');
                            $category = $pager['name'];
                            $content = '';
                            if ($browser->element('.product.attribute.description')) $content = $browser->text('.product.attribute.description');


                            $country = $browser->element('.product.attribute.description') ? $browser->text('.product-ads-content ul li p:first-child') : '';
                            $area_grape = $browser->text('.product-ads-content ul li p:last-child') ? $browser->text('.product-ads-content ul li p:last-child') : '';

                            $grape = $browser->text('.product-ads-content ul li:nth-child(2) p:first-child') ? $browser->text('.product-ads-content ul li:nth-child(2) p:first-child') : '';

                            $volume =  $browser->text('.product-ads-content ul li:nth-child(3) p:last-child') ? $browser->text('.product-ads-content ul li:nth-child(3) p:last-child') : '';
                            $alcohol_level =  $browser->text('.product-ads-content ul li:nth-child(4) p:first-child') ? $browser->text('.product-ads-content ul li:nth-child(4) p:first-child') : '';

                            $good = new Good();
                            $good->name = $productTitle;
                            $good->image = $imageSrc;
                            $good->category = $category;
                            $good->description = $content;

                            $good->area_grape = $area_grape;
                            $good->grape = $grape;
                            $good->volume = $volume;
                            $good->alcohol_level = $alcohol_level;

                            $good->save();

                            $browser->back();
                            $browser->waitFor('#category-products-grid');
                            $links = $browser->elements('.products ol.products li');
                            if ($browser->element('.modals-wrapper')) {
                                $browser->script('document.querySelector(".modals-wrapper").remove();');
                                $browser->script('document.querySelector("body").classList.remove("_has-modal");');
                            }

                            $browser->pause(2000); // Tạm dừng 2 giây

                            $countLink++;
                        } catch (Exception $e) {
                            $crawlStep = new CrawlStep();
                            $crawlStep->current_page = $pager['id'];
                            $crawlStep->page_index = $pageNumber;
                            $crawlStep->good_id = $countLink;
                            $crawlStep->log = $e->getMessage();
                            $crawlStep->save();
                            die();
                        }
                    }

                    $nextPageLink = $browser->element('.pages-item-next'); // Tìm nút "Trang kế tiếp"
                    if ($nextPageLink) {
                        $nextPageLink->click('a'); // Nếu có, click vào nút "Trang kế tiếp"
                    } else {
                        break; // Nếu không có nút "Trang kế tiếp", thoát khỏi vòng lặp ngoài
                    }
                } while (true); // Lặp cho đến khi hết trang phân trang
            });
        }
    }


    // public function testBasicExample(): void
    // {
    //     $arrayPage = array(
    //         ['id' => 0, 'page' => 'https://warehouse-asia.com/vi/red-wine.html', 'name' => 'Rượu Vang Đỏ'],
    //         ['id' => 1, 'page' => 'https://warehouse-asia.com/dn-haichau/vi/white-wine.html', 'name' => 'Rượu Vang Trắng'],
    //         ['id' => 2, 'page' => 'https://warehouse-asia.com/dn-haichau/vi/rose-wine.html', 'name' => 'Rượu Vang Hồng'],
    //         ['id' => 3, 'page' => 'https://warehouse-asia.com/dn-haichau/vi/champagne-sparkling.html', 'name' => 'SÂM BANH & VANG SỦI'],
    //         ['id' => 4, 'page' => 'https://warehouse-asia.com/dn-haichau/vi/beverages.html', 'name' => 'NƯỚC UỐNG'],
    //         ['id' => 5, 'page' => 'https://warehouse-asia.com/dn-haichau/vi/spirits-2.html', 'name' => 'Rượu Mạnh'],
    //     );

    //     foreach ($arrayPage as $pager) {

    //         $this->browse(function (Browser $browser) use ($pager) {

    //             $originalUrl = $pager['page'];
    //             $browser->visit($originalUrl)
    //                 ->waitFor('.bloque-reconocimiento');

    //             $linkPages = $browser->elements('.pages pages-items item');
    //             $countPageLink = 0;

    //             while (!empty($linkPages)) {
    //                 $linkPage = $linkPages[$countPageLink];
    //                 $links = $browser->elements('.bloque-reconocimiento a');
    //                 $countLink = 0;
    //                 while (!empty($links)) {
    //                     $link = $links[$countLink];
    //                     try {
    //                         $link->click('a');
    //                         $browser->waitFor('.short_product h1');
    //                         $productTitle = $browser->text('.short_product h1');
    //                         $imageSrc = $browser->attribute('.img_con', 'src');
    //                         $category = $pager['name'];
    //                         $content = $browser->text('#ctl00_ContentPlaceHolder1_lbldes');

    //                         $color = $browser->text('#ctl00_ContentPlaceHolder1_lblmausac');
    //                         $brand = $browser->text('#ctl00_ContentPlaceHolder1_lblbrand');
    //                         $production_area = $browser->text('#ctl00_ContentPlaceHolder1_lblregion');
    //                         $grape = $browser->text('#ctl00_ContentPlaceHolder1_lblgrape');
    //                         $volume = $browser->text('#ctl00_ContentPlaceHolder1_lblCapacity');
    //                         $alcohol_level = $browser->text('#ctl00_ContentPlaceHolder1_lblvol');

    //                         $good = new Good();
    //                         $good->name = $productTitle;
    //                         $good->image = $imageSrc;
    //                         $good->category = $category;
    //                         $good->description = $content;

    //                         $good->color = $color;
    //                         $good->brand = $brand;
    //                         $good->production_area = $production_area;
    //                         $good->grape = $grape;
    //                         $good->volume = $volume;
    //                         $good->alcohol_level = $alcohol_level;

    //                         $good->save();
    //                         $browser->back();
    //                         $browser->waitFor('.bloque-reconocimiento');
    //                         $links = $browser->elements('.bloque-reconocimiento a');
    //                         $browser->pause(2000);
    //                         $countLink++;
    //                         if ($countLink >= count($links)) {
    //                             break;
    //                         }
    //                     } catch (Exception $e) {
    //                         $crawlStep = new CrawlStep();
    //                         $crawlStep->current_page = '';
    //                         $crawlStep->page_index = $pager['id'];
    //                         $crawlStep->good_id = $countLink;
    //                         $crawlStep->log = $e->getMessage();
    //                         $crawlStep->save();
    //                         die();
    //                     }
    //                 }
    //             }
    //         });
    //     }
    // }

    // public function testBasicExample(): void
    // {
    //     $arrayPage = array(
    //         ['id' => 0, 'page' => 'https://www.phuem.com.vn/Ruou-Vang-Do-1.html', 'name' => 'Rượu Vang Đỏ'],
    //         ['id' => 1, 'page' => 'https://www.phuem.com.vn/Ruou-Vang-Trang-171.html', 'name' => 'Rượu Vang Trắng'],
    //         ['id' => 2, 'page' => 'https://www.phuem.com.vn/Ruou-Manh-177.html', 'name' => 'Rượu Mạnh'],
    //         ['id' => 3, 'page' => 'https://www.phuem.com.vn/Ruou-Sparkling-179.html', 'name' => 'Rượu Sparkling'],
    //         ['id' => 4, 'page' => 'https://www.phuem.com.vn/Ruou-Vang-Ngot-193.html', 'name' => 'Rượu Vang Ngọt'],
    //         ['id' => 5, 'page' => 'https://www.phuem.com.vn/Ruou-Champagne-195.html', 'name' => 'Rượu Champagne'],
    //     );
    //     $infoProduct = array(
    //         "Mã SP:",
    //         "Loại SP:",
    //         "Nhãn hiệu:",
    //         "Vùng sản xuất:",
    //         "Thành phần:",
    //         "Năm sản xuất:",
    //         "Dung tích:",
    //         "Nồng độ:",
    //         "Giải thưởng:",
    //         "Thông tin về rượu:",
    //         "Thông tin về rượu:",
    //     );
    //     foreach ($arrayPage as $pager) {

    //         $this->browse(function (Browser $browser) use ($pager) {

    //             $originalUrl = $pager['page'];
    //             $browser->visit($originalUrl)
    //                 ->waitFor('.bloque-reconocimiento');

    //             $links = $browser->elements('.bloque-reconocimiento a');
    //             $countLink = 0;
    //             if ($pager['id'] == 0) $countLink = 19;
    //             while (!empty($links)) {
    //                 $link = $links[$countLink];
    //                 try {
    //                     $link->click('a');
    //                     $browser->waitFor('.short_product h1');
    //                     $productTitle = $browser->text('.short_product h1');
    //                     $imageSrc = $browser->attribute('.img_con', 'src');
    //                     $category = $pager['name'];
    //                     $content = $browser->text('#ctl00_ContentPlaceHolder1_lbldes');

    //                     $color = $browser->text('#ctl00_ContentPlaceHolder1_lblmausac');
    //                     $brand = $browser->text('#ctl00_ContentPlaceHolder1_lblbrand');
    //                     $production_area = $browser->text('#ctl00_ContentPlaceHolder1_lblregion');
    //                     $grape = $browser->text('#ctl00_ContentPlaceHolder1_lblgrape');
    //                     $volume = $browser->text('#ctl00_ContentPlaceHolder1_lblCapacity');
    //                     $alcohol_level = $browser->text('#ctl00_ContentPlaceHolder1_lblvol');

    //                     $good = new Good();
    //                     $good->name = $productTitle;
    //                     $good->image = $imageSrc;
    //                     $good->category = $category;
    //                     $good->description = $content;

    //                     $good->color = $color;
    //                     $good->brand = $brand;
    //                     $good->production_area = $production_area;
    //                     $good->grape = $grape;
    //                     $good->volume = $volume;
    //                     $good->alcohol_level = $alcohol_level;

    //                     $good->save();
    //                     $browser->back();
    //                     $browser->waitFor('.bloque-reconocimiento');
    //                     $links = $browser->elements('.bloque-reconocimiento a');
    //                     $browser->pause(2000);
    //                     $countLink++;
    //                     if ($countLink >= count($links)) {
    //                         break;
    //                     }
    //                 } catch (Exception $e) {
    //                     $crawlStep = new CrawlStep();
    //                     $crawlStep->current_page = '';
    //                     $crawlStep->page_index = $pager['id'];
    //                     $crawlStep->good_id = $countLink;
    //                     $crawlStep->log = $e->getMessage();
    //                     $crawlStep->save();
    //                     die();
    //                 }
    //             }
    //         });
    //     }
    // }

    // public function testBasicExample(): void
    // {
    //     for ($page = 2; $page <= 3; $page++) {

    //         $this->browse(function (Browser $browser) use ($page) {

    //             $originalUrl = 'https://wineplaza.vn/collections/all?page=' . $page;
    //             $browser->visit($originalUrl)
    //                 ->waitFor('.setting-des');

    //             $links = $browser->elements('.product-detail');
    //             $countLink = 0;
    //             if ($page == 2) $countLink = 28;
    //             while (!empty($links)) {
    //                 $link = $links[$countLink];
    //                 try {
    //                     $link->click('a');
    //                     $browser->waitFor('.product-title h1');
    //                     $productTitle = $browser->text('.product-title h1');


    //                     $content = $browser->text('.product-title .product-des');
    //                     $category = $browser->text('.breadcrumb.breadcrumb-arrows li:nth-child(2) a');
    //                     $imageSrc = $browser->attribute('.product-image-feature', 'src');
    //                     $imageSrc = str_replace('//', '', $imageSrc);

    //                     $good = new Good();
    //                     $good->name = $productTitle;
    //                     $good->image = $imageSrc;
    //                     $good->category = $category;
    //                     $good->description = $content;

    //                     $position1 = strpos($content, 'Vùng trồng nho: ');
    //                     if ($position1 !== false) {
    //                         $grapeRegionText = trim(substr($content, $position1));
    //                         $lines = explode(PHP_EOL, $grapeRegionText);
    //                         $firstLine0 = str_replace('Vùng trồng nho: ', '', trim($lines[0]));
    //                         $good->area_grape =  $firstLine0;
    //                     }

    //                     $position2 = strpos($content, 'Hãng sản xuất: ');
    //                     if ($position2 !== false) {
    //                         $grapeRegionText = trim(substr($content, $position2));
    //                         $lines = explode(PHP_EOL, $grapeRegionText);
    //                         $firstLine0 = str_replace('Hãng sản xuất: ', '', trim($lines[0]));
    //                         $good->manufacturer =  $firstLine0;
    //                     }

    //                     $position3 = strpos($content, 'Loại rượu: ');
    //                     if ($position3 !== false) {
    //                         $grapeRegionText = trim(substr($content, $position3));
    //                         $lines = explode(PHP_EOL, $grapeRegionText);
    //                         $firstLine0 = str_replace('Loại rượu: ', '', trim($lines[0]));
    //                         $good->type =  $firstLine0;
    //                     }

    //                     $position4 = strpos($content, 'Giống nho: ');
    //                     if ($position4 !== false) {
    //                         $grapeRegionText = trim(substr($content, $position4));
    //                         $lines = explode(PHP_EOL, $grapeRegionText);
    //                         $firstLine0 = str_replace('Giống nho: ', '', trim($lines[0]));
    //                         $good->grape =  $firstLine0;
    //                     }

    //                     $position4 = strpos($content, 'Nồng độ cồn: ');
    //                     if ($position4 !== false) {
    //                         $grapeRegionText = trim(substr($content, $position4));
    //                         $lines = explode(PHP_EOL, $grapeRegionText);
    //                         $firstLine0 = str_replace('Nồng độ cồn: ', '', trim($lines[0]));
    //                         $good->alcohol_level =  $firstLine0;
    //                     }

    //                     $position5 = strpos($content, 'Thể tích: ');
    //                     if ($position5 !== false) {
    //                         $grapeRegionText = trim(substr($content, $position5));
    //                         $lines = explode(PHP_EOL, $grapeRegionText);
    //                         $firstLine0 = str_replace('Thể tích: ', '', trim($lines[0]));
    //                         $good->volume =  $firstLine0;
    //                     }

    //                     $good->save();
    //                     $browser->back();
    //                     $browser->waitFor('.setting-des');
    //                     $links = $browser->elements('.product-detail');

    //                     $countLink++;
    //                     if ($countLink >= count($links)) {
    //                         break;
    //                     }
    //                 } catch (Exception $e) {
    //                     $crawlStep = new CrawlStep();
    //                     $crawlStep->current_page = 'https://wineplaza.vn/collections/all?page=' . $page;
    //                     $crawlStep->page_index = $page;
    //                     $crawlStep->good_id = $countLink;
    //                     $crawlStep->log = $e->getMessage();
    //                     $crawlStep->save();
    //                     die();
    //                 }
    //             }
    //         });
    //     }
    // }
}
