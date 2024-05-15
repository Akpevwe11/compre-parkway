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
        'response_payload' => 'array'
    ];

    /** 
     * @return \Stanliwise\CompreParkway\Contract\Subject
     */
    public function subject()
    {
        return $this->morphTo();
    }
}
