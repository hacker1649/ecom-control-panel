<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
  use HasFactory;

  protected $table = 'cart_item'; // Ensure this is the correct table name

  protected $primaryKey = 'i_id'; // Replace with your actual primary key

  public $timestamps = false;

  protected $fillable = [
    'c_id',
    'p_id',
    'id',
    'quantity',
    'p_price',
    'tp_price',
    'created_on',
    'updated_on',
    'ip_address',
  ];

  // Define relationships (for example, a cart product belongs to a cart)
  public function cart()
  {
    return $this->belongsTo(Cart::class, 'c_id', 'c_id');
  }

  // Optionally, if you want to get the product associated with the cart product
  public function product()
  {
    return $this->belongsTo(Product::class, 'p_id', 'p_id');
  }
}
