<?php

# app/Models/Source.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Source extends Model  
{
  protected $table = 'sources';
  protected $fillable = ['id', 'name'];

  /**
   * Get the data.
   */
  public function data()
  {
    return $this->hasMany('App\Models\Data');
  }

}