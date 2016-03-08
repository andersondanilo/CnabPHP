<?php

namespace Cnab\Tests\Remessa\Cnab400;

class ArquivoTest extends \PHPUnit_Framework_TestCase
{
    public function testArquivoItauCnab400PodeSerCriado()
    {
        $codigo_banco = \Cnab\Banco::ITAU;
        $arquivo = new \Cnab\Remessa\Cnab400\Arquivo($codigo_banco);
        $arquivo->configure(array(
            'data_geracao' => new \DateTime('2015-02-01'),
            'data_gravacao' => new \DateTime('2015-02-01'),
            'nome_fantasia' => 'Nome Fantasia da sua empresa',
            'razao_social' => 'Razão social da sua empresa',
            'cnpj' => '11222333444455',
            'banco' => $codigo_banco, //código do banco
            'logradouro' => 'Logradouro da Sua empresa',
            'numero' => 'Número do endereço',
            'bairro' => 'Bairro da sua empresa',
            'cidade' => 'Cidade da sua empresa',
            'uf' => 'SP',
            'cep' => '00000111',
            'agencia' => '1234',
            'conta' => '123',
            'conta_dac' => '1',
        ));

        // você pode adicionar vários boletos em uma remessa
        $arquivo->insertDetalhe(array(
            'codigo_ocorrencia' => 1, // 1 = Entrada de título, futuramente poderemos ter uma constante
            'nosso_numero' => '12345679',
            'numero_documento' => '12345678',
            'carteira' => '111',
            'especie' => \Cnab\Especie::ITAU_DIVERSOS, // Você pode consultar as especies Cnab\Especie::CEF_OUTROS, futuramente poderemos ter uma tabela na documentação
            'aceite' => 'Z', // "S" ou "N"
            'valor' => 100.39, // Valor do boleto
            'instrucao1' => '', // 1 = Protestar com (Prazo) dias, 2 = Devolver após (Prazo) dias, futuramente poderemos ter uma constante
            'instrucao2' => '', // preenchido com zeros
            'sacado_razao_social' => 'Nome do cliente', // O Sacado é o cliente, preste atenção nos campos abaixo
            'sacado_tipo' => 'cnpj', //campo fixo, escreva 'cpf' (sim as letras cpf) se for pessoa fisica, cnpj se for pessoa juridica
            'sacado_cnpj' => '21.222.333.4444-55',
            'sacado_logradouro' => 'Logradouro do cliente',
            'sacado_bairro' => 'Bairro do cliente',
            'sacado_cep' => '00000-111',
            'sacado_cidade' => 'Cidade do cliente',
            'sacado_uf' => 'BA',
            'data_vencimento' => new \DateTime('2015-02-03'),
            'data_cadastro' => new \DateTime('2015-01-14'),
            'juros_de_um_dia' => 0.10, // Valor do juros de 1 dia'
            'data_desconto' => new \DateTime('2015-02-09'),
            'valor_desconto' => 10.0, // Valor do desconto
            'prazo' => 10, // prazo de dias para o cliente pagar após o vencimento
            'taxa_de_permanencia' => '0', //00 = Acata Comissão por Dia (recomendável), 51 Acata Condições de Cadastramento na CAIXA
            'mensagem' => 'Descrição do boleto',
            'data_multa' => new \DateTime('2015-02-07'), // data da multa
            'valor_multa' => 0.20, // valor da multa
            'tipo_multa' => 'porcentagem',
        ));

        $texto = $arquivo->getText();
        $lines = explode("\r\n", trim($texto, "\r\n"));

        $this->assertEquals(3, count($lines));
        $headerText = $lines[0];
        $detalheText = $lines[1];
        //$compl1Text = $lines[2];
        $trailerText = $lines[2];

        $asserts = array(
            'header' => array(
                '1:1' => '0',
                '2:2' => '1',
                '3:9' => 'REMESSA',
                '10:11' => '01',
                '12:26' => 'COBRANCA       ',
                '27:30' => '1234',
                '31:32' => '00',
                '33:37' => '00123',
                '38:38' => '1',
                '39:46' => '        ',
                '47:76' => str_pad('Nome Fantasia da sua empresa', 30),
                '77:79' => '341',
                '80:94' => str_pad('BANCO ITAU SA', 15),
                '95:100' => '010215',
                '101:394' => str_pad(' ', 294),
                '395:400' => sprintf('%06d', 1),
            ),
            'detalhe' => array(
                '1:1' => '1',
                '2:3' => '02', // empresa
                '4:17' => '11222333444455', // empresa
                '18:21' => '1234',
                '22:23' => '00',
                '24:28' => '00123',
                '29:29' => '1',
                '30:33' => '    ',
                '34:37' => '0000',
                '38:62' => str_pad('12345679', 25),
                '63:70' => '12345679',
                '71:83' => sprintf('%013d', 0),
                '84:86' => '0111',
                '87:107' => str_pad(' ', 21),
                '108:108' => 'I',
                '109:110' => '01',
                '111:120' => str_pad('12345678', 10),
                '121:126' => '030215',
                '127:139' => '0000000010039',
                '140:142' => '341',
                '143:147' => '00000',
                '148:149' => '99',
                '150:150' => 'Z',
                '151:156' => '140115',
                '157:158' => '  ',
                '159:160' => '  ',
                '161:173' => sprintf('%013d', 10),
                '174:179' => '090215',
                '180:192' => '0000000001000',
                '193:205' => sprintf('%013d', 0),
                '206:218' => sprintf('%013d', 0),
                '219:220' => '02', // 01 = CPF, 02 = CNPJ
                '221:234' => '21222333444455',
                '235:264' => str_pad('NOME DO CLIENTE', 30),
                '265:274' => str_pad(' ', 10),
                '275:314' => str_pad('LOGRADOURO DO CLIENTE', 40),
                '315:326' => 'BAIRRO DO CL',
                '327:334' => '00000111',
                '335:349' => str_pad('CIDADE DO CLIEN', 15),
                '350:351' => 'BA',
                '352:381' => str_pad('NOME FANTASIA DA SUA EMPRESA', 30),
                '382:385' => '    ',
                '386:391' => '070215',
                '392:393' => '10',
                '394:394' => ' ',
                '395:400' => sprintf('%06d', 2),
            ), /*
            'compl1' => array(
                '1:1' => '2',
                '2:2' => '2',
                '3:10' => '07022015',
                '11:23' => '0000000000020',
                '24:394' => str_repeat(' ', 371),
                '395:400' => '000003'
            ),*/
            'trailer' => array(
                '001:001' => '9',
                '002:394' => str_pad(' ', 393),
                '395:400' => sprintf('%06d', 3),
            ),
        );

        foreach ($asserts as $tipo => $campos) {
            $vname = "{$tipo}Text";
            foreach ($campos as $pos => $value) {
                $aux = explode(':', $pos);
                $start = $aux[0] - 1;
                $end = ($aux[1] - $aux[0]) + 1;
                $this->assertEquals($value, substr($$vname, $start, $end), "[ ] $tipo ($pos)");
            }
        }
    }
}
