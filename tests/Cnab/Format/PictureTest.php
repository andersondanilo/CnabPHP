<?php

namespace Cnab\Tests\Format;

use Cnab\Format\Picture;

class PictureTest extends \PHPUnit_Framework_TestCase
{
    public function testDecode()
    {
        // float number
        $this->assertEquals(200.00, Picture::decode('20000', '9(3)V9(2)', array()));
        $this->assertEquals(200.05, Picture::decode('20005', '9(3)V9(2)', array()));
        $this->assertEquals(2.5, Picture::decode('00250', '9(3)V9(2)', array()));
        $this->assertEquals(3.33, Picture::decode('0333', '9(2)V9(2)', array()));

        // integer number
        $this->assertEquals(200, Picture::decode('00200', '9(5)', array()));
        $this->assertEquals(3, Picture::decode('3', '9(1)', array()));
        $this->assertEquals(70.46, Picture::decode('07046', '9(3)V9(2)', array()));

        // big integer number
        $this->assertEquals('123456789123456', Picture::decode('000123456789123456', '9(15)', array()));

        // too big integer number
        $this->assertEquals('900000000048957', Picture::decode('900000000048957', '9(15)', array()));

        // string text
        $this->assertEquals('  Abc', Picture::decode('  Abc', 'X(5)', array()));
        $this->assertEquals('Abc', Picture::decode('Abc  ', 'X(5)', array()));
    }

    public function testEncode()
    {
        // float number
        $this->assertEquals('20000', Picture::encode(200.00, '9(3)V9(2)', array()));
        $this->assertEquals('20005', Picture::encode(200.05, '9(3)V9(2)', array()));
        $this->assertEquals('20050', Picture::encode(200.50, '9(3)V9(2)', array()));
        $this->assertEquals('00250', Picture::encode(2.5, '9(3)V9(2)', array()));
        $this->assertEquals('00205', Picture::encode(2.05, '9(3)V9(2)', array()));
        $this->assertEquals('07046', Picture::encode(70.45999999999999, '9(3)V9(2)', array()));
        $this->assertEquals('07045', Picture::encode(70.45111111111111, '9(3)V9(2)', array()));
        $this->assertEquals('070451', Picture::encode(70.45111111111111, '9(3)V9(3)', array()));

        $linha = new \Cnab\Format\Linha();
        $field = new \Cnab\Format\Field($linha, 'valor_titulo', '9(3)V9(2)', 0, 4, array());
        $field->set(70.45999999999999);
        $this->assertEquals('07046', $field->getEncoded());

        // integer number
        $this->assertEquals('0200', Picture::encode(200, '9(4)', array()));
        $this->assertEquals('4', Picture::encode(4, '9(1)', array()));

        // big integer number
        $this->assertEquals('900000000048957', Picture::encode('900000000048957', '9(15)', array()));

        // string text
        $this->assertEquals('Abc  ', Picture::encode('Abc', 'X(5)', array()));
        $this->assertEquals(' 123 ', Picture::encode(' 123', 'X(5)', array()));

        // encode date
        $data = new \DateTime('2003-02-01 00:00:00');
        $this->assertEquals('01022003', Picture::encode($data, '9(8)', array('date_format' => '%d%m%Y')));

        $data = new \DateTime('2009-08-07 01:02:03');
        $this->assertEquals('010203', Picture::encode($data, '9(8)', array('date_format' => '%H%M%S')));
        $this->assertEquals('030201', Picture::encode($data, '9(8)', array('date_format' => '%S%M%H')));
    }
}
