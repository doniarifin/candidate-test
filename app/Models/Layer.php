<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Layer extends Model
{
    protected $fillable = [
        'layup_id',
        'layer_order',
        'thickness',
        'width',
        'angle',
        'grade',
    ];

    public function layup()
    {
        return $this->belongsTo(Layup::class);
    }
}
