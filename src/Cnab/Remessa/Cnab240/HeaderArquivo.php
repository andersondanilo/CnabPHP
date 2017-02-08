<?php

namespace Cnab\Remessa\Cnab240;

class HeaderArquivo extends \Cnab\Format\Linha
{
    public function __construct(\Cnab\Remessa\IArquivo $arquivo)
    {
        $yamlLoad = new \Cnab\Format\YamlLoad($arquivo->codigo_banco, $arquivo->layoutVersao);
        $yamlLoad->fileNameGeneric = 'header_arquivo';
        $yamlLoad->load($this, 'cnab240', 'remessa/header_arquivo');
    }
}
