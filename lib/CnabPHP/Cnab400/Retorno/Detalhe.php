<?php
namespace Cnab\Cnab400\Retorno;

class Detalhe extends \Cnab\Format\Linha implements \Cnab\Retorno\IDetalhe
{
	public $_codigo_banco;
    
	public function __construct($codigo_banco)
	{
		$this->_codigo_banco = $codigo_banco;

		$yamlLoad = new \Cnab\Format\YamlLoad($codigo_banco);
        $yamlLoad->load($this, CNAB_FORMAT_PATH.'/cnab400/retorno/detalhe.yml');
	}
	
	/**
	 * Retorno se é para dar baixa no boleto
	 * @return Boolean
	 */
	public function isBaixa()
	{
		$tipo_baixa = array(9, 10, 32, 47, 59, 72);
		$codigo_ocorrencia = (int)$this->codigo_de_ocorrencia;
		return self::isBaixaStatic($codigo_ocorrencia);
	}
	
	public static function isBaixaStatic($codigo)
	{
		$tipo_baixa = array(9, 10, 32, 47, 59, 72);
		$codigo_ocorrencia = (int)$codigo;
		if(in_array($codigo_ocorrencia, $tipo_baixa))
			return true;
		else
			return false;
	}
	
	/**
	 * Retorno se é uma baixa rejeitada
	 * @return Boolean
	 */
	public function isBaixaRejeitada()
	{
		$tipo_baixa = array(15);
		$codigo_ocorrencia = (int)$this->codigo_de_ocorrencia;
		if(in_array($codigo_ocorrencia, $tipo_baixa))
			return true;
		else
			return false;
	}
	
	/**
	 * Identifica o tipo de detalhe, se por exemplo uma taxa de manutenção
	 * @return Integer
	 */
	public function getCodigo()
	{
		return (int)$this->codigo_de_ocorrencia;
	}
	
	/**
	 * Retorna o valor recebido em conta
	 * @return Double
	 */
	public function getValorRecebido()
	{
		return $this->valor_principal;
	}
	
	/**
	 * Retorna o valor do título
	 * @return Double
	 */
	public function getValorTitulo()
	{
		return $this->valor_do_titulo;
	}
	
	/**
	 * Retorna o valor da tarifa
	 * @return Double
	 */
	public function getValorTarifa()
	{
		return $this->valor_tarifa;
	}
	
	/**
	 * Retorna o valor do Imposto sobre operações financeiras
	 * @return Double
	 */
	public function getValorIOF()
	{
		return $this->valor_iof;
	}
	
	/**
	 * Retorna o valor dos descontos concedido (antes da emissão)
	 * @return Double;
	 */
	public function getValorDesconto()
	{
		return $this->valor_desconto;
	}
	
	/**
	 * Retornna o valor dos abatimentos concedidos (depois da emissão)
	 * @return Double
	 */
	public function getValorAbatimento()
	{
		return $this->valor_abatimento;
	}
	
	/**
	 * Retorna o valor de outros creditos 
	 * @return Double
	 */
	public function getValorOutrosCreditos()
	{
		if(\Cnab\Banco::CEF == $this->_codigo_banco)
			return 0;
		else
			return $this->valor_outros_creditos;
		
	}
	
	/**
	 * Retorna o número do documento do boleto
	 * @return String
	 */
	public function getNumeroDocumento()
	{
		return $this->numero_do_documento;
	}
	
	/** 
	 * Retorna o nosso número do boleto
	 * @return String
	 */
	public function getNossoNumero()
	{
		return $this->nosso_numero;
	}
	
	/**
	 * Retorna o objeto \DateTime da data de vencimento do boleto
	 * @return \DateTime
	 */
	public function getDataVencimento()
	{
		return $this->data_vencimento ? \DateTime::createFromFormat('dmy', sprintf('%06d', $this->data_vencimento)) : false;
	}
	
	/**
	 * Retorna a data em que o dinheiro caiu na conta
	 * @return \DateTime
	 */
	public function getDataCredito()
	{
		return $this->data_credito ? \DateTime::createFromFormat('dmy', sprintf('%06d', $this->data_credito)) : false;
	}
	
