<?php
namespace Cnab\Format;

class Picture
{
	const REGEX_VALID_FORMAT = '/(?P<tipo1>X|9)\((?P<tamanho1>[0-9]+)\)(?P<tipo2>(V9)?)\(?(?P<tamanho2>([0-9]+)?)\)?/';
	
	public static function validarFormato($format)
	{
		if(\preg_match(self::REGEX_VALID_FORMAT, $format))
			return true;
		else
			return false;
	}

	public static function getLength($format)
	{
		$m = array();
		if(preg_match(self::REGEX_VALID_FORMAT, $format, $m))
			return ((int)$m['tamanho1'] + (int)$m['tamanho2']);
		else
			throw new \InvalidArgumentException("'$format' is not a valid format");
	}
	
	public static function encode($value, $format)
	{
		$m = array();
		if(\preg_match(self::REGEX_VALID_FORMAT, $format, $m))
		{
			if($m['tipo1'] == 'X' && !$m['tipo2'])
			{
				$value = \substr($value, 0, $m['tamanho1']);
				return \str_pad($value, (int)$m['tamanho1'], ' ', STR_PAD_RIGHT);
				
			}
			else if($m['tipo1'] == '9')
			{
				if((int)$m['tamanho1'] == 8 && $value instanceof \DateTime)
				{
					$value = $value->format('dmY');
				}

				if((int)$m['tamanho1'] == 6 && $value instanceof \DateTime)
				{
					$value = $value->format('dmy');
				}
				
				if(!is_numeric($value))
					throw new \Exception("value '$value' dont is a number, need format $format");
					
				$value = (string)(round($value, 2));
				$exp   = explode('.', $value);
				if(!isset($exp[1]))
					$exp[1] = 0;
				if($m['tipo2']=='V9')
				{
					$tamanho_left  = (int)$m['tamanho1'];
					$tamanho_right = (int)$m['tamanho2'];
					$valor_left    = \str_pad($exp[0], $tamanho_left, '0', STR_PAD_LEFT);
					$valor_right   = \str_pad($exp[1], $tamanho_right, '0', STR_PAD_RIGHT);
					return $valor_left.$valor_right;
				}
				else if(!$m['tipo2'])
				{
					$value = (int)$value;
					$value = (string)$value;
					return \str_pad($value, (int)$m['tamanho1'], '0', STR_PAD_LEFT);
					
				}
				else
					throw new \InvalidArgumentException("'$format' is not a valid format");			
			}		
		}
		else
			throw new \InvalidArgumentException("'$format' is not a valid format");
	}
	
	public static function decode($value, $format)
	{
		$m = array();
		if(preg_match(self::REGEX_VALID_FORMAT, $format, $m))
		{
			if($m['tipo1'] == 'X' && !$m['tipo2'])
			{
				return rtrim($value);
			}
			else if($m['tipo1'] == '9')
			{					
				if($m['tipo2']=='V9')
				{
					$tamanho_left  = (int)$m['tamanho1'];
					$tamanho_right = (int)$m['tamanho2'];
					$valor_left    = (int)substr($value, 0, $tamanho_left); 
					$valor_right   = (int)substr($value, $tamanho_left, $tamanho_right);
					return (double)($valor_left.'.'.$valor_right);
				}
				else if(!$m['tipo2'])
				{
					return (int)$value;
				}
				else
					throw new \InvalidArgumentException("'$format' is not a valid format");			
			}		
		}
		else
			throw new \InvalidArgumentException("'$format' is not a valid format");
	}
}