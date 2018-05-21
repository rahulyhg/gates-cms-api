<?php

# app/Models/Tract.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;

final class Tract extends Model  
{
  use SpatialTrait;

  protected $table = 'tracts';
  protected $fillable = ['id', 'name'];

  protected $spatialFields = [
      'area'
  ];

  public function county()
  {
    return $this->belongsTo('App\Models\County');
  }

  public function instance()
  {
    return $this->hasMany('App\Models\Instance');
  }
}