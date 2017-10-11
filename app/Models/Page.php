<?php

# app/Models/Page.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Page extends Model  
{
  protected $table = 'pages';
  protected $fillable = ['title','slug','body'];
}