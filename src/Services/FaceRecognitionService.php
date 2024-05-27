<?php

namespace Stanliwise\CompreParkway\Services;

use Stanliwise\CompreParkway\Adaptors\File\ImageFile;
use Stanliwise\CompreParkway\Contract\FaceTech\FaceRecognitionService as FaceTechFaceRecognitionService;
use Stanliwise\CompreParkway\Contract\File;
use Stanliwise\CompreParkway\Contract\Subject;

class FaceRecognitionService extends BaseService implements FaceTechFaceRecognitionService
{
    public function getHttpClient()
    {
        return parent::getHttpClient()->withHeaders(['x-api-key' => config('compreFace.recognition_api_key')]);
    }

    public function enrollSubject(Subject $subject)
    {
        $response = $this->getHttpClient()->asJson()->post('api/v1/recognition/subjects', [
            'subject' => $subject->getUniqueID(),
        ]);

        return $this->handleFaceHttpResponse($response);
    }

    public function disenrollSubject(Subject $subject)
    {
        $response = $this->getHttpClient()->asJson()->delete("api/v1/recognition/subjects/{$subject->getUniqueID()}");

        return $this->handleFaceHttpResponse($response);
    }

    public function removeAllFaceImages(Subject $subject)
    {
        $response = $this->getHttpClient()->delete('/api/v1/recognition/faces?'.http_build_query([
            'subject' => $subject->getUniqueID(),
        ]));

        return $this->handleFaceHttpResponse($response);
    }

    public function removeFaceImage(string $image_uuid)
    {
        $response = $this->getHttpClient()->delete('/api/v1/recognition/faces?'.http_build_query([
            'image_id' => $image_uuid,
        ]));

        return $this->handleFaceHttpResponse($response);
    }

    public function addFaceImage($subject_uuid, File $file)
    {
        $file = ($file instanceof ImageFile) ? $file->toBase64File() : $file;

        $response = $this->getHttpClient()->asJson()->post('api/v1/recognition/faces?'.http_build_query([
            'subject' => $subject_uuid,
            'det_prob_threshold' => config('compreFace.trust_threshold'),
        ]), [
            'file' => (string) $file,
        ]);

        return $this->handleFaceHttpResponse($response);
    }

    public function verifyFaceImageAgainstASubject(Subject $subject, File $file)
    {
        $remoteTargetUUID = $subject->primaryExample->image_uuid;
        $file = ($file instanceof ImageFile) ? $file->toBase64File() : $file;

        $response = $this->getHttpClient()->asJson()->post("/api/v1/recognition/faces/{$remoteTargetUUID}/verify?".http_build_query([
            'subject' => $subject->getUniqueID(),
            'face_plugins' => $this->getPlugings(),
            'det_prob_threshold' => config('compreFace.trust_threshold'),
        ]), ['file' => (string) $file]);

        return $this->handleFaceHttpResponse($response);
    }

    public function findUserUsingImage(File $image): string
    {
        return '';
    }
}
