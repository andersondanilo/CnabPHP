<?php
namespace Cnab\Retorno\Cnab240;

class Detalhe extends \Cnab\Format\Linha implements \Cnab\Retorno\IDetalhe
{
    public $codigo_banco;

    public $segmento_t;
    public $segmento_u;
    public $segmento_w;

	public function __construct($codigo_banco)
	{
		$this->codigo_banco = $codigo_banco;
	}
	
	/**
	 * Retorno se é para dar baixa no boleto
	 * @return Boolean
	 */
	public function isBaixa()
    {
        $codigo_movimento = $this->segmento_t->codigo_movimento;
	    return self::isBaixaStatic($codigo_movimento);
	}

	public static function isBaixaStatic($codigo_movimento)
	{
		$tipo_baixa = array(6, 9, 17, 25);
		$codigo_movimento = (int)$codigo_movimento;
		if(in_array($codigo_movimento, $tipo_baixa))
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
		$tipo_baixa = array(3, 26, 30);
		$codigo_movimento = (int)$this->segmento_t->codigo_movimento;
		if(in_array($codigo_movimento, $tipo_baixa))
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
		return (int)$this->segmento_t->codigo_movimento;
	}
	
	/**
	 * Retorna o valor recebido em conta
	 * @return Double
	 */
	public function getValorRecebido()
	{
		return $this->segmento_u->valor_liquido;
	}

	/**
	 * Retorna o valor do título
	 * @return Double
	 */
	public function getValorTitulo()
	{
		return $this->segmento_t->valor_titulo;
	}

	/**
	 * Retorna o valor da tarifa
	 * @return Double
	 */
	public function getValorTarifa()
	{
		return $this->segmento_t->valor_tarifa;
	}

	/**
	 * Retorna o valor do Imposto sobre operações financeiras
	 * @return Double
	 */
	public function getValorIOF()
	{
		return $this->segmento_u->valor_iof;
	}

	/**
	 * Retorna o valor dos descontos concedido (antes da emissão)
	 * @return Double;
	 */
	public function getValorDesconto()
	{
		return $this->segmento_u->valor_desconto;
	}

	/**
	 * Retorna o valor dos abatimentos concedidos (depois da emissão)
	 * @return Double
	 */
	public function getValorAbatimento()
	{
		return $this->segmento_u->valor_abatimento;
	}

	/**
	 * Retorna o valor de outros creditos
	 * @return Double
	 */
	public function getValorOutrosCreditos()
	{
	    return $this->segmento_u->valor_outros_creditos;
	}

	/**
	 * Retorna o número do documento do boleto
	 * @return String
	 */
	public function getNumeroDocumento()
	{
        $numero_documento = $this->segmento_t->numero_documento;
        if(trim($numero_documento, '0') == '')
            return null;
        return $numero_documento;
	}

	/**
	 * Retorna o nosso número do boleto
	 * @return String
	 */
	public function getNossoNumero()
	{
		return $this->segmento_t->nosso_numero;
	}

	/**
	 * Retorna o objeto \DateTime da data de vencimento do boleto
	 * @return \DateTime
	 */
	public function getDataVencimento()
	{
		return $this->segmento_t->data_vencimento ? \DateTime::createFromFormat('dmY', sprintf('%08d', $this->segmento_t->data_vencimento)) : false;
	}

	/**
	 * Retorna a data em que o dinheiro caiu na conta
	 * @return \DateTime
	 */
	public function getDataCredito()
	{
		return $this->segmento_u->data_credito ? \DateTime::createFromFormat('dmy', sprintf('%08d', $this->segmento_u->data_credito)) : false;
	}

	/**
	 * Retorna o valor de juros e mora
	 */
	public function getValorMoraMulta()
	{
		return $this->segmento_u->valor_acrescimos;
	}

	/**
	 * Retorna a data da ocorrencia, o dia do pagamento
	 * @return \DateTime
	 */
	public function getDataOcorrencia()
	{
		return $this->segmento_u->data_ocorrencia ? \DateTime::createFromFormat('dmY', sprintf('%08d', $this->segmento_u->data_ocorrencia)) : false;
	}

