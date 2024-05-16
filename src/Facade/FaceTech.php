<?php

namespace Stanliwise\CompreParkway\Facade;

use Illuminate\Support\Facades\Facade;
use \Stanliwise\CompreParkway\Contract\Subject;
use Stanliwise\CompreParkway\Adaptors\File;

/**
 * @method bool hasVerifiedFaceImage(Subject $subject)
 * @method bool hasEnrolled(Subject $subject)
 * @method mixed enroll(Subject, File $image_file, ?string $disk_drive = 'local')
 * @method void addSecondaryExample(Subject $subject, File $image_file, string $disk = 'local')
 * @method mixed addExample(Subject $subject, File $image_file, string $type = 'secondary', ?string $disk = 'local')
 * @method array detectFileImage(File $image)
 * @method array compareTwoFileImages(File $sourceImage, File $targeImage)
 */
class FaceTech extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'parkwayFaceService';
    }
}
