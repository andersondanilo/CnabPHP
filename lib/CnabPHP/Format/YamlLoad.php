<?php
namespace Cnab\Format;

#require_once dirname(__FILE__).'/../../../vendors/spyc/Spyc.php';

define('CNAB_FORMAT_PATH', dirname(__FILE__).'/../../../vendors/cnab_yaml');

class YamlLoad
{
    public $codigo_banco = null;

    public function __construct($codigo_banco)
    {
        $this->codigo_banco = $codigo_banco;
    }

    public function load(Linha $cnabLinha, $filename)
    {
        if(!file_exists($filename))
            throw new \Exception('Arquivo não encontrado '.$filename);

        $array = spyc_load_file($filename);

        if(empty($array) || empty($array['generic']))
            throw new Exception('arquivo yaml inválido');

        $keys = array('generic');
        if(array_key_exists(sprintf('%03d', $this->codigo_banco), $array))
            $keys[] = sprintf('%03d', $this->codigo_banco);

        foreach($array as $key => $fields)
        {
            if(in_array($key, $keys))
            {
                foreach($fields as $name => $info)
                {
                    $picture = $info['picture'];
                    $start   = $info['pos'][0];
                    $end     = $info['pos'][1];
                    $default = isset($info['default']) ? $info['default'] : false;

                    $cnabLinha->addField($name, $start, $end, $picture, $default);
                }
            }
        }
    }
}
