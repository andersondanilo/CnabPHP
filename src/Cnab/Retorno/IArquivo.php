<?php

namespace Cnab\Retorno;

interface IArquivo
{
    /**
     * Retorna todos os detatles.
     *
     * @return iRetornoDetalhe[]
     */
    public function listDetalhes();

    /**
     * Retorna o numero da conta.
     *
     * @return string
     */
    public function getConta();

    /**
     * Retorna o digito de auto conferencia da conta.
     *
     * @return string
     */
    public function getContaDac();

    /**
     * Retorna o codigo do banco.
     *
     * @return string
     */
    public function getCodigoBanco();

    /**
     * Retorna a data de geração do arquivo.
     *
     * @return \DateTime
     */
    public function getDataGeracao();

    /**
     * Retorna o objeto DateTime da data crédito do arquivo
     * Poderá ser removido, pois em alguns bancos essa data só aparece no detalhe.
     *
     * @return \DateTime
     */
    public function getDataCredito();
}
