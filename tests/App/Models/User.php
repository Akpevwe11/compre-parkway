<?php

namespace Tests\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stanliwise\CompreParkway\Contract\Subject;
use Stanliwise\CompreParkway\Traits\HasFacialBiometrics;
use Tests\Database\Factories\UserFactory;

/**
 * @property-read \Stanliwise\CompreParkway\Models\Example $readExample
 * @property-read string $face_uuid
 */
class User extends Model implements Subject
{
    use HasFacialBiometrics, HasFactory;

    public static function newFactory()
    {
        return UserFactory::new();
    }

    public function setfacialUUID(string $uuid)
    {
        $this->face_uuid = $uuid;
        $this->save();
    }

    public function getUniqueID()
    {
        return $this->face_uuid;
    }
}
