<?php

# app/Models/Data.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Data extends Model  
{
  protected $table = 'data';
  protected $fillable = ['id', 'date', 'datatype', 'city_id', 'crime_id', 'crimeCount', 'per100k', 'population'];
  
  /**
  * Get the city records associated with the data.
  */
  public function city()
  {
    return $this->belongsTo('App\Models\City');
  }

  /**
  * Get the crime records associated with the data.
  */
  public function crime()
  {
    return $this->belongsTo('App\Models\Crime');
  }
}