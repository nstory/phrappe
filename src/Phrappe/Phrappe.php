<?php
namespace Phrappe;

class Phrappe
{
    public static function __callStatic($name, $arguments)
    {
        // temporary files to capture stdout and stderr
        $stdout_file = tempnam(sys_get_temp_dir(), 'Phrappe');
        $stderr_file = tempnam(sys_get_temp_dir(), 'Phrappe');

        // construct the command line
        $cmd = $name . ' ' .implode(' ', $arguments);

        // we'll feed-in stdin directly
        $descriptorspec = [
            0 => ['pipe','r'],
            1 => ['file', $stdout_file, 'w'],
            2 => ['file', $stderr_file, 'w']
        ];

        $process = proc_open($cmd, $descriptorspec, $pipes);
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
}
