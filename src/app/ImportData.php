<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImportData extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'free_shipping', 'description', 'price', 'category_id', 'import_file_id'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function importFiles() {
        return $this->hasMany('App\ImportFile');
    }
}
