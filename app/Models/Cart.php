<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
  use HasFactory;

  protected $table = 'cart'; // Ensure this is the correct table name

  protected $primaryKey = 'c_id'; // Replace with your actual primary key

  public $timestamps = false;

  protected $fillable = [
    'id',
    'p_mode',
    'c_status',
    't_amount',
    'created_on',
    'updated_on',
    'ip_address',
    'transaction_details',
  ];

  // Define relationships (for example, a cart has many cart products)
  public function cartItems()
  {
    return $this->hasMany(CartItem::class, 'c_id', 'c_id');
  }

  // Optionally, if you want to get the user who owns the cart
  public function user()
  {
    return $this->belongsTo(User::class, 'id', 'id');
  }
}
