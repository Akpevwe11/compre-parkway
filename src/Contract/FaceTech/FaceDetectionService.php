<?php

namespace Stanliwise\CompreParkway\Contract\FaceTech;

use Stanliwise\CompreParkway\Contract\File;

interface FaceDetectionService
{
    public function detectFileImage(File $file);

    public function detectBase64Image(string $file);
}
