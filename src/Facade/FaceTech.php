<?php

namespace Stanliwise\CompreParkway\Facade;

use Illuminate\Support\Facades\Facade;
use Stanliwise\CompreParkway\Contract\File;
use Stanliwise\CompreParkway\Contract\FaceTech\Adaptor;
use Stanliwise\CompreParkway\Contract\FaceTech\FaceDetectionService;
use Stanliwise\CompreParkway\Contract\FaceTech\FaceRecognitionService;
use Stanliwise\CompreParkway\Contract\FaceTech\FaceVerificationService;
use Stanliwise\CompreParkway\Contract\Subject;

/**
 * @method static void setDriver(Adaptor $adaptor)
 * @method static Adaptor getDriver(Adaptor $adaptor)
 * @method static bool hasVerifiedFaceImage(Subject $subject)
 * @method static bool hasEnrolled(Subject $subject)
 * @method static mixed enroll(Subject, File $image_file, ?string $disk_drive = 'local')
 * @method static mixed disenroll(Subject $subject)
 * @method static void addSecondaryExample(Subject $subject, File $image_file, string $disk = 'local')
 * @method static array detectFileImage(File $image)
 * @method static array compareTwoFileImages(File $sourceImage, File $targeImage)
 * @method static array verifyFaceImageAgainstASubject(Subject $subject, File $file)
 * @method static FaceRecognitionService getFacialRecognitionService()
 * @method static FaceDetectionService getFacialDetectionService()
 * @method static FaceVerificationService getFacialVerificationService()
 *
 * @see \Stanliwise\CompreParkway\Services\ParkwayFaceTechService
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
