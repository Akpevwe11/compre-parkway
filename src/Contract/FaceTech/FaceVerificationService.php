<?php

namespace Stanliwise\CompreParkway\Contract\FaceTech;

use Stanliwise\CompreParkway\Adaptors\File\Base64File;
use Stanliwise\CompreParkway\Contract\File;

interface FaceVerificationService
{

    public function compareTwoFileImages(File $source_image, File $target_image);

    public function compareTwoBas64Images(Base64File $source_image, Base64File $target_image);
}
