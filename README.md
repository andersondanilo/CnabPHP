CnabPHP
=======

[![Build Status](https://secure.travis-ci.org/andersondanilo/CnabPHP.png?branch=master)](http://travis-ci.org/andersondanilo/CnabPHP)
[![Latest Stable Version](https://poser.pugx.org/andersondanilo/cnab_php/v/stable.svg)](https://packagist.org/packages/andersondanilo/cnab_php)
[![Latest Unstable Version](https://poser.pugx.org/andersondanilo/cnab_php/v/unstable.svg)](https://packagist.org/packages/andersondanilo/cnab_php)

Projeto para criar arquivos de remessas e processar arquivos de retorno no formato CNAB, utilizado nos bancos geralmente para boleto bancário.

## Funcionalidades

* Leitura de arquivos de retorno no formato Cnab 240 e 400
* Criação de arquivos de remessa no formato Cnab 400

## Instalação
### Composer
Se você já conhece o **Composer**, adicione a dependência abaixo à diretiva *"require"* no seu **composer.json**:
```
"andersondanilo/CnabPHP": "1.1.*"
```

## Como Usar
### Lendo um arquivo de Retorno
```
$cnabFactory = new Cnab\Factory();
$arquivo = $cnabFactory->createRetorno('AQUI VAI O CAMINHO DO ARQUIVO DE RETORNO, EX: RET1010.RET');
$detalhes = $arquivo->listDetalhes();
foreach($detalhes as $detalhe) {
    if($detalhe->getValorRecebido() > 0) {
        $nossoNumero   = $detalhe->getNossoNumero();
        $valorRecebido = $detalhe->getValorRecebido();
        $dataPagamento = $detalhe->getDataOcorrencia();
        $carteira      = $detalhe->getCarteira();
    }
}
```

## Como Contribuir
Você pode contribuir com testes (unitários ou manuais), ou adaptando o formato para outro banco através do projeto cnab_yaml (https://github.com/andersondanilo/cnab_yaml) (que é utilidado pelo cnab_php)

## Licença
Este projeto esta sobre a licença MIT
