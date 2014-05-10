<?php
namespace Cnab\Cnab400\Remessa;

class Header extends \Cnab\Format\Linha
{
    public $_codigo_banco;

    public function __construct(\Cnab\Remessa\IArquivo $arquivo)
    {
        $this->_codigo_banco = $arquivo->codigo_banco;

        $yamlLoad = new \Cnab\Format\YamlLoad($this->_codigo_banco);
        $yamlLoad->load($this, CNAB_FORMAT_PATH.'/cnab400/remessa/header_arquivo.yml');
    }
}

/*
class Cnab_Remessa_Cnab400_Header_BaseFields
{
    public $detalhe;

    public function __construct($detalhe)
    {
        $this->detalhe = $detalhe;      
    }

    public function addFields()
    {
        $this->detalhe->addField('tipo_de_registro',        1,   1, '9(01)',  '0');
        $this->detalhe->addField('operacao',                2,   2, '9(01)',  '1');
        $this->detalhe->addField('literal_de_remessa',      3,   9, 'X(07)',  'REMESSA');
        $this->detalhe->addField('codigo_de_servico',      10,  11, '9(02)',  '1');
        $this->detalhe->addField('literal_de_servico',     12,  26, 'X(15)',  'COBRANCA');

        $this->detalhe->addField('nome_da_empresa',        47,  76, 'X(30)',  false);

        $this->detalhe->addField('data_de_geracao',        95, 100, '9(06)',  false);
    }
}

class Cnab_Remessa_Cnab400_Header_ItauFields extends Cnab_Remessa_Cnab400_Header_BaseFields
{
    public function addFields()
    {
        $banco = Cnab_Banco::getBanco(Cnab_Banco::ITAU);

        $this->detalhe->addField('codigo_do_banco',        77,  79, '9(03)',  $banco['codigo_do_banco']);
        $this->detalhe->addField('nome_do_banco',          80,  94, 'X(15)',  $banco['nome_do_banco']);


        $this->detalhe->addField('agencia',                27,  30, '9(04)',  false);
        $this->detalhe->addField('zeros01',                31,  32, '9(02)',  '0');
        $this->detalhe->addField('conta',                  33,  37, '9(05)',  false);
        $this->detalhe->addField('dac',                    38,  38, '9(01)',  false);
        $this->detalhe->addField('brancos01',              39,  46, 'X(08)',  '');

        $this->detalhe->addField('brancos02',             101, 394, 'X(294)', '');
        $this->detalhe->addField('numero_sequencial',     395, 400, '9(06)',  '1');

        return parent::addFields();
    }
}

class Cnab_Remessa_Cnab400_Header_CefFields extends Cnab_Remessa_Cnab400_Header_BaseFields
{
    public function addFields()
    {
        $banco = Cnab_Banco::getBanco(Cnab_Banco::CEF);

        $this->detalhe->addField('codigo_do_banco',        77,  79, '9(03)',  $banco['codigo_do_banco']);
        $this->detalhe->addField('nome_do_banco',          80,  94, 'X(15)',  $banco['nome_do_banco']);

        $this->detalhe->addField('codigo_cedente',     27,  42, '9(16)',  false);
        $this->detalhe->addField('brancos01',          43,  46, 'X(04)',  '');

        $this->detalhe->addField('brancos02',               101, 389, 'X(289)', '');
        $this->detalhe->addField('numero_sequencial_a',     390, 394, '9(05)',  '1');
        $this->detalhe->addField('numero_sequencial_b',     395, 400, '9(06)',  '1');

        $this->codigo_cedente = $this->generateCodigoCedente();

        return parent::addFields();
    }

    public function generateCodigoCedente()
    {
        $agencia     = $this->detalhe->arquivo->configuracao['agencia'];
        $conta       = $this->detalhe->arquivo->configuracao['conta'];
        $operacao    = $this->detalhe->arquivo->configuracao['operacao'];
        $cedente_dac = $this->detalhe->arquivo->configuracao['codigo_cedente_dac'];
        $result = sprintf('%04d%03d%08d%01d', $agencia, $operacao, $conta, $cedente_dac);
        return $result;
    }
}*/