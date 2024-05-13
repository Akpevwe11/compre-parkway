<?php

namespace Stanliwise\CompreParkway\Contract;

interface Subject
{
    /**
     * @return mixed
     */
    public function getUniqueID();

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function primaryExample();
    
    /** 
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function examples();

    /** 
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function verifiedExamples();


    public function refresh();
}
