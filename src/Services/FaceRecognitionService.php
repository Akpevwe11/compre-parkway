<?php

namespace Stanliwise\CompreParkway\Services;

use Illuminate\Support\Arr;
use Stanliwise\CompreParkway\Adaptors\File\ImageFile;
use Stanliwise\CompreParkway\Contract\FaceTech\FaceRecognitionService as FaceTechFaceRecognitionService;
use Stanliwise\CompreParkway\Contract\File;
use Stanliwise\CompreParkway\Contract\Subject;
use Stanliwise\CompreParkway\Exceptions\FaceHasNotBeenIndexed;
use Stanliwise\CompreParkway\Exceptions\MultipleFaceDetected;

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

    public function addFaceImage(Subject $subject, File $file, $shouldAssociate = true)
    {
        $file = ($file instanceof ImageFile) ? $file->toBase64File() : $file;

        $response = $this->getHttpClient()->asJson()->post('api/v1/recognition/faces?'.http_build_query([
            'subject' => $subject->getUniqueID(),
            'det_prob_threshold' => config('compreFace.trust_threshold'),
        ]), [
            'file' => parent::removeAfterQuote((string) $file),
        ]);

        $array_response = $this->handleFaceHttpResponse($response);

        $image_id = data_get($array_response, 'image_id');
        $array_response['image_uuid'] = $image_id;
        $array_response['similarity_threshold'] = config('compreFace.trust_threshold');

        return $array_response;
    }

    public function verifyFaceImageAgainstASubject(Subject $subject, File $file)
    {
        $remoteTargetUUID = $subject->primaryExample->image_uuid;
        $file = ($file instanceof ImageFile) ? $file->toBase64File() : $file;

        $response = $this->getHttpClient()->asJson()->post("/api/v1/recognition/faces/{$remoteTargetUUID}/verify?".http_build_query([
            'subject' => $subject->getUniqueID(),
            'face_plugins' => $this->getPlugings(),
            'det_prob_threshold' => config('compreFace.trust_threshold'),
        ]), ['file' => parent::removeAfterQuote((string) $file)]);

        return $this->handleFaceHttpResponse($response);
    }

    public function findUserUsingImage(File $file): string
    {
        $file = ($file instanceof ImageFile) ? $file->toBase64File() : $file;
        $response = $this->getHttpClient()->asJson()->post('api/v1/recognition/recognize?'.http_build_query([
            'face_plugins' => $this->getPlugings(),
            'det_prob_threshold' => config('compreFace.trust_threshold'),
        ]), ['file' => parent::removeAfterQuote((string) $file)]);

        $toArray = (array) $response->json();

        $subject = Arr::get($toArray, 'result.0.subjects');

        if (count($subject) == 0) {
            throw new FaceHasNotBeenIndexed;
        }

        if (count($subject) > 1) {
            throw new MultipleFaceDetected;
        }

        return data_get($subject, '0.subject');
    }
}
