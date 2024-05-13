<?php

namespace Stanliwise\CompreParkway\Services;

use Illuminate\Http\File;
use Stanliwise\CompreParkway\Contract\Subject;

class FaceRecognitionService extends BaseService
{
    public function getHttpClient()
    {
        return parent::getHttpClient()->withHeaders(['x-api-key' => config('compreFace.recognition_api_key')]);
    }

    public function enrollSubject(Subject $subject)
    {
        $response = $this->getHttpClient()->asJson()->post('api/v1/recognition/subjects', [
            'subject' => $subject->getUniqueID()
        ]);

        return $this->handleFaceHttpResponse($response);
    }

    public function disenrollSubject(Subject $subject)
    {
        $response = $this->getHttpClient()->asJson()->delete("api/v1/recognition/subjects/{$subject}");

        return $this->handleFaceHttpResponse($response);
    }

    public function addImage(Subject $subject, File $file)
    {
        $response = $this->getHttpClient()->asMultipart()->attach('file', $file->getContent(),  $file->getFilename())->post('/api/v1/recognition/faces?' . http_build_query([
            'subject' => $subject->getUniqueID(),
            'det_prob_threshold' =>  config('compreFace.trust_threshold'),
        ]));

        return $this->handleFaceHttpResponse($response);
    }

    public function removeAll(Subject $subject)
    {
        $response = $this->getHttpClient()->delete('/api/v1/recognition/faces?' . http_build_query([
            'subject' => $subject->getUniqueID(),
        ]));

        return $this->handleFaceHttpResponse($response);
    }

    public function remove(string $image_uuid)
    {
        $response = $this->getHttpClient()->delete('/api/v1/recognition/faces?' . http_build_query([
            'image_id' => $image_uuid,
        ]));

        return $this->handleFaceHttpResponse($response);
    }

    public function addImageBase64(Subject $subject, string $file)
    {
        $response = $this->getHttpClient()->asJson()->post('api/v1/recognition/faces?' . http_build_query([
            'subject' => $subject->getUniqueID(),
            'det_prob_threshold' =>  config('compreFace.trust_threshold'),
        ]), [
            'file' => $file
        ]);

        return $this->handleFaceHttpResponse($response);
    }

    public function verifyFileImageAgainstSubjectRemoteExample(Subject $subject, File $source, string $remoteTargetUUID)
    {
        $response = $this->getHttpClient()->asMultipart()->attach('file', $source->getContent(),  $source->getFilename())->post("/api/v1/recognition/faces/{$remoteTargetUUID}/verify?" . http_build_query([
            'subject' => $subject->getUniqueID(),
            'face_plugins' => $this->getPlugings(),
            'det_prob_threshold' => config('compreFace.trust_threshold')
        ]));

        return $this->handleFaceHttpResponse($response);
    }

    public function verifyBase64ImageAgainstSubjectRemoteExample(Subject $subject, string $base64Source, string $remoteTargetUUID)
    {
        $response = $this->getHttpClient()->asJson()->post("/api/v1/recognition/faces/{$remoteTargetUUID}/verify?" . http_build_query([
            'subject' => $subject->getUniqueID(),
            'face_plugins' => $this->getPlugings(),
            'det_prob_threshold' => config('compreFace.trust_threshold')
        ]), ['file' => $base64Source]);

        return $this->handleFaceHttpResponse($response);
    }
}
