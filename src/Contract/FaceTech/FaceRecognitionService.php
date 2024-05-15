<?php

namespace Stanliwise\CompreParkway\Contract\FaceTech;

use Illuminate\Http\File;
use Stanliwise\CompreParkway\Contract\Subject;

interface FaceRecognitionService
{
    public function enrollSubject(Subject $subject);

    public function disenrollSubject(Subject $subject);

    public function addImage(Subject $subject, File $file);

    public function removeAll(Subject $subject);

    public function removeFace(string $image_uuid);

    public function addImageBase64(Subject $subject, string $file);

    public function verifyFileImageAgainstSubjectRemoteExample(Subject $subject, File $source, string $remoteTargetUUID);

    public function verifyBase64ImageAgainstSubjectRemoteExample(Subject $subject, string $base64Source, string $remoteTargetUUID);
}
