<?php

# app/Models/Media.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Media extends Model  
{
  protected $table = 'media';
  protected $fillable = ['id', 'name', 'cloudinary', 'city_id'];
}