# Phrappe
Pronounced |frap| (the same way a New Englander pronounces "milkshake!")

# Usage by Example
```php
use Phrappe\Phrappe as ph;
use Phrappe\PhrappeException;

// Phrappe let's you run and capture the output of external commands with ease

// Let's get a list of the files in the current directory
$files = ph::ls();

// The list will contain this file
assert(in_array('README.md', explode("\n", $files)));

// You can run any shell command
$gzip_version = ph::gzip('--version');

// the above command can also be run using:
$gzip_version = ph::gzip(['version' => true]);

// this is a convenient syntax for passing flags:
$jan_calendar = ph::cal(['m' => 'January']);

// combine the different syntaxes to create complex command lines:
ph::gzip(['c' => true], '-9', 'README.md');

// if the command returns a non-zero error code, an exception is thrown
try {
    ph::cat('this_file_does_not_exist');
} catch(PhrappeException $e) {
    // $e->getMessage() is stderr
    assert(preg_match('/No such file or directory/', $e->getMessage()));

    // $e->getCode() is the exist status
    assert(1 == $e->getCode());
}

// if you want to capture both stdout and stderr (this also stops
// exceptions from being thrown):
ph::$return_result = true;
$result = ph::cat('this_file_does_not_exist');
assert('' == $result->stdout);
assert(preg_match('/No such file or directory/', $result->stderr));
assert(1 == $result->exit_code);

// if static methods make you uncomfortable, instatiate it:
$ph = new Phrappe\Phrappe;
$june_1984_calendar = $ph->cal('June', '1984');

// the instance can be configured by setting a property:
$ph->return_result = true;
assert(1 == $ph->cat('this_file_does_not_exists')->exit_code);

// or at the time of instantiation
$ph = new Phrappe\Phrappe(['return_result' => true]);
assert(1 == $ph->cat('this_file_does_not_exists')->exit_code);

// one last way of running commands; this is useful when the command
// name is a reserved PHP keyword:
$ph = new Phrappe\Phrappe;
$greeting = $ph('echo', 'Hello, World!');
assert("Hello, World!\n" == $greeting);
```
