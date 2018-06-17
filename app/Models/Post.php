<?php

# app/Models/Post.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Post extends Model  
{
  protected $table = 'posts';
  protected $fillable = ['id', 'title', 'slug', 'body', 'photo', 'date'];

}