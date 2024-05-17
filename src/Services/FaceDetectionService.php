<?php

namespace Stanliwise\CompreParkway\Services;

use Illuminate\Http\Client\RequestException;
use Stanliwise\CompreParkway\Adaptors\File\Base64File;
use Stanliwise\CompreParkway\Adaptors\File\ImageFile;
use Stanliwise\CompreParkway\Contract\FaceTech\FaceDetectionService as FaceTechFaceDetectionService;
use Stanliwise\CompreParkway\Contract\File;
use Stanliwise\CompreParkway\Exceptions\NoFaceWasDetected;

class FaceDetectionService extends BaseService implements FaceTechFaceDetectionService
{
    public function getHttpClient()
    {
        return parent::getHttpClient()->withHeaders(['x-api-key' => config('compreFace.detection_api_key')]);
    }


    public function handleFaceHttpResponse($response): array
    {
        try {
            return parent::handleFaceHttpResponse($response);
        } catch (RequestException $th) {
            if ($th->response->json('code') === 28)
                throw new NoFaceWasDetected;

            throw $th;
        }
    }


    public function detectFileImage(File $file)
    {
        $response =  $this->getHttpClient()->asMultipart()->attach('file', $file->getContent(),  $file->getFilename())->post('/api/v1/detection/detect?' . http_build_query([
            'face_plugins' => $this->getPlugings(),
            'det_prob_threshold' => config('compreFace.trust_threshold')
        ]));

        return static::handleFaceHttpResponse($response);
    }

    public function detectFace(File $file)
    {
        $file = ($file instanceof ImageFile) ? $file->toBase64File() : $file;
        $response = $this->getHttpClient()->asJson()->post('/api/v1/recognition/faces?' . http_build_query([
            'face_plugins' => $this->getPlugings(),
            'det_prob_threshold' => config('compreFace.trust_threshold')
        ]), [
            "file" => (string) $file
        ]);

        return static::handleFaceHttpResponse($response);
    }
}
