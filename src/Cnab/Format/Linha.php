<?php

namespace Cnab\Format;

class Linha
{
    private $fields = array();
    public $last_error = false;

    public function __set($key, $valor)
    {
        if (\array_key_exists($key, $this->fields)) {
            $this->fields[$key]->set($valor);
        } else {
            throw new \InvalidArgumentException("field '$key' dont exists");
        }
    }

    public function __get($key)
    {
        if (\array_key_exists($key, $this->fields)) {
            return $this->fields[$key]->getValue();
        } else {
            throw new \InvalidArgumentException("field '$key' dont exists");
        }
    }

    public static function cmpSortFields(Field $field1, Field $field2)
    {
        return $field1->pos_start > $field2->pos_start ? 1 : -1;
    }

    public function addField($nome, $pos_start, $pos_end, $format, $default, $options)
    {
        foreach ($this->fields as $key => $field) {
            $current_pos_start = $field->pos_start;
            $current_pos_end = $field->pos_end;

            if (($pos_start >= $current_pos_start && $pos_start <= $current_pos_end) ||
                 ($pos_end <= $current_pos_end && $pos_end >= $current_pos_start) ||
                 ($current_pos_start >= $pos_start && $current_pos_start <= $pos_end) ||
                 ($current_pos_end <= $pos_end && $current_pos_end >= $pos_start)) {
                unset($this->fields[$key]);
            }
        }

        $this->fields[$nome] = new Field($this, $nome, $format, $pos_start, $pos_end, $options);
        if ($default !== false) {
            $this->fields[$nome]->set($default);
        }
    }

    public function loadFromString($text)
    {
        foreach ($this->fields as $field) {
            $field->set(Picture::decode(\substr($text, $field->pos_start - 1, $field->length), $field->format, $field->options));
        }
    }

    public function getEncoded()
    {
        if ($this->validate()) {
            $max_pos_end = 0;
            $dados = '';
            $fields = $this->fields;
            usort($fields, 'self::cmpSortFields');
            $lastField = null;
            foreach ($fields as $field) {
                if ($lastField && $field->pos_start != $lastField->pos_end + 1) {
                    throw new \Exception("gap between {$lastField->nome} and {$field->nome}");
                }
                $dados .= $field->getEncoded();
                if ($field->pos_end > $max_pos_end) {
                    $max_pos_end = $field->pos_end;
                }
                $lastField = $field;
            }

            if (strlen($dados) != $max_pos_end) {
                throw new \Exception('line length is '.\strlen($dados)." and max pos_end is $max_pos_end");
            }

            return $dados;
        } else {
            return false;
        }
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function validate()
    {
        foreach ($this->fields as $fieldNome => $field) {
            if ($field->getValue() === null || $field->getValue() === false) {
                $this->last_error = "$fieldNome dont be null or false";

                return false;
            }
        }

        return true;
    }

    public function existField($name)
    {
        $exist = \array_key_exists($name, $this->fields);

        return $exist ? true : false;
    }

    public function dump()
    {
        $dump = '';
        $dump .= PHP_EOL;
        $dump .= '============= Dump ==============';
        $dump .= PHP_EOL;
        foreach ($this->fields as $fieldNome => $field) {
            $dump .= $fieldNome.': ';
            $dump .= $field->getValue();
            $dump .= PHP_EOL;
        }

        return $dump;
    }
}
