<?php
namespace Phrappe;

class Phrappe
{
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
        return $this->call($name, $arguments, []);
    }

    public function __invoke($cmd)
    {
        return $this->call($cmd, array_slice(func_get_args(), 1), []);
    }

    private function call($name, $arguments, $options)
    {
        // temporary files to capture stdout and stderr
        $stdout_file = tempnam(sys_get_temp_dir(), 'Phrappe');
        $stderr_file = tempnam(sys_get_temp_dir(), 'Phrappe');

        // construct the command line
        $cmd = $name . ' ' . implode(' ', array_map('escapeshellarg', $arguments));

        // we'll feed-in stdin directly
        $descriptorspec = [
            0 => ['pipe','r'],
            1 => ['file', $stdout_file, 'w'],
            2 => ['file', $stderr_file, 'w']
        ];

        $process = $this->proc_open($cmd, $descriptorspec, $pipes);
        if ($process !== FALSE) {
            // immediately send EOF to stdin
            fclose($pipes[0]);

            // wait for the command to finish
            $exit_code = proc_close($process);

            $stdout = file_get_contents($stdout_file);
            $stderr = file_get_contents($stderr_file);
        }

        // clean up
        unlink($stdout_file);
        unlink($stderr_file);

        if ($exit_code != 0) {
            throw new PhrappeException($stderr, $exit_code);
        }
        return $stdout;
    }

    protected function proc_open($cmd, $descriptorspec, &$pipes) {
        return proc_open($cmd, $descriptorspec, $pipes);
    }
}
