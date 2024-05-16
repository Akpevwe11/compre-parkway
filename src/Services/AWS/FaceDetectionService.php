<?php

namespace Stanliwise\CompreParkway\Services\AWS;

use Stanliwise\CompreParkway\Contract\FaceTech\FaceDetectionService as FaceTechFaceDetectionService;
use Stanliwise\CompreParkway\Contract\File;
use Stanliwise\CompreParkway\Exceptions\NoFaceWasDetected;

class FaceDetectionService extends BaseService implements FaceTechFaceDetectionService
{
    protected function handleHttpResponse(\Aws\Result $response)
    {
        $toArray = $response->toArray();

        $faceDetails = data_get($toArray, 'FaceDetails');
        $confidence = data_get($toArray, 'FaceDetails.0.Confidence');

        if (!$faceDetails)
            throw new NoFaceWasDetected;

        if ($confidence <  (config('compreFace.trust_threshold') * 100))
            throw new NoFaceWasDetected;

        return $toArray;
    }

    public function detectFileImage(File $file)
    {
        $this->detectBase64Image(base64_encode($file->getContent()));
    }

    public function detectBase64Image(string $file)
    {
        $response = $this->getHttpClient()->detectFaces([
            "Image" => [
                'Bytes' => base64_decode($file),
            ]
        ]);

        return $this->handleHttpResponse($response);
    }
}
