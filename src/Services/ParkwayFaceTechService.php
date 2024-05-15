<?php

namespace Stanliwise\CompreParkway\Services;

use CompreFace;
use Exception;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Stanliwise\CompreParkway\Contract\FaceTech\Adaptor;
use Stanliwise\CompreParkway\Contract\Subject;
use Stanliwise\CompreParkway\Exceptions\SubjectAlreadyEnrolled;
use Stanliwise\CompreParkway\Exceptions\SubjectNameAlreadyExist;
use Stanliwise\CompreParkway\Models\Example;

class ParkwayFaceTechService
{
    public static $driver;

    public function hasVerifiedFaceImage(Subject $subject)
    {
        return $subject->verifiedExamples()->count() > 0;
    }

    public function hasEnrolled(Subject $subject)
    {
        return (bool) $subject->primaryExample;
    }

    public function enroll(Subject $subject, File $image_file, ?string $disk_drive = 'local')
    {
        if ($this->hasEnrolled($subject))
            throw new SubjectAlreadyEnrolled;

        static::facialRecognitionService()->enrollSubject($subject);
        $this->addPrimaryExample($subject, $image_file, false, $disk_drive);
    }

    public function addSecondaryExample(Subject $subject, File $image_file, string $disk = 'local')
    {
        if ($this->hasEnrolled($subject) == false)
            throw new Exception('Subject not enrolled properly');

        $this->addExample($subject, $image_file, 'seconeary', $disk);
    }

    protected function addExample(Subject $subject, File $image_file, string $type = 'secondary', ?string $disk = 'local')
    {
        return tap(self::facialRecognitionService()->addImage($subject, $image_file), function ($response) use ($subject, $image_file, $disk, $type) {
            $relative_path = $image_file->getFilename();

            $subject->examples()->create([
                'is_primary' => ($type == 'primary') ? true : false,
                'response_payload' => $response,
                'provider' => config('compreFace.driver_name'),
                'image_uuid' => $response['image_uuid'],
                'response_payload' => $response,
                'image_path' => $relative_path,
                'similarity_score' => ($type == 'primary') ? null : $response['similarity_threshold'],
                'storage_driver' => $disk,
            ]);
        });
    }

    protected function addPrimaryExample(Subject $subject, File $image_file, bool $reset = false, $disk)
    {
        if ($reset)
            $this->removeAllExample($subject);


        if ($subject->primaryExample)
            throw new SubjectNameAlreadyExist;

        //TODO: check there is no image remotely

        //if there is then remove all other samples

        //self::facialDetectionService()->detectFileImage($image_file);
        return $this->addExample($subject, $image_file, 'primary', $disk);
    }

    public function disenroll(Subject $subject)
    {
        return tap(self::facialRecognitionService()->disenrollSubject($subject), function () use ($subject) {
            $subject->examples()->delete();
        });
    }

    public function removeExample(Example $example)
    {
        if ($example->is_primary)
            throw new Exception('Cannot remove Primary Model, Disenroll or remove all exmaples of Example');

        tap(self::facialRecognitionService()->removeFace($example->image_uuid), function () use ($example) {
            $example->delete();
        });
    }

    public function removeAllExample(Subject $subject)
    {
        self::facialRecognitionService()->removeAll($subject);
        $subject->examples()->delete();
        $subject->refresh();
    }

    /**
     * @return self
     */
    public static function instance()
    {
        return app('parkwayFaceService');
    }

    public static function facialRecognitionService()
    {
        return self::driver()->facialRecognitionService();
    }

    public static function facialDetectionService()
    {
        return self::driver()->facialDetectionService();
    }


    public static function driver(string $driver = null): Adaptor
    {
        if ($driver == null)
            $driver = config('compreFace.driver');


        if (($driver = app($driver)) instanceof Adaptor == false)
            throw new Exception('Invalid Driver provider');

        return $driver;
    }
}
