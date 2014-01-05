<?php
require_once dirname(__FILE__).'/Field.php';

class Cnab_Format_Field {
	private $cnabLinha;
	private $nome;
	public  $format;
	private $valor_decoded;
	private $valor_encoded = null;
	
	public $pos_start;
	public $pos_end;
	public $length;
	
	public function __construct(Cnab_Format_Linha $linha, $nome, $format, $pos_start, $pos_end)
	{
		if(!Cnab_Format_Picture::validarFormato($format))
			throw new InvalidArgumentException("'$format' is not a valid format");
			
		$this->nome         = $nome;
		$this->cnabLinha    = $linha;
		$this->format       = $format;
		$this->pos_start    = $pos_start;
		$this->pos_end      = $pos_end;
		$this->length       = ($pos_end + 1) - $pos_start;
		
		$p_length = Cnab_Format_Picture::getLength($this->format);
		if($p_length > $this->length)
			throw new Exception("Picture length of '$this->nome' need more positions than  $pos_start : $pos_end");
		else if($p_length < $this->length)
			throw new Exception("Picture length of '$this->nome' need less positions than  $pos_start : $pos_end");
	}

	public function set($valor)
	{
		if($valor === false || is_null($valor))
			throw new Exception("'$this->nome' dont be false or null");
			
		$this->valor_decoded = $valor;
		
		try 
		{
			$this->valor_encoded = Cnab_Format_Picture::encode($valor, $this->format);	
		}
		catch(Exception $e)
		{
			echo "Error in field '$this->nome': " . $e->getMessage();
		}
		
		$len = strlen($this->valor_encoded);
		if($len != $this->length)
			throw new Exception("'$this->nome' have length '$len', but field need length $this->length");
	}
	
	public function getValue()
	{		
		return $this->valor_decoded;
	}
	
	public function getEncoded()
	{
		if(is_null($this->valor_encoded))
			throw new Exception("'$this->nome' dont be null, need to set any value");
		return $this->valor_encoded;
	}
}