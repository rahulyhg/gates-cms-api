<?php

# app/Models/City.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class City extends Model  
{
  protected $table = 'cities';
  protected $fillable = ['title','slug','body','photo','state_id'];
  /**
   * Get the phone record associated with the user.
   */
  public function state()
  {
    return $this->belongsTo('App\Models\State');
  }
}