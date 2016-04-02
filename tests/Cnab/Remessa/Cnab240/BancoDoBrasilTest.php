<?php

namespace Cnab\Tests\Remessa\Cnab240;

class BancoDoBrasilTest extends \PHPUnit_Framework_TestCase
{
    public function testArquivoBancoDoBrasil240PodeSerCriado()
    {
        $codigoBanco = \Cnab\Banco::BANCO_DO_BRASIL;
        $cnabFactory = new \Cnab\Factory();
        $arquivo = $cnabFactory->createRemessa($codigoBanco, 'cnab240');
        $arquivo->configure(array(
            'data_geracao' => new \DateTime('2015-02-01 01:02:03'),
            'data_gravacao' => new \DateTime('2015-02-01'),
            'nome_fantasia' => 'Nome Fantasia da sua empresa',
            'razao_social' => 'Razão social da sua empresa',
            'cnpj' => '11222333444455',
            'banco' => $codigoBanco, //código do banco
            'logradouro' => 'Logradouro da Sua empresa',
            'numero' => 'Número do endereço',
            'bairro' => 'Bairro da sua empresa',
            'cidade' => 'Cidade da sua empresa',
            'uf' => 'SP',
            'cep' => '00000111',
            'conta' => '123456',
            'conta_dv' => '5',
            'operacao' => '012',
            'agencia' => '1234',
            'agencia_dv' => '3',
            'codigo_convenio' => '123123',
            'codigo_carteira' => '11', // número da carteira
            'variacao_carteira' => '345',
            'numero_sequencial_arquivo' => 1,
        ));

        // você pode adicionar vários boletos em uma remessa
        $arquivo->insertDetalhe(array(
            'codigo_ocorrencia' => 1, // 1 = Entrada de título, futuramente poderemos ter uma constante
            'nosso_numero' => '12345',
            'numero_documento' => '12345678',
            'carteira' => '11',
            'codigo_carteira' => \Cnab\CodigoCarteira::COBRANCA_SIMPLES,
            'especie' => \Cnab\Especie::BB_DUPLICATA_MERCANTIL, // Você pode consultar as especies Cnab\Especie::CEF_OUTROS, futuramente poderemos ter uma tabela na documentação
            'aceite' => 'N', // "S" ou "N"
            'registrado' => false,
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
            'valor_multa' => 11.2, // valor da multa
            'baixar_apos_dias' => 30,
            'dias_iniciar_contagem_juros' => 1,
        ));

        $texto = $arquivo->getText();
        $lines = explode("\r\n", trim($texto, "\r\n"));

        $this->assertEquals(7, count($lines));

        $headerArquivoText = $lines[0];
        $headerLoteText = $lines[1];
        $segmentoPText = $lines[2];
        $segmentoQText = $lines[3];
        $segmentoRText = $lines[4];
        $trailerLoteText = $lines[5];
        $trailerArquivoText = $lines[6];

        $asserts = array(
            'headerArquivo' => array(
                '1:3' => '001', // codigo_banco 
                '4:7' => '0000', // lote_servico 
                '8:8' => '0', // tipo_registro 
                '9:17' => '         ', // uso_exclusivo_febraban_01 
                '18:18' => '2', // codigo_inscricao 
                '19:32' => '11222333444455', // numero_inscricao 
                '33:41' => '000123123', // codigo convenio
                '42:45' => '0014', // Cobrança Cedende BB: Informar 0014 para cobrança cedente
                '46:47' => '11', // Carteira
                '48:50' => '345', // Variação carteira
                '51:52' => '  ', // uso reservado bb
                '53:57' => '01234', // agencia 
                '58:58' => '3', // agencia_dv 
                '59:70' => '000000123456', // conta
                '71:71' => '5', // conta dv
                '72:72' => ' ', // campo não tratado pelo bb
                '73:102' => 'Nome Fantasia da sua empresa  ', // nome_empresa 
                '103:132' => 'BANCO DO BRASIL S.A.          ', // nome_banco 
                '133:142' => '          ', // uso_exclusivo_febraban_02 
                '143:143' => '1', // codigo_remessa_retorno 
                '144:151' => '01022015', // data_geracao 
                '152:157' => '010203', // hora_geracao 
                '158:163' => '000001', // numero_sequencial_arquivo 
                '164:166' => '030', // versao_layout_arquivo 
                '167:171' => '00000', // densidade_gravacao_arquivo 
                '172:191' => '                    ', // Para Uso Reservado do Banco 
                '192:211' => 'REMESSA-PRODUCAO    ',  // Para Uso Reservado da Empresa
                '212:225' => '              ', // Uso Exclusivo FEBRABAN / CNAB
                '226:240' => '0000000000000000', // Uso Exclusivo FEBRABAN / CNAB
            ),
            'headerLote' => array(
                '1:3' => '001', // codigo_banco 
                '4:7' => '0001', // lote_servico 
                '8:8' => '1', // tipo_registro 
                '9:9' => 'R', // tipo_operacao 
                '10:11' => '01', // tipo_servico 
                '12:13' => '00', // Uso Exclusivo FEBRABAN/CNAB
                '14:16' => '030', // versao_layout_lote 
                '17:17' => ' ', // uso_exclusivo_febraban_01 
                '18:18' => '2', // codigo_inscricao 
                '19:33' => '011222333444455', // numero_inscricao 
                '34:42' => '000123123', // codigo_convenio 
                '43:46' => '0014', // Cobrança cedente, Informar 0014 para cobrança
                '47:48' => '11', // carteira
                '49:51' => '345', // variaçã carteira
                '52:53' => '  ', // Use TS para testes
                '54:58' => '01234', // agencia 
                '59:59' => '3', // agencia_dv 
                '60:71' => '000000123456', // conta
                '72:72' => '5', // conta dv
                '73:73' => ' ', // uso_exclusivo_banco_02 
                '74:103' => 'Nome Fantasia da sua empresa  ', // nome_empresa 
                '104:143' => '                                        ', // mensagem_1 
                '144:183' => '                                        ', // mensagem_2 
                '184:191' => '00000001', // numero_sequencial_arquivo 
                '192:199' => '01022015', // data_geracao 
                '200:207' => '00000000', // data_credito 
                '208:240' => '                                 ', // uso_exclusivo_febraban_02 
            ),
            'segmentoP' => array(
                '1:3' => '001', // codigo_banco 
                '4:7' => '0001', // lote_servico 
                '8:8' => '3', // tipo_registro 
                '9:13' => '00001', // numero_sequencial_lote 
                '14:14' => 'P', // codigo_segmento 
                '15:15' => ' ', // uso_exclusivo_febraban_01 
                '16:17' => '01', // codigo_ocorrencia 
                '18:22' => '01234', // agencia 
                '23:23' => '3', // agencia_dv 
                '24:35' => '000000123456', // número da conta
                '36:36' => '5', // conta dv
                '37:37' => ' ', // Dígito Verificador da Ag/Conta (Não usado pelo BB)
                '38:57' => '123123123459        ', // nosso_numero 
                '58:58' => '1', // codigo_carteira 
                '59:59' => '2', // forma_cadastramento 
                '60:60' => '2', // tipo_documento 
                '61:61' => '2', // identificacao_emissao 
                '62:62' => '2', // identificacao_distribuicao 
                '63:77' => '12345678       ', // numero_documento 
                '78:85' => '03022015', // vencimento 
                '86:100' => '000000000010039', // valor_titulo 
                '101:105' => '00000', // agencia_cobradora 
                '106:106' => '0', // agencia_cobradora_dv 
                '107:108' => '02', // especie 
                '109:109' => 'N', // aceite 
                '110:117' => '14012015', // data_emissao 
                '118:118' => '1', // codigo_juros_mora 
                '119:126' => '04022015', // data_juros_mora
                '127:141' => '000000000000010', // valor_juros_mora 
                '142:142' => '1', // codigo_desconto_1 
                '143:150' => '09022015', // data_desconto_1 
                '151:165' => '000000000001000', // valor_desconto_1 
                '166:180' => '000000000000000', // valor_iof 
                '181:195' => '000000000000000', // valor_abatimento 
                '196:220' => '12345678                 ', // uso_empresa 
                '221:221' => '3', // codigo_protesto 
                '222:223' => '00', // prazo_protesto 
                '224:224' => '0', // codigo_baixa 
                '225:227' => '000', // prazo_baixa 
                '228:229' => '09', // codigo_moeda 
                '230:239' => '0000000000', // uso_exclusivo_banco_03 
                '240:240' => ' ', // uso_exclusivo_febraban_02 
            ),
            'segmentoQ' => array(
                '1:3' => '001', // codigo_banco 
                '4:7' => '0001', // lote_servico 
                '8:8' => '3', // tipo_registro 
                '9:13' => '00002', // numero_sequencial_lote 
                '14:14' => 'Q', // codigo_segmento 
                '15:15' => ' ', // uso_exclusivo_febraban_01 
                '16:17' => '01', // codigo_ocorrencia 
                '18:18' => '2', // sacado_codigo_inscricao 
                '19:33' => '021222333444455', // sacado_numero_inscricao 
                '34:73' => 'NOME DO CLIENTE                         ', // nome 
                '74:113' => 'LOGRADOURO DO CLIENTE                   ', // logradouro 
                '114:128' => 'BAIRRO DO CLIEN', // bairro 
                '129:136' => '00000111', // cep 
                '137:151' => 'CIDADE DO CLIEN', // cidade 
                '152:153' => 'BA', // estado 
                '154:154' => '2', // sacador_codigo_inscricao 
                '155:169' => '011222333444455', // sacador_numero_inscricao 
                '170:209' => 'Nome Fantasia da sua empresa            ', // sacador_nome 
                '210:212' => '   ', // uso_exclusivo_febraban_02 
                '213:232' => '                    ', // uso_exclusivo_febraban_03 
                '233:240' => '        ', // uso_exclusivo_febraban_04 
            ),
            'segmentoR' => array(
                '1:3' => '001', // codigo_banco 
                '4:7' => '0001', // lote_servico 
                '8:8' => '3', // tipo_registro 
                '9:13' => '00003', // numero_sequencial_lote 
                '14:14' => 'R', // codigo_segmento 
                '15:15' => ' ', // uso_exclusivo_febraban_01 
                '16:17' => '01', // codigo_ocorrencia 

                // campos não tratado pelo banco do brasil
                '18:65' => '                                                ', // uso_exclusivo_febraban_02 

                '66:66' => '1', // codigo_multa 
                '67:74' => '07022015', // data_multa 
                '75:89' => '000000000001120', // valor_multa 
                '90:99' => '          ', // informacao_sacado 
                '100:139' => '                                        ', // mensagem_3 
                '140:179' => '                                        ', // mensagem_4 
                '180:240' => '                                                             ', // uso_exclusivo_febraban_03 
            ),
            'trailerLote' => array(
                '1:3' => '001', // codigo_banco 
                '4:7' => '0001', // lote_servico 
                '8:8' => '5', // tipo_registro 
                '18:23' => '000005', // qtde_registro_lote 
                '24:240' => str_repeat(' ', 217), // uso_exclusivo_febraban_02
            ),
            'trailerArquivo' => array(
                '1:3' => '001', // codigo_banco 
                '4:7' => '9999', // lote_servico 
                '8:8' => '9', // tipo_registro 
                '9:17' => '         ', // uso_exclusivo_febraban01 
                '18:23' => '000001', // qtde_lotes 
                '24:29' => '000007', // qtde_registros 
                '30:35' => '      ', // uso_exclusivo_febraban02 
                '36:240' => '                                                                                                                                                                                                             ', // uso_exclusivo_febraban_03 
            ),
        );

        foreach ($asserts as $tipo => $campos) {
            $vname = "{$tipo}Text";
            foreach ($campos as $pos => $value) {
                $aux = explode(':', $pos);
                $start = $aux[0] - 1;
                $end = ($aux[1] - $aux[0]) + 1;
                $this->assertEquals($value, substr($$vname, $start, $end), "[ ] Campo $pos do $tipo");
            }
        }
    }
}
