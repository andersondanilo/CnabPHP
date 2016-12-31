<?php

namespace Cnab\Retorno\Cnab240;

class Lote
{
    public $header;
    public $trailer;

    public $arquivo;
    public $codigo_banco;

    public $detalhes = array();

    private $lastDetalhe;

    public function __construct(\Cnab\Retorno\IArquivo $arquivo)
    {
        $this->arquivo = $arquivo;
        $this->codigo_banco = $this->arquivo->codigo_banco;
    }

    public function insertSegmento($linha)
    {
        $codigo_segmento = strtoupper(substr($linha, 13, 1));
        $segmento = null;
        if ('T' == $codigo_segmento) {
            $segmento = new SegmentoT($this->arquivo);
            $segmento->loadFromString($linha);
            $this->lastDetalhe = new Detalhe($this->arquivo);
            $this->detalhes[] = $this->lastDetalhe;
            $this->lastDetalhe->segmento_t = $segmento;
        } elseif ('U' == $codigo_segmento) {
            $segmento = new SegmentoU($this->arquivo);
            $segmento->loadFromString($linha);
            if ($this->lastDetalhe) {
                $this->lastDetalhe->segmento_u = $segmento;
            }
        } elseif ('W' == $codigo_segmento) {
            $segmento = new SegmentoW($this->arquivo);
            $segmento->loadFromString($linha);
            if ($this->lastDetalhe) {
                $this->lastDetalhe->segmento_w = $segmento;
            }
        }

        return $segmento;
    }

    public function listDetalhes()
    {
        return $this->detalhes;
    }
}
