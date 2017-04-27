<?php

namespace Cnab\Retorno\Cnab240;

use Cnab\Retorno\IHeaderArquivo;

class HeaderArquivo extends \Cnab\Format\Linha implements IHeaderArquivo
{
    public function __construct(\Cnab\Retorno\IArquivo $arquivo)
    {
        $yamlLoad = new \Cnab\Format\YamlLoad($arquivo->codigo_banco, $arquivo->layoutVersao);
        $yamlLoad->load($this, 'cnab240', 'header_arquivo');
    }

    public function getConta()
    {
        if ($this->existField('conta')) {
            return $this->conta;
        } else {
            return;
        }
    }

    public function getContaDac()
    {
        if ($this->existField('conta_dv')) {
            return $this->conta_dv;
        } else {
            return;
        }
    }

    public function getCodigoConvenio()
    {
        if ($this->existField('codigo_convenio')) {
            return $this->codigo_convenio;
        } else {
            return;
        }
    }

    public function getCodigoRetorno()
    {
        if ($this->existField('codigo_remessa_retorno')) {
            return $this->codigo_remessa_retorno;
        } else {
            return;
        }
    }


}
