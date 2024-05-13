<?php

namespace Tests\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stanliwise\CompreParkway\Contract\Subject;
use Stanliwise\CompreParkway\Traits\HasFacialBiometrics;
use Tests\Database\Factories\UserFactory;

class User extends Model implements Subject
{
    use HasFactory, HasFacialBiometrics;

    public static function newFactory()
    {
        return UserFactory::new();
    }
}
