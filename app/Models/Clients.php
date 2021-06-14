<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class clients extends Model
{
  protected $fillable = [
      'username',
      'email',
      'token'
  ];
}
