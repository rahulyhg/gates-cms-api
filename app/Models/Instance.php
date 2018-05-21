<?php

# app/Models/Instance.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Instance extends Model  
{
  protected $table = 'instances';

  protected $fillable = [
    'id',
    'year',
    'month',
    'date',
    'state_abr',
    'crime_type',
    'crimeCount',
    'lat',
    'long',
    'tract_id',
    'population'
  ];
  
  /**
  * Get the crime records associated with the instance.
  */
  public function tract()
  {
    return $this->belongsTo('App\Models\Tract');
  }

}