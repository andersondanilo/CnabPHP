<?php
namespace Cnab\Retorno\Cnab240;

class Detalhe extends \Cnab\Format\Linha implements \Cnab\Retorno\IDetalhe
{
    public $codigo_banco;
    public $arquivo;

    public $segmento_t;
    public $segmento_u;
    public $segmento_w;

	public function __construct(\Cnab\Retorno\IArquivo $arquivo)
	{
		$this->codigo_banco = $arquivo->codigo_banco;
        $this->arquivo = $arquivo;
	}

	/**
	 * Procura pelo dado solicitado nos segmentos 'T', 'U' e 'W' (nesta sequencia)
	 * Retorna o valor do mesmo não tratado (para tratar dados especificos usar as outras funções criadas)
	 * O nome deve começar com 'get' e pode ser usado o nome igual encontrado com '_' ou em CamelCase
	 *
	 * Ex: Para retornar o dado de $this->segmento_u->valor_pago usar: getValorPago ou get_valor_pago
	 *
	 * @author Sandro Boçon
	 * @return mixed
	 */
	public function __call($name, $arguments)
	{
		if (substr($name, 0, 3) == 'get') {
			// Transforma CamelCase para separação com '_', se necessário
			$name = strtolower(preg_replace("/([A-Z])/", "_\\1", $name));

			// Retira o 'get_'
			$name = str_replace('get_', '', $name);

			$find = array('t', 'u', 'w');
			foreach ($find as $segmento) {
				$segmento = 'segmento_'.$segmento;

				if (method_exists($this->{$segmento}, 'existField')
					and $this->{$segmento}->existField($name)) {
					return $this->{$segmento}->{$name};
				}
			}
		}

		throw new \InvalidArgumentException("'$name' dont exists on segments 'T', 'U' or 'W'");
		
		return null;
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
	 * Retorna o valor do Imposto sobre operações financeiras
	 * @return Double
	 */
	public function getValorIOF()
	{
		return $this->segmento_u->valor_iof;
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
        $nossoNumero = $this->segmento_t->nosso_numero;

        if ($this->codigo_banco == 1) {
            $nossoNumero = preg_replace(
                '/^'.strval($this->arquivo->getCodigoConvenio()).'/',
                '',
                $nossoNumero
            );
        }

        if(in_array($this->codigo_banco, array(\Cnab\Banco::SANTANDER))) {
            // retira o dv
            $nossoNumero = substr($nossoNumero, 0, -1);
        }

        return $nossoNumero;
	}

	/**
	 * Retorna o objeto \DateTime da data de vencimento do boleto
	 * @return \DateTime
	 */
	public function getDataVencimento()
	{
		$data = $this->segmento_t->data_vencimento ? \DateTime::createFromFormat('dmY', sprintf('%08d', $this->segmento_t->data_vencimento)) : false;
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
		$data = $this->segmento_u->data_credito ? \DateTime::createFromFormat('dmY', sprintf('%08d', $this->segmento_u->data_credito)) : false;
        if($data)
            $data->setTime(0,0,0);
        return $data;
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
		$data = $this->segmento_u->data_ocorrencia ? \DateTime::createFromFormat('dmY', sprintf('%08d', $this->segmento_u->data_ocorrencia)) : false;
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

    /**
     * Retorna o código de liquidação, normalmente usado para 
     * saber onde o cliente efetuou o pagamento
     * @return String
     */
    public function getCodigoLiquidacao() {
        // @TODO: Resgatar o código de liquidação
        return null;
    }

    /**
     * Retorna a descrição do código de liquidação, normalmente usado para 
     * saber onde o cliente efetuou o pagamento
     * @return String
     */
    public function getDescricaoLiquidacao() {
        // @TODO: Resgator descrição do código de liquidação
        return null;
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

    public function isDDA()
    {
        // @TODO: implementar funçao isDDA no Cnab240
    }

    public function getAlegacaoPagador()
    {
        // @TODO: implementar funçao getAlegacaoPagador no Cnab240
    }
}
