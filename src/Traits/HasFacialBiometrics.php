<?php

namespace Stanliwise\CompreParkway\Traits;

use Stanliwise\CompreParkway\Contract\File;
use Stanliwise\CompreParkway\Facade\FaceTech;
use Stanliwise\CompreParkway\Models\Example;

trait HasFacialBiometrics
{
    public function primaryExample()
    {
        return $this->morphOne(Example::class, 'exampleable')->where('is_primary', true);
    }

    public function examples()
    {
        return $this->morphMany(Example::class, 'exampleable');
    }

    public function verifiedExamples()
    {
        return $this->morphMany(Example::class, 'exampleable');
    }

    public function addFaceImage(File $file, $driver = 'local')
    {
        return FaceTech::addSecondaryExample($this, $file, $driver);
    }

    public function enroll(File $file)
    {
        return FaceTech::enroll($this, $file);
    }
}
