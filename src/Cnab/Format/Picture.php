<?php

namespace Cnab\Format;

class Picture
{
    const REGEX_VALID_FORMAT = '/(?P<tipo1>X|9)\((?P<tamanho1>[0-9]+)\)(?P<tipo2>(V9)?)\(?(?P<tamanho2>([0-9]+)?)\)?/';

    public static function validarFormato($format)
    {
        if (\preg_match(self::REGEX_VALID_FORMAT, $format)) {
            return true;
        } else {
            return false;
        }
    }

    public static function getLength($format)
    {
        $m = array();
        if (preg_match(self::REGEX_VALID_FORMAT, $format, $m)) {
            return (int) $m['tamanho1'] + (int) $m['tamanho2'];
        } else {
            throw new \InvalidArgumentException("'$format' is not a valid format");
        }
    }

    public static function parseNumber($value)
    {
        $value = preg_replace('/[^0-9.]/', '', $value);
        $value = preg_replace('/^0+/', '', $value);
        if ($value) {
            return $value;
        } else {
            return '0';
        }
    }

    public static function encode($value, $format, $options)
    {
        $m = array();
        if (\preg_match(self::REGEX_VALID_FORMAT, $format, $m)) {
            if ($m['tipo1'] == 'X' && !$m['tipo2']) {
                $value = \substr($value, 0, $m['tamanho1']);

                return \str_pad($value, (int) $m['tamanho1'], ' ', STR_PAD_RIGHT);
            } elseif ($m['tipo1'] == '9') {
                if ($value instanceof \DateTime) {
                    if (@$options['date_format']) {
                        $value = strftime($options['date_format'], $value->getTimestamp());
                    } else {
                        if ((int) $m['tamanho1'] == 8) {
                            $value = $value->format('dmY');
                        }

                        if ((int) $m['tamanho1'] == 6) {
                            $value = $value->format('dmy');
                        }
                    }
                }

                if (!is_numeric($value)) {
                    throw new \Exception("value '$value' dont is a number, need format $format");
                }

                $value = self::parseNumber($value);
                $exp = explode('.', $value);
                if (!isset($exp[1])) {
                    $exp[1] = 0;
                }
                if ($m['tipo2'] == 'V9') {
                    $tamanho_left = (int) $m['tamanho1'];
                    $tamanho_right = (int) $m['tamanho2'];
                    $valor_left = \str_pad($exp[0], $tamanho_left, '0', STR_PAD_LEFT);
                    if (strlen($exp[1]) > $tamanho_right) {
                        $extra = strlen($exp[1]) - $tamanho_right;
                        $extraPow = pow(10, $extra);
                        $exp[1] = round($exp[1] / $extraPow);
                    }
                    $valor_right = \str_pad($exp[1], $tamanho_right, '0', STR_PAD_RIGHT);

                    return $valor_left.$valor_right;
                } elseif (!$m['tipo2']) {
                    $value = self::parseNumber($value);

                    return \str_pad($value, (int) $m['tamanho1'], '0', STR_PAD_LEFT);
                } else {
                    throw new \InvalidArgumentException("'$format' is not a valid format");
                }
            }
        } else {
            throw new \InvalidArgumentException("'$format' is not a valid format");
        }
    }

    public static function decode($value, $format, $options)
    {
        $m = array();
        if (preg_match(self::REGEX_VALID_FORMAT, $format, $m)) {
            if ($m['tipo1'] == 'X' && !$m['tipo2']) {
                return rtrim($value);
            } elseif ($m['tipo1'] == '9') {
                if ($m['tipo2'] == 'V9') {
                    $tamanho_left = (int) $m['tamanho1'];
                    $tamanho_right = (int) $m['tamanho2'];
                    $valor_left = self::parseNumber(substr($value, 0, $tamanho_left));
                    $valor_right = '0.'.substr($value, $tamanho_left, $tamanho_right);
                    if ((double) $valor_right > 0) {
                        return $valor_left + (double) $valor_right;
                    } else {
                        return self::parseNumber($valor_left);
                    }
                } elseif (!$m['tipo2']) {
                    return self::parseNumber($value);
                } else {
                    throw new \InvalidArgumentException("'$format' is not a valid format");
                }
            }
        } else {
            throw new \InvalidArgumentException("'$format' is not a valid format");
        }
    }
}
