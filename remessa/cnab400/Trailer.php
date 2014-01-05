<?php
require_once dirname(__FILE__).'/../../format/Linha.php';

class Cnab_Remessa_Cnab400_Trailer extends Cnab_Format_Linha
{
	public $fieldGroup;
	public $arquivo;

	public function __construct(Cnab_Remessa_IArquivo $arquivo)
	{
		$this->arquivo = $arquivo;
		$codigo_banco = $arquivo->codigo_banco;
		$this->fieldGroup = new Cnab_Remessa_Cnab400_Trailer_BaseFields($this);

		if(!$this->fieldGroup)
			throw new Exception('Banco não encontrado');

		$this->fieldGroup->addFields();
	}
}

class Cnab_Remessa_Cnab400_Trailer_BaseFields
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