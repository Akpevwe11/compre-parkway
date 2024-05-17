<?php

namespace Stanliwise\CompreParkway\Services;

use Stanliwise\CompreParkway\Adaptors\File\ImageFile;
use Stanliwise\CompreParkway\Contract\FaceTech\FaceVerificationService as FaceTechFaceVerificationService;
use Stanliwise\CompreParkway\Contract\File;

class FaceVerificationService extends BaseService implements FaceTechFaceVerificationService
{
    public function getHttpClient()
    {
        return parent::getHttpClient()->withHeaders(['x-api-key' => config('compreFace.verification_api_key')]);
    }

    public function compareTwoFileImages(File $source_image, File $target_image)
    {
        $response = $this->getHttpClient()->asMultipart()
            ->attach('source_image', $source_image->getContent(), $source_image->path())
            ->attach('target_image', $target_image->getContent(), $target_image->path())
            ->post('api/v1/recognition/faces?'.http_build_query([
                'det_prob_threshold' => config('compreFace.trust_threshold'),
                'face_plugins' => $this->getPlugings(),
            ]));

        return $this->handleFaceHttpResponse($response);
    }

    public function compareTwoFaceImages(File $source_image, File $target_image)
    {
        $source_image = ($source_image instanceof ImageFile) ? $source_image->toBase64File() : $source_image;
        $target_image = ($target_image instanceof ImageFile) ? $target_image->toBase64File() : $target_image;

        $response = $this->getHttpClient()->asJson()->post('api/v1/recognition/faces?'.http_build_query([
            'det_prob_threshold' => config('compreFace.trust_threshold'),
            'face_plugins' => $this->getPlugings(),
        ]), [
            'source_image' => (string) $source_image,
            'target_image' => (string) $target_image,
        ]);

        return $this->handleFaceHttpResponse($response);
    }
}
