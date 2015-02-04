<?php
namespace Cnab\Retorno\Cnab400;

class Trailer extends \Cnab\Format\Linha
{
	public function __construct($codigo_banco)
	{
		$yamlLoad = new \Cnab\Format\YamlLoad($codigo_banco);
        $yamlLoad->load($this, 'cnab400', 'retorno/trailer_arquivo');
	}	
}
