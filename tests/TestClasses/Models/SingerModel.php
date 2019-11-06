<?php

namespace CrudBuilder\Tests\TestClasses\Models;

use Illuminate\Database\Eloquent\Model;

class SingerModel extends Model
{
    protected $table = 'singers';
    protected $guarded = [];

    public function band()
    {
        return $this->belongsTo(BandModel::class, 'band_id');
    }
}