	/**
	 * Retorna o valor de juros e mora
	 */
	public function getValorMoraMulta()
	{
		if(\Cnab\Banco::CEF == $this->_codigo_banco)
			return $this->valor_juros + $this->valor_multa;
		else
			return $this->valor_mora_multa;
	}
	
	/**
	 * Retorna a data da ocorrencia, o dia do pagamento
	 * @return \DateTime
	 */
	public function getDataOcorrencia()
	{
		return $this->data_de_ocorrencia ? \DateTime::createFromFormat('dmy', sprintf('%06d', $this->data_de_ocorrencia)) : false;
	}
	
	/**
	 * Retorna o número da carteira do boleto
	 * @return String
	 */
	public function getCarteira()
	{
		return $this->carteira;
	}
	
	/**
	 * Retorna o número da carteira do boleto
	 * @return String
	 */
	public function getAgencia()
	{
		return $this->agencia;
	}
	
	/**
	 * Retorna a agencia cobradora
	 * @return string
	 */
	public function getAgenciaCobradora()
	{
		return $this->agencia_cobradora;
	}
	
	/**
	 * Retorna a o dac da agencia cobradora
	 * @return string
	 */
	public function getAgenciaCobradoraDac()
	{
		return $this->agencia_cobradora_dac;
	}
	
	/**
	 * Retorna o numero sequencial
	 * @return Integer;
	 */
	public function getNumeroSequencial()
	{
		return $this->numero_sequencial;
	}
	
