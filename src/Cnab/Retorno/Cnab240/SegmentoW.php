<?php

namespace Cnab\Retorno\Cnab240;

class SegmentoW extends \Cnab\Format\Linha
{
    public function __construct(\Cnab\Retorno\IArquivo $arquivo)
    {
        $yamlLoad = new \Cnab\Format\YamlLoad($arquivo->codigo_banco, $arquivo->layoutVersao);
        $yamlLoad->load($this, 'cnab240', 'retorno/detalhe_segmento_w');
    }
}
