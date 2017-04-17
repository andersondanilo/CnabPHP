<?php

namespace Cnab\Retorno\Cnab400;

class Trailer extends \Cnab\Format\Linha
{
    public function __construct(\Cnab\Retorno\IArquivo $arquivo)
    {
        $yamlLoad = new \Cnab\Format\YamlLoad($arquivo->codigo_banco, $arquivo->layoutVersao);
        $yamlLoad->load($this, 'cnab400', 'retorno/trailer_arquivo');
    }
}
