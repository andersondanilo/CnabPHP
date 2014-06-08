<?php
namespace Cnab\Format;

define('CNAB_FORMAT_PATH', dirname(__FILE__).'/../../../data/cnab_yaml');

class YamlLoad
{
    public $codigo_banco = null;

    public function __construct($codigo_banco)
    {
        $this->codigo_banco = $codigo_banco;
    }

    public function validateCollision($fields)
    {
        foreach ($fields as $name => $field)
        {
            $pos_start = $field['pos'][0];
            $pos_end = $field['pos'][1];

            foreach ($fields as $current_name => $current_field)
            {
                if ($current_name === $name)
                    continue;

                $current_pos_start = $current_field['pos'][0];
                $current_pos_end = $current_field['pos'][1];

                if ($current_pos_start > $current_pos_end)
                {
                    throw new \DomainException("No campo $current_name a posição inicial deve ser menor ou igual à posição final");
                }

                if ( ($pos_start >= $current_pos_start && $pos_start <= $current_pos_end) ||
                     ($pos_end <= $current_pos_end && $pos_end >= $current_pos_start))
                {
                    throw new \DomainException("O campo $name colide com o campo $current_name");
                }
            }

            return true;
        }
    }

    public function validateArray($array)
    {
        if (empty($array) || empty($array['generic']))
            throw new \Exception('arquivo yaml sem campo "generic"');

        foreach ($array as $key => $fields)
        {
            $this->validateCollision($fields);
        }

        return true;
    }

    public function load(Linha $cnabLinha, $filename)
    {
        if(!file_exists($filename))
            throw new \Exception('Arquivo não encontrado '.$filename);

        $array = spyc_load_file($filename);

        $this->validateArray($array);

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
