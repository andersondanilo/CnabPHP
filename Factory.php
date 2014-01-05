<?php

require_once dirname(__FILE__).'/Banco.php';
require_once dirname(__FILE__).'/Especie.php';

class Cnab_Factory
{
	/**
	 * Cria um arquivo de remessa
	 * @return IRemessaArquivo
	 */
	public function createRemessa($codigo_banco)
	{
		if(empty($codigo_banco))
			throw new InvalidArgumentException('$codigo_banco cannot be empty');
		// por enquanto só suporta o Cnab400
		require_once dirname(__FILE__).'/remessa/cnab400/Arquivo.php';
		return new Cnab_Remessa_Cnab400_Arquivo($codigo_banco);
	}

	/**
	 * Cria um arquivo de retorno
	 * @param  string $filename
	 * @return IRetornoArquivo
	 */
	public function createRetorno($filename)
	{
		if(empty($filename))
			throw new InvalidArgumentException('$filename cannot be empty');
		// por enquanto só suporta o Cnab400
		require_once dirname(__FILE__).'/retorno/cnab400/Arquivo.php';
		return new Cnab_Retorno_Cnab400_Arquivo($filename);
	}
}