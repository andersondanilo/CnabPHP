<?php
namespace Cnab\Tests\Format;

use Cnab\Format\Picture;

class PictureTest extends \PHPUnit_Framework_TestCase
{
    public function testDecode()
    {
        // float number
        $this->assertEquals(200.00, Picture::decode('20000', '9(3)V9(2)'));
        $this->assertEquals(200.05, Picture::decode('20005', '9(3)V9(2)'));
        $this->assertEquals(2.5, Picture::decode('00250', '9(3)V9(2)'));
        $this->assertEquals(3.33, Picture::decode('0333', '9(2)V9(2)'));

        // integer number
        $this->assertEquals(200, Picture::decode('00200', '9(5)'));
        $this->assertEquals(3, Picture::decode('3', '9(1)'));
        
        // big integer number
        $this->assertEquals('123456789123456', Picture::decode('000123456789123456', '9(15)'));

        // string text
        $this->assertEquals('  Abc', Picture::decode('  Abc', 'X(5)'));
        $this->assertEquals('Abc', Picture::decode('Abc  ', 'X(5)'));
    }

    public function testEncode() {
        // float number
        $this->assertEquals('20000', Picture::encode(200.00, '9(3)V9(2)'));
        $this->assertEquals('20005', Picture::encode(200.05, '9(3)V9(2)'));
        $this->assertEquals('20050', Picture::encode(200.50, '9(3)V9(2)'));
        $this->assertEquals('00250', Picture::encode(2.5, '9(3)V9(2)'));
        $this->assertEquals('00205', Picture::encode(2.05, '9(3)V9(2)'));

        // integer number
        $this->assertEquals('0200', Picture::encode(200, '9(4)'));
        $this->assertEquals('4', Picture::encode(4, '9(1)'));
        
        // string text
        $this->assertEquals('Abc  ', Picture::encode('Abc', 'X(5)'));
        $this->assertEquals(' 123 ', Picture::encode(' 123', 'X(5)')); 
    }
}
