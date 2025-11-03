<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    protected $table = 'kecamatan';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
    ];

    public function kelurahan()
    {
        return $this->hasMany(Kelurahan::class, 'kecamatan_id', 'id');
    }
}
