<?php
namespace Cnab\Retorno\Cnab240;
use Cnab\Factory;

class Arquivo implements \Cnab\Retorno\IArquivo
{
	private $content;

	public $header   = false;
	public $lotes    = array();
	public $trailer  = false;

	public $codigo_banco;
    public $layoutVersao;

	private $filename;

	public function __construct($codigo_banco, $filename, $layoutVersao=null)
	{
		$this->filename = $filename;
        $this->layoutVersao = $layoutVersao;

		if(!file_exists($this->filename))
			throw new \Exception("Arquivo não encontrado: {$this->filename}");

		$this->content = file_get_contents($this->filename);

		$this->codigo_banco = (int)$codigo_banco;

		$linhas = explode("\r\n", $this->content);
		if(count($linhas) < 2)
			$linhas = explode("\n", $this->content);
		$this->header  = new HeaderArquivo($this);
        $this->trailer = new TrailerArquivo($this);

        $lastLote = null;

		foreach($linhas as $linha)
        {
            if(!$linha)
                continue;
            $tipo_registro = substr($linha, 7, 1);
			if($tipo_registro == '0')
            {
                // header
                $this->header->loadFromString($linha);
			}
			else if($tipo_registro == '1')
            {
                // header do lote
                if($lastLote)
                    $this->lotes[] = $lastLote;
                $lastLote = new Lote($this);
                $lastLote->header = new HeaderLote($this);
                $lastLote->header->loadFromString($linha);
			}
            else if($tipo_registro == '2')
            {
                // registros iniciais do lote (opcional)
            }
            else if($tipo_registro == '3')
            {
                // registros de detalhe - Segmentos
                if($lastLote)
                    $lastLote->insertSegmento($linha);
            }
            else if($tipo_registro == '4')
            {
                // registros finais do lote (opcional)
            }
            else if($tipo_registro == '5')
            {
                // registro trailer do lote
                $lastLote->trailer = new TrailerLote($this);
                $lastLote->trailer->loadFromString($linha);
                $this->lotes[] = $lastLote;
                $lastLote = null;
            }
            else if($tipo_registro == '9')
            {
                // trailer do arquivo
                $this->trailer->loadFromString($linha);
            }
		}
	}

	public function listDetalhes()
    {
        $detalhes = array();
	    foreach($this->lotes as $lote)
        {
            foreach($lote->listDetalhes() as $detalhe)
            {
                $detalhes[] = $detalhe;
            }
        }
        return $detalhes;
	}

	/**
	 * Retorna o numero da conta
	 * @return String
	 */
	public function getConta()
    {
		return $this->header->getConta();
	}

	/**
	 * Retorna o digito de auto conferencia da conta
	 * @return String
	 */
	public function getContaDac()
    {
		return $this->header->getContaDac();
	}

	/**
	 * Retorna o codigo do banco
	 * @return String
	 */
	public function getCodigoBanco()
	{
		return $this->header->codigo_banco;
	}

	/**
	 * Retorna a data de geração do arquivo
	 * @return \DateTime
	 */
	public function getDataGeracao()
    {
        $data_geracao_str = $this->header->data_geracao;
        $format = strlen($data_geracao_str) > 6 ? 'dmY' : 'dmy';
        $format_printf = strlen($data_geracao_str) > 6 ? '%08d' : '%06d';
		return $data_geracao_str ? \DateTime::createFromFormat($format, sprintf($format_printf, $data_geracao_str)) : false;
	}
    
    /**
     * Retorna o objeto DateTime da data crédito do arquivo
     * É melhor consultar no Detalhe a data de crédito, a caixa só informa no detalhe
     * (Esta função poderá ser removida, pois em alguns banco você só encontra esta data no detalhe)
     * @return DateTime
     */
    public function getDataCredito()
    {
        $lote = $this->lotes[0];
        $header_lote = $lote->header;
        return $header_lote->data_credito ? \DateTime::createFromFormat('dmY', sprintf('%08d', $header_lote->data_credito)) : false;
    }

    public function getCodigoConvenio() {
        return $this->header->getCodigoConvenio();
    }
}
