<?php

# app/Models/County.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class County extends Model  
{
  protected $table = 'counties';
  protected $fillable = ['id', 'STATE','STATEFP','COUNTYNAME', 'CLASSFP'];
  /**
   * Get the data.
   */
  public function tracts()
  {
    return $this->hasMany('App\Models\Tract');
  }

  /**
   * Get the data.
   */
  public function cities()
  {
    return $this->belongsToMany('App\Models\City');
  }

}