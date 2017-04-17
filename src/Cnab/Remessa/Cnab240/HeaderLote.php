<?php

namespace Cnab\Remessa\Cnab240;

class HeaderLote extends \Cnab\Format\Linha
{
    public function __construct(\Cnab\Remessa\IArquivo $arquivo)
    {
        $yamlLoad = new \Cnab\Format\YamlLoad($arquivo->codigo_banco, $arquivo->layoutVersao);
        $yamlLoad->fileNameGeneric = 'header_lote';
        $yamlLoad->load($this, 'cnab240', 'remessa/header_lote');
    }
}
