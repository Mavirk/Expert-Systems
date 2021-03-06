<?php

function rem_com($str)
{
	$i = 0;
	$pos = strpos($str, "#");
	if ($pos !== false)
		return strtoupper(trim(substr($str, 0, $pos)));
	else
		return strtoupper(trim($str));
}

function br_solver($str, $br_pos)
{
	$i = 0;
	$j = 0;
	$done = false;
	$start = $br_pos[0];
	$end = $br_pos[1];
	echo "BRS str: ".$str.PHP_EOL;
	while ($done == false)
	{
		if (count($start) == 1 || ($start[$i] < $end[$j] && ($start[$i + 1] > $end[$j] || $i = count($start))))
		{
			$done = true;
			var_dump($start);
			var_dump($end);
			echo "i: ".$i." j: ".$j.PHP_EOL;
			//check for '!' before current opening bracket; supports '+'
			if ($start[$i] > 0 && $str[$start[$i] -1] == "!")
			{
				echo "found !".PHP_EOL;
				for ($w = $start[$i] + 1; $w < $end[$j]; $w++)
				{
					echo "Letter: ".$str[$w].PHP_EOL;
					if ($str[$w] === "!")
						$exclm = true;
					else if (ctype_alpha($str[$w]) && $exclm == false)
						$solvd .= "!" . $str[$w];
					else
					{
						$solvd .= $str[$w];
						$exclm = false;
					}
				}
				$str = substr($str, 0, $start[$i] -1).$solvd.substr($str, $end[$j] + 1);
			}
			//if there is no '!' before the current opening bracket; supports '+'
			else
			{
				echo "no !".PHP_EOL;
				for ($w = $start[$i] + 1; $w < $end[$j]; $w++)
				{
					echo "Letter: ".$str[$w].PHP_EOL;
					$solvd .= $str[$w];
				}
				$str = substr($str, 0, $start[$i]).$solvd.substr($str, $end[$j] + 1);
			}
			echo "BRS Facts: ".$solvd.PHP_EOL;
		}
		else
			$i++;
	}
	echo "BRS RET: ".$str.PHP_EOL;
	return $str;
}

function br_finder($str)
{
	echo "BRF str: ".$str.PHP_EOL;
	$brackets = 0;
	$flag = false;
	for ($q = 0; $q < iconv_strlen($str); $q++)
	{
		if ($str[$q] === "(")
		{
			$start[] = $q;
			$brackets++;
			$flag = true;
		}
		else if ($str[$q] === ")")
		{
			if ($flag === false)
				echo "Cannot process backets in conclusion for rule: ".$e.PHP_EOL;
			$brackets--;
			$end[] = $q;
		}
	}
	if ($brackets > 0 && $flag === true)
		echo "Cannot process backets in conclusion for rule: ".$e.PHP_EOL;
	else if ($brackets === 0 && $flag === true && $solve == true)
	{
		$br_pos = array($start, $end);
		return $br_pos;
	}
	else if ($flag === false)
		return null;
}

function expand_rules($rls)
{
	foreach ($rls as $e)
	{
		if (strpos($e, '<') !== false)	//IFAOIF
		{
			$rule = $e;
			while ($br_pos = br_finder($rule) !== null)
				$rule = br_solver($rule, $br_pos);
			$a = substr($rule, 0, strpos($rule, '<') - 1) . " => " . substr($rule, strpos($rule, '>') + 2, iconv_strlen($rule));
			$b = substr($rule, strpos($rule, '>') + 2, iconv_strlen($rule)) . " => " . substr($rule, 0, strpos($rule, '<') - 1);
			if ($rules)
			{
				if (in_array($a, $rules) === false)
					$rules[] = $a;
				if (in_array($b, $rules) === false)
					$rules[] = $b;	
			}
		}
		else
		{
			$new_e = br_finder($e);
			if ($rules && in_array($new_e, $rules) === false)
				$rules[] = $new_e;
		}
	}
}

