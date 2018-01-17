<?php

# app/Models/Sheet.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Sheet extends Model  
{
  protected $table = 'sheets';
  protected $fillable = ['id', 'data', 'type'];
}