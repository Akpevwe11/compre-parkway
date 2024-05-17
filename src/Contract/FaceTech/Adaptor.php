<?php

namespace Stanliwise\CompreParkway\Contract\FaceTech;

interface Adaptor
{
    public function getName();

    public function facialRecognitionService(): FaceRecognitionService;

    public function facialVerificationService(): FaceVerificationService;

    public function facialDetectionService(): FaceDetectionService;
}
