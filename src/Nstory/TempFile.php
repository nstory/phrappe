<?php
namespace Nstory;

/*
 * For creating temp files. RAII pattern. The file is automatically
 * deleted when the TempFile instance is no longer in scope.
 */
class TempFile
{
    public $path;

    public function __construct()
    {
        $this->path = tempnam(sys_get_temp_dir(), 'Phrappe');
    }

    public function __destruct()
    {
        unlink($this->path);
    }
}