	/**
	 * Retorna o número da carteira do boleto
	 * @return String
	 */
	public function getCarteira()
    {
        if($this->codigo_banco == 104)
        {
            /*
            É formado apenas o código da carteira
            Código da Carteira
            Código adotado pela FEBRABAN, para identificar a característica dos títulos dentro das modalidades de
            cobrança existentes no banco.
            ‘1’ = Cobrança Simples
            ‘3’ = Cobrança Caucionada
            ‘4’ = Cobrança Descontada
            O Código ‘1’ Cobrança Simples deve ser obrigatoriamente informado nas modalidades Cobrança Simples
            e Cobrança Rápida.
            */
            return null;
        }
        else if($this->segmento_t->existField('carteira'))
    		return $this->segmento_t->carteira;
        else
            return null;
            
	}

	/**
	 * Retorna o número da agencia do boleto
	 * @return String
	 */
	public function getAgencia()
	{
		return $this->segmento_t->agencia_mantenedora;
	}
	
	/**
	 * Retorna a agencia cobradora
	 * @return string
	 */
	public function getAgenciaCobradora()
	{
		return $this->segmento_t->agencia_cobradora;
	}
	
	/**
	 * Retorna a o dac da agencia cobradora
	 * @return string
	 */
	public function getAgenciaCobradoraDac()
	{
		return $this->segmento_t->agencia_cobradora_dac;
	}
	
	/**
	 * Retorna o numero sequencial
	 * @return Integer;
	 */
	public function getNumeroSequencial()
	{
		return $this->segmento_t->numero_sequencial_lote;
	}

	/**
	 * Retorna o nome do código
	 * @return string
	 */
	public function getCodigoNome()
	{
        $codigo = (int)$this->getCodigo();

        $table = array(
    	     2 => 'Entrada Confirmada',
             3 => 'Entrada Rejeitada',
             4 => 'Transferência de Carteira/Entrada',
             5 => 'Transferência de Carteira/Baixa',
             6 => 'Liquidação',
             9 => 'Baixa',
            12 => 'Confirmação Recebimento Instrução de Abatimento',
            13 => 'Confirmação Recebimento Instrução de Cancelamento Abatimento',
            14 => 'Confirmação Recebimento Instrução Alteração de Vencimento',
            17 => 'Liquidação Após Baixa ou Liquidação Título Não Registrado',
            19 => 'Confirmação Recebimento Instrução de Protesto',
            20 => 'Confirmação Recebimento Instrução de Sustação/Cancelamento de Protesto',
            23 => 'Remessa a Cartório (Aponte em Cartório)',
            24 => 'Retirada de Cartório e Manutenção em Carteira',
            25 => 'Protestado e Baixado (Baixa por Ter Sido Protestado)',
            26 => 'Instrução Rejeitada',
            27 => 'Confirmação do Pedido de Alteração de Outros Dados',
            28 => 'Débito de Tarifas/Custas',
            30 => 'Alteração de Dados Rejeitada',
            36 => 'Confirmação de envio de e-mail/SMS',
            37 => 'Envio de e-mail/SMS rejeitado',
            43 => 'Estorno de Protesto/Sustação',
            44 => 'Estorno de Baixa/Liquidação',
            45 => 'Alteração de dados',
            51 => 'Título DDA reconhecido pelo sacado',
            52 => 'Título DDA não reconhecido pelo sacado',
            53 => 'Título DDA recusado pela CIP',
        );

        if(array_key_exists($codigo, $table))
            return $table[$codigo];
        else
            return 'Desconhecido';
    }

    public function dump()
    {
        $dump  = PHP_EOL;
        $dump .= '== SEGMENTO T ==';
        $dump .= PHP_EOL;
        $dump .= $this->segmento_t->dump();
        $dump .= '== SEGMENTO U ==';
        $dump .= PHP_EOL;
        $dump .= $this->segmento_u->dump();

        if ($this->segmento_w)
        {
            $dump .= '== SEGMENTO W ==';
            $dump .= PHP_EOL;
            $dump .= $this->segmento_w->dump();
        }

        return $dump;
    }
}
