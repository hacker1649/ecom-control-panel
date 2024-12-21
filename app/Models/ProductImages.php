<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImages extends Model
{

  protected $table = 'p_img';

  public $timestamps = false;

  protected $fillable = [
    'f_name',
    'f_path',
    'p_id',
    'priority',
    'created_at',
    'updated_at',
  ];

  public function products()
  {
    return $this->belongsTo(Product::class, 'p_id', 'p_id');
  }
}
