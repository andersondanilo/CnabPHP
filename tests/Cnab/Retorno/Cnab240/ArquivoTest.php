<?php

namespace Cnab\Tests\Retorno\Cnab240;

class ArquivoTest extends \PHPUnit_Framework_TestCase
{
    public function testArquivoSantanderPodeSerLido()
    {
        $factory = new \Cnab\Factory();
        $arquivo = $factory->createRetorno('tests/fixtures/cnab240/retorno_santander.ret');
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

    public function testArquivoBancoDoBrasilPodeSerLido()
    {
        $factory = new \Cnab\Factory();
        $arquivo = $factory->createRetorno('tests/fixtures/cnab240/retorno_bb.ret');
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

    public function testArquivoCaixaSigcbPodeSerLido()
    {
        $factory = new \Cnab\Factory();
        $arquivo = $factory->createRetorno('tests/fixtures/cnab240/retorno_cnab240_caixa.ret');
        $this->assertNotNull($arquivo);
        $this->assertNotNull($arquivo->header);
        $this->assertNotNull($arquivo->lotes);
        $this->assertNotNull($arquivo->trailer);

        $this->assertEquals(104, $arquivo->getCodigoBanco());

        $this->assertEquals(
            \DateTime::createFromFormat('d/m/Y', '06/01/2014'),
            $arquivo->getDataGeracao()
        );

        $detalhe = $arquivo->listDetalhes();
        $detalhe = $detalhe[0];

        $this->assertEquals(6, $detalhe->getCodigo());
        $this->assertEquals(80.00, $detalhe->getValorRecebido());
        $this->assertEquals(80.00, $detalhe->getValorTitulo());
        $this->assertEquals(1.25, $detalhe->getValorTarifa());
        $this->assertEquals(0, $detalhe->getValorIOF());
        $this->assertEquals(0, $detalhe->getValorDesconto());
        $this->assertEquals(0, $detalhe->getValorAbatimento());
        $this->assertEquals(0, $detalhe->getValorOutrosCreditos());
        $this->assertEquals(0, $detalhe->getValorMoraMulta());
        $this->assertEquals(null, $detalhe->getNumeroDocumento());
        $this->assertEquals(null, $detalhe->getCarteira());
        $this->assertEquals('0', $detalhe->getAgencia());
        $this->assertEquals(11136997, $detalhe->getNossoNumero());
        $this->assertEquals(new \DateTime('2014-01-02 00:00:00'), $detalhe->getDataVencimento());
        $this->assertEquals(new \DateTime('2014-01-07 00:00:00'), $detalhe->getDataCredito());
        $this->assertEquals(new \DateTime('2014-01-06 00:00:00'), $detalhe->getDataOcorrencia());
        $this->assertEquals(1086, $detalhe->getAgenciaCobradora());
        $this->assertEquals(0, $detalhe->getAgenciaCobradoraDac());
        $this->assertEquals(1, $detalhe->getNumeroSequencial());
        //$this->assertEquals('LIQUIDAÇÃO NORMAL', $detalhe->getCodigoNome());
        $this->assertEquals(true, $detalhe->isBaixa());
        $this->assertEquals(false, $detalhe->isBaixaRejeitada());
        $this->assertEquals(null, $detalhe->getCodigoLiquidacao());
        $this->assertEquals(false, $detalhe->isDDA());
        $this->assertEquals(null, $detalhe->getAlegacaoPagador());
        $this->assertEquals(null, $detalhe->getDescricaoLiquidacao());
    }
}
