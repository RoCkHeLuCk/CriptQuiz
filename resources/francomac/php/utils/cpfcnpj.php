<?php

/**
 *   Validate CPF
 *
 *   @method   validateCPF
 *   @param    string        $cpf
 *   @return   bool
 */
function validateCPF(string $cpf) : bool
{
   for($i = 0;$i <= 9;$i++)
   {
      if ($cpf == str_repeat($i,11))
      {
         return false;
      }
   }

	for ($t = 9; $t < 11; $t++)
	{
		for ($d = 0, $c = 0; $c < $t; $c++)
		{
			$d += $cpf{$c} * (($t + 1) - $c);
		}

		$d = ((10 * $d) % 11) % 10;

		if ($cpf{$c} != $d)
		{
			return false;
		}
	}
	return true;
}

/**
 *   Validate CNPJ
 *
 *   @method   validateCNPJ
 *   @param    string         $cnpj
 *   @return   bool
 */
function validateCNPJ(string $cnpj) : bool
{
   for($i = 0;$i <= 9;$i++)
   {
      if ($cnpj == str_repeat($i,14))
      {
         return false;
      }
   }

	for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++)
	{
		$soma += $cnpj{$i} * $j;
		$j = ($j == 2) ? 9 : $j - 1;
	}
	$resto = $soma % 11;

	if ($cnpj{12} != ($resto < 2 ? 0 : 11 - $resto))
		return false;

	for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++)
	{
		$soma += $cnpj{$i} * $j;
		$j = ($j == 2) ? 9 : $j - 1;
	}
	$resto = $soma % 11;

	return $cnpj{13} == ($resto < 2 ? 0 : 11 - $resto);
}

/**
 *   Validate CPF or CNPJ
 *
 *   @method   validateCXXX
 *   @param    string         $cxxx
 *   @return   bool
 */
function validateCXXX(string $cxxx) : bool
{
   $cxxx = preg_replace("/[^0-9]/", "", (string) $cxxx);
   switch (strlen($cxxx))
   {
      case 11:
      {
         return validateCPF($cxxx);
      } break;
      case 14:
      {
         return validateCNPJ($cxxx);
      } break;
      default:
      {
         return false;
      } break;
   }
}

?>
