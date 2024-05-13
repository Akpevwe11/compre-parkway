<?php

namespace Stanliwise\CompreParkway\Contract\FaceTech;

use Illuminate\Http\File;

interface FaceDetectionService
{
    public function detectFileImage(File $file);

    public function detectBase64Image(string $file);
}
