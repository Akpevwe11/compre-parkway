<?php

namespace Stanliwise\CompreParkway\Adaptors;

use Stanliwise\CompreParkway\Contract\FaceTech\Adaptor;
use Stanliwise\CompreParkway\Contract\FaceTech\FaceDetectionService;
use Stanliwise\CompreParkway\Contract\FaceTech\FaceRecognitionService;
use Stanliwise\CompreParkway\Contract\FaceTech\FaceVerificationService;

class CompreFaceFacialAdaptor implements Adaptor
{
    public function getName()
    {
        return 'compreFace';
    }

    public function facialRecognitionService(): FaceRecognitionService
    {
        return app('compreFace.faceRecognition');
    }

    public function facialVerificationService(): FaceVerificationService
    {
        return app('compreFace.faceVerification');
    }

    public function facialDetectionService(): FaceDetectionService
    {
        return app('compreFace.faceDetection');
    }
}
