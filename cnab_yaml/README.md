cnab_yaml
=========

O Objetivo deste projeto é fornecer arquivos Yaml com a estrutura dos arquivos Cnab240 e Cnab400, atualmente temos os Cnab240 da Caixa e o Cnab400 da Caixa e do Itaú

Como posso contribuir
---------------------
Você pode contribuir lendo a documentação do seu banco e criando um arquivo yaml com base nela

E para que isso serve?
----------------------
Esse projeto é usado para ser base para outros projeto, como por exempo o CnabPHP, cnab_python e
muitos outros que poderão ser criados a partir deste projeto

O que eu preciso saber
----------------------
* Utilizamos nomes simples para o campo, por exemplo para "Código do banco" utilize o "codigo_banco" (com underline e sem o "do")
* Para definir o tipo do campo utilizamos uma Picture

O que é uma Picture
-------------------
Essa Picture foi baseada na documentação do itaú, disponível em http://download.itau.com.br/bankline/layout_cobranca_400bytes_cnab_itau_mensagem.pdf

Cada registro é formado por campos que são apresentados em dois formatos:
* Alfanumérico (picture X): alinhados à esquerda com brancos à direita. Preferencialmente, todos os caracteres devem ser maiúsculos. Aconselhase a não utilização de caracteres especiais (ex.: “Ç”, “?”,, etc) e acentuação gráfica (ex.: “Á”, “É”, “Ê”, etc) e os campos não utiliza dos deverão ser preenchidos com brancos.
* Numérico (picture 9): alinhado à direita com zeros à esquerda e os campos não utilizados deverão ser preenchidos com zeros. - Vírgula assumida (picture V): indica a posição da vírgula dentro de um campo numérico. E xemplo: num campo com picture “9(5)V9(2)”, o número “876,54” será representado por “0087654”

Exemplo de Arquivo
------------------
```yaml
generic:
  # Registro Header de Lote

  # Baseado na documentação da Caixa
  # Disponível em: http://downloads.caixa.gov.br/_arquivos/cobrcaixasicob/manuaissicob/CNAB_240_SICOB.pdf (Acesso em  23/04/2014)

  codigo_banco:
    # Código fornecido pelo Banco Central para identificação do Banco que está recebendo ou enviando o
    # arquivo, com o qual se firmou o contrato de prestação de serviços.
    # CAIXA ECONÔMICA FEDERAL = ‘104’
    pos: [1, 3]
    picture: '9(3)' # isso significa: campo númerico, 3 digitos, preenchido com 0 a direita

  lote_servico:
    # Lote de Serviço
    # Número seqüencial para identificar cada lote de serviço.
    # Preencher com '0001' para o primeiro lote do arquivo. Para os demais: número do lote anterior
    # acrescido de 1. Deve ser o mesmo número dentro do lote. O número não poderá ser repetido dentro
    # do arquivo.
    # Se registro for Header do Arquivo = '0000'
    # Se registro for Trailer do Arquivo = '9999'
    pos: [4, 7]
    picture: '9(4)'

104:
  # Os seguintes campos são exclusivos da Caixa econômica federal (Código do Banco: 104)
  data_exemplo:
    pos: [8, 15]
    picture: '9(8)'
    date_format: '%d%m%Y' # mesmo padrão usado por linguagens como python e ruby

  valor_exemplo:
    pos: [16, 25]
    picture: '9(8)V9(2)' # isso significa 8 posições para a numero inteiro, mais 2 posições para as casas decimais

341:
  # Os seguintes campos são exclusivos do Itaú (Código do Banco 341)

  data_exemplo:
    pos: [8, 13]
    picture: '9(6)'
    date_format: '%d%m%y'

  outro_campo:
    pos: [14, 15]
    picture: 'X(2)' # Isso significa campo de texto com 2 caracteres (preenchido com espaço a direita)
    default: 'T' # Valor padrão do campo 
```
