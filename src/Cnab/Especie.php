<?php
// espécies de cobranças
namespace Cnab;

class Especie
{
    /*
        Código adotado pela FEBRABAN para identificar o tipo de título de cobrança.
        Domínio:
            '01' = CH Cheque
            '02' = DM Duplicata Mercantil
            '03' = DMI Duplicata Mercantil p/ Indicação
            '04' = DS Duplicata de Serviço
            '05' = DSI Duplicata de Serviço p/ Indicação
            '06' = DR Duplicata Rural
            '07' = LC Letra de Câmbio
            '08' = NCC Nota de Crédito Comercial
            '09' = NCE Nota de Crédito a Exportação
            '10' = NCI Nota de Crédito Industrial
            '11' = NCR Nota de Crédito Rural
            '12' = NP Nota Promissória
            '13' = NPR Nota Promissória Rural
            '14' = TM Triplicata Mercantil
            '15' = TS Triplicata de Serviço
            '16' = NS Nota de Seguro
            '17' = RC Recibo
            '18' = FAT Fatura
            '19' = ND Nota de Débito
            '20' = AP Apólice de Seguro
            '21' = ME Mensalidade Escolar
            '22' = PC Parcela de Consórcio
            '23' = NF Nota Fiscal
            '24' = DD Documento de Dívida
            '25' = Cédula de Produto Rural
            '26' = Warrant
            '27' = Dívida Ativa de Estado
            '28' = Dívida Ativa de Município
            '29' = Dívida Ativa da União
            '30' = Encargos condominiais
            '31' = CC Cartão de Crédito
            '99' = Outros
    */

    // Itaú
    const ITAU_DUPLICATA_MERCANTIL = '01';
    const ITAU_NOTA_PROMISSORIA = '02';
    const ITAU_NOTA_DE_SEGURO = '03';
    const ITAU_MENSALIDADE_ESCOLAR = '04';
    const ITAU_RECIBO = '05';
    const ITAU_CONTRATO = '06';
    const ITAU_COSSEGUROS = '07';
    const ITAU_DUPLICATA_DE_SERVICO = '08';
    const ITAU_LETRA_DE_CAMBIO = '09';
    const ITAU_NOTA_DE_DEBITOS = '13';
    const ITAU_DOCUMENTO_DE_DIVIDA = '15';
    const ITAU_ENCARGOS_CONDOMINIAIS = '16';
    const ITAU_CONTRATO_DE_PRESTACAO_DE_SERVICOS = '17';
    const ITAU_DIVERSOS = '99';

    // Caixa
    const CEF_DUPLICATA_MERCANTIL = 2;
    const CEF_NOTA_PROMISSORIA = 12;
    const CEF_DUPLICATA_DE_PRESTACAO_DE_SERVICOS = 4;
    const CEF_NOTA_DE_SEGURO = 16;
    const CEF_LETRA_DE_CAMBIO = 7;
    const CEF_OUTROS = 99;

    // Banco do Brasil
    /*
        Para carteira 11 e 17 modalidade Simples, pode ser usado:
            01 - Cheque,
            02 - Duplicata Mercantil,
            04 - Duplicata de Serviço,
            06 - Duplicata Rural,
            07 - Letra de Câmbio,
            12 - Nota Promissória,
            17 - Recibo,
            19 - Nota de Debito,
            26 - Warrant,
            27 - Dívida Ativa de Estado,
            28 - Divida Ativa de Município
            29 - Dívida Ativa União.

        Para carteira 12 (moeda variável) pode ser usado:
            02 - Duplicata Mercantil,
            04 - Duplicata de Serviço,
            07 - Letra de Câmbio,
            12 - Nota Promissória,
            17 - Recibo
            19 - Nota de Débito.

        Para carteira 15 (prêmio de seguro) pode ser usado:
            16 - Nota de Seguro
            20 - Apólice de Seguro.

        Para carteira 11/17 modalidade Vinculada e carteira 31, pode ser usado:
            02 - Duplicata Mercantil
            04 - Duplicata de Serviço.

        Para carteira 11/17 modalidade Descontada e carteira 51, pode ser usado:
            02 - Duplicata Mercantil,
            04 - Duplicata de Serviço,
            07 - Letra de Câmbio.

        Obs.: O Banco do Brasil encaminha para protesto os seguintes títulos:
            02 - Duplicata Mercantil
            04 - Duplicata de Serviço
            06 - Duplicata Rural
            07 - Letra de Câmbio
            27 - Certidão de Dívida Ativa dos Estados
            28 - Certidão de Dívida Ativa do Município
            29 - Certidão de Dívida Ativa da União
     */


    const BB_CHEQUE = 1;
    const BB_DUPLICATA_MERCANTIL = 2;
    const BB_DUPLICATA_DE_SERVICO = 4;

    const CNAB240_OUTROS = '99';
}
