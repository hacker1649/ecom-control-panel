<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersProfile extends Model
{
  use HasFactory;

  protected $table = 'user_profiles'; // Ensure this matches your table name

  protected $fillable = [
    'user_id',
    'phone',
    'address',
    'country',
    'state',
    'city',
  ];

  // Profile belongs to a user
  public function user()
  {
    return $this->belongsTo(User::class, 'user_id');
  }
}
