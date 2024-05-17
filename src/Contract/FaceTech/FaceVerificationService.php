<?php

namespace Stanliwise\CompreParkway\Contract\FaceTech;

use Stanliwise\CompreParkway\Contract\File;

interface FaceVerificationService
{
    public function compareTwoFaceImages(File $source_image, File $target_image);
}
