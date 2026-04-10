<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Layup extends Model
{
    protected $fillable = [
        'supplier_id',
        'code',
        'name',
        'grade',
        'revision',
        'status'
    ];

    protected $appends = ['total_thickness', 'ply_count'];

    // total thickness 
    public function getTotalThicknessAttribute()
    {
        return $this->layers->sum('thickness');
    }

    // total ply count
    public function getPlyCountAttribute()
    {
        return $this->layers->count();
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function layers()
    {
        return $this->hasMany(Layer::class)->orderBy('layer_order');
    }
}
