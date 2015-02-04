<?php
namespace Cnab\Retorno;

if(!defined('CNAB_FORMAT_PATH'))
    define('CNAB_FORMAT_PATH', dirname(__FILE__).'/../../../data/cnab_yaml');

class CodigoOcorrencia
{
    /**
     * Por enquanto só foi implementado no Cnab400
     */
    public function getNome($codigo_banco, $codigo_ocorrencia, $format='cnab400')
    {
        $format             = strtolower($format);
        $codigo_banco       = (int)$codigo_banco;
        $codigo_ocorrencia  = (int)$codigo_ocorrencia;
        $yamlLoad           = new \Cnab\Format\YamlLoad($codigo_banco);
        $array              = $yamlLoad->loadFormat($format, 'retorno/codigo_ocorrencia');

        if(array_key_exists($codigo_banco, $array) && array_key_exists($codigo_ocorrencia, $array[$codigo_banco]))
        {
            return $array[$codigo_banco][$codigo_ocorrencia];
        }
    }
}
