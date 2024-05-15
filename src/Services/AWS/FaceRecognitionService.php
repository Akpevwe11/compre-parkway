<?php

namespace Stanliwise\CompreParkway\Services\AWS;

use Exception;
use Illuminate\Http\File;
use Stanliwise\CompreParkway\Contract\FaceTech\FaceRecognitionService as FaceTechFaceRecognitionService;
use Stanliwise\CompreParkway\Contract\Subject;
use Stanliwise\CompreParkway\Exceptions\FaceDoesNotMatch;
use Stanliwise\CompreParkway\Exceptions\MultipleFaceDetected;
use Stanliwise\CompreParkway\Exceptions\NoFaceWasDetected;

class FaceRecognitionService extends BaseService implements FaceTechFaceRecognitionService
{

    public function createCollection(string $collectionID)
    {
        $response = $this->getHttpClient()->createCollection([
            "CollectionId" => $collectionID
        ]);

        return $response->toArray();
    }

    public function enrollSubject(Subject $subject)
    {
        $response = $this->getHttpClient()->createUser([
            "ClientRequestToken" => (string) $subject->getUniqueID() . config('compreFace.aws_collection_id'),
            'CollectionId' => config('compreFace.aws_collection_id'),
            'UserId' => "{$subject->getUniqueID()}",
        ]);

        if ($response)
            return true;
    }

    public function getAlifeSessionID()
    {
        $response = $this->getHttpClient()->createFaceLivenessSession([]);

        //handle logic;
    }

    public function checkLifeSessionResult()
    {
        $response = $this->getHttpClient()->getFaceLivenessSessionResults([
            'ClientRequestToken' => \Illuminate\Support\Str::uuid(),
            'Settings' => [
                'AuditImagesLimit' => 3,
            ],
        ]);

        //handle logic
    }



    public function addImage(Subject $subject, File $file)
    {
        $indexFaceResponse = $this->getHttpClient()->indexFaces([
            "CollectionId" => config('compreFace.aws_collection_id'),
            "DetectionAttributes" => ["ALL"],
            "ExternalImageId" => $uid = $file->getFilename() . $subject->getUniqueID(),
            "Image" => [
                "Bytes" => $file->getContent()
            ],
            "MaxFaces" => 1,
            "QualityFilter" => "AUTO"
        ]);

        $toArray = $indexFaceResponse->toArray();

        $faceRecords = data_get($toArray, 'FaceRecords');

        if (count($faceRecords) > 1)
            throw new MultipleFaceDetected;

        /** @var array */
        $firstFace = $faceRecords[0] ?? null;

        if (!$firstFace)
            throw new NoFaceWasDetected;


        $faceDetails = data_get($firstFace, 'FaceDetail');
        $face = data_get($firstFace, 'Face');
        $confidence = data_get($face, 'Confidence');
        $face_id = data_get($face, 'FaceId');


        if ($confidence <  (config('compreFace.trust_threshold') * 100))
            throw new NoFaceWasDetected;

        //associate Face
        $associatFaceResponse = $this->getHttpClient()->associateFaces([
            "ClientRequestToken" => $subject->getUniqueID() . $face_id,
            "CollectionId" => config('compreFace.aws_collection_id'),
            "FaceIds" => [$face_id],
            "UserId" => "{$subject->getUniqueID()}",
            "UserMatchThreshold" => $similarity_threshold = (config('compreFace.trust_threshold') * 100),
        ]);

        $associatFaces = data_get($associatFaceResponse, 'AssociatedFaces');

        if (count($associatFaces) < 1)
            throw new FaceDoesNotMatch;

        if(count($associatFaces) > 1)
            throw new MultipleFaceDetected;

        return $faceDetails + ["image_uuid" => $face_id, 'similarity_threshold' => $similarity_threshold];
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

    public function removeFace(string $image_uuid)
    {
        $response = $this->getHttpClient()->deleteFaces([
            "CollectionId" => config('compreFace.aws_collection_id'),
            "FaceIds" => [$image_uuid],
        ]);

        return $response->toArray();
    }

    public function removeFaceFromUser(Subject $subject, $image_uuid)
    {
        $response = $this->getHttpClient()->disassociateFaces([]);
    }

    public function removeAll(Subject $subject)
    {
    }

    public function listUsers()
    {
        $response = $this->getHttpClient()->listUsers([
            "CollectionId" => config('compreFace.aws_collection_id'),
            //"MaxResults" =>  20,
            //"NextToken" => 1,
        ]);

        return $response->toArray();
    }

    public function listFaces(?Subject $subject = null)
    {
        $payload = [
            "CollectionId" => config('compreFace.aws_collection_id'),
            //"MaxResults" =>  20,
            //"NextToken" => 1,
        ];

        if ($subject)
            $payload = array_merge($payload, ["UserId" => "{$subject->getUniqueID()}"]);

        $response = $this->getHttpClient()->listFaces($payload);

        return $response->toArray();
    }

    public function verifyFileImageAgainstSubjectRemoteExample(Subject $subject, File $source, string $remoteTargetUUID)
    {
        $response = $this->getHttpClient()->searchUsersByImage([]);
    }

    public function verifyBase64ImageAgainstSubjectRemoteExample(Subject $subject, string $base64Source, string $remoteTargetUUID)
    {
    }
}
