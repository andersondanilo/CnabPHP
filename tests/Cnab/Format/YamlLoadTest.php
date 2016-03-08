<?php

namespace Cnab\Tests\Format;

use Cnab\Format\YamlLoad;
use Cnab\Format\Linha;

define('CNAB_FIXTURE_PATH', dirname(__FILE__).'/../../fixtures/yaml');

class YamlLoadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException DomainException
     * @expectedExceptionMessage O campo codigo_banco colide com o campo tipo_registro
     */
    public function testEmiteExceptionEmCamposComColisao()
    {
        $yamlLoad = new YamlLoad(0);

        $fields = array(
            'codigo_banco' => array(
                'pos' => array(1, 3),
            ),
            'tipo_registro' => array(
                'pos' => array(1, 4),
            ),
        );

        $yamlLoad->validateCollision($fields);
    }

    public function testNaoEmiteExceptionEmCamposSemColisao()
    {
        $yamlLoad = new YamlLoad(0);

        $fields1 = array(
            'codigo_banco' => array(
                'pos' => array(1, 3),
            ),
            'tipo_registro' => array(
                'pos' => array(4, 4),
            ),
        );

        $fields2 = array(
            'codigo_banco' => array(
                'pos' => array(1, 3),
            ),
        );

        $this->assertTrue($yamlLoad->validateCollision($fields1));
        $this->assertTrue($yamlLoad->validateCollision($fields2));
    }

    /**
     * @expectedException DomainException
     */
    public function testEmiteExceptionEmArrayMalformado()
    {
        $array = array(
            'generic' => array(
                'codigo_banco' => array(
                    'pos' => array(1, 3),
                    'picture' => '',
                ),
                'tipo_registro' => array(
                    'pos' => array(4, 4),
                    'picture' => '',
                ),
            ),
            '033' => array(
                'nome_empresa' => array(
                    'pos' => array(40, 80),
                    'picture' => '',
                ),
                'numero_inscricao' => array(
                    'pos' => array(79, 80),
                    'picture' => '',
                ),
            ),
        );

        $yamlLoad = new YamlLoad(0);
        $yamlLoad->validateArray($array);
    }

    public function testNaoEmiteExceptionEmArrayValido()
    {
        $array = array(
            'generic' => array(
                'codigo_banco' => array(
                    'pos' => array(1, 3),
                    'picture' => '',
                ),
                'tipo_registro' => array(
                    'pos' => array(4, 4),
                    'picture' => '',
                ),
            ),
            '033' => array(
                'nome_empresa' => array(
                    'pos' => array(40, 80),
                    'picture' => '',
                ),
                'numero_inscricao' => array(
                    'pos' => array(81, 81),
                    'picture' => '',
                ),
            ),
        );

        $yamlLoad = new YamlLoad(0);
        $this->assertTrue($yamlLoad->validateArray($array));
    }

    public function testBuscaFormatoGenericoEEspecifico()
    {
        $yamlLoad = $this->getMockBuilder('\Cnab\Format\YamlLoad')
                         ->setMethods(array('loadYaml'))
                         ->setConstructorArgs(array(33))
                         ->getMock();

        $testFormat = array(
            'codigo_banco' => array(
                'pos' => array(1, 3),
                'picture' => '9(3)',
            ),
        );

        $yamlLoad->expects($this->at(0))
                 ->method('loadYaml')
                 ->with(
                    $this->equalTo($yamlLoad->formatPath.'/cnab240/generic/header_lote.yml')
                )
                ->will($this->returnValue($testFormat));

        $yamlLoad->expects($this->at(1))
                 ->method('loadYaml')
                 ->with(
                    $this->equalTo($yamlLoad->formatPath.'/cnab240/033/header_lote.yml')
                )
                ->will($this->returnValue($testFormat));

        $linha = new Linha();
        $yamlLoad->load($linha, 'cnab240', 'header_lote');
    }
}
