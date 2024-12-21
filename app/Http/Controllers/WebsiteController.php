<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

class WebsiteController extends Controller
{
  public function index()
  {
    // Fetch all categories (no need to filter H priority images for categories)
    $categories = Category::with('products')->get();

    // Fetch hot products with H priority images
    $hotProducts = Product::hotProducts(); // This already filters for H priority images

    // Fetch products for specific categories (Football, Cricket, etc.)
    $electronicsCategory = Category::where('c_name', 'Electronics')->first();
    $booksCategory = Category::where('c_name', 'Books')->first();
    $furnitureCategory = Category::where('c_name', 'Furniture')->first();
    $clothingCategory = Category::where('c_name', 'Clothing')->first();
    $toysCategory = Category::where('c_name', 'Toys')->first();

    // Get products for each category, only if the category exists, and filter for H priority images
    $electronicsProducts = $electronicsCategory ? $electronicsCategory->products()->whereHas('images', function ($query) {
      $query->where('priority', '1');
    })->get() : [];

    $booksProducts = $booksCategory ? $booksCategory->products()->whereHas('images', function ($query) {
      $query->where('priority', '1');
    })->get() : [];

    $furnitureProducts = $furnitureCategory ? $furnitureCategory->products()->whereHas('images', function ($query) {
      $query->where('priority', '1');
    })->get() : [];

    $clothingProducts = $clothingCategory ? $clothingCategory->products()->whereHas('images', function ($query) {
      $query->where('priority', '1');
    })->get() : [];

    $toysProducts = $toysCategory ? $toysCategory->products()->whereHas('images', function ($query) {
      $query->where('priority', '1');
    })->get() : [];

    return view('website', compact(
      'categories',
      'hotProducts',
      'electronicsProducts',
      'booksProducts',
      'furnitureProducts',
      'clothingProducts',
      'toysProducts'
    ));
  }

  public function details($productId)
  {
    // Fetch the product with its images and category using Eloquent relationships
    $product = Product::with(['category', 'images' => function ($query) {
      $query->orderBy('priority', 'desc'); // Prioritize 'H' images first
    }])->where('p_id', $productId)->firstOrFail();

    // Separate the high priority images
    $mainImage = $product->images->firstWhere('priority', '1');
    $otherImages = $product->images->where('priority', '!=', '1');

    return view('details', compact('product', 'mainImage', 'otherImages'));
  }
}
