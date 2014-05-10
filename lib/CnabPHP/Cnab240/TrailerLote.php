<?php
namespace Cnab\Cnab240;

class TrailerLote extends \Cnab\Format\Linha
{
	public function __construct($codigo_banco)
    {
        $yamlLoad = new \Cnab\Format\YamlLoad($codigo_banco);
        $yamlLoad->load($this, CNAB_FORMAT_PATH.'/cnab240/trailer_lote.yml');
	}
}
