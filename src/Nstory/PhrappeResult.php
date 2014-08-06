<?php
namespace Nstory;

class PhrappeResult
{
    public $stdout;
    public $stderr;
    public $exit_code;

    public function __construct($stdout, $stderr, $exit_code)
    {
        $this->stdout = $stdout;
        $this->stderr = $stderr;
        $this->exit_code = $exit_code;
    }
}
