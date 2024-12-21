<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
  use HasFactory;

  protected $table = 'category'; // Ensure this is the correct table name

  public $timestamps = false;

  protected $fillable = [
    'c_name',
    'c_path',
    'created_at',
    'updated_at',
  ];

  public function products()
  {
    return $this->hasMany(Product::class, 'c_id', 'c_id');
  }

  // Define method to get category ID by name
  public static function getCategoryIdByName(string $categoryName): ?int
  {
    $id = self::query()
      ->where('c_name', $categoryName)
      ->value('c_id');

    return $id;
  }
}
