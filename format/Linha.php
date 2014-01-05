<?php
require_once dirname(__FILE__).'/Picture.php';
require_once dirname(__FILE__).'/Field.php';

class Cnab_Format_Linha {
	private $fields     = array();
	public  $last_error = false;
	
	public function __set($key, $valor)
	{
		if(array_key_exists($key, $this->fields))
			$this->fields[$key]->set($valor);
		else
			throw new InvalidArgumentException("'$key' dont exists");
	}
	
	public function __get($key)
	{
		if(array_key_exists($key, $this->fields))
			return $this->fields[$key]->getValue();
		else
			throw new InvalidArgumentException("'$key' dont exists");
	}
	
	public static function cmpSortFields(Cnab_Format_Field $field1, Cnab_Format_Field $field2)
	{
		return $field1->pos_start > $field2->pos_start ? 1 : -1;
	}
	
	public function addField($nome, $pos_start, $pos_end, $format, $default)
	{
		if(array_key_exists($nome, $this->fields))
			throw new Exception(" Already exist field with name $nome ");
			
		foreach($this->fields as $field)
		{
			if($pos_start >= $field->pos_start && $pos_end <= $field->pos_end)
				throw new Exception(" Already exist field at position $pos_start : $pos_end ");
		}
		
		$this->fields[$nome] = new Cnab_Format_Field($this, $nome, $format, $pos_start, $pos_end);
		if($default !== false)
			$this->fields[$nome]->set($default);
	}
	
	public function loadFromString($text)
	{
		foreach($this->fields as $field)
		{
			$field->set(Cnab_Format_Picture::decode(substr($text, $field->pos_start - 1, $field->length), $field->format));
		}
	}
	
	public function getEncoded()
	{
		if($this->validate())
		{
			$max_pos_end = 0;
			$dados = "";
			$fields = $this->fields;
			usort($fields, 'self::cmpSortFields');
			foreach($fields as $field)
			{
				$dados .= $field->getEncoded();
				if($field->pos_end > $max_pos_end)
					$max_pos_end = $field->pos_end; 
			}
			
			if(strlen($dados) <> $max_pos_end)
				throw new Exception("length of dados is " . strlen($dados) . " and max pos_end id $max_pos_end");
			
			return $dados;
		}
		else
			return false;
	}
	
	public function validate()
	{
		foreach($this->fields as $fieldNome => $field)
			if($field->getValue() === null || $field->getValue() === false)
			{
				$this->last_error = "$fieldNome dont be null or false"; 
				return false;
			}
		return true;
	}
}