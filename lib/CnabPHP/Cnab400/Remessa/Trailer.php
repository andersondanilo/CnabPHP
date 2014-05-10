<?php
namespace Cnab\Cnab400\Remessa;

class Trailer extends \Cnab\Format\Linha
{
    public $_codigo_banco;

    public function __construct(\Cnab\Remessa\IArquivo $arquivo)
    {
        $this->_codigo_banco = $arquivo->codigo_banco;

        $yamlLoad = new \Cnab\Format\YamlLoad($this->_codigo_banco);
        $yamlLoad->load($this, CNAB_FORMAT_PATH.'/cnab400/remessa/trailer_arquivo.yml');
    }
}
/*
class BaseFields
{
	public $detalhe;

	public function __construct($detalhe)
	{
		$this->detalhe = $detalhe;
	}

	public function addFields()
	{
		$this->detalhe->addField('tipo_de_registro',        1,   1, '9(01)',  '9');	
		$this->detalhe->addField('brancos01',               2, 394, 'X(393)',  '');
		$this->detalhe->addField('numero_sequencial',     395, 400, '9(06)',  false);		
	}
}
 * 
 */