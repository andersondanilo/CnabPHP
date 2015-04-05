<?php
namespace Cnab\Retorno;

interface IDetalhe
{
	/**
	 * Identifica o tipo de detalhe, se por exemplo uma taxa de manutenção
	 * @return Integer
	 */
	public function getCodigo();
	
	/**
	 * Retorna o valor recebido em conta
	 * @return Double
	 */
	public function getValorRecebido();
	
	/**
	 * Retorna o valor da tarifa
	 * @return Double
	 */
	public function getValorTitulo();
	
	/**
	 * Retorna o valor da tarifa
	 * @return Double
	 */
	public function getValorTarifa();

	/**
	 * Retorna o valor do Imposto sobre operações financeiras
	 * @return Double
	 */
	public function getValorIOF();
	
	/**
	 * Retorna o valor dos descontos concedido (antes da emissão)
	 * @return Double;
	 */
	public function getValorDesconto();
	
	/**
	 * Retornna o valor dos abatimentos concedidos (depois da emissão)
	 * @return Double
	 */
	public function getValorAbatimento();
	
	/**
	 * Retorna o valor de outros creditos 
	 * @return Double
	 */
	public function getValorOutrosCreditos();
	
	/**
	 * Retorna o valor de juros e mora
	 * @return Double
	 */
	public function getValorMoraMulta();
	
	/**
	 * Retorna o número do documento do boleto
	 * @return String
	 */
	public function getNumeroDocumento();
	
	/**
	 * Retorna o número da carteira do boleto
	 * @return String
	 */
	public function getCarteira();
	
	/**
	 * Retorna o número da agencia
	 * @return String
	 */
	public function getAgencia();
	
	/** 
	 * Retorna o nosso número do boleto (sem o digito)
	 * @return String
	 */
	public function getNossoNumero();
	
	/**
	 * Retorna o objeto DateTime da data de vencimento do boleto
	 * @return DateTime
	 */
	public function getDataVencimento();
	
	/**
	 * Retorna a data em que o dinheiro caiu na conta
	 * @return DateTime
	 */
	public function getDataCredito();
	
	/**
	 * Retorna a data da ocorrencia, o dia do pagamento
	 * @return DateTime
	 */
	public function getDataOcorrencia();
	
	/**
	 * Retorna a agencia cobradora
	 * @return string
	 */
	public function getAgenciaCobradora();
	
	/**
	 * Retorna a o dac da agencia cobradora
	 * @return string
	 */
	public function getAgenciaCobradoraDac();
	
	/**
	 * Retorna o numero sequencial
	 * @return Integer
	 */
	public function getNumeroSequencial();
	
	/**
	 * Retorna o nome do código
	 * @return string
	 */
	public function getCodigoNome();
	
	/**
	 * Retorno se é para dar baixa no boleto
	 * @return Boolean
	 */
	public function isBaixa();
	

	/**
	 * Retorno se é uma baixa rejeitada
	 * @return Boolean
	 */
	public function isBaixaRejeitada();

	/**
	 * Retorna o código de liquidação, normalmente usado para 
	 * saber onde o cliente efetuou o pagamento
	 * @return String
	 */
	public function getCodigoLiquidacao();

	/**
	 * Retorna a descrição do código de liquidação, normalmente usado para 
	 * saber onde o cliente efetuou o pagamento
	 * @return String
	 */
	public function getDescricaoLiquidacao();

	/**
	 * Retorna de o boleto foi pago através do Débito Direto Autorizado
	 * @return bool
	 */
	public function isDDA();

	/**
	 * Retorna a alegação do pagador (para erros)
	 */
	public function getAlegacaoPagador();
}