function grouper($str)
{
	$ex_num = 0;
	$br_num = 0;
	$op_num = 0;
	$char_num = 0;
	$br_waiter = 0;
	$ex = false;
	$or = false;
	$xor = false;
	$br_overide = false;
	for ($i = 0; $i < iconv_strlen($str); $i++)
	{
		echo "Index: ".$i.PHP_EOL;
		echo "Char: ".$str[$i].PHP_EOL;
		if ($str[$i] == '!')
		{
			if ($ex == false)
			{
				echo "Ex now TRUE".PHP_EOL;
				$ex = true;
				$ex_index[] = $i;
				$ex_br_num[] = $br_num;
				$ex_num++;
			}
		}
		else if ($str[$i] == '(')
		{
			//ex is true and was the last char before this '('
			if ($ex == true && $ex_index[$ex_num -1] == $i -1)
			{
				$br_overide = true;
				echo "br_overide now TRUE".PHP_EOL;
			}
			//wait for another ')' before switching back overide state & switch overide to false
			if ($br_overide == true && $ex_index[$ex_num -1] != $i -1)
			{
				$br_overide = false;
				echo "br_overide now FALSE".PHP_EOL;
				$br_waiter++;
				$ex_waiting[] = $i;
				echo "br_waiter++: ".$br_waiter.PHP_EOL;
			}
			//wait for another ')' before switching back overide state
			else if ($br_waiter > 0)
			{
				$br_waiter++;
				$ex_waiting[] = $i;
				echo "br_waiter++: ".$br_waiter.PHP_EOL;
				var_dump($ex_waiting);
			}
			//wait for another ')' before switching back or state
			if ($or == true)
			{
				$or_waiter++;
				$or_waiting[] = $i;
				$or = false;
				echo "or_waiter++: ".$or_waiter.PHP_EOL;
				var_dump($or_waiting);
			}
			//wait for another ')' before switching back xor state
			if ($xor == true)
			{
				$xor_waiter++;
				$xor_waiting[] = $i;
				$xor = false;
				echo "xor_waiter++: ".$xor_waiter.PHP_EOL;
				var_dump($xor_waiting);
			}
			$br_index[] = $i;
			$br_ex[] = $ex;
			$br_num++;
		}
		else if ($str[$i] == ')')
		{
			$br_num--;
			$or = false;
			$xor = false;
			echo "br_overide: ".$br_overide.PHP_EOL;
			//Check for '!' and create group
			if ($br_overide == false)
				$groups[] = [$br_index[$br_num], $i];
			else
				$groups[] = [$br_index[$br_num] -1, $i];
			//Check states of ex and or and xor of previous br level and adjust accordingly
			if ($br_index[$br_num] == $ex_waiting[$br_waiter -1])
			{
				$br_overide = true;
				echo "br_overide now TRUE".PHP_EOL;
				$br_waiter--;
				unset($ex_waiting[$br_waiter]);
				$ex_waiting = array_values($ex_waiting);
			}
			if ($br_index[$br_num] == $or_waiting[$or_waiter -1])
			{
				$or = true;
				echo "OR now TRUE".PHP_EOL;
				$or_waiter--;
				unset($or_waiting[$or_waiter]);
				$or_waiting = array_values($or_waiting);
			}
			if ($br_index[$br_num] == $xor_waiting[$xor_waiter -1])
			{
				$xor = true;
				echo "XOR now TRUE".PHP_EOL;
				$xor_waiter--;
				unset($xor_waiting[$xor_waiter]);
				$xor_waiting = array_values($xor_waiting);
			}
			//remove grouped info from ints and arrays
			unset($br_index[$br_num]);
			unset($br_ex[$br_num]);
			$br_index = array_values($br_index);
			$br_ex = array_values($br_ex);
			for ($k = 0; $k < count($op_br_num); $k++)
			{
				if ($op_br_num[$k] == $br_num + 1)
				{
					unset($op_br_num[$k]);
					$op_br_num = array_values($op_br_num);
					unset($op_index[$k]);
					$op_index = array_values($op_index);
					unset($op_pri[$k]);
					$op_pri = array_values($op_pri);
					$op_num--;
					$k--;
				}
			}
			echo "PRE ')' char elem removal char_num ".$char_num." br_num: ".$br_num.PHP_EOL;
			var_dump($char_br_num);
			for ($k = 0; $k < count($char_br_num); $k++)
			{
				echo "k is: ".$k." and less than: ".count($char_br_num).PHP_EOL;
				if ($char_br_num[$k] == $br_num + 1)
				{
					echo "removing char elems at k = ".$k.PHP_EOL;
					unset($char_br_num[$k]);
					$char_br_num = array_values($char_br_num);
					unset($char_index[$k]);
					$char_index = array_values($char_index);
					unset($char_ex[$k]);
					$char_ex = array_values($char_ex);
					$char_num--;
					echo "POST ')' char elem removal char_num ".$char_num." br_num: ".$br_num.PHP_EOL;
					var_dump($char_br_num);
					$k--;
				}
			}
			for ($k = 0; $k < count($ex_br_num); $k++)
			{
				if ($ex_br_num[$k] == $br_num + 1)
				{
					unset($ex_br_num[$k]);
					$ex_br_num = array_values($ex_br_num);
					unset($ex_index[$k]);
					$ex_index = array_values($ex_index);
					$ex_num--;
					$k--;
				}
			}
			echo "var_dump".PHP_EOL;
			var_dump($groups);
		}
		else if ($str[$i] == '|')
		{
			$or = true;
			$op_num++;
			$op_br_num[] = $br_num;
			$op_index[] = $i;
			$op_pri[] = 0;
		}
		else if ($str[$i] == '^')
		{
			$xor = true;
			$op_num++;
			$op_br_num[] = $br_num;
			$op_index[] = $i;
			$op_pri[] = 1;
		}
		else if (ctype_alpha($str[$i]))
		{
			$prev_char_num = $char_num -1;
			if ($char_num > 0 && $op_num > 0 && $char_br_num[$prev_char_num] == $br_num && $str[$i + 1] != ")" && $or == false)
			{
				echo "Check for '!' at ".$prev_char_num." and br_overide: ".$br_overide." then create group".PHP_EOL;
				var_dump($char_ex);
				var_dump($char_index);
				if ($char_ex[$prev_char_num] == false && $ex == false)
					$groups[] = [$char_index[$prev_char_num], $i];
				else if ($char_ex[$prev_char_num] == false && $ex == true)
					$groups[] = [$char_index[$prev_char_num], $i];
				else if ($char_ex[$prev_char_num] == true && $br_overide == false)
					$groups[] = [$char_index[$prev_char_num] -1, $i];
				else if ($char_ex[$prev_char_num] == true && $br_overide == true)
					$groups[] = [$char_index[$prev_char_num] -2, $i];
				echo "PRE char elem removal char_num ".$char_num." br_num: ".$br_num.PHP_EOL;
				var_dump($char_br_num);
				for ($k = 0; $k < count($op_br_num); $k++)
				{
					if ($op_br_num[$k] == $br_num)
					{
						unset($op_br_num[$k]);
						$op_br_num = array_values($op_br_num);
						unset($op_index[$k]);
						$op_index = array_values($op_index);
						unset($op_pri[$k]);
						$op_pri = array_values($op_pri);
						$op_num--;
						$k--;
					}
				}
				for ($k = 0; $k < count($char_br_num); $k++)
				{
					if ($char_br_num[$k] == $br_num)
					{
						echo "removing char elems at k = ".$k.PHP_EOL;
						unset($char_br_num[$k]);
						$char_br_num = array_values($char_br_num);
						unset($char_index[$k]);
						$char_index = array_values($char_index);
						unset($char_ex[$k]);
						$char_ex = array_values($char_ex);
						$char_num--;
						echo "POST char elem removal char_num ".$char_num." br_num: ".$br_num.PHP_EOL;
						var_dump($char_br_num);
						$k--;
					}
				}
				for ($k = 0; $k < count($ex_br_num); $k++)
				{
					if ($ex_br_num[$k] == $br_num)
					{
						unset($ex_br_num[$k]);
						$ex_br_num = array_values($ex_br_num);
						unset($ex_index[$k]);
						$ex_index = array_values($ex_index);
						$ex_num--;
						$k--;
					}
				}
				echo "var_dump".PHP_EOL;
				var_dump($groups);
			}
			else
			{
				echo "Does not meet the conditions for group".PHP_EOL;
				$char_num++;
				$char_index[] = $i;
				$char_ex[] = $ex;
				$char_br_num[] = $br_num;
			}
			if ($char_num > 0)
			{
				echo "char_num ".$char_num." br_num: ".$br_num.PHP_EOL;
				var_dump($char_br_num);
			}
		}
		if ($ex == true)
		{
			if ($ex_index[$ex_num -1] != $i)
			{
				$ex = false;
				echo "Ex now FALSE".PHP_EOL;
			}

		}
	}
	if (iconv_strlen($str) != 1)
	{
		foreach ($groups as $grp)
		{
			for ($k = $grp[0]; $k <= $grp[1]; $k++)
			{
				if ($in)
				{
					if (array_search($k, $in) == false)
						$in[] = $k;
				}
				else
					$in[] = $k;
			}
		}
		echo "chars in: ".PHP_EOL;
		var_dump($in);
		for ($k = 0; $k < iconv_strlen($str); $k++)
		{
			if ($in && array_search($k, $in) == false)
			{
				$groups[] = [0, iconv_strlen($str)];
				echo "Added whole group".PHP_EOL;
				break;
			}
		}
	}
	else
	{
		$groups[] = [0, iconv_strlen($str)];
				echo "Added whole group".PHP_EOL;
	}
	echo "str:".$str.PHP_EOL."END GROUPER Groups:".PHP_EOL;
	var_dump($groups);
	return $groups;
}

