<?php
namespace Nstory;

class TempFileTest extends \PHPUnit_Framework_TestCase
{
    public function test_creates_file()
    {
        $tf = new TempFile();
        $this->assertTrue(file_exists($tf->path));
    }

    public function test_deletes_file()
    {
        $tf = new TempFile();
        $path = $tf->path;
        $tf = null;
        $this->assertFalse(file_exists($path));
    }
}
