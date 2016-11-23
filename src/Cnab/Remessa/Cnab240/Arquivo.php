<?php

namespace Cnab\Remessa\Cnab240;

class Arquivo implements \Cnab\Remessa\IArquivo
{
    public $headerArquivo;
    public $headerLote;
    public $detalhes = array();
    public $trailerLote;
    public $trailerArquivo;

    private $_data_gravacao;
    private $_data_geracao;
    public $banco;
    public $codigo_banco;
    public $configuracao = array();
    public $layoutVersao;
    const   QUEBRA_LINHA = "\n";

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
            'agencia',
            'agencia_dv',
            'bairro',
            'cep',
            'cidade',
            'cnpj',
            'data_geracao',
            'data_gravacao',
            'logradouro',
            'nome_fantasia',
            'numero',
            'numero_sequencial_arquivo',
            'razao_social',
            'uf',
        );

        if ($this->codigo_banco == \Cnab\Banco::CEF) {
            $campos[] = 'codigo_cedente';
            $campos[] = 'operacao';
        }

        if($this->codigo_banco == \Cnab\Banco::BANCO_DO_BRASIL) {
            $campos[] = 'codigo_convenio';
            $campos[] = 'codigo_carteira';
            $campos[] = 'variacao_carteira';
            $campos[] = 'conta_dv';
        }

        if ($this->codigo_banco == \Cnab\Banco::CEF || $this->codigo_banco == \Cnab\Banco::BANCO_DO_BRASIL) {
            $campos[] = 'conta';
        }

        foreach ($campos as $campo) {
            if (array_key_exists($campo, $params)) {
                if (strpos($campo, 'data_') === 0 && !($params[$campo] instanceof \DateTime)) {
                    throw new \Exception("Configuração '$campo' need to be instance of DateTime");
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

        $this->headerArquivo = new HeaderArquivo($this);
        $this->headerLote = new HeaderLote($this);
        $this->trailerLote = new TrailerLote($this);
        $this->trailerArquivo = new TrailerArquivo($this);

        $this->headerArquivo->codigo_banco = $this->banco['codigo_do_banco'];
        $this->headerArquivo->codigo_inscricao = 2;
        $this->headerArquivo->numero_inscricao = $this->prepareText($this->configuracao['cnpj'], '.-/');
        $this->headerArquivo->agencia = $this->configuracao['agencia'];
        $this->headerArquivo->agencia_dv = $this->configuracao['agencia_dv'];

        if($this->codigo_banco == \Cnab\Banco::BANCO_DO_BRASIL) {
            $this->headerArquivo->codigo_convenio = $this->configuracao['codigo_convenio'];
            $this->headerArquivo->carteira = $this->configuracao['codigo_carteira'];
            $this->headerArquivo->variacao_carteira = $this->configuracao['variacao_carteira'];
            $this->headerArquivo->conta = $this->configuracao['conta'];
            $this->headerArquivo->conta_dv = $this->configuracao['conta_dv'];
        }

        if($this->codigo_banco == \Cnab\Banco::CEF) {
            $this->headerArquivo->codigo_cedente = $this->configuracao['codigo_cedente'];
        }

        $this->headerArquivo->nome_empresa = $this->configuracao['nome_fantasia'];
        $this->headerArquivo->nome_banco = $this->banco['nome_do_banco'];
        $this->headerArquivo->codigo_remessa_retorno = 1;
        $this->headerArquivo->data_geracao = $this->configuracao['data_geracao'];
        $this->headerArquivo->hora_geracao = $this->configuracao['data_geracao'];
        $this->headerArquivo->numero_sequencial_arquivo = $this->configuracao['numero_sequencial_arquivo'];

        if ($this->codigo_banco == \Cnab\Banco::CEF) {
            if($this->layoutVersao === 'sigcb') {
                $this->headerArquivo->codigo_convenio = 0;
            } else {
                $codigoConvenio = sprintf(
                    '%04d%03d%08d',
                    $params['agencia'],
                    $params['operacao'],
                    $params['conta']
                );
                $codigoConvenio .= $this->mod11($codigoConvenio);
                $this->headerArquivo->codigo_convenio = $codigoConvenio;
            }
        }

        $this->headerLote->codigo_banco = $this->headerArquivo->codigo_banco;
        $this->headerLote->lote_servico = 1;
        $this->headerLote->tipo_operacao = 'R';
        $this->headerLote->codigo_inscricao = $this->headerArquivo->codigo_inscricao;
        $this->headerLote->numero_inscricao = $this->headerArquivo->numero_inscricao;
        $this->headerLote->agencia = $this->headerArquivo->agencia;
        $this->headerLote->agencia_dv = $this->headerArquivo->agencia_dv;


        if ($this->codigo_banco == \Cnab\Banco::CEF) {
            $this->headerLote->codigo_convenio = $this->headerArquivo->codigo_cedente;
            $this->headerLote->codigo_cedente = $this->headerArquivo->codigo_cedente;
        }

        if ($this->codigo_banco == \Cnab\Banco::BANCO_DO_BRASIL) {
            $this->headerLote->codigo_convenio = $this->headerArquivo->codigo_convenio;
            $this->headerLote->carteira = $this->headerArquivo->carteira;
            $this->headerLote->variacao_carteira = $this->headerArquivo->variacao_carteira;
            $this->headerLote->conta = $this->headerArquivo->conta;
            $this->headerLote->conta_dv = $this->headerArquivo->conta_dv;
        }

        $this->headerLote->nome_empresa = $this->headerArquivo->nome_empresa;
        $this->headerLote->numero_sequencial_arquivo = $this->headerArquivo->numero_sequencial_arquivo;
        $this->headerLote->data_geracao = $this->headerArquivo->data_geracao;

        if ($this->codigo_banco == \Cnab\Banco::CEF) {
            $this->headerLote->tipo_servico = 2;
        }

        $this->trailerLote->codigo_banco = $this->headerArquivo->codigo_banco;
        $this->trailerLote->lote_servico = $this->headerLote->lote_servico;

        $this->trailerArquivo->codigo_banco = $this->headerArquivo->codigo_banco;
    }

    public function mod11($num, $base = 9, $r = 0)
    {
        $soma = 0;
        $fator = 2;
        /* Separacao dos numeros */
        for ($i = strlen($num); $i > 0; --$i) {
            // pega cada numero isoladamente
            $numeros[$i] = substr($num, $i - 1, 1);
            // Efetua multiplicacao do numero pelo falor
            $parcial[$i] = $numeros[$i] * $fator;
            // Soma dos digitos
            $soma += $parcial[$i];
            if ($fator == $base) { // restaura fator de multiplicacao para 2
                $fator = 1;
            }
            ++$fator;
        }
        /* Calculo do modulo 11 */
        if ($r == 0) {
            $soma *= 10;
            $digito = $soma % 11;
            if ($digito == 10) {
                $digito = 0;
            }

            return $digito;
        } elseif ($r == 1) {
            $resto = $soma % 11;

            return $resto;
        }
    }

    public function insertDetalhe(array $boleto, $tipo = 'remessa')
    {
        $campos = array(
            'aceite',
            'data_cadastro',
            'data_vencimento',
            'especie',
            'juros_de_um_dia',
            'nosso_numero',
            'numero_documento',
            'registrado',
            'sacado_bairro',
            'sacado_cep',
            'sacado_cidade',
            'sacado_logradouro',
            'sacado_uf',
            'valor',
            'sacado_tipo',
        );

        if (!isset($boleto['aceite']))
        {   $boleto['aceite'] = "A";
        }

        if (isset($boleto['sacado_tipo']))
        {   if ($boleto['sacado_tipo'] == "cpf")
            {   $campos[] = "sacado_" . $boleto['sacado_tipo'];
                $campos[] = "sacado_nome";
            }
            elseif ($boleto['sacado_tipo'] == "cnpj")
            {   $campos[] = "sacado_" . $boleto['sacado_tipo'];
                $campos[] = "sacado_razao_social";
            }
            else
            {   throw new \Exception('Tipo de sacado inválido: '.$boleto['sacado_tipo']);
            }
        }

        // obcionais
        if (isset($boleto['valor_desconto']) and $boleto['valor_desconto'] > 0)
        {   $campos[] = 'valor_desconto';
            $campos[] = 'data_desconto';
        }
        else
        {   $boleto['valor_desconto'] = 0;
        }

        if (isset($boleto['valor_multa']) and $boleto['valor_multa'] > 0)
        {   $campos[] = 'valor_multa';
            $campos[] = 'data_multa';
        }
        else
        {   $boleto['valor_multa'] = 0;
        }

        // específicos
        if ($this->codigo_banco == \Cnab\Banco::CEF) {
            // $campos[] = 'modalidade_carteira';
        }

        if($this->codigo_banco == \Cnab\Banco::BANCO_DO_BRASIL) {
            // $campos[] = 'codigo_convenio';
        }

        if ($this->codigo_banco == \Cnab\Banco::CEF || $this->codigo_banco == \Cnab\Banco::BANCO_DO_BRASIL) {
            $campos[] = 'modalidade_carteira';
        }

        // validação
        foreach ($campos as $campo) {
            if (array_key_exists($campo, $boleto)) {
                if (strpos($campo, 'data_') === 0 && !($boleto[$campo] instanceof \DateTime)) {
                    throw new \Exception("Detalhes '$campo' need to be instance of DateTime");
                }
                // $this->configuracao[$campo] = $boleto[$campo];
            } else {
                throw new \Exception('Detalhes "'.$campo.'" need to be set');
            }
        }

        foreach ($campos as $key) {
            if (!array_key_exists($key, $boleto)) {
                throw new Exception('Detalhes "'.$key.'" dont exists');
            }
        }

        $dateVencimento = $boleto['data_vencimento'] instanceof \DateTime ? $boleto['data_vencimento'] : new \DateTime($boleto['data_vencimento']);
        $dateCadastro = $boleto['data_cadastro'] instanceof \DateTime ? $boleto['data_cadastro'] : new \DateTime($boleto['data_cadastro']);
        $dateJurosMora = clone $dateVencimento;

        $detalhe = new Detalhe($this);

        // SEGMENTO P -------------------------------
        $detalhe->segmento_p->codigo_banco = $this->headerArquivo->codigo_banco;
        $detalhe->segmento_p->lote_servico = $this->headerLote->lote_servico;
        $detalhe->segmento_p->agencia = $this->headerArquivo->agencia;
        $detalhe->segmento_p->agencia_dv = $this->headerArquivo->agencia_dv;

        if ($this->codigo_banco == \Cnab\Banco::CEF) {
            $detalhe->segmento_p->codigo_cedente = $this->headerArquivo->codigo_cedente;
        }

        if ($this->codigo_banco == \Cnab\Banco::BANCO_DO_BRASIL) {
            $detalhe->segmento_p->conta = $this->headerArquivo->conta;
            $detalhe->segmento_p->conta_dv = $this->headerArquivo->conta_dv;
        }

        if($this->codigo_banco == \Cnab\Banco::BANCO_DO_BRASIL) {
            /*
                Código da Carteira

                Código adotado pela FEBRABAN, para identificar a característica dos títulos dentro das
                modalidades de cobrança existentes no banco
                Domínio:
                    '1' = Cobrança Simples
                    '2' = Cobrança Vinculada
                    '3' = Cobrança Caucionada
                    '4' = Cobrança Descontada
                    '5' = Cobrança Vendor

                Informar:
                    '1' = para carteira 11/12 na modalidade Simples
                    '2' ou '3' = para carteira 11/17 modalidade Vinculada/Caucionada e carteira 31
                    '4' = para carteira 11/17 modalidade Descontada e carteira 51
                    '7' = para carteira 17 modalidade Simples
                (1 digito) [numerico]
             */

            // var_dump($this->configuracao, $boleto); exit();



            if($this->configuracao['codigo_carteira'] == 17 and $boleto['modalidade_carteira'] == \Cnab\CodigoCarteira::COBRANCA_SIMPLES) {
                $detalhe->segmento_p->codigo_carteira = 7;
            } else {
                $detalhe->segmento_p->codigo_carteira = $boleto['modalidade_carteira'];
            }
        }
        elseif ($this->layoutVersao === 'sigcb' && $this->codigo_banco == \Cnab\Banco::CEF) {
            $detalhe->segmento_p->codigo_carteira = 1; // 1 = Cobrança Simples
            $detalhe->segmento_p->modalidade_carteira = $boleto['modalidade_carteira']; // 21 = (título Sem Registro emissão CAIXA)
        }

        $detalhe->segmento_p->forma_cadastramento = $boleto['registrado'] ? 1 : 2; // 1 = Com, 2 = Sem Registro
        if ($this->codigo_banco == \Cnab\Banco::CEF and $boleto['registrado']) {
            $this->headerLote->tipo_servico = 1;
        }

        $detalhe->segmento_p->numero_documento = $boleto['numero_documento'];
        $detalhe->segmento_p->vencimento = $dateVencimento;
        $detalhe->segmento_p->valor_titulo = $boleto['valor'];
        $detalhe->segmento_p->especie = $boleto['especie']; // 4 = Duplicata serviço
        $detalhe->segmento_p->aceite = $boleto['aceite'];
        $detalhe->segmento_p->data_emissao = $dateCadastro;
        $detalhe->segmento_p->codigo_juros_mora = 1; // 1 = Por dia

        if (isset($boleto['dias_iniciar_contagem_juros']) && is_numeric($boleto['dias_iniciar_contagem_juros'])) {
            $dateJurosMora->modify("+{$boleto['dias_iniciar_contagem_juros']} days");
        } else {
            $dateJurosMora->modify('+1 day');
        }

        $detalhe->segmento_p->data_juros_mora = $dateJurosMora;

        $detalhe->segmento_p->valor_juros_mora = $boleto['juros_de_um_dia'];
        if ($boleto['valor_desconto'] > 0) {
            $detalhe->segmento_p->codigo_desconto_1 = 1; // valor fixo
            $detalhe->segmento_p->data_desconto_1 = $boleto['data_desconto'];
            $detalhe->segmento_p->valor_desconto_1 = $boleto['valor_desconto'];
        } else {
            $detalhe->segmento_p->codigo_desconto_1 = 0; // sem desconto
            $detalhe->segmento_p->data_desconto_1 = 0;
            $detalhe->segmento_p->valor_desconto_1 = 0;
        }
        $detalhe->segmento_p->valor_abatimento = 0;
        $detalhe->segmento_p->uso_empresa = $boleto['numero_documento'];

        if (isset($boleto['codigo_protesto']) && isset($boleto['prazo_protesto'])) {
            $detalhe->segmento_p->codigo_protesto = $boleto['codigo_protesto'];
            $detalhe->segmento_p->prazo_protesto = $boleto['prazo_protesto'];
        } else {
            $detalhe->segmento_p->codigo_protesto = 3; // 3 = Não protestar
            $detalhe->segmento_p->prazo_protesto = 0;
        }

        if ($this->codigo_banco == \Cnab\Banco::BANCO_DO_BRASIL) {
            // Campo não tratado pelo sistema. Informar 'zeros'.
            // O sistema considera a informação que foi cadastrada na
            // sua carteira junto ao Banco do Brasil.
            $detalhe->segmento_p->codigo_baixa = 0;
            $detalhe->segmento_p->prazo_baixa = 0;
        } else {
            if(isset($boleto['baixar_apos_dias'])) {
                if($boleto['baixar_apos_dias'] === false) {
                    // não baixar / devolver
                    $detalhe->segmento_p->codigo_baixa = 2;
                    $detalhe->segmento_p->prazo_baixa = 0;
                } else {
                    // baixa automática
                    $detalhe->segmento_p->codigo_baixa = 1;
                    $detalhe->segmento_p->prazo_baixa = $boleto['baixar_apos_dias'];
                }
            } else {
                $detalhe->segmento_p->codigo_baixa = 0;
                $detalhe->segmento_p->prazo_baixa = 0;
            }
        }

        if (isset($boleto['identificacao_distribuicao'])) {
            $detalhe->segmento_p->identificacao_distribuicao = $boleto['identificacao_distribuicao'];
        }

        if ($tipo == 'remessa') {
            $detalhe->segmento_p->codigo_ocorrencia = 1;
        } elseif ($tipo == 'baixa') {
            $detalhe->segmento_p->codigo_ocorrencia = 2;
        } else {
            throw new \Exception('Tipo de detalhe inválido: '.$tipo);
        }

        $detalhe->segmento_p->nosso_numero = $this->formatarNossoNumero($boleto['nosso_numero'], $detalhe->segmento_p->codigo_ocorrencia);

        // SEGMENTO Q -------------------------------
        $detalhe->segmento_q->codigo_banco = $this->headerArquivo->codigo_banco;
        $detalhe->segmento_q->lote_servico = $this->headerLote->lote_servico;
        $detalhe->segmento_q->codigo_ocorrencia = $detalhe->segmento_p->codigo_ocorrencia;
        $__sacado_tipo = $boleto['sacado_tipo'];
        $detalhe->segmento_q->sacado_numero_inscricao = $this->prepareNumber($boleto[('sacado_' . $__sacado_tipo)]);
        if ($__sacado_tipo == 'cnpj') {
            $detalhe->segmento_q->sacado_codigo_inscricao = '2';
            $detalhe->segmento_q->nome = $this->prepareText($boleto['sacado_razao_social']);
        } else {
            $detalhe->segmento_q->sacado_codigo_inscricao = '1';
            $detalhe->segmento_q->nome = $this->prepareText($boleto['sacado_nome']);
        }
        $detalhe->segmento_q->logradouro = $this->prepareText($boleto['sacado_logradouro']);
        $detalhe->segmento_q->bairro = $this->prepareText($boleto['sacado_bairro']);
        $detalhe->segmento_q->cep = $this->prepareNumber($boleto['sacado_cep']);
        $detalhe->segmento_q->cidade = $this->prepareText($boleto['sacado_cidade']);
        $detalhe->segmento_q->estado = $this->prepareText($boleto['sacado_uf']);

        if ($this->codigo_banco != \Cnab\Banco::BANCO_DO_BRASIL)
        {   // se o titulo for de terceiro, o sacador é o terceiro
            $detalhe->segmento_q->sacador_codigo_inscricao = $this->headerArquivo->codigo_inscricao;
            $detalhe->segmento_q->sacador_numero_inscricao = $this->headerArquivo->numero_inscricao;
            $detalhe->segmento_q->sacador_nome = $this->headerArquivo->nome_empresa;
        }

        // SEGMENTO R -------------------------------
        $detalhe->segmento_r->codigo_banco = $detalhe->segmento_p->codigo_banco;
        $detalhe->segmento_r->lote_servico = $detalhe->segmento_p->lote_servico;
        $detalhe->segmento_r->codigo_ocorrencia = $detalhe->segmento_p->codigo_ocorrencia;
        if ($boleto['valor_multa'] > 0) {
            $detalhe->segmento_r->codigo_multa = 1;
            $detalhe->segmento_r->valor_multa = $boleto['valor_multa'];
            $detalhe->segmento_r->data_multa = $boleto['data_multa'];
        } else {
            $detalhe->segmento_r->codigo_multa = 0;
            $detalhe->segmento_r->valor_multa = 0;
            $detalhe->segmento_r->data_multa = 0;
        }
        // segmento não utilizado, caso não haja multa definida no BB
        if (!$detalhe->segmento_r->codigo_multa and $this->codigo_banco == \Cnab\Banco::BANCO_DO_BRASIL)
        {   unset($detalhe->segmento_r);
        }

        $this->detalhes[] = $detalhe;
    }

    public function formatarNossoNumero($nossoNumero, $codigo_ocorrencia = 1)
    {
        if(!$nossoNumero)
            return $nossoNumero;

        if ($codigo_ocorrencia == 1) {
            if ($this->codigo_banco == \Cnab\Banco::BANCO_DO_BRASIL) {
                /*
                    Identificação do Título no Banco

                    Número adotado pelo Banco Cedente para identificar o Título.
                    Para código de movimento igual a '01' (Entrada de Títulos), caso esteja preenchido com
                    zeros, a numeração será feita pelo Banco.

                    Obs.:
                        1. Caso seja o Banco do Brasil quem gera o "Nosso Número", informar 'brancos' (espaços) ou Zeros.
                        2. Caso seja a empresa quem gera o "Nosso Número", informar de acordo com o número do convênio de
                        cobrança conforme as seguintes regras:
                            a) Para número de convênio de 4 posições (carteira 17 até 9.999) informar o nosso número com
                            11 posições mais o DV (dígito verificador), sendo as 4 primeiras posições o número do convênio,
                            as 7 posições seguintes um número sequencial para controle e mais o DV.
                            Exemplo: CONVÊNIOS DE 0001 ATÉ 9.999
                                123412345671
                                CCCCSSSSSSSD
                                Onde: C = Convênio S = Sequencial D = dígito verificador

                            b) Para número de convênio de 6 posições (de 10.000 a 999.999), informar o nosso número com
                            11 posições mais o DV, sendo as 6 primeiras posições o número do convênio, as 5 posições
                            seguintes um número sequencial para controle e mais o DV.
                            Exemplo: CONVÊNIOS DE 010.000 ATÉ 999.999
                                123456123451
                                CCCCCCSSSSSD
                                Onde: C = Convênio S = Sequencial D = dígito verificador

                            c) Para número de convênio de 7 posições (acima de 1.000.000) informar o nosso número com
                            17 posições sem DV, sendo as 7 primeiras posições o número do convênio e as 10 posições seguintes
                            um número sequencial para controle. Esse é o padrão mais utilizado atualmente.
                            Exemplo: CONVÊNIOS DE 1.000.000 ATÉ 9.999.999
                                12345671234567890
                                CCCCCCCSSSSSSSSSS
                                Onde: C = Convênio S = Sequencial

                        Importante:
                            todos os "nosso número" devem ser alinhados à esquerda com brancos à direita.
                 */

                $codigo_convenio = $this->configuracao['codigo_convenio'];
                // convênios de 0001 até 9.999
                if ($codigo_convenio >= 1 and $codigo_convenio <= 9999) { // carteira 17 até 9.999
                    # Convênio de 4 digitos
                    if(strlen($nossoNumero) > 7) {
                        throw new \InvalidArgumentException(
                            "Para número de convênio de 4 posições o nosso número deve ter no máximo 7 posições (sem o digito)"
                        );
                    }
                    $number = sprintf('%04d%07d', $codigo_convenio, $nossoNumero);
                    return $number . $this->mod11($number);
                }
                // convênios de 010.000 até 999.999
                elseif ($codigo_convenio >= 10000 and $codigo_convenio <= 999999) {
                    # Convênio de 6 digitos
                    if(strlen($nossoNumero) > 5) {
                        throw new \InvalidArgumentException(
                            "Para número de convênio de 6 posições o nosso número deve ter no máximo 5 posições (sem o digito)"
                        );
                    }
                    $number = sprintf('%06d%05d', $codigo_convenio, $nossoNumero);
                    return $number . $this->mod11($number);
                }
                // convênios de 1.000.000 até 9.999.999
                elseif ($codigo_convenio >= 1000000 and $codigo_convenio <= 9999999) {
                    # Convênio de 7 digitos
                    if(strlen($nossoNumero) > 10) {
                        throw new \InvalidArgumentException(
                            "Para número de convênio de 7 posições o nosso número deve ter no máximo 10 posições"
                        );
                    }
                    $number = sprintf('%07d%010d', $codigo_convenio, $nossoNumero);
                    return $number;
                }
            }
        } elseif ($codigo_ocorrencia == 0) {
            if ($this->codigo_banco == \Cnab\Banco::BANCO_DO_BRASIL) {
                return '';
            }
        }

        return $nossoNumero;
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

    private function prepareNumber($num)
    {   $result = preg_replace("/[^0-9]+/", "", $num);
        return $result;
    }

    /**
     * Completa o $valor_original passado com $valor_completar a esquerda até atingir o tamanho $quantidade
     *
     * @author Guilherme Mattioli
     *
     * @param $valor_original "string de entrada a ser completada"
     * @param $quantidade "quantidade de digitos que deve ter no total"
     * @param $valor_completar "caracter que deve utilizar para completar"
     * @param $to_replace "regex utilizado para 'limpar' o $valor_original"
     * @return string
     */
    private function completeLeft($valor_original, $quantidade = 0, $valor_completar = "0", $to_replace = "/[^0-9]+/")
    {   $val = $valor_original;
        if ($valor_original)
        {   $val = preg_replace($to_replace, "", $valor_original);
        }
        if ($quantidade > 0)
        {   while (strlen($val) < $quantidade)
            {   $val = "{$valor_completar}{$val}";
            }

            if (strlen($val) > $quantidade)
            {   $val = substr($val, 0, $quantidade);
            }
        }
        return $val;
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

    public function getText()
    {
        $numero_sequencial_lote = 1;
        $qtde_registro_lote = 2; // header e trailer = 2
        $qtde_titulo_cobranca_simples = 0;
        $valor_total_titulo_simples = 0;

        // valida os dados
        if (!$this->headerArquivo->validate()) {
            throw new \InvalidArgumentException($this->headerArquivo->last_error);
        }

        if (!$this->headerLote->validate()) {
            throw new \InvalidArgumentException($this->headerLote->last_error);
        }

        $dados = $this->headerArquivo->getEncoded().self::QUEBRA_LINHA;
        $dados .= $this->headerLote->getEncoded().self::QUEBRA_LINHA;

        foreach ($this->detalhes as $detalhe) {
            ++$qtde_titulo_cobranca_simples;
            $valor_total_titulo_simples += $detalhe->segmento_p->valor_titulo;
            foreach ($detalhe->listSegmento() as $segmento) {
                ++$qtde_registro_lote;
                $segmento->numero_sequencial_lote = $numero_sequencial_lote++;
            }

            if (!$detalhe->validate()) {
                throw new \InvalidArgumentException($detalhe->last_error);
            }

            $dados .= $detalhe->getEncoded().self::QUEBRA_LINHA;
        }

        $this->trailerLote->qtde_registro_lote = $qtde_registro_lote;

        if ($this->codigo_banco == \Cnab\Banco::CEF) {
            $this->trailerLote->qtde_titulo_cobranca_simples = $qtde_titulo_cobranca_simples;
            $this->trailerLote->valor_total_titulo_simples = $valor_total_titulo_simples;
            $this->trailerLote->qtde_titulo_cobranca_caucionada = 0;
            $this->trailerLote->valor_total_titulo_caucionada = 0;
            $this->trailerLote->qtde_titulo_cobranca_descontada = 0;
            $this->trailerLote->valor_total_titulo_descontada = 0;
        }

        $this->trailerArquivo->qtde_lotes = 1;
        $this->trailerArquivo->qtde_registros = $this->trailerLote->qtde_registro_lote + 2;

        if (!$this->trailerLote->validate()) {
            throw new \InvalidArgumentException($this->trailerLote->last_error);
        }

        if (!$this->trailerArquivo->validate()) {
            throw new \InvalidArgumentException($this->trailerArquivo->last_error);
        }

        $dados .= $this->trailerLote->getEncoded().self::QUEBRA_LINHA;
        $dados .= $this->trailerArquivo->getEncoded().self::QUEBRA_LINHA;

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

        return $filename;
    }
}
