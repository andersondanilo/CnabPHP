<?php

namespace Cnab\Retorno;

class CodigoOcorrencia
{
    /**
     * Por enquanto só foi implementado no Cnab400.
     */
    public function getNome($codigo_banco, $codigo_ocorrencia, $format = 'cnab400')
    {
        $format = strtolower($format);
        $codigo_banco = (int) $codigo_banco;
        $codigo_ocorrencia = (int) $codigo_ocorrencia;
        $yamlLoad = new \Cnab\Format\YamlLoad($codigo_banco);
        $array = $yamlLoad->loadFormat($format, 'retorno/codigo_ocorrencia');

        $codigo_banco = str_pad($codigo_banco, 3, '0', STR_PAD_LEFT);

        if (array_key_exists($codigo_banco, $array) && array_key_exists($codigo_ocorrencia, $array[$codigo_banco])) {
            return $array[$codigo_banco][$codigo_ocorrencia];
        }
    }
}
