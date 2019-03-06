<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImportFile extends Model
{
    /**
     * The attrisbutes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'filename', 'status'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function importData()
    {
        return $this->belongsTo('App\importData');
    }    
}
