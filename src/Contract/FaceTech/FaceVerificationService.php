<?php

namespace Stanliwise\CompreParkway\Contract\FaceTech;

use Stanliwise\CompreParkway\Contract\File;

interface FaceVerificationService
{

    public function compareTwoFileImages(File $source_image, File $target_image);

    public function compareTwoBas64Images(string $source_image, string $target_image);
}
