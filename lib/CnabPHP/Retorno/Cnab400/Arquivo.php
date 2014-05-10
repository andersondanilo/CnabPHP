<?php
namespace Cnab\Retorno\Cnab400; 

class Arquivo implements \Cnab\Retorno\IArquivo
{
	private $content;
	
	public $header   = false;
	public $detalhes = array();
	public $trailer  = false;

	public $codigo_banco;

	private $filename;
	
	public function __construct($codigo_banco, $filename)
	{
		$this->filename = $filename;

		if(!file_exists($this->filename))
			throw new Exception("Arquivo não encontrado: {$this->filename}");

		$this->content = file_get_contents($this->filename); 
		
		$this->codigo_banco = (int)$codigo_banco;

		$linhas = explode("\r\n", $this->content);
		if(count($linhas) < 2)
			$linhas = explode("\n", $this->content);
		$this->header  = new Header($this->codigo_banco);
		$this->trailer = new Trailer($this->codigo_banco);
		
		foreach($linhas as $linha)
		{
			$tipo_registro = substr($linha, 0, 1);
			if($tipo_registro == '0' && $linha)
			{
				$this->header->loadFromString($linha);
			}
			else if($tipo_registro == '1')
			{
				$detalhe = new Detalhe($this->codigo_banco);
				$detalhe->loadFromString($linha);
				$this->detalhes[] = $detalhe;
			}
			else if($tipo_registro == '9')
				$this->trailer->loadFromString($linha);
		}
	}
	
	public function listDetalhes()
	{
		return $this->detalhes;
	}
	
	/**
	 * Retorna o numero da conta
	 * @return String
	 */
	public function getConta()
	{
		return $this->header->getConta();
	}
	
	/**
	 * Retorna o digito de auto conferencia da conta
	 * @return String
	 */
	public function getContaDac()
	{
		return $this->header->getContaDac();
	}
	
	/**
	 * Retorna o codigo do banco
	 * @return String
	 */
	public function getCodigoBanco()
	{
		return $this->header->codigo_do_banco;
	}
	
	/**
	 * Retorna a data de geração do arquivo
	 * @return \DateTime
	 */
	public function getDataGeracao()
	{
		return $this->header->data_de_geracao ? \DateTime::createFromFormat('dmy', sprintf('%06d', $this->header->data_de_geracao)) : false;
	}
    
    /**
     * Retorna o objeto DateTime da data crédito do arquivo
     * É melhor consultar no Detalhe a data de crédito, a caixa só informa no detalhe
     * (Esta função poderá ser removida, pois em alguns banco você só encontra esta data no detalhe)
     * @return DateTime
     */
    public function getDataCredito()
    {
        if($this->header->existField('data_de_credito'))
            return $this->header->data_de_credito ? \DateTime::createFromFormat('dmy', sprintf('%06d', $this->header->data_de_credito)) : false;
        else
            return null;
    }
}
