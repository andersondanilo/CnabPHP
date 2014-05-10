<?php
namespace Cnab;
class Banco
{
	const ITAU = 341;
	const CEF  = 104;

	public static function getBanco($codigo)
	{
		if($codigo == self::ITAU)
		{
			return array(
				'codigo_do_banco' => '341',
				'nome_do_banco' => 'BANCO ITAU SA',
			);
		}
		else if($codigo == self::CEF)
		{
			return array(
				'codigo_do_banco' => '104',
				'nome_do_banco' => 'C ECON FEDERAL',
			);
		}
		else
			return false;
    }

    public static function existBanco($codigo_banco)
    {
        $banco = self::getBanco($codigo_banco);
        return $banco ? true : false;
    }
}
