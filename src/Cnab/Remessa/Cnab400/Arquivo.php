<?php

namespace Cnab\Remessa\Cnab400;

class Arquivo implements \Cnab\Remessa\IArquivo
{
    public $header;
    public $trailer;
    public $detalhes = array();
    private $_data_gravacao;
    private $_data_geracao;
    public $banco;
    public $codigo_banco;
    public $configuracao = array();
    public $layoutVersao;
    const   QUEBRA_LINHA = "\r\n";

    public function __construct($codigo_banco, $layoutVersao = null)
    {
        $this->codigo_banco = $codigo_banco;
        $this->layoutVersao = $layoutVersao;
        $this->banco = \Cnab\Banco::getBanco($this->codigo_banco);
        //$this->data_gravacao = date('dmY');
    }

    public function configure(array $params)
    {
        $campos = array(
            'data_geracao', 'data_gravacao', 'nome_fantasia', 'razao_social', 'cnpj', 'logradouro', 'numero', 'bairro',
            'cidade', 'uf', 'cep', 'agencia'
        );

        switch($this->codigo_banco) {
        	case \Cnab\Banco::CEF:
        		$campos[] = 'conta';
        		$campos[] = 'codigo_cedente';
                break;
        	case \Cnab\Banco::BRADESCO:
        		$campos[] = 'agencia';
        		$campos[] = 'conta';
                $campos[] = 'conta_dac';
        		$campos[] = 'codigo_cedente';
        		$campos[] = 'sequencial_remessa';
        		break;
        	case \Cnab\Banco::SANTANDER:
        		$campos[] = 'complemento_conta';
        		$campos[] = 'conta_movimento';
        		$campos[] = 'conta_cobranca';
        		$campos[] = 'codigo_transmissao';
        		break;
        	default:
        		$campos[] = 'conta';
        		$campos[] = 'conta_dac';
        		break;
        }
        
        foreach ($campos as $campo) {
            if (array_key_exists($campo, $params)) {
                if (strpos($campo, 'data_') === 0 && !($params[$campo] instanceof \DateTime)) {
                    throw new \Exception("config '$campo' need to be instance of DateTime");
                }
                $this->configuracao[$campo] = $params[$campo];
            } else {
                throw new \Exception('Configuração "'.$campo.'" need to be set');
            }
        }
        
        foreach ($campos as $key) {
            if (!array_key_exists($key, $params)) {
                throw new Exception('Configuração "'.$key.'" dont exists');
            }
        }
        
        $this->data_geracao = $this->configuracao['data_geracao'];
        $this->data_gravacao = $this->configuracao['data_gravacao'];

        $this->header = new Header($this);
        
        $this->header->codigo_banco = $this->banco['codigo_do_banco'];
        
        switch ($this->codigo_banco) {
            case \Cnab\Banco::CEF:
                $this->header->nome_banco = 'C ECON FEDERAL';
        		$this->header->agencia = $this->configuracao['agencia'];
        		$this->header->codigo_cedente = $this->configuracao['codigo_cedente'];
                break;
        	case \Cnab\Banco::BRADESCO:
        		// $this->header->agencia = $this->configuracao['agencia'];
        		$this->header->codigo_cedente = $this->configuracao['codigo_cedente'];
        		$this->header->sequencial_remessa = $this->configuracao['sequencial_remessa'];
                $this->header->razao_social = $this->configuracao['nome_fantasia'];
        		break;
        	case \Cnab\Banco::SANTANDER:
        		$this->header->codigo_transmissao = $this->configuracao['codigo_transmissao'];
        		$this->configuracao['valor_total'] = 0;
        		$this->configuracao['qtd_documentos'] = 1; // Header
        		break;
        	default:
        		$this->header->agencia = $this->configuracao['agencia'];
        		$this->header->conta = $this->configuracao['conta'];
        		$this->header->conta_dv = $this->configuracao['conta_dac'];
        		break;
        }
        
        if ($this->header->codigo_banco == \Cnab\Banco::ITAU)
        {
            $this->header->nome_banco = 'BANCO ITAU SA';
        }

        if ($this->codigo_banco != \Cnab\Banco::BRADESCO)
        {
            $this->header->nome_empresa = $this->configuracao['nome_fantasia'];
        }
        $this->header->data_geracao = $this->configuracao['data_geracao']->format('dmy');
    }

