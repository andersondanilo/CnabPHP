<?php
require_once dirname(__FILE__).'/../../format/Linha.php';

class Cnab_Remessa_Cnab400_Detalhe extends Cnab_Format_Linha
{
	public $fieldsGroup;
	public $arquivo;

	public function __construct(Cnab_Remessa_IArquivo $arquivo)
	{
		$this->arquivo = $arquivo;
		$codigo_banco = $arquivo->codigo_banco;
		if(Cnab_Banco::ITAU == $codigo_banco)
			$this->fieldsGroup = new Cnab_Remessa_Cnab400_Detalhe_ItauFields($this);
		else if(Cnab_Banco::CEF == $codigo_banco)
			$this->fieldsGroup = new Cnab_Remessa_Cnab400_Detalhe_CefFields($this);

		if(!$this->fieldsGroup)
			throw new Exception('Banco não encontrado');

		$this->fieldsGroup->addFields();
	}
}

class Cnab_Remessa_Cnab400_Detalhe_BaseFields
{
	public $detalhe;

	public function __construct($detalhe)
	{
		$this->detalhe = $detalhe;
	}

	public function addFields()
	{
		$this->detalhe->addField('tipo_de_registro',           1,   1, '9(01)',  '1');
		$this->detalhe->addField('codigo_de_inscricao',        2,   3, '9(02)',  false);  // Nota 1
		/* Nota 1
		 * 1 = Cpf Cedente, 2 = CNPJ Cedente, 3 = CPF Sacador, 4 CPF Sacador
		 */ 
		$this->detalhe->addField('numero_de_inscricao',        4,  17, '9(14)',  false);  // nota 1

		$this->detalhe->addField('codigo_de_ocorrencia',     109, 110, '9(02)',  '1'); // nota 6, // 1 = Remessa
		$this->detalhe->addField('numero_do_documento',      111, 120, 'X(10)',  false); // nota18
		$this->detalhe->addField('vencimento',               121, 126, '9(06)',  false); // nota 7
		$this->detalhe->addField('valor_do_titulo',          127, 139, '9(11)V9(2)',  false); // nota 8 
		
		$this->detalhe->addField('agencia_cobradora',        143, 147, '9(05)',  '0'); // nota 9, no arquivo de remessa preencher com zeros 
		$this->detalhe->addField('especie',                  148, 149, 'X(02)',  false); // nota 10
		$this->detalhe->addField('aceite',                   150, 150, 'X(01)',  false); // A = Aceite, N = Não Aceite
		$this->detalhe->addField('data_de_emissao',          151, 156, '9(06)',  false); // nota 31
		$this->detalhe->addField('instrucao1',               157, 158, 'X(02)',  false); // nota 11, página 20
		$this->detalhe->addField('instrucao2',               159, 160, 'X(02)',  false); // nota 11, 20 = Não receber após 10 dias do vencimento
		$this->detalhe->addField('juros_de_um_dia',          161, 173, '9(11)V9(2)',  false); // nota 12
		$this->detalhe->addField('data_desconto',            174, 179, '9(06)',  false);
		$this->detalhe->addField('valor_desconto',           180, 192, '9(11)V9(2)',  false); // nota 13
		$this->detalhe->addField('valor_do_iof',             193, 205, '9(11)V9(2)',  '0'); // nota 14
		$this->detalhe->addField('abatimento',               206, 218, '9(11)V9(2)',  '0'); // nota 13
		$this->detalhe->addField('sacado_codigo_de_inscricao',      219, 220, '9(02)',  false); // 1 = cpf, 2 = cnpj
		$this->detalhe->addField('sacado_numero_de_inscricao',      221, 234, '9(14)',  false);

		$this->detalhe->addField('logradouro',               275, 314, 'X(40)',  false);
		$this->detalhe->addField('bairro',                   315, 326, 'X(12)',  false);
		$this->detalhe->addField('cep',                      327, 334, '9(08)',  false);
		$this->detalhe->addField('cidade',                   335, 349, 'X(15)',  false);
		$this->detalhe->addField('estado',                   350, 351, 'X(02)',  false);

		$this->detalhe->addField('prazo',                    392, 393, '9(02)',  false); // nota 11 (A)

		$this->detalhe->addField('numero_sequencial',        395, 400, '9(06)',  false);
	}
}

