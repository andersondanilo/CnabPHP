<?php
include '../vendor/autoload.php';

$codigo_banco = Cnab\Banco::SANTANDER;
$arquivo = new Cnab\Remessa\Cnab240\Arquivo($codigo_banco);

$agencia = ''; //Preencher com a agencia
$codigo_cedente = ''; //Preencher com o codigo cedente

$arquivo->configure(array(
    'data_geracao'  => new DateTime(),
    'data_gravacao' => new DateTime(),
    'nome_fantasia' => '', // seu nome de empresa
    'razao_social'  => '',  // sua razão social
    'cnpj'          => '', // seu cnpj completo
    'banco'         => $codigo_banco, //código do banco
    'logradouro'    => '',
    'numero'        => '',
    'bairro'        => '',
    'cidade'        => '',
    'uf'            => '',
    'cep'           => '',
    'agencia'       => '',
    'agencia_dv'       => '',
    'conta'         => '', // número da conta
    'conta_dv'         => '', // número da conta
    'conta_dac'     => '', // digito da conta,
    'codigo_cedente' => '',
    'numero_sequencial_arquivo' => 1,
    'codigo_transmissao' => $agencia . $codigo_cedente
));

// você pode adicionar vários boletos em uma remessa
$arquivo->insertDetalhe(array(
    'codigo_ocorrencia'   => 1,
    'nosso_numero'        => '',
    'numero_documento'    => '',
    'carteira'            => '1',
    'especie'             => Cnab\Especie::SANTANDER_DUPLICATA_MERCANTIL, // Você pode consultar as especies Cnab\Especie
    'valor'               => 100.39, // Valor do boleto
    'instrucao1'          => 2, // 1 = Protestar com (Prazo) dias, 2 = Devolver após (Prazo) dias, futuramente poderemos ter uma constante
    'instrucao2'          => 0, // preenchido com zeros
    'sacado_nome'         => '', // O Sacado é o cliente, preste atenção nos campos abaixo
    'sacado_tipo'         => 'cpf', //campo fixo, escreva 'cpf' (sim as letras cpf) se for pessoa fisica, cnpj se for pessoa juridica
    'sacado_cpf'          => '',//cpf com mascara
    'sacado_logradouro'   => '',// Rua
    'sacado_bairro'       => '',// Bairro
    'sacado_cep'          => '', // sem hífem
    'sacado_cidade'       => '',//Cidade
    'sacado_uf'           => 'RS', //Sigla estado
    'data_vencimento'     => new DateTime('2017-02-28'),
    'data_cadastro'       => new DateTime('2017-02-01'),
    'juros_de_um_dia'     => 2.00, // Valor do juros de 1 dia'
    'data_desconto'       => new DateTime('2017-02-15'),
    'valor_desconto'      => 10.0, // Valor do desconto
    'prazo'               => 10, // prazo de dias para o cliente pagar após o vencimento
    'taxa_de_permanencia' => '0', //00 = Acata Comissão por Dia (recomendável), 51 Acata Condições de Cadastramento na CAIXA
    'mensagem'            => 'Mensalidade',
    'data_multa'          => new DateTime('2017-03-01'), // data da multa
    'valor_multa'         => 10.0, // valor da multa,
    'aceite'              =>'N',
    'registrado'          => 1,
    'codigo_protesto'     =>'0',
    'codigo_multa'        => 2,
    'codigo_juros_mora'   => 2
));
// para salvar
$arquivo->save('exemplo.txt');

?>