<?php

namespace Cnab\Tests\Retorno;

use Cnab\Retorno\CodigoOcorrencia;

class CodigoOcorrenciaTest extends \PHPUnit_Framework_TestCase
{
    public function testIdentificaCodigoDeOcorrencia()
    {
        $codigoOcorrencia = new CodigoOcorrencia();
        $this->assertEquals('entrada confirmada', strtolower($codigoOcorrencia->getNome(237, 2, 'cnab400')));
        $this->assertEquals('entrada confirmada', strtolower($codigoOcorrencia->getNome(237, 2)));
        $this->assertEquals(null, strtolower($codigoOcorrencia->getNome(237, 25446)));
    }
}
