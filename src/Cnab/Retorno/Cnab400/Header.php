<?php
namespace Cnab\Retorno\Cnab400;

class Header extends \Cnab\Format\Linha
{
    private $_codigo_banco = null;
    
	public function __construct($codigo_banco)
	{
	    $this->_codigo_banco = $codigo_banco;
        $yamlLoad = new \Cnab\Format\YamlLoad($codigo_banco);
        $yamlLoad->load($this, CNAB_FORMAT_PATH.'/cnab400/retorno/header_arquivo.yml');
        
	}

	public function getConta()
	{
	    if($this->existField('conta'))
		    return $this->conta;
        else if ($this->_codigo_banco == 104)
        {
            $codigo_cedente = sprintf('%016d', $this->codigo_cedente);
            return substr($codigo_cedente, 7, 8);
        }
	}

	public function getContaDac()
	{
	    if($this->existField('dac'))
            return $this->dac;
        else if ($this->_codigo_banco == 104)
        {
            $codigo_cedente = sprintf('%016d', $this->codigo_cedente);
            return substr($codigo_cedente, 15, 1);
        }
	}
    
    public function getCodigoCedente()
    {
        if ($this->existField('codigo_cedente'))
            return $this->codigo_cedente;
    }
}