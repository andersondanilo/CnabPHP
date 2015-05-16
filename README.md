CnabPHP
=======

[![Build Status](https://secure.travis-ci.org/andersondanilo/CnabPHP.png?branch=master)](http://travis-ci.org/andersondanilo/CnabPHP)
[![Latest Stable Version](https://poser.pugx.org/andersondanilo/cnab_php/v/stable.svg)](https://packagist.org/packages/andersondanilo/cnab_php)
[![Latest Unstable Version](https://poser.pugx.org/andersondanilo/cnab_php/v/unstable.svg)](https://packagist.org/packages/andersondanilo/cnab_php)
[![Code Climate](https://codeclimate.com/github/andersondanilo/CnabPHP/badges/gpa.svg)](https://codeclimate.com/github/andersondanilo/CnabPHP)
[![Test Coverage](https://codeclimate.com/github/andersondanilo/CnabPHP/badges/coverage.svg)](https://codeclimate.com/github/andersondanilo/CnabPHP/coverage)


Projeto para criar arquivos de remessas e processar arquivos de retorno no formato CNAB, utilizado nos bancos geralmente para boleto bancário.


## Funcionalidades

* Leitura e geração de arquivos de retorno e remessa nos formatos CNAB 240 e 400

| Banco           | Versão da Remessa  | Versão do Retorno  |
|-----------------|--------------------|--------------------|
| Banco do Brasil |                    | 240                |
| Bradesco        |                    | 240                |
| Caixa           | 240                | 240 e 400          |
| Itaú            | 400 (Falta testar) | 400                |
| Santander       |                    | 240                |

## Instalação
### Composer
Se você já conhece o **Composer**, adicione a dependência abaixo à diretiva *"require"* no seu **composer.json**:
```
"andersondanilo/cnab_php": "1.3.*"
```

## Como Usar
### Lendo um arquivo de Retorno
```php
$cnabFactory = new Cnab\Factory();
$arquivo = $cnabFactory->createRetorno('AQUI VAI O CAMINHO DO ARQUIVO DE RETORNO, EX: RET1010.RET');
$detalhes = $arquivo->listDetalhes();
foreach($detalhes as $detalhe) {
    if($detalhe->getValorRecebido() > 0) {
        $nossoNumero   = $detalhe->getNossoNumero();
        $valorRecebido = $detalhe->getValorRecebido();
        $dataPagamento = $detalhe->getDataOcorrencia();
        $carteira      = $detalhe->getCarteira();
        // você já tem as informações, pode dar baixa no boleto aqui
    }
}
```
### Criando um arquivo de remessa
```php
$codigo_banco = Cnab\Banco::CEF;
$arquivo = new Cnab\Remessa\Cnab400\Arquivo($codigo_banco);
$arquivo->configure(array(
	'data_geracao'  => new DateTime(),
	'data_gravacao' => new DateTime(), 
	'nome_fantasia' => 'Nome Fantasia da sua empresa', 
	'razao_social'  => 'Razão social da sua empresa', 
	'cnpj'          => 'CPNJ da sua empresa',
	'banco'         => $codigo_banco, //código do banco
	'logradouro'    => 'Logradouro da Sua empresa',
	'numero'        => 'Número do endereço',
	'bairro'        => 'Bairro da sua empresa', 
    'cidade'        => 'Cidade da sua empresa',
    'uf'            => 'Sigla da cidade, ex SP',
    'cep'           => 'CEP do endereço da sua cidade',
    'agencia'       => 'Agencia da conta',
    'conta'         => 'Número da conta',
    'operacao'      => 'Operação',
    'codigo_cedente'     => 'Código do Cedente',
    'codigo_cedente_dac' => 'Digito verificador do código do cedente',
));

// você pode adicionar vários boletos em uma remessa
$arquivo->insertDetalhe(array(
	'codigo_ocorrencia' => 1, // 1 = Entrada de título, futuramente poderemos ter uma constante
	'nosso_numero'      => 'Nosso número do boleto',
	'numero_documento'  => 'Seu Número / Número do Documento',
	'carteira'          => 'Carteira do Boleto',
	'especie'           => Cnab\Especie::CEF_OUTROS, // Você pode consultar as especies Cnab\Especie::CEF_OUTROS, futuramente poderemos ter uma tabela na documentação
	'valor'             => 100.39, // Valor do boleto
	'instrucao1'        => 2, // 1 = Protestar com (Prazo) dias, 2 = Devolver após (Prazo) dias, futuramente poderemos ter uma constante
	'instrucao2'        => 0, // preenchido com zeros
	'sacado_nome'       => 'Nome do cliente', // O Sacado é o cliente, preste atenção nos campos abaixo
	'sacado_tipo'       => 'cpf', //campo fixo, escreva 'cpf' (sim as letras cpf) se for pessoa fisica, cnpj se for pessoa juridica
	'sacado_cpf'        => 'CPF do Cliente',
	'sacado_logradouro' => 'Logradouro do cliente',
	'sacado_bairro'     => 'Bairro do cliente',
	'sacado_cep'        => 'CEP do cliente (somente numeros, sem hífen)',
	'sacado_cidade'     => 'Cidade do cliente',
	'sacado_uf'         => 'Sigla do estado do cliente',
	'data_vencimento'   => new DateTime('Data de vencimento do Boleto, ex: 2014-06-08'),
	'data_cadastro'     => new DateTime('Data de criação do Boleto, ex: 2014-06-01'),
	'juros_de_um_dia'     => 0.10, // Valor do juros de 1 dia'
	'data_desconto'       => new DateTime('Data limite para desconto, ex: 2014-06-01'),
	'valor_desconto'      => 10.0, // Valor do desconto
	'prazo'               => 10, // prazo de dias para o cliente pagar após o vencimento
	'taxa_de_permanencia' => '0', //00 = Acata Comissão por Dia (recomendável), 51 Acata Condições de Cadastramento na CAIXA
	'mensagem'            => 'Descrição do boleto',
	'data_multa'          => new DateTime('Data da multa, ex: 2014-06-09'), // data da multa
	'valor_multa'         => 10.0, // valor da multa
));

// para salvar
$arquivo->save('meunomedearquivo');
```

## Como Contribuir
Você pode contribuir com testes (unitários ou manuais), ou adaptando o formato para outro banco através do projeto cnab_yaml (https://github.com/andersondanilo/cnab_yaml) (que é utilidado pelo cnab_php).  [Leia a wiki](https://github.com/andersondanilo/CnabPHP/wiki)

## Licença
Este projeto esta sobre a licença MIT
