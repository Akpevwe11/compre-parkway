<?php

namespace Stanliwise\CompreParkway\Contract\FaceTech;

use Stanliwise\CompreParkway\Adaptors\File\Base64File;
use Stanliwise\CompreParkway\Adaptors\File\ImageFile;
use Stanliwise\CompreParkway\Contract\File;
use Stanliwise\CompreParkway\Contract\Subject;

interface FaceRecognitionService
{
    public function enrollSubject(Subject $subject);

    public function disenrollSubject(Subject $subject);

    public function addImage(Subject $subject, File $file);
    
    public function addImageBase64(Subject $subject, Base64File $file);

    public function removeAllImages(Subject $subject);

    public function removeImage(string $image_uuid);

    public function verifyFileImageAgainstSubjectRemoteExample(Subject $subject, ImageFile $source, string $remoteTargetUUID);

    public function verifyBase64ImageAgainstSubjectRemoteExample(Subject $subject, Base64File $base64Source, string $remoteTargetUUID);
}
