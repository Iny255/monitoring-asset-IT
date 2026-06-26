<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MapingAccess extends Model
{
    use HasFactory;
     protected $fillable = [

        'maping_id',

        'access_id',

        'status'

    ];

    public function maping()
    {
        return $this->belongsTo(Maping::class);
    }

    public function access()
    {
        return $this->belongsTo(Access::class);
    }
}
