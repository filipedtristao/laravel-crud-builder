<?php

namespace CrudBuilder\Tests\TestClasses\Models;

use Illuminate\Database\Eloquent\Model;

class BandModel extends Model
{
    protected $table = 'bands';
    protected $guarded = [];

    public function members()
    {
        return $this->hasMany(SingerModel::class, 'band_id');
    }
}