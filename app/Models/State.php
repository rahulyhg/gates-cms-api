<?php

# app/Models/State.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class State extends Model  
{
  protected $table = 'states';
  protected $fillable = ['id', 'title','slug','abbreviation'];

  /**
   * Get the cities.
   */
  public function cities()
  {
    return $this->hasMany('App\Models\City');
  }
}