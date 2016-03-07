<?php

namespace Cnab\Retorno\Cnab400;

class Header extends \Cnab\Format\Linha
{
    private $_codigo_banco = null;

    public function __construct(\Cnab\Retorno\IArquivo $arquivo)
    {
        $this->_codigo_banco = $arquivo->codigo_banco;
        $yamlLoad = new \Cnab\Format\YamlLoad($arquivo->codigo_banco, $arquivo->layoutVersao);
        $yamlLoad->load($this, 'cnab400', 'retorno/header_arquivo');
    }

    public function getConta()
    {
        if ($this->existField('conta')) {
            return $this->conta;
        } elseif ($this->_codigo_banco == 104) {
            $codigo_cedente = sprintf('%016d', $this->codigo_cedente);

            return substr($codigo_cedente, 7, 8);
        }
    }

    public function getContaDac()
    {
        if ($this->existField('dac')) {
            return $this->dac;
        } elseif ($this->_codigo_banco == 104) {
            $codigo_cedente = sprintf('%016d', $this->codigo_cedente);

            return substr($codigo_cedente, 15, 1);
        }
    }

    public function getCodigoCedente()
    {
        if ($this->existField('codigo_cedente')) {
            return $this->codigo_cedente;
        }
    }
}
