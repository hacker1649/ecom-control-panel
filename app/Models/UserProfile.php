<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
  use HasFactory;

  protected $table = 'users_profile'; // Ensure this matches your table name
  public  $timestamps = false;

  protected $fillable = [
    'id',
    'phone',
    'address',
  ];

  // Profile belongs to a user
  public function user()
  {
    return $this->belongsTo(User::class, 'id');
  }
}
