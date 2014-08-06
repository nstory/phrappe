<?php
namespace Phrappe;

class PhrappeResult
{
    public $stdin;
    public $stderr;
    public $exit_code;

    public function __construct($stdin, $stderr, $exit_code)
    {
        $this->stdin = $stdin;
        $this->stderr = $stderr;
        $this->exit_code = $exit_code;
    }
}
