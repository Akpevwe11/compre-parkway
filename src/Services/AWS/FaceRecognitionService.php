<?php

namespace Stanliwise\CompreParkway\Services\AWS;

use Illuminate\Http\File;
use Stanliwise\CompreParkway\Contract\FaceTech\FaceRecognitionService as FaceTechFaceRecognitionService;
use Stanliwise\CompreParkway\Contract\Subject;
use Stanliwise\CompreParkway\Exceptions\MultipleFaceDetected;
use Stanliwise\CompreParkway\Exceptions\NoFaceWasDetected;

class FaceRecognitionService extends BaseService implements FaceTechFaceRecognitionService
{
    protected function handleHttpResponse(\Aws\Result $response, string $type = '')
    {
        $toArray = $response->toArray();

        if($type == 'addImage'){
            $faceRecords = data_get($toArray, 'FaceRecords');

            if(count($faceRecords) > 1)
                throw new MultipleFaceDetected;

            /** @var array */
            $toArray = $faceRecords[0];
        }

        $faceDetails = data_get($toArray, 'FaceDetails');
        $confidence = data_get($faceDetails, 'Confidence');

        if (!$faceDetails)
            throw new NoFaceWasDetected;

        if ($confidence <  (config('compreFace.trust_threshold') * 100))
            throw new NoFaceWasDetected;

        return $toArray;
    }

    public function createCollection(string $collectionID)
    {
        $response = $this->getHttpClient()->createCollection([
            "CollectionId" => $collectionID
        ]);

        return $this->handleHttpResponse($response);
    }

    public function enrollSubject(Subject $subject)
    {
        $response = $this->getHttpClient()->createUser([
            "ClientRequestToken" => (string) $subject->getUniqueID() . config('compreFace.aws_collection_id'),
            'CollectionId' => config('compreFace.aws_collection_id'),
            'UserId' => $subject->getUniqueID(),
        ]);

        $this->handleHttpResponse($response);
    }

    public function addImage(Subject $subject, File $file)
    {
       $indexFace = $this->getHttpClient()->indexFaces([
            "CollectionId" => config('compreFace.aws_collection_id'),
            "DetectionAttributes" => ["ALL"],
            "ExternalImageId" => $uid = $file->getFilename() . $subject->getUniqueID(),
            "Image" => [
                "Bytes" => $file->getContent()
            ],
            "MaxFaces" => 1,
            "QualityFilter" => "AUTO"
        ]);

        $faceDetails = $this->handleHttpResponse($indexFace, 'addImage');

        $response2 = $this->getHttpClient()->associateFaces([
            "ClientRequestToken" => "string",
            "CollectionId" => "string",
            "FaceIds" => [$face_id = data_get($faceDetails, 'Face.FaceId')],
            "UserId" => $subject->getUniqueID(),
            "UserMatchThreshold" => (config('compreFace.trust_threshold') * 100),
        ]);



        return $faceDetails + ["image_uuid" => $face_id];
    }

    public function addImageBase64(Subject $subject, string $file)
    {
        
    }

    public function disenrollSubject(Subject $subject)
    {
        $response = $this->getHttpClient()->deleteUser([
            "ClientRequestToken" => "string",
            "CollectionId" => "string",
            "UserId" => $subject->getUniqueID(),
        ]);
    }

    public function remove(string $image_uuid)
    {
        
    }

    public function removeAll(Subject $subject)
    {
        
    }

    public function verifyFileImageAgainstSubjectRemoteExample(Subject $subject, File $source, string $remoteTargetUUID)
    {
        
    }

    public function verifyBase64ImageAgainstSubjectRemoteExample(Subject $subject, string $base64Source, string $remoteTargetUUID)
    {
        
    }
}
