<?php

namespace Cnab\Format;

use Cnab\Factory;

class YamlLoad
{
    public $codigo_banco = null;
    public $formatPath;
    public $layoutVersao;

    public function __construct($codigo_banco, $layoutVersao = null)
    {
        $this->codigo_banco = $codigo_banco;
        $this->layoutVersao = $layoutVersao;
        $this->formatPath = Factory::getCnabFormatPath();
    }

    public function validateCollision($fields)
    {
        foreach ($fields as $name => $field) {
            $pos_start = $field['pos'][0];
            $pos_end = $field['pos'][1];

            foreach ($fields as $current_name => $current_field) {
                if ($current_name === $name) {
                    continue;
                }

                $current_pos_start = $current_field['pos'][0];
                $current_pos_end = $current_field['pos'][1];

                if ($current_pos_start > $current_pos_end) {
                    throw new \DomainException("No campo $current_name a posição inicial ($current_pos_start) deve ser menor ou igual à posição final ($current_pos_end)");
                }

                if (($pos_start >= $current_pos_start && $pos_start <= $current_pos_end) ||
                     ($pos_end <= $current_pos_end && $pos_end >= $current_pos_start)) {
                    throw new \DomainException("O campo $name colide com o campo $current_name");
                }
            }

            return true;
        }
    }

    public function validateArray($array)
    {
        if (empty($array) || empty($array['generic'])) {
            throw new \Exception('arquivo yaml sem campo "generic"');
        }

        foreach ($array as $key => $fields) {
            $this->validateCollision($fields);
        }

        return true;
    }

    public function loadArray(Linha $cnabLinha, $array)
    {
        $this->validateArray($array);

        $keys = array('generic');
        if (array_key_exists(sprintf('%03d', $this->codigo_banco), $array)) {
            $keys[] = sprintf('%03d', $this->codigo_banco);
        }

        foreach ($array as $key => $fields) {
            if (in_array($key, $keys)) {
                foreach ($fields as $name => $info) {
                    $picture = $info['picture'];
                    $start = $info['pos'][0];
                    $end = $info['pos'][1];
                    $default = isset($info['default']) ? $info['default'] : false;
                    $options = $info;

                    $cnabLinha->addField($name, $start, $end, $picture, $default, $options);
                }
            }
        }
    }

    public function loadYaml($filename)
    {
        if (file_exists($filename)) {
            return spyc_load_file($filename);
        } else {
            return;
        }
    }

    public function loadFormat($cnab, $filename)
    {
        $banco = sprintf('%03d', $this->codigo_banco);
        $filenamePadrao = $this->formatPath.'/'.$cnab.'/generic/'.$filename.'.yml';
        $filenameEspecifico = $this->formatPath.'/'.$cnab.'/'.$banco.'/'.$filename.'.yml';

        if ($this->layoutVersao != null && $this->codigo_banco == 104) {
            // Usado quando o banco possuir mais de uma versao de Layout
            $filenameEspecifico = $this->formatPath.'/'.$cnab.'/'.$banco.'/'.$this->layoutVersao.'/'.$filename.'.yml';
        }

        if (!file_exists($filenamePadrao) && !file_exists($filenameEspecifico)) {
            throw new \Exception('Arquivo não encontrado '.$filename);
        }

        $arrayPadrao = $this->loadYaml($filenamePadrao);
        $arrayEspecifico = $this->loadYaml($filenameEspecifico);

        $arrayFormat = array();

        if ($arrayPadrao) {
            $arrayFormat['generic'] = $arrayPadrao;
        }

        if ($arrayEspecifico) {
            $arrayFormat[$banco] = $arrayEspecifico;
        }

        return $arrayFormat;
    }

    public function load(Linha $cnabLinha, $cnab, $filename)
    {
        $arrayFormat = $this->loadFormat($cnab, $filename);
        $this->loadArray($cnabLinha, $arrayFormat);
    }
}
