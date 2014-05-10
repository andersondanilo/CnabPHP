<?php
namespace Cnab\Cnab400\Retorno;

class Trailer extends \Cnab\Format\Linha
{
	public function __construct($codigo_banco)
	{
		$yamlLoad = new \Cnab\Format\YamlLoad($codigo_banco);
        $yamlLoad->load($this, CNAB_FORMAT_PATH.'/cnab400/retorno/trailer_arquivo.yml');
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

	public function decorate()
	{
		$this->detalhe->addField('tipo_de_registro',         1,    1, '9(01)',                '9'); // Identificação Do Registro Trailer
		$this->detalhe->addField('codigo_de_retorno',        2,    2, '9(01)',                '2'); // Identificação De Arquivo Retorno
		$this->detalhe->addField('codigo_de_servico',        3,    4, '9(02)',                '01'); // Identificação Do Tipo De Serviço
		$this->detalhe->addField('codigo_do_banco',          5,    7, '9(03)',                false); // Identificação Do Banco Na Compensação

		$this->detalhe->addField('numero_sequencial',      395,  400, '9(06)',                false); // Número Seqüencial Do Registro No Arquivo
	}
}

class ItauFields extends BaseFields
{
	public function decorate()
	{
		$this->detalhe->addField('brancos01',                8,   17, 'X(10)',                false); // Complemento De Registro
		$this->detalhe->addField('qtdede_titulos',          18,   25, '9(08)',                false); // Nota 21 - Qtde. De Títulos Em Cobr. Simples
		$this->detalhe->addField('valor_total',             26,   39, '9(12)V9(2)',           false); // Nota 21 - Vr Total Dos Títulos Em Cobrança Simples
		$this->detalhe->addField('aviso_bancario',          40,   47, 'X(08)',                false); // Nota 22 - Referência Do Aviso Bancário
		$this->detalhe->addField('brancos02',               48,   57, 'X(10)',                false); // Complemento Do Registro
		$this->detalhe->addField('qtdede_titulos_dup',      58,   65, '9(08)',                false); // Nota 21 - Qtde De Títulos Em Cobrança/vinculada
		$this->detalhe->addField('valor_total_dup',         66,   79, '9(12)V9(2)',           false); // Nota 21 - Vr Total Dos Títulos Em Cobrança/vinculada
		$this->detalhe->addField('aviso_bancario_dup',      80,   87, 'X(08)',                false); // Nota 22 - Referência Do Aviso Bancário
		$this->detalhe->addField('brancos03',               88,  177, 'X(90)',                false); // Complemento Do Registro
		$this->detalhe->addField('qtdede_titulos_dup2',    178,  185, '9(08)',                false); // Nota 21 - Qtde. De Títulos Em Cobr. Direta./escritural
		$this->detalhe->addField('valor_total_dup2',       186,  199, '9(12)V9(2) NOTA 21',   false); // Vr Total Dos Títulos Em Cobr. Direta/escrit.
		$this->detalhe->addField('aviso_bancario_dup2',    200,  207, 'X(08)',                false); // Nota 22 - Referência Do Aviso Bancário
		$this->detalhe->addField('controle_do_arquivo',    208,  212, '9(05)',                false); // Número Seqüencial Do Arquivo Retorno
		$this->detalhe->addField('qtde_de_detalhes',       213,  220, '9(08)',                false); // Quantidade De Registros De Transação
		$this->detalhe->addField('vlr_total_informado',    221,  234, '9(12)V9(2)',           false); // Valor Dos Títulos Informados No Arquivo
		$this->detalhe->addField('brancos04',              235,  394, 'X(160)',               false); // Complemento Do Registro

		return parent::decorate();	
	}
}

class CefFields extends BaseFields
{
	public function decorate()
	{
		$this->detalhe->addField('brancos01',                8,  394, 'X(387)',           false); // Complemento De Registro
		return parent::decorate();
	}
}
*/