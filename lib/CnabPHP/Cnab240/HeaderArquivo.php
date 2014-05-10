<?php
namespace Cnab\Cnab240;

class HeaderArquivo extends \Cnab\Format\Linha
{
	public function __construct($codigo_banco)
    {
        $yamlLoad = new \Cnab\Format\YamlLoad($codigo_banco);
        $yamlLoad->load($this, CNAB_FORMAT_PATH.'/cnab240/header_arquivo.yml');
	}

	public function getConta()
    {
        if($this->existField('conta'))
            return $this->conta;
        else
            return null;
    }

    public function getContaDac()
    {
        if($this->existField('conta_dv'))
            return $this->conta_dv;
        else
            return null;
    }
}
