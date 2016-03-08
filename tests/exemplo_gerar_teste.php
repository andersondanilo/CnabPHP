<?php

// exemplo pré-gerar teste unitário

$dumpLine = function ($line) {
    foreach ($line->getFields() as $field) {
        echo "        '{$field->pos_start}:{$field->pos_end}' => '{$field->getEncoded()}', // {$field->nome} \n";
    }
};

echo "\n";
echo "$assets = array(\n";
echo "    'headerArquivo' => array(\n";
$dumpLine($arquivo->headerArquivo);
echo "    ),\n";
echo "    'headerLote' => array(\n";
$dumpLine($arquivo->headerLote);
echo "    ),\n";
echo "    'segmentoP' => array(\n";
$dumpLine($arquivo->detalhes[0]->segmento_p);
echo "    ),\n";
echo "    'segmentoQ' => array(\n";
$dumpLine($arquivo->detalhes[0]->segmento_q);
echo "    ),\n";
echo "    'segmentoR' => array(\n";
$dumpLine($arquivo->detalhes[0]->segmento_r);
echo "    ),\n";
echo "    'trailerLote' => array(\n";
$dumpLine($arquivo->trailerLote);
echo "    ),\n";
echo "    'trailerArquivo' => array(\n";
$dumpLine($arquivo->trailerArquivo);
echo "    ),\n";
