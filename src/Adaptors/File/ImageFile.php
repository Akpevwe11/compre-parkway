<?php

namespace Stanliwise\CompreParkway\Adaptors\File;

use Illuminate\Http\File;
use Stanliwise\CompreParkway\Contract\File as ContractFile;

class ImageFile extends File implements ContractFile
{
    protected string $tag;

    public function toBase64File(): Base64File
    {
        return new Base64File(base64_encode($this->getContent()));
    }

    public function setTag(string $tag)
    {
        $this->tag = $tag;
    }

    public function getTag(): string
    {
        return $this->tag;
    }
}
