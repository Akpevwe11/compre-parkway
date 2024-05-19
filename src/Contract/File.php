<?php

namespace Stanliwise\CompreParkway\Contract;

interface File
{
    public function getContent();

    public function getFilename();

    public function getPath();

    public function path();

    public function getTag(): string;
}
