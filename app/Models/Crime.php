<?php

# app/Models/Crime.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Crime extends Model  
{
  protected $table = 'crimes';
  protected $fillable = ['id', 'name'];

  /**
   * Get the data.
   */
  public function data()
  {
    return $this->hasMany('App\Models\Data');
  }

}