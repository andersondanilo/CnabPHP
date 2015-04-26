<?php
namespace Cnab\Format;

class Identifier
{
    public function __construct()
    {
    }

    public function identifyFile($filename)
    {
        if(!file_exists($filename))
            throw new \Exception('file dont exists: '.$filename);

        $contents = \file_get_contents($filename);

        $lines = \explode("\n", $contents);

        if(\count($lines) < 2)
            return null;

        $length = 0;
        foreach($lines as $line)
        {
            $length = \max($length, strlen($line));
        }

        if($length == 240 || $length == 241)
            $bytes = 240;
        else if ($length == 400 || $length == 401)
            $bytes = 400;
        else
            return null;

        $layout_versao = null;

        if($bytes == 400)
        {
            $codigo_banco = \substr($lines[0], 76, 3);
            $codigo_tipo  = \substr($lines[0],  1, 1);
            $tipo = null;

            if($codigo_tipo == '1')
                $tipo = 'remessa';
            else if ($codigo_tipo == '2')
                $tipo = 'retorno';
        }
        else if($bytes == 240)
        {
            $codigo_banco = \substr($lines[0], 0, 3);
            $codigo_tipo  = \substr($lines[0],  142, 1);
            $tipo = null;

            // Pega a Versao do Layout da CEF 
            if(\Cnab\Banco::CEF == $codigo_banco)
            {
                $layout_versao = \substr($lines[0], 163, 3);

                if($layout_versao == '040' || $layout_versao == '050')
                {
                    // Layout SIGCB
                    $layout_versao = 'sigcb';
                }
                else
                {
                    // Layout SICOB
                    $layout_versao = null;
                }
            }

            if(\strtoupper($codigo_tipo) == '1')
                $tipo = 'remessa';
            elseif (\strtoupper($codigo_tipo) == '2')
                $tipo = 'retorno';
        }
        else
            return null;

        $result = array(
            'banco' => $codigo_banco,
            'tipo'  => $tipo,
            'bytes' => $bytes,
            'layout_versao' => $layout_versao,
        );

        return $result;
    }
}
