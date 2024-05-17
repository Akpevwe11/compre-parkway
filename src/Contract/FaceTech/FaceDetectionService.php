<?php

namespace Stanliwise\CompreParkway\Contract\FaceTech;

use Stanliwise\CompreParkway\Contract\File;

interface FaceDetectionService
{
    public function detectFace(File $file);
}
