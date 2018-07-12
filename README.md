CnabPHP
=======

[![Join the chat at https://gitter.im/andersondanilo/CnabPHP](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/andersondanilo/CnabPHP?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

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
| Banco do Brasil | 240 (Beta)         | 240 e 400          |
| Bradesco        |                    | 240                |
| Caixa           | 240                | 240 e 400          |
| Itaú            | 400                | 400                |
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

Consulte na wiki: https://github.com/andersondanilo/CnabPHP/wiki/Criando-um-arquivo-de-remessa

## Como Contribuir
Você pode contribuir com testes (unitários ou manuais), ou adaptando o formato para outro banco através do projeto cnab_yaml (https://github.com/andersondanilo/cnab_yaml) (que é utilizado pelo cnab_php).  [Leia a wiki](https://github.com/andersondanilo/CnabPHP/wiki)

O projeto está usando o Waffle para gerenciar o status da impleentação de novas remessas e retornos:

[![Stories in Ready](https://badge.waffle.io/andersondanilo/CnabPHP.png?label=ready&title=Ready)](http://waffle.io/andersondanilo/CnabPHP)


## Licença
Este projeto esta sobre a licença MIT
