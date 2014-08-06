<?php

class ReadMeTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $readme = file_get_contents(__DIR__ . '/../README.md');
        $code = '';
        preg_match_all('/```php(.*?)```/s', $readme, $matches);
        foreach ($matches[1] as $bit_of_code) {
            $code .= "$bit_of_code\n";
        }
        eval($code);
    }
}
