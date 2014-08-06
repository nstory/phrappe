<?php
namespace Phrappe;

class Phrappe
{
    public $return_result = false;
    public $proc_open = 'proc_open';

    public function __construct()
    {
    }

    public static function __callStatic($name, $arguments)
    {
        $ph = new Phrappe;
        return call_user_func_array([$ph, $name], $arguments);
    }

    public function __call($name, $arguments)
    {
        return $this->call($name, $arguments);
    }

    public function __invoke($cmd)
    {
        return $this->call($cmd, array_slice(func_get_args(), 1));
    }

    private function call($name, $arguments)
    {
        // temporary files to capture stdout and stderr
        $stdout_file = new TempFile();
        $stderr_file = new TempFile();

        // construct the command line
        $cmd = $name . ' ' . implode(' ', array_map('escapeshellarg', $arguments));

        // we'll feed-in stdin directly
        $descriptorspec = [
            0 => ['pipe','r'],
            1 => ['file', $stdout_file->path, 'w'],
            2 => ['file', $stderr_file->path, 'w']
        ];

        $process = $this->proc_open($cmd, $descriptorspec, $pipes);
        if ($process === FALSE) {
            throw new PhrappeException('proc_open failed', -1);
        }

        // immediately send EOF to stdin
        fclose($pipes[0]);

        // wait for the command to finish
        $exit_code = proc_close($process);

        $stdout = file_get_contents($stdout_file->path);
        $stderr = file_get_contents($stderr_file->path);

        if ($this->return_result) {
            return new PhrappeResult($stdout, $stderr, $exit_code);
        }

        if ($exit_code != 0) {
            throw new PhrappeException($stderr, $exit_code);
        }
        return $stdout;
    }

    /**
     * Provides some redirection so we can mock out proc_open in unit
     * tests.
     */
    protected function proc_open($cmd, $descriptorspec, &$pipes) {
        $proc_open = $this->proc_open;
        return $proc_open($cmd, $descriptorspec, $pipes);
    }
}
