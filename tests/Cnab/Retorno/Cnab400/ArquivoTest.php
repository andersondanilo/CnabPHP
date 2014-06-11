<?php
namespace Cnab\Tests\Retorno\Cnab400;

use Cnab\Retorno\Cnab400\Arquivo;

class ArquivoTest extends \PHPUnit_Framework_TestCase 
{
    public function testArquivoBradescoCnab400PodeSerLido()
    {
        $arquivo = new Arquivo(\Cnab\Banco::BRADESCO, 'tests/fixtures/cnab400/retorno-cb030400-bradesco.ret');
        $this->assertNotNull($arquivo);
        $this->assertNotNull($arquivo->header);
        $this->assertNotNull($arquivo->trailer);

        $this->assertEquals(4466911, $arquivo->getCodigoCedente());
        $this->assertEquals(\Cnab\Banco::BRADESCO, $arquivo->getCodigoBanco());

        $detalhes = $arquivo->listDetalhes();
        $detalhe = $detalhes[1];
        
        $this->assertEquals(new \DateTime('2012-04-12 00:00:00'), $detalhe->getDataVencimento());
        $this->assertEquals(5.0, $detalhe->getValorRecebido());
        $this->assertEquals(97, $detalhe->getNossoNumero());
        $this->assertEquals(15, $detalhe->getNumeroDocumento());
        $this->assertEquals(new \DateTime('2012-04-11 00:00:00'), $detalhe->getDataOcorrencia());
    }
}