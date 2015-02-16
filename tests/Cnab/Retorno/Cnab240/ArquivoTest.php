<?php
namespace Cnab\Tests\Retorno\Cnab240;

use Cnab\Retorno\Cnab240\Arquivo;

class ArquivoTest extends \PHPUnit_Framework_TestCase 
{
    public function testArquivoSantanderPodeSerLido()
    {
        $arquivo = new Arquivo(33, 'tests/fixtures/cnab240/retorno_santander.ret');
        $this->assertNotNull($arquivo);
        $this->assertNotNull($arquivo->header);
        $this->assertNotNull($arquivo->lotes);
        $this->assertNotNull($arquivo->trailer);

        $this->assertEquals(11111111, $arquivo->getConta());
        $this->assertEquals(9, $arquivo->getContaDac());
        $this->assertEquals(33, $arquivo->getCodigoBanco());

        $detalhe = $arquivo->listDetalhes();
        $detalhe = $detalhe[0];

        $this->assertEquals(1040, $detalhe->segmento_t->nosso_numero);
        $this->assertEquals(10, $detalhe->segmento_t->valor_titulo);
    }

    public function testArquivoBancoDoBrasilPodeSerLido() {
        $arquivo = new Arquivo(1, 'tests/fixtures/cnab240/retorno_bb.ret');
        $this->assertNotNull($arquivo);
        $this->assertNotNull($arquivo->header);
        $this->assertNotNull($arquivo->lotes);
        $this->assertNotNull($arquivo->trailer);

        $this->assertEquals(7536, $arquivo->getConta());
        $this->assertEquals(7, $arquivo->getContaDac());
        $this->assertEquals(1, $arquivo->getCodigoBanco());
        $this->assertEquals(3294860, $arquivo->getCodigoConvenio());

        $this->assertEquals(
            \DateTime::createFromFormat('d/m/Y', '21/03/2011'),
            $arquivo->getDataGeracao()
        );

        $detalhe = $arquivo->listDetalhes();
        $detalhe = $detalhe[0];

        $this->assertEquals(40.00, $detalhe->getValorTitulo());
        $this->assertEquals(196, $detalhe->getNossoNumero());
    }

    
}