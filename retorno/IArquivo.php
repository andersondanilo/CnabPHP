<?php

interface Cnab_Retorno_IArquivo
{	
	public function __construct($filename);	
	
	/**
	 * Retorna todos os detatles
	 * @return iRetornoDetalhe[]
	 */
	public function listDetalhes();
	
	/**
	 * Retorna o numero da conta
	 * @return String
	 */
	public function getConta();
	
	/**
	 * Retorna o digito de auto conferencia da conta
	 * @return String
	 */
	public function getContaDac();
	
	/**
	 * Retorna o codigo do banco
	 * @return String
	 */
	public function getCodigoBanco();
	
	/**
	 * Retorna a data de geração do arquivo
	 * @return DateTime
	 */
	public function getDataGeracao();
}