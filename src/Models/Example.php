<?php

namespace Stanliwise\CompreParkway\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Example extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'response_payload' => 'array',
    ];

    public function subject()
    {
        return $this->morphTo();
    }
}
