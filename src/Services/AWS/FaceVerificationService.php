<?php

namespace Stanliwise\CompreParkway\Services\AWS;

use Aws\Rekognition\Exception\RekognitionException;
use Exception;
use GuzzleHttp\Psr7\Response;
use Stanliwise\CompreParkway\Contract\FaceTech\FaceVerificationService as FaceTechFaceVerificationService;
use Stanliwise\CompreParkway\Contract\File;
use Stanliwise\CompreParkway\Exceptions\FaceDoesNotMatch;
use Stanliwise\CompreParkway\Exceptions\NoFaceWasDetected;

class FaceVerificationService extends BaseService implements FaceTechFaceVerificationService
{
    protected function handleHttpResponse(\Aws\Result $response)
    {
        $toArray = $response->toArray();

        $sourceImage = data_get($toArray, 'SourceImageFace');
        $confidence = data_get($sourceImage, 'Confidence');
        $faceMatches = data_get($toArray, 'FaceMatches');
        $similarity = data_get($faceMatches, '0.Similarity');

        if (!$sourceImage)
            throw new NoFaceWasDetected;

        if ($confidence < (config('compreFace.trust_threshold') * 100))
            throw new NoFaceWasDetected('No Face was detected in source Image');

        if (!$faceMatches || ($similarity < (config('compreFace.trust_threshold') * 100)))
            throw new FaceDoesNotMatch;

        if (count($faceMatches) > 1)
            throw new Exception('Multiple Faces Detected');

        return $toArray;
    }

    public function compareTwoFileImages(File $source_image, File $target_image)
    {
        (new FaceDetectionService)->detectFileImage($source_image);

        $response = $this->getHttpClient()->compareFaces([
            'SimilarityThreshold' => (config('compreFace.trust_threshold') * 100),
            'SourceImage' => [
                'Bytes' => $source_image->getContent(),
            ],
            'TargetImage' => [
                'Bytes' => $target_image->getContent(),
            ]
        ]);
        return $this->handleHttpResponse($response);
    }

    public function compareTwoBas64Images(string $source_image, string $target_image)
    {
    }
}
