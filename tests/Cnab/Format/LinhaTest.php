<?php

namespace Cnab\Tests\Format;

use Cnab\Format\Linha;

class LinhaTest extends \PHPUnit_Framework_TestCase
{
    public function testPodeAdicionarCampo()
    {
        $linha = new Linha();
        $linha->addField('codigo_banco', 1, 3, '9(3)', '000', array());
        $this->assertTrue($linha->existField('codigo_banco'));
        $this->assertFalse($linha->existField('codigo_branco'));
        $this->assertTrue($linha->validate());
    }

    /**
     * @depends testPodeAdicionarCampo
     */
    public function testPodeSubstituirCampo()
    {
        $linha = new Linha();
        $linha->addField('codigo_cedente_dv', 36, 36, '9(1)', '1', array());
        $linha->addField('uso_exclusivo_banco_01', 33, 40, 'X(8)', str_repeat(' ', 8), array());
        $this->assertFalse($linha->existField('codigo_cedente_dv'));
        $this->assertTrue($linha->existField('uso_exclusivo_banco_01'));

        $linha = new Linha();
        $linha->addField('codigo_banco', 1, 3, '9(3)', '000', array());
        $linha->addField('numero_lote', 4, 7, '9(4)', '000', array());
        $this->assertTrue($linha->existField('codigo_banco'));
        $this->assertTrue($linha->existField('numero_lote'));

        $linha->addField('codigo_banco', 1, 7, '9(7)', '00000000', array());
        $this->assertTrue($linha->existField('codigo_banco'));
        $this->assertTrue($linha->validate());

        $linha->addField('codigo_e_lote', 1, 7, '9(7)', '2', array());
        $this->assertTrue($linha->existField('codigo_e_lote'));
        $this->assertFalse($linha->existField('codigo_banco'));
        $this->assertFalse($linha->existField('numero_lote'));
        $this->assertTrue($linha->validate());

        return $linha;
    }

    /**
     * @depends testPodeSubstituirCampo
     */
    public function testPodeCodificarLinha($linha)
    {
        $linha->addField('teste', 8, 12, 'X(5)', 'teste', array());

        $encoded = $linha->getEncoded();
        $this->assertEquals(12, strlen($encoded));
        $this->assertEquals('0000002teste', $encoded);
    }
}
