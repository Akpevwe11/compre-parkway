<?php

namespace Stanliwise\CompreParkway\Services;

use Exception;
use Stanliwise\CompreParkway\Contract\FaceTech\Adaptor;
use Stanliwise\CompreParkway\Contract\File;
use Stanliwise\CompreParkway\Contract\Subject;
use Stanliwise\CompreParkway\Exceptions\FaceHasNotBeenIndexed;
use Stanliwise\CompreParkway\Exceptions\SubjectAlreadyEnrolled;
use Stanliwise\CompreParkway\Exceptions\SubjectNameAlreadyExist;
use Stanliwise\CompreParkway\Models\Example;

class ParkwayFaceTechService
{
    /** @var \Stanliwise\CompreParkway\Contract\FaceTech\Adaptor */
    protected $driver;

    public function __construct(?Adaptor $driver = null)
    {
        $this->driver = $driver ? $driver : app(config('compreFace.driver'));

        if (($this->driver instanceof Adaptor) == false) {
            throw new Exception('Invalid Driver provided');
        }
    }

    public function getDriver()
    {
        return $this->driver;
    }

    public function setDriver(Adaptor $adaptor)
    {
        $this->driver = $adaptor;
    }

    public function hasVerifiedFaceImage(Subject $subject)
    {
        return $subject->verifiedExamples()->count() > 0;
    }

    public function hasEnrolled(Subject $subject): bool
    {
        return (bool) $subject->primaryExample;
    }

    public function userHasBeenIndexed(File $file)
    {
        return $this->getFacialRecognitionService()->findUserUsingImage($file);
    }

    public function enroll(Subject $subject, File $image_file, ?string $disk_drive = 'local')
    {
        if ($this->hasEnrolled($subject)) {
            throw new SubjectAlreadyEnrolled;
        }

        $enrolled = false;
        $subject->setfacialUUID((string) \Illuminate\Support\Str::uuid());

        //but if the user has been indexed then set base on id
        try {
            $face_uuid = $this->userHasBeenIndexed($image_file);
            $subject->setfacialUUID($face_uuid);
            $subject->refresh();
            $enrolled = true;
        } catch (FaceHasNotBeenIndexed $th) {
            if (app()->runningUnitTests()) {
                logger($th);
            }
        }

        if ($enrolled == false) {
            $this->getfacialRecognitionService()->enrollSubject($subject);
        }

        if ($subject->primaryExample) {
            throw new SubjectNameAlreadyExist;
        }

        return $this->addExample($subject, $image_file, 'primary', $disk_drive, ! $enrolled);
    }

    public function addSecondaryExample(Subject $subject, File $image_file, string $disk = 'local')
    {
        $subject->refresh();

        if ($this->hasEnrolled($subject) == false) {
            throw new Exception('Subject not enrolled properly');
        }

        //but if the user has been indexed then set base on id
        try {
            $face_uuid = $this->userHasBeenIndexed($image_file);
            $image_already_indexed = $face_uuid == $subject->getUniqueID();
        } catch (FaceHasNotBeenIndexed $th) {
            if (app()->runningUnitTests()) {
                logger($th);
            }
        }

        $this->addExample($subject, $image_file, 'secondary', $disk, ! $image_already_indexed);
    }

    protected function addExample(Subject $subject, File $image_file, string $type = 'secondary', ?string $disk = 'local', $shouldAssociate = true)
    {
        return tap($this->getfacialRecognitionService()->addFaceImage($subject, $image_file, $shouldAssociate), function ($response) use ($subject, $image_file, $disk, $type) {
            $relative_path = $image_file->getFilename();

            $subject->examples()->create([
                'is_primary' => ($type == 'primary') ? true : false,
                'response_payload' => $response,
                'tag' => $image_file->getTag(),
                'provider' => $this->driver->getName(),
                'image_uuid' => $response['image_uuid'],
                'image_path' => $relative_path,
                'similarity_score' => ($type == 'primary') ? null : $response['similarity_threshold'],
                'storage_driver' => $disk,
            ]);
        });
    }

    public function disenroll(Subject $subject)
    {
        return tap($this->getfacialRecognitionService()->disenrollSubject($subject), function () use ($subject) {
            $subject->examples()->delete();
        });
    }

    public function removeExample(Example $example)
    {
        if ($example->is_primary) {
            throw new Exception('Cannot remove Primary Model, Disenroll or remove all exmaples of Example');
        }

        tap($this->getfacialRecognitionService()->removeFaceImage($example->image_uuid), function () use ($example) {
            $example->delete();
        });
    }

    public function removeAllExamples(Subject $subject)
    {
        $this->getfacialRecognitionService()->removeAllFaceImages($subject);
        $subject->examples()->delete();
        $subject->refresh();
    }

    public function verifyFaceImageAgainstASubject(Subject $subject, File $file)
    {
        return $this->getFacialRecognitionService()->verifyFaceImageAgainstASubject($subject, $file);
    }

    public function detectFileImage(File $image)
    {
        return $this->getFacialDetectionService()->detectFace($image);
    }

    public function compareTwoFileImages(File $sourceImage, File $targeImage)
    {
        return $this->getfacialVerificationService()->compareTwoFaceImages($sourceImage, $targeImage);
    }

    public function getFacialRecognitionService()
    {
        return $this->getDriver()->facialRecognitionService();
    }

    public function getFacialDetectionService()
    {
        return $this->getDriver()->facialDetectionService();
    }

    public function getFacialVerificationService()
    {
        return $this->getDriver()->facialVerificationService();
    }
}
