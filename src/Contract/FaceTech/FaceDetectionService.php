<?php

namespace Stanliwise\CompreParkway\Contract\FaceTech;

use Stanliwise\CompreParkway\Adaptors\File\Base64File;
use Stanliwise\CompreParkway\Contract\File;

interface FaceDetectionService
{
    public function detectFileImage(File $file);

    public function detectBase64Image(Base64File $file);
}