	/**
	 * Retorna o nome do código
	 * @return string
	 */
	public function getCodigoNome()
	{
		$codigo = $this->getCodigo();
	
		if(\Cnab\Banco::CEF == $this->_codigo_banco)
		{
			if(01 == $codigo)
			    return 'Entrada Confirmada';
			else if(02 == $codigo)
			    return 'Baixa Confirmada';
			else if(03 == $codigo)
			    return 'Abatimento Concedido';
			else if(04 == $codigo)
			    return 'Abatimento Cancelado';
			else if(05 == $codigo)
			    return 'Vencimento Alterado';
			else if(06 == $codigo)
			    return 'Uso da Empresa Alterado';
			else if(07 == $codigo)
			    return 'Prazo de Protesto Alterado';
			else if(08 == $codigo)
			    return 'Prazo de Devolução Alterado';
			else if(09 == $codigo)
			    return 'Alteração Confirmada';
			else if(10 == $codigo)
			    return 'Alteração com Reemissão de Bloqueto Confirmada';
			else if(11 == $codigo)
			    return 'Alteração da Opção de Protesto para Devolução';
			else if(12 == $codigo)
			    return 'Alteração da Opção de Devolução para protesto';
			else if(20 == $codigo)
			    return 'Em Ser';
			else if(21 == $codigo)
			    return 'Liquidação';
			else if(22 == $codigo)
			    return 'Liquidação em Cartório';
			else if(23 == $codigo)
			    return 'Baixa por Devolução';
			else if(24 == $codigo)
			    return 'Baixa por Franco Pagamento';
			else if(25 == $codigo)
			    return 'Baixa por Protesto';
			else if(26 == $codigo)
			    return 'Título enviado para Cartório';
			else if(27 == $codigo)
			    return 'Sustação de Protesto';
			else if(28 == $codigo)
			    return 'Estorno de Protesto';
			else if(29 == $codigo)
			    return 'Estorno de Sustação de Protesto';
			else if(30 == $codigo)
			    return 'Alteração de Título';
			else if(31 == $codigo)
			    return 'Tarifa sobre Título Vencido';
			else if(32 == $codigo)
			    return 'Outras Tarifas de Alteração';
			else if(33 == $codigo)
			    return 'Estorno de Baixa/Liquidação';
			else if(34 == $codigo)
			    return 'Transferência de Carteira/Entrada';
			else if(35 == $codigo)
			    return 'Transferência de Carteira/Baixa';
			else if(99 == $codigo)
			    return 'Rejeição do Título – Cód. Rejeição informado nas POS 80 a 82';
		}
		else
		{
			if($codigo == 2) 
				return 'ENTRADA CONFIRMADA COM POSSIBILIDADE DE MENSAGEM (NOTA 20 – TABELA 10) ';
			else if($codigo == 3)  
				return 'ENTRADA REJEITADA (NOTA 20 - TABELA 1)';
			else if($codigo == 4) 
				return 'ALTERAÇÃO DE DADOS - NOVA ENTRADA OU ALTERAÇÃO/EXCLUSÃO DE DADOS ACATADA ';
			else if($codigo == 5)  
				return 'ALTERAÇÃO DE DADOS – BAIXA';
			else if($codigo == 6) 
				return 'LIQUIDAÇÃO NORMAL ';
			else if($codigo == 7)
				return 'LIQUIDAÇÃO PARCIAL – COBRANÇA INTELIGENTE (B2B)';
			else if($codigo == 8) 
				return 'LIQUIDAÇÃO EM CARTÓRIO ';
			else if($codigo == 9)  
				return 'BAIXA SIMPLES';
			else if($codigo == 10) 
				return 'BAIXA POR TER SIDO LIQUIDADO ';
			else if($codigo == 11)  
				return 'EM SER (SÓ NO RETORNO MENSAL)';
			else if($codigo == 12) 
				return 'ABATIMENTO CONCEDIDO ';
			else if($codigo == 13)  
				return 'ABATIMENTO CANCELADO';
			else if($codigo == 14) 
				return 'VENCIMENTO ALTERADO ';
			else if($codigo == 15)  
				return 'BAIXAS REJEITADAS (NOTA 20 - TABELA 4)';
			else if($codigo == 16) 
				return 'INSTRUÇÕES REJEITADAS (NOTA 20 - TABELA 3) ';
			else if($codigo == 17) 
				return 'ALTERAÇÃO/EXCLUSÃO DE DADOS REJEITADOS (NOTA 20 - TABELA 2)';
			else if($codigo == 18) 
				return 'COBRANÇA CONTRATUAL - INSTRUÇÕES/ALTERAÇÕES REJEITADAS/PENDENTES (NOTA 20 - TABELA 5) ';
			else if($codigo == 19) 
				return 'CONFIRMA RECEBIMENTO DE INSTRUÇÃO DE PROTESTO';
			else if($codigo == 20) 
				return 'CONFIRMA RECEBIMENTO DE INSTRUÇÃO DE SUSTAÇÃO DE PROTESTO /TARIFA';
			else if($codigo == 21) 
				return 'CONFIRMA RECEBIMENTO DE INSTRUÇÃO DE NÃO PROTESTAR';
			else if($codigo == 23) 
				return 'TÍTULO ENVIADO A CARTÓRIO/TARIFA';
			else if($codigo == 24) 
				return 'INSTRUÇÃO DE PROTESTO REJEITADA / SUSTADA / PENDENTE (NOTA 20 - TABELA 7)';
			else if($codigo == 25) 
				return 'ALEGAÇÕES DO SACADO (NOTA 20 - TABELA 6)';
			else if($codigo == 26) 
				return 'TARIFA DE AVISO DE COBRANÇA';
			else if($codigo == 27) 
				return 'TARIFA DE EXTRATO POSIÇÃO (B40X)';
			else if($codigo == 28) 
				return 'TARIFA DE RELAÇÃO DAS LIQUIDAÇÕES';
			else if($codigo == 29) 
				return 'TARIFA DE MANUTENÇÃO DE TÍTULOS VENCIDOS';
			else if($codigo == 30)  
				return 'DÉBITO MENSAL DE TARIFAS (PARA ENTRADAS E BAIXAS)';
			else if($codigo == 32) 
				return 'BAIXA POR TER SIDO PROTESTADO';
			else if($codigo == 33) 
				return 'CUSTAS DE PROTESTO';
			else if($codigo == 34) 
				return 'CUSTAS DE SUSTAÇÃO';
			else if($codigo == 35) 
				return 'CUSTAS DE CARTÓRIO DISTRIBUIDOR';
			else if($codigo == 36) 
				return 'CUSTAS DE EDITAL';
			else if($codigo == 37) 
				return 'TARIFA DE EMISSÃO DE BOLETO/TARIFA DE ENVIO DE DUPLICATA';
			else if($codigo == 38) 
				return 'TARIFA DE INSTRUÇÃO';
			else if($codigo == 39) 
				return 'TARIFA DE OCORRÊNCIAS';
			else if($codigo == 40) 
				return 'TARIFA MENSAL DE EMISSÃO DE BOLETO/TARIFA MENSAL DE ENVIO DE DUPLICATA';
			else if($codigo == 41) 
				return 'DÉBITO MENSAL DE TARIFAS – EXTRATO DE POSIÇÃO (B4EP/B4OX)';
			else if($codigo == 42) 
				return 'DÉBITO MENSAL DE TARIFAS – OUTRAS INSTRUÇÕES';
			else if($codigo == 43) 
				return 'DÉBITO MENSAL DE TARIFAS – MANUTENÇÃO DE TÍTULOS VENCIDOS';
			else if($codigo == 44) 
				return 'DÉBITO MENSAL DE TARIFAS – OUTRAS OCORRÊNCIAS';
			else if($codigo == 45) 
				return 'DÉBITO MENSAL DE TARIFAS – PROTESTO';
			else if($codigo == 46) 
				return 'DÉBITO MENSAL DE TARIFAS – SUSTAÇÃO DE PROTESTO';
			else if($codigo == 47) 
				return 'BAIXA COM TRANSFERÊNCIA PARA DESCONTO';
			else if($codigo == 48) 
				return 'CUSTAS DE SUSTAÇÃO JUDICIAL';
			else if($codigo == 51)  
				return 'TARIFA MENSAL REF A ENTRADAS BANCOS CORRESPONDENTES NA CARTEIRA';
			else if($codigo == 52) 
				return 'TARIFA MENSAL BAIXAS NA CARTEIRA';
			else if($codigo == 53) 
				return 'TARIFA MENSAL BAIXAS EM BANCOS CORRESPONDENTES NA CARTEIRA';
			else if($codigo == 54) 
				return 'TARIFA MENSAL DE LIQUIDAÇÕES NA CARTEIRA';
			else if($codigo == 55)  
				return 'TARIFA MENSAL DE LIQUIDAÇÕES EM BANCOS CORRESPONDENTES NA CARTEIRA';
			else if($codigo == 56) 
				return 'CUSTAS DE IRREGULARIDADE';
			else if($codigo == 57)  
				return 'INSTRUÇÃO CANCELADA (NOTA 20 – TABELA 8)';
			else if($codigo == 59) 
				return 'BAIXA POR CRÉDITO EM C/C ATRAVÉS DO SISPAG';
			else if($codigo == 60)  
				return 'ENTRADA REJEITADA CARNÊ (NOTA 20 – TABELA 1)';
			else if($codigo == 61) 
				return 'TARIFA EMISSÃO AVISO DE MOVIMENTAÇÃO DE TÍTULOS (2154)';
			else if($codigo == 62) 
				return 'DÉBITO MENSAL DE TARIFA - AVISO DE MOVIMENTAÇÃO DE TÍTULOS (2154)';
			else if($codigo == 63) 
				return 'TÍTULO SUSTADO JUDICIALMENTE';
			else if($codigo == 64) 
				return 'ENTRADA CONFIRMADA COM RATEIO DE CRÉDITO';
			else if($codigo == 69) 
				return 'CHEQUE DEVOLVIDO (NOTA 20 - TABELA 9)';
			else if($codigo == 71) 
				return 'ENTRADA REGISTRADA, AGUARDANDO AVALIAÇÃO';
			else if($codigo == 72) 
				return 'BAIXA POR CRÉDITO EM C/C ATRAVÉS DO SISPAG SEM TÍTULO CORRESPONDENTE';
			else if($codigo == 73) 
				return 'CONFIRMAÇÃO DE ENTRADA NA COBRANÇA SIMPLES – ENTRADA NÃO ACEITA NA COBRANÇA CONTRATUAL';
			else if($codigo == 76) 
				return 'CHEQUE COMPENSADO';
			else
				return 'Código Inexistente';
		}
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
		$this->detalhe->addField('tipo_de_registro',         1,    1, '9(01)',        '1'); // Identificação Do Registro Transação
		$this->detalhe->addField('codigo_de_inscricao',      2,    3, '9(02)',        false); // Identificação Do Tipo De Inscrição/empresa
		$this->detalhe->addField('numero_de_inscricao',      4,   17, '9(14)',        false); // Número De Inscrição Da Empresa (cpf/cnpj)		

		$this->detalhe->addField('uso_da_empresa',          38,   62, 'X(25)',        false); // Nota 2 - Identificação Do Título Na Empresa

		$this->detalhe->addField('codigo_de_ocorrencia',   109,  110, '9(02)',        false); // Nota 17 - Identificação Da Ocorrência
		$this->detalhe->addField('data_de_ocorrencia',     111,  116, '9(06)',        false); // Data De Ocorrência No Banco
		$this->detalhe->addField('numero_do_documento',    117,  126, 'X(10)',        false); // Nota 18 - Nº Do Documento De Cobrança (dupl, Np Etc)

		$this->detalhe->addField('data_vencimento',        147,  152, '9(06)',        false); // Data De Vencimento Do Título
		$this->detalhe->addField('valor_do_titulo',        153,  165, '9(11)V9(2)',   false); // Valor Nominal Do Título
		$this->detalhe->addField('codigo_do_banco',        166,  168, '9(03)',        false); // Número Do Banco Na Câmara De Compensação
		$this->detalhe->addField('agencia_cobradora',      169,  172, '9(04)',        false); // Nota 9 - Ag. Cobradora, Ag. De Liquidação Ou Baixa
		$this->detalhe->addField('agencia_cobradora_dac',  173,  173, '9(01)',        false); // Dac Da Agência Cobradora
		$this->detalhe->addField('especie',                174,  175, '9(02)',        false); // Nota 10 - Espécie Do Título
		$this->detalhe->addField('valor_tarifa',           176,  188, '9(11)V9(2)',   false); // Valor Da Despesa De Cobrança

		$this->detalhe->addField('valor_iof',              215,  227, '9(11)V9(2)',   false); // Valor Do Iof A Ser Recolhido (notas Seguro)
		$this->detalhe->addField('valor_abatimento',       228,  240, '9(11)V9(2)',   false); // Nota 19 - Valor Do Abatimento Concedido
		$this->detalhe->addField('valor_desconto',         241,  253, '9(11)V9(2)',   false); // Nota 19 - Valor Do Desconto Concedido
		$this->detalhe->addField('valor_principal',        254,  266, '9(11)V9(2)',   false); // Valor Lançado Em Conta Corrente

		$this->detalhe->addField('numero_sequencial',      395,  400, '9(06)',        false); // Número Seqüencial Do Registro No Arquivo
	}
}

