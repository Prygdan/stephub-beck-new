<?php

namespace App\Console\Commands;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use Carbon\Carbon;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate the sitemap.';

    public function handle()
    {
        $sitemap = Sitemap::create()
            ->add(Url::create('https://stephub.store/')
                ->setLastModificationDate(Carbon::now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(1.0));

        $categories = Category::with(['subcategories' => function ($query) {
            $query->orderBy('name');
        }])->orderBy('id', 'asc')->get();
    
        foreach ($categories as $category) {
            // Категорія
            $sitemap->add(Url::create('https://stephub.store/' . "/{$category->slug}")
                ->setLastModificationDate($category->updated_at)
                ->setPriority(0.8));
    
            // Підкатегорії
            foreach ($category->subcategories as $subcategory) {
                $sitemap->add(Url::create('https://stephub.store/' . "/{$category->slug}/{$subcategory->slug}")
                    ->setLastModificationDate($subcategory->updated_at)
                    ->setPriority(0.7));
            }
        }

        $brands = Brand::all();
        foreach ($brands as $item) {
            $sitemap->add(Url::create("https://stephub.store/brand/{$item->slug}")
                ->setLastModificationDate($item->updated_at)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(0.8));
        }
        $products = Product::all();
        foreach ($products as $item) {
            $sitemap->add(Url::create("https://stephub.store/product/{$item->slug}")
                ->setLastModificationDate($item->updated_at)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(0.8));
        }
        $pages = Page::all();
        foreach ($pages as $item) {
            $sitemap->add(Url::create("https://stephub.store/{$item->slug}")
                ->setLastModificationDate($item->updated_at)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(0.8));
        }
        $sitemap->add(Url::create('https://stephub.store/404')
            ->setLastModificationDate(Carbon::now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
            ->setPriority(0.1));

        $sitemap->writeToFile(base_path('../stephub-front/public/sitemap.xml'));

	return 0;
    }
}
