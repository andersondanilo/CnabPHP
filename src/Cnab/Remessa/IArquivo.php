<?php

namespace Cnab\Remessa;

interface IArquivo
{
    /**
     * Configura alguns parametros básicos como conta, agência, cnpj e etc.
     *
     * @param array $params
     */
    public function configure(array $params);

    /**
     * Adiciona um detalhe (boleto, pedido de baixa, etc).
     *
     * @param array $params dados de detalhe
     */
    public function insertDetalhe(array $params);

    /**
     * lista todos os detalhes que foram adicionados.
     *
     * @return array
     */
    public function listDetalhes();

    /**
     * Salva a remessa em algum arquivo de texto.
     *
     * @param string $filename nome do arquivo que será criado
     */
    public function save($filename);

    /**
     * Retorna o texto que será salvo no arquivo de texto da remessa.
     *
     * @return string
     */
    public function getText();
}
