<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class ProductFilter
{
  protected Request $request;
  protected Builder|Relation $query;
  protected array $exclude = [];

  public function __construct(Request $request, array $exclude = [])
  {
    $this->request = $request;
    $this->exclude = $exclude;
  }

  /**
   * Apply filters to a query
   */
  public function apply(Builder|Relation $query): Builder|Relation
  {
    $this->query = $query;

    $this->priceFrom();
    $this->priceTo();
    $this->brands();
    $this->seasons();
    $this->materials();
    $this->sizes();
    $this->categories();
    $this->subcategories();
    $this->sort();

    return $this->query;
  }

  protected function priceFrom()
  {
    if (in_array('priceFrom', $this->exclude) || !$this->request->filled('priceFrom')) return;

    $price = (int) $this->request->priceFrom;

    $this->query->where(function ($q) use ($price) {
        $q->whereNotNull('discounted_price')->where('discounted_price', '>=', $price)
          ->orWhere(function ($q2) use ($price) {
              $q2->whereNull('discounted_price')->where('price', '>=', $price);
          });
    });
  }

  protected function priceTo()
  {
    if (in_array('priceTo', $this->exclude) || !$this->request->filled('priceTo')) return;

    $price = (int) $this->request->priceTo;

    $this->query->where(function ($q) use ($price) {
        $q->whereNotNull('discounted_price')->where('discounted_price', '<=', $price)
          ->orWhere(function ($q2) use ($price) {
              $q2->whereNull('discounted_price')->where('price', '<=', $price);
          });
    });
  }

  protected function brands()
  {
    if (in_array('brands', $this->exclude) || !$this->request->filled('brands')) return;

    $this->query->whereIn('brand_id', explode(',', $this->request->brands));
  }

  protected function seasons()
  {
    if (in_array('seasons', $this->exclude) || !$this->request->filled('seasons')) return;

    $this->query->whereIn('season_id', explode(',', $this->request->seasons));
  }

  protected function materials()
  {
    if (in_array('materials', $this->exclude) || !$this->request->filled('materials')) return;

    $this->query->whereIn('material_id', explode(',', $this->request->materials));
  }

  protected function categories()
  {
    if (in_array('categories', $this->exclude) || !$this->request->filled('categories')) return;

    $this->query->whereIn('category_id', explode(',', $this->request->categories));
  }

  protected function subcategories()
  {
    if (in_array('subcategories', $this->exclude) || !$this->request->filled('subcategories')) return;

    $this->query->whereIn('subcategory_id', explode(',', $this->request->subcategories));
  }

  protected function sizes()
  {
    if (in_array('sizes', $this->exclude) || !$this->request->filled('sizes')) return;

    $sizes = explode(',', $this->request->sizes);

    $this->query->whereHas('sizes', fn($q) => $q->whereIn('sizes.id', $sizes));
  }

  protected function sort()
  {
    if (!$this->request->filled('sortBy')) return;

    match ($this->request->sortBy) {
        'price_asc'  => $this->query->orderBy('products.price', 'asc'),
        'price_desc' => $this->query->orderBy('products.price', 'desc'),
        'newest'     => $this->query->orderBy('products.created_at', 'desc'),
        'oldest'     => $this->query->orderBy('products.created_at', 'asc'),
        default      => null,
    };
  }
}
