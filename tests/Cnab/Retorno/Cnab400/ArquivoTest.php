<?php

namespace Cnab\Tests\Retorno\Cnab400;

class ArquivoTest extends \PHPUnit_Framework_TestCase
{
    public function testArquivoBradescoCnab400PodeSerLido()
    {
        $factory = new \Cnab\Factory();
        $arquivo = $factory->createRetorno('tests/fixtures/cnab400/retorno-cb030400-bradesco.ret');
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

    public function testArquivoBancoDoBrasilCnab400PodeSerLido()
    {
        $factory = new \Cnab\Factory();
        $arquivo = $factory->createRetorno('tests/fixtures/cnab400/retorno-cnab400-bb.ret');

        $this->assertNotNull($arquivo);
        $this->assertNotNull($arquivo->header);
        $this->assertNotNull($arquivo->trailer);

        $this->assertEquals('33448000011113', $arquivo->getCodigoCedente());
        $this->assertEquals(\Cnab\Banco::BANCO_DO_BRASIL, $arquivo->getCodigoBanco());

        $detalhes = $arquivo->listDetalhes();
        $detalhe = $detalhes[0];

        $this->assertEquals(25.0, $detalhe->getValorRecebido());
        $this->assertEquals('11122450000000290', $detalhe->getNossoNumero());
        $this->assertEquals(new \DateTime('2015-09-10 00:00:00'), $detalhe->getDataCredito());
        $this->assertEquals(new \DateTime('2015-09-08 00:00:00'), $detalhe->getDataOcorrencia());
        $this->assertTrue($detalhe->isBaixa());
    }

    public function testArquivoItauCnab400PodeSerLido()
    {
        $factory = new \Cnab\Factory();
        $arquivo = $factory->createRetorno('tests/fixtures/cnab400/retorno-cnab400-itau.ret');
        $this->assertNotNull($arquivo);
        $this->assertNotNull($arquivo->header);
        $this->assertNotNull($arquivo->trailer);

        $this->assertEquals(\Cnab\Banco::ITAU, $arquivo->getCodigoBanco());

        $detalhes = $arquivo->listDetalhes();

        $this->assertEquals(4, count($detalhes));
        $detalhe = $detalhes[0];

        $this->assertEquals(12345, $arquivo->getConta());
        $this->assertEquals(0, $arquivo->getContaDac());
        $this->assertEquals(341, $arquivo->getCodigoBanco());
        $this->assertEquals(new \DateTime('2013-08-22 00:00:00'), $arquivo->getDataGeracao());
        $this->assertEquals(new \DateTime('2013-06-21 00:00:00'), $arquivo->getDataCredito());

        $this->assertEquals(6, $detalhe->getCodigo());
        $this->assertEquals(209.97, $detalhe->getValorRecebido());
        $this->assertEquals(389.75, $detalhe->getValorTitulo());
        $this->assertEquals(3.33, $detalhe->getValorTarifa());
        $this->assertEquals(0.1, $detalhe->getValorIOF());
        $this->assertEquals(176.45, $detalhe->getValorDesconto());
        $this->assertEquals(0.19, $detalhe->getValorAbatimento());
        $this->assertEquals(0.18, $detalhe->getValorOutrosCreditos());
        $this->assertEquals(123123.12, $detalhe->getValorMoraMulta());
        $this->assertEquals('1A', $detalhe->getNumeroDocumento());
        $this->assertEquals(109, $detalhe->getCarteira());
        $this->assertEquals('0177', $detalhe->getAgencia());
        $this->assertEquals(231327, $detalhe->getNossoNumero());
        $this->assertEquals(null, $detalhe->getDataVencimento());
        $this->assertEquals(new \DateTime('2013-06-21 00:00:00'), $detalhe->getDataCredito());
        $this->assertEquals(new \DateTime('2013-06-20 00:00:00'), $detalhe->getDataOcorrencia());
        $this->assertEquals(3027, $detalhe->getAgenciaCobradora());
        $this->assertEquals(2, $detalhe->getAgenciaCobradoraDac());
        $this->assertEquals(2, $detalhe->getNumeroSequencial());
        $this->assertEquals('LIQUIDAÇÃO NORMAL', $detalhe->getCodigoNome());
        $this->assertEquals(false, $detalhe->isBaixa());
        $this->assertEquals(false, $detalhe->isBaixaRejeitada());
        $this->assertEquals('B2', $detalhe->getCodigoLiquidacao());
        $this->assertEquals(false, $detalhe->isDDA());
        $this->assertEquals(null, $detalhe->getAlegacaoPagador());
        $this->assertEquals('OUTROS BANCOS – PELA LINHA DIGITÁVEL', $detalhe->getDescricaoLiquidacao());

        $this->assertNotEmpty($detalhe->getDescricaoLiquidacao());

        // teste boleto dda e alegacao sacado
        $detalhe = $detalhes[1];

        $this->assertEquals(true, $detalhe->isDDA());
        $this->assertEquals('BOLETO DDA, DIVIDA NÃO RECONHECIDA PELO PAGADOR', $detalhe->getAlegacaoPagador());
    }
}
