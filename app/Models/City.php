<?php

# app/Models/City.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class City extends Model  
{
  protected $table = 'cities';
  protected $fillable = ['id', 'title','slug','body','photo','state_id', 'populationGroup', 'county', 'long', 'lat'];
  /**
   * Get the state records associated with the city.
   */
  public function state()
  {
    return $this->belongsTo('App\Models\State');
  }

  /**
   * Get the data.
   */
  public function data()
  {
    return $this->hasMany('App\Models\Data');
  }

  /**
   * Get the data.
   */
  public function county()
  {
    return $this->hasMany('App\Models\County');
  }
}