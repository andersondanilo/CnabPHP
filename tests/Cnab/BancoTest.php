<?php

namespace Cnab\Tests;

use Cnab\Banco;

class BancoTest extends \PHPUnit_Framework_TestCase
{
    public function testContemOsBancosEsperados()
    {
        $this->assertTrue(Banco::existBanco(Banco::ITAU));
        $this->assertTrue(Banco::existBanco(Banco::CEF));
        $this->assertTrue(Banco::existBanco(Banco::SANTANDER));
        $this->assertTrue(Banco::existBanco(Banco::BRADESCO));
    }
}
