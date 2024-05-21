<?php

namespace Stanliwise\CompreParkway\Services;

use Exception;
use Stanliwise\CompreParkway\Contract\FaceTech\Adaptor;
use Stanliwise\CompreParkway\Contract\File;
use Stanliwise\CompreParkway\Contract\Subject;
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

    public function setDriver(\Stanliwise\CompreParkway\Contract\FaceTech\Adaptor $adaptor)
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

    public function enroll(Subject $subject, File $image_file, ?string $disk_drive = 'local')
    {
        if ($this->hasEnrolled($subject)) {
            throw new SubjectAlreadyEnrolled;
        }

        $this->getfacialRecognitionService()->enrollSubject($subject);
        $this->addPrimaryExample($subject, $image_file, false, $disk_drive);
    }

    public function addSecondaryExample(Subject $subject, File $image_file, string $disk = 'local')
    {
        $subject->refresh();
        if ($this->hasEnrolled($subject) == false) {
            throw new Exception('Subject not enrolled properly');
        }

        $this->addExample($subject, $image_file, 'seconeary', $disk);
    }

    protected function addExample(Subject $subject, File $image_file, string $type = 'secondary', ?string $disk = 'local')
    {
        return tap($this->getfacialRecognitionService()->addFaceImage($subject, $image_file), function ($response) use ($subject, $image_file, $disk, $type) {
            $relative_path = $image_file->getFilename();

            $subject->examples()->create([
                'is_primary' => ($type == 'primary') ? true : false,
                'response_payload' => $response,
                'tag' => $image_file->getTag(),
                'provider' => $this->driver->getName(),
                'image_uuid' => $response['image_uuid'],
                'response_payload' => $response,
                'image_path' => $relative_path,
                'similarity_score' => ($type == 'primary') ? null : $response['similarity_threshold'],
                'storage_driver' => $disk,
            ]);
        });
    }

    protected function addPrimaryExample(Subject $subject, File $image_file, bool $reset, $disk)
    {
        if ($reset) {
            $this->removeAllExamples($subject);
        }

        if ($subject->primaryExample) {
            throw new SubjectNameAlreadyExist;
        }

        //TODO: check there is no image remotely

        //if there is then remove all other samples

        //self::facialDetectionService()->detectFileImage($image_file);
        return $this->addExample($subject, $image_file, 'primary', $disk);
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
