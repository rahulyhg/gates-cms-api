<?php

# app/Models/Member.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Member extends Model  
{
  protected $table = 'members';
  protected $fillable = ['id', 'title','slug','body', 'photo'];
}