    public function insertDetalhe(array $boleto, $tipo = 'remessa')
    {
        $dateVencimento = $boleto['data_vencimento'] instanceof \DateTime ? $boleto['data_vencimento'] : new \DateTime($boleto['data_vencimento']);
        $dateCadastro = $boleto['data_cadastro']   instanceof \DateTime ? $boleto['data_cadastro']   : new \DateTime($boleto['data_cadastro']);

        $detalhe = new Detalhe($this);
        $complementos = array();

        if ($tipo == 'remessa') {
            $detalhe->codigo_ocorrencia = !empty($boleto['codigo_ocorrencia']) ? $boleto['codigo_ocorrencia'] : '1';

            if ($this->codigo_banco != \Cnab\Banco::BRADESCO)
            {
                $detalhe->codigo_inscricao = 2;
                $detalhe->numero_inscricao = $this->prepareText($this->configuracao['cnpj'], '.-/');
            }

            switch ($this->codigo_banco) {
            	case \Cnab\Banco::CEF:
                    $detalhe->agencia = $this->header->agencia;
                    $detalhe->codigo_cedente = $this->header->codigo_cedente;
            		$detalhe->data_multa = $boleto['data_multa'];
            		$detalhe->valor_multa = $boleto['valor_multa'];
            		break;
            	case \Cnab\Banco::BRADESCO:
            		// $detalhe->codigo_cedente = $this->header->codigo_cedente;
                    $detalhe->agencia = $this->configuracao['agencia'];
                    $detalhe->conta = $this->configuracao['conta'];
                    $detalhe->conta_dv = $this->configuracao['conta_dac'];
            		$detalhe->digito_nosso_numero = $boleto['digito_nosso_numero'];
                    $detalhe->valor_multa = $boleto['juros_de_um_dia'];

                    if($boleto['valor_multa'] > 0)
                    {
                        $detalhe->multa = 2;
                    }

            		break;
            	case \Cnab\Banco::SANTANDER:
            		$detalhe->codigo_transmissao = $this->configuracao['codigo_transmissao'];
                    $detalhe->complemento_conta = $this->configuracao['complemento_conta'];
            		$detalhe->data_seg_desconto = $boleto['data_seg_desconto'];

                    if($detalhe->data_seg_desconto instanceof \DateTime)
                    {
                        $detalhe->data_seg_desconto = $boleto['data_seg_desconto']->format('dmy');
                    }

            		$detalhe->data_multa = $boleto['data_multa'];

                    if($detalhe->data_multa instanceof \DateTime)
                    {
                        $detalhe->data_multa = $boleto['data_multa']->format('dmy');
                    }

                    if($boleto['pct_multa_atraso']==0
                    && $boleto['data_seg_desconto']=='000000')
                    {
                        $detalhe->info_multa = 0;
                    }

            		$detalhe->pct_multa_atraso = $boleto['pct_multa_atraso'];
            		$this->configuracao['valor_total'] += $boleto['valor'];
            		$this->configuracao['qtd_documentos']++;
            		break;
            	default:
            		$detalhe->agencia = $this->header->agencia;
            		$detalhe->conta = $this->header->conta;
            		$detalhe->conta_dv = $this->header->conta_dv;
            		$detalhe->codigo_instrucao = '0';
            		$detalhe->qtde_moeda = '0'; # Este campo deverá ser preenchido com zeros caso a moeda seja o Real.
            		$detalhe->codigo_carteira = 'I';
            		$detalhe->uso_banco = '';
            		$detalhe->data_mora = $boleto['data_multa'];
            		
            		if ($boleto['valor_multa'] > 0) {
            			/*
            			 // Não está presente na documentação disponibilizada no site
            			// os valores de multa devem ser configurados com o gerente da sua conta
            			$detalheMulta = new DetalheMulta($this);
            			if(@$boleto['tipo_multa'] == 'porcentagem')
            				$detalheMulta->codigo_multa = 2;
            			else if(!@$boleto['tipo_multa'] || $boleto['tipo_multa'] == 'valor')
            				$detalheMulta->codigo_multa = 1;
            			else
            				throw new Exception('tipo de multa inválido, deve ser "porcentagem" ou "valor"');
            			$detalheMulta->data_multa = $boleto['data_multa'];
            			$detalheMulta->valor_multa = $boleto['valor_multa'];
            			$complementos[] = $detalheMulta;
            			*/
            		}
            		break;
            }
            
            /*
               Deve ser preenchido na remessa somente quando utilizados, na posição 109-110, os códigos de
               ocorrência 35 – Cancelamento de Instrução e 38 – Cedente não concorda com alegação do sacado. Para
               os demais códigos de ocorrência este campo deverá ser preenchido com zeros.
            */
            $detalhe->uso_empresa = isset($boleto['uso_empresa']) 
                                  ? $boleto['uso_empresa'] 
                                  : $boleto['nosso_numero'];
            $detalhe->nosso_numero = $boleto['nosso_numero'];

            $detalhe->numero_carteira = $boleto['carteira'];
            $detalhe->numero_documento = $boleto['numero_documento'];


            if ($this->codigo_banco==\Cnab\Banco::CEF)
            {
                $detalhe->uso_empresa = str_pad($detalhe->uso_empresa, 25, 0, STR_PAD_LEFT);
                $detalhe->numero_documento = str_pad($detalhe->numero_documento, 10, 0, STR_PAD_LEFT);
            }


            $detalhe->vencimento = $dateVencimento->format('dmy');
            $detalhe->valor_titulo = $boleto['valor'];
            $detalhe->aceite = empty($boleto['aceite']) ? 'N' : $boleto['aceite'];
            $detalhe->instrucao1 = $boleto['instrucao1'];
            $detalhe->instrucao2 = $boleto['instrucao2'];
            $detalhe->especie = $boleto['especie'];
            $detalhe->data_emissao = $dateCadastro->format('dmy');

            $sacado_tipo = @$boleto['sacado_tipo'] or $sacado_tipo = 'cpf';

            if ($sacado_tipo == 'cnpj') {
                $detalhe->sacado_codigo_inscricao = '2';
                /*
                 * @todo Trocar espécie
                 */
                $detalhe->sacado_numero_inscricao = $this->prepareText($boleto['sacado_cnpj'], '.-/');
                $detalhe->nome = $this->prepareText($boleto['sacado_razao_social']);
            } else {
                $detalhe->sacado_codigo_inscricao = '1';
                /*
                 * @todo Trocar espécie
                 */
                $detalhe->sacado_numero_inscricao = $this->prepareText($boleto['sacado_cpf'], '.-/');
                $detalhe->nome = $this->prepareText($boleto['sacado_nome']);
            }
            $detalhe->logradouro = $this->prepareText($boleto['sacado_logradouro']);
            $detalhe->cep = str_replace('-', '', $boleto['sacado_cep']);

            if ($this->codigo_banco != \Cnab\Banco::BRADESCO)
            {
                $detalhe->bairro = $this->prepareText($boleto['sacado_bairro']);
                $detalhe->cidade = $this->prepareText($boleto['sacado_cidade']);
                $detalhe->estado = $boleto['sacado_uf'];
            }

            if ($sacado_tipo == 'cpf')
            {
                $detalhe->sacador = '';
            }

            $detalhe->juros_um_dia = $boleto['juros_de_um_dia'];
            $detalhe->desconto_ate = $boleto['data_desconto'];

            if($detalhe->desconto_ate instanceof \DateTime)
            {
                $detalhe->desconto_ate = $boleto['data_desconto']->format('dmy');
            }

            $detalhe->valor_desconto = $boleto['valor_desconto'];
            if ($this->codigo_banco != \Cnab\Banco::BRADESCO)
            {
                $detalhe->prazo = $boleto['prazo'];
            }
        } elseif ($tipo == 'baixa') {
            $detalhe->codigo_inscricao = '0';
            $detalhe->numero_inscricao = '0';
            $detalhe->agencia = $this->header->agencia;
            $detalhe->conta = $this->header->conta;
            $detalhe->conta_dac = $this->header->dac;
            $detalhe->codigo_instrucao = '0';
            /*
               Deve ser preenchido na remessa somente quando utilizados, na posição 109-110, os códigos de
               ocorrência 35 – Cancelamento de Instrução e 38 – Cedente não concorda com alegação do sacado. Para
               os demais códigos de ocorrência este campo deverá ser preenchido com zeros.
            */
            $detalhe->codigo_ocorrencia = $boleto['codigo_de_ocorrencia'];
            $detalhe->uso_empresa = $boleto['nosso_numero'];
            $detalhe->nosso_numero = $boleto['nosso_numero'];
            $detalhe->qtde_moeda = '0'; # Este campo deverá ser preenchido com zeros caso a moeda seja o Real.
            $detalhe->numero_carteira = $boleto['carteira'];
            $detalhe->codigo_carteira = 'I';
            $detalhe->uso_banco = '';
            $detalhe->numero_documento = $boleto['numero_documento'];
            $detalhe->vencimento = '0';
            $detalhe->valor_titulo = $boleto['valor'];
            $detalhe->aceite = ' ';
            $detalhe->data_emissao = '0';
            $detalhe->sacado_codigo_inscricao = '2';
            $detalhe->especie = ' ';
            $detalhe->sacado_numero_inscricao = '0';
            $detalhe->juros_um_dia = $boleto['juros_de_um_dia'];
            $detalhe->data_juros = $boleto['data_juros'];

            $detalhe->nome = ' ';
            $detalhe->logradouro = ' ';
            $detalhe->bairro = ' ';
            $detalhe->cep = '0';
            $detalhe->cidade = ' ';
            $detalhe->estado = ' ';
            $detalhe->sacador = ' ';
        } else {
            throw new Exception('Tipo de $detalhe desconhecido');
        }

        $detalhe->codigo_banco = $this->banco['codigo_do_banco'];

        $this->detalhes[] = $detalhe;

        foreach ($complementos as $complemento) {
            $this->detalhes[] = $complemento;
        }
    }