function expand_posibls($pos)
{
	foreach ($pos as $e)
	{
		if  (strpos($e, '<') !== false)	//IFaoIF
		{
			$pre_end = strpos($e, '<') - 1;
			$con_start = strpos($e, '>') + 1;
		}
		else
		{
			$pre_end = strpos($e, '=')-1;
			$con_start = strpos($e, '=')+3;
		}
		echo "Pre: ".$pre_end.PHP_EOL."Con: ".$con_start.PHP_EOL;
		$pre = substr($e, 0, $pre_end);
		$con = substr($e, $con_start, iconv_strlen($e));
		echo "Pre: ".$pre.PHP_EOL."Con: ".$con.PHP_EOL;
		$p_groups = grouper(trim($pre));
		$c_groups = grouper(trim($con));
	}
}

if (file_exists($argv[1]))
	$file = fopen($argv[1], "r");
$i = 0;
if ($file)
{
	while (($lines[$i] = fgets($file)) !== false)
	{
		$i++;
	}
	fclose($file);
}
else
{
	echo "Error reading file: ".$argv[1].PHP_EOL;
}

$i = 0;
$j = 0;
foreach ($lines as $e)
{
	$noCom = rem_com($e);
	echo $noCom.PHP_EOL;
	if (ctype_alpha($e[0]) || ($e[0] == '!'  || $e[0] == '('))
	{
		if (strpos($noCom, '^') == false && strpos($noCom, '|') == false)
		{
			$pre_rules[$i] = $noCom;
			$i++;
		}
		else
		{
			$pre_posibls[$j] = $noCom;
			$j++;
		}
	}
	else if ($e[0] === "=")
		$facts = substr($noCom, 1, iconv_strlen($noCom));
	else if ($e[0] === "?")
		$queries = substr($noCom, 1, iconv_strlen($noCom));
	else
		echo "Comment: " . $e . PHP_EOL;
}

//$rules = expand_rules($pre_rules);
$posibilities = expand_posibls($pre_posibls);
//echo "Pre Rules".PHP_EOL;
//var_dump($pre_rules);
//echo "Rules".PHP_EOL;
//var_dump($rules);
//var_dump($pre_posibls);
//echo "\nFacts: ".$facts.PHP_EOL."Queries: ".$queries.PHP_EOL;
?>
