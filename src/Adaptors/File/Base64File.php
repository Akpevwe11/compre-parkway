<?php

namespace Stanliwise\CompreParkway\Adaptors\File;

use Exception;
use Stanliwise\CompreParkway\Contract\File;

class Base64File implements File
{
    protected string $base64;
    protected string $tag;

    public function __construct(string $base64)
    {
        $this->base64 = $base64;
    }

    public function getContent()
    {
        return base64_decode($this->base64);
    }

    public function setTag(string $tag)
    {
        $this->tag = $tag;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function __toString()
    {
        return $this->base64;
    }

    public function getFilename()
    {
        return sha1($this->base64);
    }

    public function getPath()
    {
        throw new Exception('Path Not Supported');
    }

    public function path()
    {
        return $this->getPath();
    }
}
