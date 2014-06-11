<?php
namespace Cnab\Remessa\Cnab400;

class Arquivo implements \Cnab\Remessa\IArquivo
{
	public  $header;
	public  $trailer;
	public  $detalhes = array();
	private $_data_gravacao;
	private $_data_geracao;
    public  $banco;
    public  $codigo_banco; 
	public $configuracao = array();
	const   QUEBRA_LINHA = "\r\n";

	public function __construct($codigo_banco)
	{
        $this->codigo_banco = $codigo_banco;
        $this->banco  = \Cnab\Banco::getBanco($this->codigo_banco);
		//$this->data_gravacao = date('dmY');
	}

	public function configure(array $params)
	{
        $campos = array(
            'data_geracao', 'data_gravacao', 'nome_fantasia', 'razao_social', 'cnpj', 'logradouro', 'numero', 'bairro', 
            'cidade', 'uf', 'cep',
        );

        if($this->codigo_banco == \Cnab\Banco::CEF)
        {
            //$campos[] = 'codigo_cedente';
            $campos[] = 'agencia';
            $campos[] = 'conta';
            $campos[] = 'operacao';
            $campos[] = 'codigo_cedente';
            $campos[] = 'codigo_cedente_dac';
        }
        else
        {
            $campos[] = 'agencia';
            $campos[] = 'conta';
            $campos[] = 'conta_dac';
        }

        foreach($campos as $campo)
        {
        	if(array_key_exists($campo, $params))
        	{
                if(strpos($campo, 'data_') === 0 && !($params[$campo] instanceof DateTime))
                    throw new Exception("config '$campo' need to be instance of DateTime");
                $this->configuracao[$campo] = $params[$campo];
           	}
            else
                throw new Exception('Configuração "'.$campo.'" need to be set');
        }
            
        foreach($campos as $key)
        {
        	if(!array_key_exists($key, $params))
                throw new Exception('Configuração "'.$key.'" dont exists');
        }
            
        $this->data_geracao  = $this->configuracao['data_geracao'];
        $this->data_gravacao = $this->configuracao['data_gravacao'];

        $this->header = new Header($this);

        $this->header->codigo_do_banco = $this->banco['codigo_do_banco'];
        $this->header->nome_do_banco = $this->banco['nome_do_banco'];

        if($this->codigo_banco == \Cnab\Banco::CEF)
        {
            $this->header->codigo_cedente = $this->configuracao['codigo_cedente'];
        }
        else
        {
            $this->header->agencia = $this->configuracao['agencia'];
            $this->header->conta = $this->configuracao['conta'];
            $this->header->dac = $this->configuracao['conta_dac'];
        }
        
        $this->header->nome_da_empresa = $this->configuracao['nome_fantasia'];
        $this->header->data_de_geracao = $this->configuracao['data_geracao']->format('dmy');
    }
       
