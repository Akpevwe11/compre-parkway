<?php

namespace Stanliwise\CompreParkway\Traits;

use Illuminate\Http\File;
use Stanliwise\CompreParkway\Models\Example;
use Stanliwise\CompreParkway\Services\ParkwayFaceTechService;

trait HasFacialBiometrics
{

    /**
     * @return mixed
     */
    public function getUniqueID()
    {
        return $this->id;
    }

    public function primaryExample()
    {
        return $this->morphOne(Example::class, 'exampleable');
    }

    public function examples()
    {
        return $this->morphMany(Example::class, 'exampleable');
    }

    public function verifiedExamples()
    {
        return $this->morphMany(Example::class, 'exampleable');
    }

    public function enroll(string $image_path)
    {
        return ParkwayFaceTechService::instance()->enroll($this, new File($image_path));
    }
}