class ItauFields extends BaseFields
{
	public function decorate()
	{
		$this->detalhe->addField('agencia',                 18,   21, '9(04)',        false); // Agência Mantenedora Da Conta
		$this->detalhe->addField('zeros01',                 22,   23, '9(02)',        '00'); // Complemento De Registro
		$this->detalhe->addField('conta',                   24,   28, '9(05)',        false); // Número Da Conta Corrente Da Empresa
		$this->detalhe->addField('dac',                     29,   29, '9(01)',        false); // Dígito De Auto Conferência Ag/conta Empresa
		$this->detalhe->addField('brancos01',               30,   37, 'X(08)',        false); // Complemento De Registro	


		$this->detalhe->addField('nosso_numero',            63,   70, '9(08)',        false); // Identificação Do Título No Banco
		$this->detalhe->addField('brancos02',               71,   82, 'X(12)',        false); // Complemento Do Registro
		$this->detalhe->addField('carteira',                83,   85, '9(03)',        false); // Nota 5 - Numero Da Carteira
		$this->detalhe->addField('nosso_numero_dup',        86,   93, '9(08)',        false); // Nota 3 - Identificação Do Título No Banco
		$this->detalhe->addField('dac_nosso_numero',        94,   94, '9(01)',        false); // Nota 3 - Dac Do Nosso Número
		$this->detalhe->addField('brancos03',               95,  107, 'X(13)',        false); // Complemento Do Registro
		$this->detalhe->addField('carteira_cod',           108,  108, 'X(01)',        false); // Nota 5 - Código Da Carteira

		$this->detalhe->addField('nosso_numero_dup2',      127,  134, '9(08)',        false); // Confirmação Do Número Do Título No Banco
		$this->detalhe->addField('brancos04',              135,  146, 'X(12)',        false); // Complemento Do Registro

		$this->detalhe->addField('brancos05',              189,  214, 'X(26)',        false); // Complemento Do Registro	

		$this->detalhe->addField('valor_mora_multa',       267,  279, '9(11)V9(2)',   false); // Valor De Mora E Multa
		$this->detalhe->addField('valor_outros_creditos',  280,  292, '9(11)V9(2)',   false); // Valor De Outros Créditos
		$this->detalhe->addField('boleto_dda',             293,  293, 'X(01)',        false); // Nota 34 - Indicador De Boleto Dda
		$this->detalhe->addField('brancos06',              294,  295, 'X(02)',        false); // Complemento Do Registro
		$this->detalhe->addField('data_credito',           296,  301, 'X(06)',        false); // Data De Crédito Desta Liquidação
		$this->detalhe->addField('instr_cancelada',        302,  305, '9(04)',        false); // Nota 20 - Código Da Instrução Cancelada
		$this->detalhe->addField('brancos07',              306,  311, 'X(06)',        false); // Complemento De Registro
		$this->detalhe->addField('zeros02',                312,  324, '9(13)',        false); // Complemento De Registro
		$this->detalhe->addField('nome_do_sacado',         325,  354, 'X(30)',        false); // Nome Do Sacado
		$this->detalhe->addField('brancos08',              355,  377, 'X(23)',        false); // Complemento Do Registro
		$this->detalhe->addField('erros',                  378,  385, 'X(08)',        false); // Nota 20 - Mensagem Informativa Registros Rejeitados Ou Alegação Do Sacado Ou Registro De Mensagem Informativa
		$this->detalhe->addField('brancos09',              386,  392, 'X(07)',        false); // Complemento Do Registro
		$this->detalhe->addField('codde_liquidacao',       393,  394, 'X(02)',        false); // Nota 28 - Meio Pelo Qual O Título Foi Liquidado

		return parent::decorate();
	}
}

