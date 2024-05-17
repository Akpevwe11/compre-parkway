<?php

namespace Stanliwise\CompreParkway\Contract\FaceTech;

use Stanliwise\CompreParkway\Contract\File;
use Stanliwise\CompreParkway\Contract\Subject;

interface FaceRecognitionService
{
    public function enrollSubject(Subject $subject_uuid);

    public function disenrollSubject(Subject $subject_uuid);

    public function addFaceImage(Subject $subject_uuid, File $file);

    public function removeAllFaceImages(Subject $subject_uuid);

    public function removeFaceImage(string $image_uuid);

    public function verifyFaceImageAgainstASubject(Subject $subject_uuid, File $image);
}
