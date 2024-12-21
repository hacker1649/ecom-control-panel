<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  use HasFactory;

  protected $table = 'product'; // Ensure this is the correct table name

  public $timestamps = false;

  protected $fillable = [
    'p_name',
    'p_price',
    'p_desc',
    'c_id',
    'popularity',
    'created_on',
    'updated_on',
    'p_status'
  ];

  public function category()
  {
    return $this->belongsTo(Category::class, 'c_id', 'c_id');
  }

  public static function hotProducts()
  {
    // Only fetch products with 'H' priority images
    return self::where('popularity', 1)
      ->with(['images' => function ($query) {
        $query->where('priority', '1');
      }])
      ->limit(10)
      ->get();
  }

  // Fetch images with H priority
  public function imagesWithHPriority()
  {
    return $this->images()->where('priority', '1');
  }

  public function images()
  {
    return $this->hasMany(ProductImages::class, 'p_id', 'p_id');
  }

  // method to fetch products from product table
  public static function fetchProductsWithFilters($productNameSearch = '', $categoryNameSearch = '', $priceSearch = '')
  {
    $products = self::query()
      ->join('category', 'category.c_id', '=', 'product.c_id')
      ->where('product.p_status', 1) // Filter for active products
      ->where('product.p_name', 'like', "%$productNameSearch%")
      ->where('category.c_name', 'like', "%$categoryNameSearch%")
      ->where('product.p_price', 'like', "%$priceSearch%")
      ->orderby('product.p_id');

    return $products;
  }

  // Set the popularity attribute of the product as 1 (high) or 0 (low)
  public static function setPopularity(string $popularity): ?int
  {
    return ($popularity == "Featured") ? 1 : 0;
  }
}