class CefFields extends BaseFields
{
	public function decorate()
	{
		$this->detalhe->addField('codigo_cedente',            18,  33, 'X(16)',  '');	
		$this->detalhe->addField('brancos01',                 34,  37, 'X(04)',  ''); // Complemento De Registro			

		$this->detalhe->addField('nosso_numero',           63,   73, '9(11)',       false); // Identificação Do Título No Banco	
		$this->detalhe->addField('brancos21',              74,   79, 'X(6)',        false); 
		$this->detalhe->addField('codigo_rejeicao',        80,   82, '9(3)',        false); 
		$this->detalhe->addField('uso_do_banco',           83,  106, 'X(24)',        false); 
		$this->detalhe->addField('carteira',              107,  108, '9(2)',        false); 

		$this->detalhe->addField('brancos04',              127,  146, 'X(20)',        false); // Complemento Do Registro

		$this->detalhe->addField('tipo_de_liquidacao', 189,  191, '9(3)',        false);
		$this->detalhe->addField('forma_de_pagamento', 192,  192, '9(1)',        false);
		$this->detalhe->addField('float',              193,  194, '9(2)',        false);
		$this->detalhe->addField('data_debito_tarifa', 195,  200, '9(4)V9(2)',   false);
		$this->detalhe->addField('brancos05',          201,  214, 'X(14)',       false);

		$this->detalhe->addField('valor_juros',       267,  279, '9(11)V9(2)',   false);
		$this->detalhe->addField('valor_multa',       280,  292, '9(11)V9(2)',   false);
		$this->detalhe->addField('moeda',             293,  293, '9(1)',         false);
		$this->detalhe->addField('data_credito',      294,  299, 'X(06)',        false); // Data De Crédito Desta Liquidação
		$this->detalhe->addField('brancos06',         300,  394, 'X(95)',        false); // Data De Crédito Desta Liquidação

		return parent::decorate();
	}
}
 */