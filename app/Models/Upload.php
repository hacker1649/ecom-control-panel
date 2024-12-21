<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
  use HasFactory;

  // Specify the table name explicitly
  protected $table = 'upload';
  public  $timestamps = false;

  // Specify the fillable columns for mass assignment
  protected $fillable = [
    'id',
    'f_name',
    'f_size',
    'f_path',
    'created_at',
    'updated_at',
    'f_status'
  ];

  // Define a relationship with the User model
  public function user()
  {
    return $this->belongsTo(User::class, 'id');
  }
}
