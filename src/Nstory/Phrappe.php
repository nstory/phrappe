<?php
namespace Nstory;

class Phrappe
{
    /** @var boolean if this is true, returns a PhrappeResult instead of stdout */
    public static $return_result = false;

    /** @var callable used by unit tests; don't change this */
    public $proc_open = 'proc_open';

    private $options;

    public function __construct($options=[])
    {
        $this->options = array_merge([
            'return_result' => false
        ], $options);
    }

    public function __set($name, $value)
    {
        $this->options[$name] = $value;
    }

    public static function __callStatic($name, $arguments)
    {
        $ph = new Phrappe([
            'return_result' => self::$return_result
        ]);
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
        $cmd = $name . ' ' . implode(' ', $this->parseArguments($arguments));

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

        if ($this->options['return_result']) {
            return new PhrappeResult($stdout, $stderr, $exit_code);
        }

        if ($exit_code != 0) {
            throw new PhrappeException($stderr, $exit_code);
        }
        return $stdout;
    }

    /**
     * Takes an array of arguments (possibly containing nested arrays of
     * even arguments) and converts it into a flat, escaped array of
     * parameters ready to pass to an external command. Example:
     * parseArguments(['foo', ['bar' => 'baz']]) -> ['foo', '--bar', 'baz']
     * @param array $arguments
     * @return array
     */
    private function parseArguments($arguments)
    {
        $ret = [];
        foreach ($arguments as $key => $value) {
            if (is_array($value)) {
                $ret = array_merge($ret, $this->parseArguments($value));
            } else if (is_int($key)) {
                $ret[] = escapeshellarg($value);
            } else {
                $hyph = strlen($key)>1 ? '--' : '-';
                $ret[] = "$hyph$key";
                if ($value !== true) {
                    $ret[] = escapeshellarg($value);
                }
            }
        }
        return $ret;
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
