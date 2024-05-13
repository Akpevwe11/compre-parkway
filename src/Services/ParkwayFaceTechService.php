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

    public function hasVerifiedFaceImage(Subject $subject)
    {
        return $subject->verifiedExamples()->count() > 0;
    }

    public function hasEnrolled(Subject $subject)
    {
        return (bool) $subject->getPrimaryExample;
    }

    public function enroll(Subject $subject, File $image_file)
    {
        if ($this->hasEnrolled($subject))
            throw new SubjectAlreadyEnrolled;

        self::facialRecognitionService()->enrollSubject($subject);
        $this->addPrimaryExample($subject, $image_file, false);
    }

    public function addSecondaryExample(Subject $subject, File $image_file, string $disk = 'local')
    {
        if ($this->hasEnrolled($subject) == false)
            throw new Exception('Subject not enrolled properly');

        $this->addExample($subject, $image_file, $disk);
    }

    protected function addExample(Subject $subject, File $image_file, string $disk = 'local')
    {
        return tap(self::facialRecognitionService()->addImage($subject, $image_file), function ($response) use ($subject, $image_file, $disk) {
            $relative_path = config('compreFace.image_directiory') . DIRECTORY_SEPARATOR . $image_file->getFilename();
            Storage::disk($disk)->put($relative_path, $image_file->getContent());

            $subject->examples()->create([
                'is_primary' => true,
                'response_payload' => $response,
                'image_uuid' => $response['image_id'],
                'response_payload' => $response,
                'image_path' => $relative_path,
                'driver' => $disk,
            ]);
        });
    }

    protected function addPrimaryExample(Subject $subject, File $image_file, bool $reset = false)
    {
        if ($reset)
            $this->removeAllExample($subject);


        if ($subject->primaryExample)
            throw new SubjectNameAlreadyExist;

        //TODO: check there is no image remotely

        //if there is then remove all other samples

        self::facialDetectionService()->detectFileImage($image_file);
        return $this->addExample($subject, $image_file);
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

        tap(self::facialRecognitionService()->remove($example->image_uuid), function () use ($example) {
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


    public static function driver(string $driver = '\Stanliwise\CompreFace\Adaptors\CompreFaceFacialAdaptor'): Adaptor
    {
        if (($driver = app($driver)) instanceof Adaptor == false)
            throw new Exception('Invalid Driver provider');

        return $driver;
    }
}
