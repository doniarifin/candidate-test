<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'code',
        'email',
        'location',
        'certifications',
        'last_audit_date',
        'status'
    ];

    public function layups()
    {
        return $this->hasMany(Layup::class);
    }
}
