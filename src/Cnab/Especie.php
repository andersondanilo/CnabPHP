<?php
// espécies de cobranças
namespace Cnab;

class Especie
{
	const ITAU_DUPLICATA_MERCANTIL  = 1;
	const ITAU_NOTA_PROMISSORIA     = 2;
	const ITAU_NOTA_DE_SEGURO       = 3;
	const ITAU_MENSALIDADE_ESCOLAR  = 4;
	const ITAU_RECIBO               = 5;
	const ITAU_CONTRATO             = 6;
	const ITAU_COSSEGUROS           = 7;
	const ITAU_DUPLICATA_DE_SERVICO = 8;
	const ITAU_LETRA_DE_CAMBIO      = 9;
	const ITAU_NOTA_DE_DEBITOS      = 13;
	const ITAU_DOCUMENTO_DE_DIVIDA  = 15;
	const ITAU_ENCARGOS_CONDOMINIAIS = 16;
	const ITAU_CONTRATO_DE_PRESTACAO_DE_SERVICOS = 17;
	const ITAU_DIVERSOS              = 99;

	const CEF_DUPLICATA_MERCANTIL = 1;
	const CEF_NOTA_PROMISSORIA = 2;
	const CEF_DUPLICATA_DE_PRESTACAO_DE_SERVICOS = 3;
	const CEF_NOTA_DE_SEGURO = 5;
	const CEF_LETRA_DE_CAMBIO = 6;
	const CEF_OUTROS = 9;

	const CNAB240_OUTROS = 99; 
}