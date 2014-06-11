<?php
namespace Cnab\Retorno\Cnab400;

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
		$data = $this->data_vencimento ? \DateTime::createFromFormat('dmy', sprintf('%06d', $this->data_vencimento)) : false;
        if($data)
            $data->setTime(0,0,0);
        return $data;
	}
	
	/**
	 * Retorna a data em que o dinheiro caiu na conta
	 * @return \DateTime
	 */
	public function getDataCredito()
	{
		$data = $this->data_credito ? \DateTime::createFromFormat('dmy', sprintf('%06d', $this->data_credito)) : false;
        if($data)
            $data->setTime(0,0,0);
        return $data;
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
		$data = $this->data_de_ocorrencia ? \DateTime::createFromFormat('dmy', sprintf('%06d', $this->data_de_ocorrencia)) : false;
        if($data)
            $data->setTime(0,0,0);
        return $data;
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
        
        if(\Cnab\Banco::BRADESCO == $this->_codigo_banco)
        {
            if(2 == $codigo)
                return 'Entrada Confirmada';
            else if(3 == $codigo)
                return 'Entrada Rejeitada';
            else if(6 == $codigo)
                return 'Liquidação normal';
            else if(9 == $codigo)
                return 'Baixado Automat. via Arquivo';
            else if(10 == $codigo)
                return 'Baixado conforme instruções da Agência';
            else if(11 == $codigo)
                return 'Em Ser - Arquivo de Títulos pendentes';
            else if(12 == $codigo)
                return 'Abatimento Concedido';
            else if(13 == $codigo)
                return 'Abatimento Cancelado';
            else if(14 == $codigo)
                return 'Vencimento Alterado';
            else if(15 == $codigo)
                return 'Liquidação em Cartório';
            else if(16 == $codigo)
                return 'Título Pago em Cheque – Vinculado';
            else if(17 == $codigo)
                return 'Liquidação após baixa ou Título não registrado';
            else if(18 == $codigo)
                return 'Acerto de Depositária (sem motivo)';
            else if(19 == $codigo)
                return 'Confirmação Receb. Inst. de Protesto';
            else if(20 == $codigo)
                return 'Confirmação Recebimento Instrução Sustação de Protesto';
            else if(21 == $codigo)
                return 'Acerto do Controle do Participante';
            else if(22 == $codigo)
                return 'Título Com Pagamento Cancelado';
            else if(23 == $codigo)
                return 'Entrada do Título em Cartório';
            else if(24 == $codigo)
                return 'Entrada rejeitada por CEP Irregular';
            else if(27 == $codigo)
                return 'Baixa Rejeitada';
            else if(28 == $codigo)
                return 'Débito de tarifas/custas';
            else if(30 == $codigo)
                return 'Alteração de Outros Dados Rejeitados';
            else if(32 == $codigo)
                return 'Instrução Rejeitada';
            else if(33 == $codigo)
                return 'Confirmação Pedido Alteração Outros Dados';
            else if(34 == $codigo)
                return 'Retirado de Cartório e Manutenção Carteira';
            else if(35 == $codigo)
                return 'Desagendamento do débito automático';
            else if(40 == $codigo)
                return 'Estorno de pagamento';
            else if(55 == $codigo)
                return 'Sustado judicial';
            else if(68 == $codigo)
                return 'Acerto dos dados do rateio de Crédito';
            else if(69 == $codigo)
                return 'Cancelamento dos dados do rateio';
        }
        else if(\Cnab\Banco::CEF == $this->_codigo_banco)
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