    public function listDetalhes()
    {
        return $this->detalhes;
    }

    private function prepareText($text, $remove = null)
    {
        $result = strtoupper($this->removeAccents(trim(html_entity_decode($text))));
        if ($remove) {
            $result = str_replace(str_split($remove), '', $result);
        }

        return $result;
    }

    private function removeAccents($string)
    {
        return preg_replace(
            array(
                    '/\xc3[\x80-\x85]/',
                    '/\xc3\x87/',
                    '/\xc3[\x88-\x8b]/',
                    '/\xc3[\x8c-\x8f]/',
                    '/\xc3([\x92-\x96]|\x98)/',
                    '/\xc3[\x99-\x9c]/',

                    '/\xc3[\xa0-\xa5]/',
                    '/\xc3\xa7/',
                    '/\xc3[\xa8-\xab]/',
                    '/\xc3[\xac-\xaf]/',
                    '/\xc3([\xb2-\xb6]|\xb8)/',
                    '/\xc3[\xb9-\xbc]/',
            ),
            str_split('ACEIOUaceiou', 1),
            $this->isUtf8($string) ? $string : utf8_encode($string)
        );
    }

    private function isUtf8($string)
    {
        return preg_match('%^(?:
                 [\x09\x0A\x0D\x20-\x7E]
                | [\xC2-\xDF][\x80-\xBF]
                | \xE0[\xA0-\xBF][\x80-\xBF]
                | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}
                | \xED[\x80-\x9F][\x80-\xBF]
                | \xF0[\x90-\xBF][\x80-\xBF]{2}
                | [\xF1-\xF3][\x80-\xBF]{3}
                | \xF4[\x80-\x8F][\x80-\xBF]{2}
                )*$%xs',
                $string
        );
    }

    public function __set($name, $value)
    {
        if (strpos($name, 'data_') === 0) {
            if ($value instanceof \DateTime) {
                $property = "_$name";
                $this->$property = $value;
            } else {
                throw new InvalidArgumentException("$nome need to be instance of DateTime");
            }
        } else {
            throw new Exception("property '$name' dont exists");
        }
    }

    public function getText()
    {
        $numero_sequencial = 1;

        $this->header->numero_sequencial = $numero_sequencial++;

        // valida os dados
        if (!$this->header->validate()) {
            throw new \InvalidArgumentException($this->header->last_error);
        }

        $dados = $this->header->getEncoded().self::QUEBRA_LINHA;
        $this->trailer = new Trailer($this);
        foreach ($this->detalhes as $detalhe) {
            $detalhe->numero_sequencial = $numero_sequencial++;
            if (!$detalhe->validate()) {
                throw new \InvalidArgumentException($detalhe->last_error);
            }

            $dados .= $detalhe->getEncoded().self::QUEBRA_LINHA;
        }
        
        switch($this->codigo_banco) {
        	case \Cnab\Banco::SANTANDER:
                $this->configuracao['qtd_documentos']++;
                $this->trailer->qtd_documentos = $this->configuracao['qtd_documentos'];
                $this->trailer->valor_total = $this->configuracao['valor_total'];
        		break;
        }
        
        $this->trailer->numero_sequencial = $numero_sequencial++;

        if (!$this->trailer->validate()) {
            throw new \InvalidArgumentException($this->trailer->last_error);
        }

        $dados .= $this->trailer->getEncoded().self::QUEBRA_LINHA;

        return $dados;
    }

    public function countDetalhes()
    {
        return count($this->detalhes);
    }

    public function save($filename)
    {
        $text = $this->getText();
        file_put_contents($filename, $text);
    }
}
