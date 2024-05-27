<?php

namespace Stanliwise\CompreParkway\Adaptors;

use Stanliwise\CompreParkway\Contract\FaceTech\Adaptor;
use Stanliwise\CompreParkway\Contract\FaceTech\FaceDetectionService;
use Stanliwise\CompreParkway\Contract\FaceTech\FaceRecognitionService;
use Stanliwise\CompreParkway\Contract\FaceTech\FaceVerificationService;

class AwsFacialAdaptor implements Adaptor
{
    public function getName()
    {
        return self::class;
    }

    public function facialRecognitionService(): FaceRecognitionService
    {
        return app('compreFace.aws.faceRecognition');
    }

    public function facialVerificationService(): FaceVerificationService
    {
        return app('compreFace.aws.faceVerification');
    }

    public function facialDetectionService(): FaceDetectionService
    {
        return app('compreFace.aws.faceDetection');
    }
}