    public function insertDetalhe(array $boleto, $tipo='remessa')
    {
        $dateVencimento = $boleto['data_vencimento'] instanceof \DateTime ? $boleto['data_vencimento'] : new \DateTime($boleto['data_vencimento']);
        $dateCadastro   = $boleto['data_cadastro']   instanceof \DateTime ? $boleto['data_cadastro']   : new \DateTime($boleto['data_cadastro']);
        
        $detalhe = new Detalhe($this);
    
        if($tipo == 'remessa')
        {
            $detalhe->codigo_de_ocorrencia =  !empty($boleto['codigo_de_ocorrencia']) ? $boleto['codigo_de_ocorrencia'] : '1';
            
            $detalhe->codigo_de_inscricao =   2;
            $detalhe->numero_de_inscricao =   $this->prepareText($this->configuracao['cnpj'], '.-/');

            if(Cnab_Banco::CEF == $this->codigo_banco)
            {
                $detalhe->codigo_cedente = $this->header->codigo_cedente;
                $detalhe->taxa_de_permanencia = $boleto['taxa_de_permanencia'];
                $detalhe->mensagem = $boleto['mensagem'];
                $detalhe->data_multa = $boleto['data_multa'];
                $detalhe->valor_multa = $boleto['valor_multa'];
                
            }
            else
            {
                $detalhe->agencia   =   $this->header->agencia;
                $detalhe->conta     =   $this->header->conta;
                $detalhe->conta_dac =   $this->header->dac;    
                $detalhe->codigo_instrucao =   '0';
                $detalhe->qtde_de_moeda =   '0'; # Este campo deverá ser preenchido com zeros caso a moeda seja o Real.
                $detalhe->codigo_da_carteira =   'I';
                $detalhe->uso_do_banco =   '';
            }
            

            
            /*
               Deve ser preenchido na remessa somente quando utilizados, na posição 109-110, os códigos de
               ocorrência 35 – Cancelamento de Instrução e 38 – Cedente não concorda com alegação do sacado. Para
               os demais códigos de ocorrência este campo deverá ser preenchido com zeros.
            */
            $detalhe->uso_da_empresa =   $boleto['nosso_numero'];
            $detalhe->nosso_numero =   $boleto['nosso_numero'];
            
            $detalhe->numero_da_carteira =   $boleto['carteira'];
            $detalhe->numero_do_documento =   $boleto['numero_documento'];
            $detalhe->vencimento =  $dateVencimento->format('dmy');
            $detalhe->valor_do_titulo =   $boleto['valor'];
            $detalhe->aceite =   'N';
            $detalhe->instrucao1 = $boleto['instrucao1'];
            $detalhe->instrucao2 = $boleto['instrucao2'];
            $detalhe->especie = $boleto['especie'];
            $detalhe->data_de_emissao =  $dateCadastro->format('dmy');
            
            $sacado_tipo = @$boleto['sacado_tipo'] or $sacado_tipo = 'cpf';
            
            if($sacado_tipo == 'cnpj')
            {
                $detalhe->sacado_codigo_de_inscricao =   '2';
                /**
                 * @todo Trocar espécie
                 */
                $detalhe->sacado_numero_de_inscricao =   $this->prepareText($boleto['sacado_cnpj'], '.-/');
                $detalhe->nome =   $this->prepareText($boleto['sacado_razao_social']);
            }
            else
            {
                $detalhe->sacado_codigo_de_inscricao =   '1';
                /**
                 * @todo Trocar espécie
                 */
                //$detalhe->especie =   '4'; # Mensalidade Escolar
                $detalhe->sacado_numero_de_inscricao =   $this->prepareText($boleto['sacado_cpf'], '.-/');
                $detalhe->nome =   $this->prepareText($boleto['sacado_nome']);
            }
            $detalhe->logradouro =   $this->prepareText($boleto['sacado_logradouro']);
            $detalhe->bairro =   $this->prepareText($boleto['sacado_bairro']);
            $detalhe->cep =   str_replace('-', '', $boleto['sacado_cep']);
            $detalhe->cidade =   $this->prepareText($boleto['sacado_cidade']);
            $detalhe->estado =   $boleto['sacado_uf'];
            $detalhe->sacador =   $this->prepareText($this->configuracao['nome_fantasia']);

            $detalhe->juros_de_um_dia = $boleto['juros_de_um_dia'];
            $detalhe->data_desconto = $boleto['data_desconto'];
            $detalhe->valor_desconto = $boleto['valor_desconto'];
            $detalhe->prazo = $boleto['prazo'];
        }
        else if($tipo == 'baixa')
        {
            $detalhe->codigo_de_inscricao =   '0';
            $detalhe->numero_de_inscricao =   '0';
            $detalhe->agencia =   $this->header->agencia;
            $detalhe->conta =   $this->header->conta;
            $detalhe->conta_dac =   $this->header->dac;
            $detalhe->codigo_instrucao =   '0';
            /*
               Deve ser preenchido na remessa somente quando utilizados, na posição 109-110, os códigos de
               ocorrência 35 – Cancelamento de Instrução e 38 – Cedente não concorda com alegação do sacado. Para
               os demais códigos de ocorrência este campo deverá ser preenchido com zeros.
            */
            $detalhe->codigo_de_ocorrencia =  $boleto['codigo_de_ocorrencia'];
            $detalhe->uso_da_empresa =   $boleto['nosso_numero'];
            $detalhe->nosso_numero =   $boleto['nosso_numero'];
            $detalhe->qtde_de_moeda =   '0'; # Este campo deverá ser preenchido com zeros caso a moeda seja o Real.
            $detalhe->numero_da_carteira =   $boleto['carteira'];
            $detalhe->codigo_da_carteira =   'I';
            $detalhe->uso_do_banco =   '';
            $detalhe->numero_do_documento =   $boleto['numero_documento'];
            $detalhe->vencimento =   '0';
            $detalhe->valor_do_titulo =   $boleto['valor'];
            $detalhe->aceite =   ' ';
            $detalhe->data_de_emissao =  '0';
            $detalhe->sacado_codigo_de_inscricao =   '2';
            $detalhe->especie =   ' '; # Conta de Prestação de Serviços
            $detalhe->sacado_numero_de_inscricao =   '0';
            $detalhe->juros_de_um_dia = $boleto['juros_de_um_dia'];
            $detalhe->data_juros = $boleto['data_juros'];
            
            $detalhe->nome =   ' ';
            $detalhe->logradouro =  ' ';
            $detalhe->bairro =  ' ';
            $detalhe->cep =  '0';
            $detalhe->cidade =  ' ';
            $detalhe->estado =  ' ';
            $detalhe->sacador =  ' ';
        }
        else
        {
            throw new Exception('Tipo de $detalhe desconhecido');
        }
        
        $detalhe->codigo_do_banco = $this->banco['codigo_do_banco'];

        $this->detalhes[] = $detalhe;
        $detalhe->numero_sequencial =  count($this->detalhes) + 1;
    }
	
    public function listDetalhes()
    {
        return $this->detalhes;
    }
	
	private function prepareText($text, $remove=null)
	{
		$result = strtoupper($this->removeAccents(trim(html_entity_decode($text))));;
		if($remove)
			$result = str_replace(str_split($remove), '', $result);
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
            str_split( 'ACEIOUaceiou' , 1 ),
            $this->isUtf8( $string ) ? $string : utf8_encode( $string )
        );
	}

	private function isUtf8($string)
	{
        return preg_match( '%^(?:
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
		if(strpos($name, 'data_') === 0)
		{
			if($value instanceof \DateTime)
			{
				$property = "_$name";
				$this->$property = $value;
			}
			else
				throw new InvalidArgumentException("$nome need to be instance of DateTime");
		}
		else 
			throw new Exception("property '$name' dont exists");
	}

	public function getText()
	{
        // valida os dados
        if(!$this->header->validate())
            throw new \InvalidArgumentException($this->header->last_error);

		$dados = $this->header->getEncoded() . self::QUEBRA_LINHA;
		$this->trailer = new Trailer($this);
		foreach($this->detalhes as $detalhe)
		{
            if(!$detalhe->validate())
                throw new InvalidArgumentException($detalhe->last_error);

			$dados .= $detalhe->getEncoded() . self::QUEBRA_LINHA;
		}
		$this->trailer->numero_sequencial = count($this->detalhes) + 2;

        if(!$this->trailer->validate())
            throw new \InvalidArgumentException($this->trailer->last_error);

        $dados .= $this->trailer->getEncoded() . self::QUEBRA_LINHA;
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