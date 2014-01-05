<?php
require_once dirname(__FILE__).'/../../format/Linha.php';

class Cnab_Retorno_Cnab400_Header extends Cnab_Format_Linha
{
	public $fieldsGroup;

	public function __construct($codigo_banco)
	{

		if(Cnab_Banco::ITAU == $codigo_banco)
			$this->fieldsGroup = new Cnab_Retorno_Cnab400_Header_ItauFields($this);

		if(Cnab_Banco::CEF == $codigo_banco)
			$this->fieldsGroup = new Cnab_Retorno_Cnab400_Header_CefFields($this);

		if(!$this->fieldsGroup)
			throw new Exception('Banco não encontrado');

		$this->fieldsGroup->decorate();
	}

	public function getConta()
	{
		return $this->fieldsGroup->getConta();
	}

	public function getContaDac()
	{
		return $this->fieldsGroup->getContaDac();
	}
}

abstract class Cnab_Retorno_Cnab400_Header_BaseFields
{
	public $detalhe;

	public function __construct($detalhe)
	{
		$this->detalhe = $detalhe;
	}

	public function decorate()
	{
		$this->detalhe->addField('tipo_de_registro',               1,    1, '9(01)',    '0'); // Identificação Do Registro Header
		$this->detalhe->addField('codigo_de_retorno',              2,    2, '9(01)',    '2'); // Identificação Do Arquivo Retorno
		$this->detalhe->addField('literal_de_retorno',             3,    9, 'X(07)',    false); // Identificação. Por Extenso Do Tipo De Movimento
		$this->detalhe->addField('retorno_codigo_do_servico',     10,   11, '9(02)',    '01'); // Identificação Do Tipo De Serviço
		$this->detalhe->addField('literal_de_servico',            12,   26, 'X(15)',    false); // Identificação Por Extenso Do Tipo De Serviço

		$this->detalhe->addField('nome_da_empresa',               47,   76, 'X(30)',    false); // Nome Por Extenso Da "empresa Mãe"	
		$this->detalhe->addField('codigo_do_banco',               77,   79, '9(03)',    false/*'341'*/); // Número Do Banco Na Câmara De Compensação
		$this->detalhe->addField('nome_do_banco',                 80,   94, 'X(15)',    false/*'BANCO ITAU SA'*/); // Nome Por Extenso Do Banco Cobrador
		$this->detalhe->addField('data_de_geracao',               95,  100, '9(06)',    false); // Data De Geração Do Arquivo
	}

	abstract public function getConta();
	abstract public function getContaDac();
}

class Cnab_Retorno_Cnab400_Header_CefFields extends Cnab_Retorno_Cnab400_Header_BaseFields
{
	public function decorate()
	{
		$this->detalhe->addField('codigo_cedente',     27,  42, '9(16)',  false);
		$this->detalhe->addField('brancos01',          43,  46, 'X(04)',  '');		

		$this->detalhe->addField('mensagem',                     101,  158, 'X(58)',    false); // Unidade Da Densidade
		$this->detalhe->addField('brancos02',                    159,  389, 'X(231)',   ''); // Complemento Do Registro
		$this->detalhe->addField('numero_sequencial_a',     390, 394, '9(05)',  '1');
		$this->detalhe->addField('numero_sequencial_b',     395, 400, '9(06)',  '1');

		return parent::decorate();
	}

	public function getConta()
	{
		$codigo_cedente = sprintf('%016d', $this->detalhe->codigo_cedente);
		return substr($codigo_cedente, 7, 8);
	}

	public function getContaDac()
	{
		$codigo_cedente = sprintf('%016d', $this->detalhe->codigo_cedente);
		return substr($codigo_cedente, 15, 1);
	}
}

class Cnab_Retorno_Cnab400_Header_ItauFields extends Cnab_Retorno_Cnab400_Header_BaseFields
{
	public function decorate()
	{
		$this->detalhe->addField('cobranca',                      27,   30, '9(04)',    false); // Agência Agência Mantenedora Da Conta
		$this->detalhe->addField('zeros01',                       31,   32, '9(02)',    '00'); // Complemento De Registro
		$this->detalhe->addField('conta',                         33,   37, '9(05)',    false); // Número Da Conta Corrente Da Empresa
		$this->detalhe->addField('dac',                           38,   38, '9(01)',    false); // Dígito De Auto Conferência Ag/conta Empresa
		$this->detalhe->addField('brancos01',                     39,   46, 'X(08)',    ''); // Complemento Do Registro

		$this->detalhe->addField('densidade',                    101,  105, '9(05)',    false); // Unidade Da Densidade
		$this->detalhe->addField('unidade_de_densid.',           106,  108, 'X(03)',    'BPI'); // Densidade De Gravação Do Arquivo
		$this->detalhe->addField('nº_seq._arquivo_ret.',         109,  113, '9(05)',    false); // Número Seqüencial Do Arquivo Retorno
		$this->detalhe->addField('data_de_credito',              114,  119, '9(06)',    false); // Data De Crédito Dos Lançamentos
		$this->detalhe->addField('brancos02',                    120,  394, 'X(275)',   ''); // Complemento Do Registro
		$this->detalhe->addField('numero_sequencial',            395,  400, '9(06)',    '000001'); // Número Seqüencial Do Registro No Arquivo	

		return parent::decorate();
	}

	public function getConta()
	{
		return $this->detalhe->conta;
	}

	public function getContaDac()
	{
		return $this->detalhe->dac;
	}
}