class Cnab_Remessa_Cnab400_Detalhe_CefFields extends Cnab_Remessa_Cnab400_Detalhe_BaseFields
{
	public function addFields()
	{
		$banco = Cnab_Banco::getBanco(Cnab_Banco::CEF);
		// Cnab_Banco::CEF
		$this->detalhe->addField('codigo_cedente',            18,  33, 'X(16)',  '');	

		// Cnab_Banco::CEF
		$this->detalhe->addField('brancos21',                 34,  35, 'X(2)',   '');
		$this->detalhe->addField('taxa_de_permanencia',       36,  37, '9(2)',   false);
		$this->detalhe->addField('uso_da_empresa',            38,  62, 'X(25)',  false);
		$this->detalhe->addField('nosso_numero',              63,  73, '9(11)',  false);
		$this->detalhe->addField('brancos22',                 74,  76, 'X(3)',   '');
		$this->detalhe->addField('mensagem',                  77, 106, 'X(30)',  false);
		$this->detalhe->addField('numero_da_carteira',       107, 108, '9(2)',   false);

		$this->detalhe->addField('codigo_do_banco',          140, 142, '9(03)',  $banco['codigo_do_banco']);

		// CEF
		$this->detalhe->addField('nome',                     235, 274, 'X(40)',  false); // nota 15	

		// CEF
		$this->detalhe->addField('data_multa',               352, 357, '9(6)',  false);
		$this->detalhe->addField('valor_multa',              358, 367, '9(8)V9(2)',  false);
		$this->detalhe->addField('sacador',                  368, 389, 'X(22)',  false);
		$this->detalhe->addField('instrucao3',               390, 391, '9(2)',  '0');

		$this->detalhe->addField('moeda',                394, 394, 'X(01)',  '1');

		return parent::addFields();
	}
}

class Cnab_Remessa_Cnab400_Detalhe_ItauFields extends Cnab_Remessa_Cnab400_Detalhe_BaseFields
{
	public function addFields()
	{
		$banco = Cnab_Banco::getBanco(Cnab_Banco::CEF);

		$this->detalhe->addField('agencia',                   18,  21, '9(04)',  false);
		$this->detalhe->addField('zeros01',                   22,  23, '9(02)',  '0');
		$this->detalhe->addField('conta',                     24,  28, '9(05)',  false);
		$this->detalhe->addField('conta_dac',                 29,  29, '9(01)',  false);
		$this->detalhe->addField('brancos01',                 30,  33, 'X(04)',  '');

		$this->detalhe->addField('codigo_instrucao',          34,  37, '9(04)',  false); // Nota 27
		$this->detalhe->addField('uso_da_empresa',            38,  62, 'X(25)',  false); // Nota 2
		$this->detalhe->addField('nosso_numero',              63,  70, '9(08)',  false); // Nota 3
		$this->detalhe->addField('qtde_de_moeda',             71,  83, '9(08)V9(5)',  false); // nota 4
		$this->detalhe->addField('numero_da_carteira',        84,  86, '9(03)',  false); // nota 5
		$this->detalhe->addField('uso_do_banco',              87, 107, 'X(21)',  false);
		$this->detalhe->addField('codigo_da_carteira',       108, 108, 'X(01)',  false); // nota 5 pagina 17, 'I'

		$this->detalhe->addField('codigo_do_banco',          140, 142, '9(03)',  $banco['codigo_do_banco']);

		$this->detalhe->addField('nome',                     235, 264, 'X(30)',  false); // nota 15
		$this->detalhe->addField('brancos02',                265, 274, 'X(10)',  ''); // nota 15

		$this->detalhe->addField('sacador',                  352, 381, 'X(30)',  false); // nota 16
		$this->detalhe->addField('brancos03',                382, 385, 'X(04)',  '');
		$this->detalhe->addField('data_de_mora',             386, 391, '9(06)',  '0');

		$this->detalhe->addField('brancos04',                394, 394, 'X(01)',  '');

		return parent::addFields();
	}
}