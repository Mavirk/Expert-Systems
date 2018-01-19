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
	if ($verbose === true) {echo "In GROUPER str: ".$str.PHP_EOL;}
	$ex_num = 0;
	$br_num = 0;
	$op_num = 0;
	$char_num = 0;
	$br_waiter = 0;
	$ex = false;
	$or = false;
	$xor = false;
	$br_overide = false;
	$groups;
	for ($i = 0; $i < iconv_strlen($str); $i++)
	{
		if ($verbose === true)
		{
			echo "Index: ".$i.PHP_EOL;
			echo "Char: ".$str[$i].PHP_EOL;	
		}
		if ($str[$i] == '!')
		{
			if ($ex == false)
			{
				if ($verbose === true) {echo "Ex Found, now TRUE".PHP_EOL;}
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
				if ($verbose === true) {echo "br_overide now TRUE".PHP_EOL;}
			}
			//wait for another ')' before switching back overide state & switch overide to false
			if ($br_overide == true && $ex_index[$ex_num -1] != $i -1)
			{
				$br_overide = false;
				$br_waiter++;
				$ex_waiting[] = $i;
				if ($verbose === true)
				{
					echo "br_overide now FALSE".PHP_EOL;
					echo "br_waiter++: ".$br_waiter.PHP_EOL;
				}
			}
			//wait for another ')' before switching back overide state
			else if ($br_waiter > 0)
			{
				$br_waiter++;
				$ex_waiting[] = $i;
				if ($verbose === true)
				{
					echo "br_waiter++: ".$br_waiter.PHP_EOL;
					var_dump($ex_waiting);
				}
			}
			//wait for another ')' before switching back or state
			if ($or == true)
			{
				$or_waiter++;
				$or_waiting[] = $i;
				$or = false;
				if ($verbose === true)
				{
					echo "or_waiter++: ".$or_waiter.PHP_EOL;
					var_dump($or_waiting);
				}
			}
			//wait for another ')' before switching back xor state
			if ($xor == true)
			{
				$xor_waiter++;
				$xor_waiting[] = $i;
				$xor = false;
				if ($verbose === true)
				{
					echo "xor_waiter++: ".$xor_waiter.PHP_EOL;
					var_dump($xor_waiting);
				}
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
			if ($verbose === true) {echo "br_overide: ".$br_overide.PHP_EOL;}
			//Check for '!' and create group
			if ($br_overide == false)
				$groups[] = [$br_index[$br_num], $i];
			else
				$groups[] = [$br_index[$br_num] -1, $i];
			//Check states of ex and or and xor of previous br level and adjust accordingly
			if ($br_index[$br_num] == $ex_waiting[$br_waiter -1])
			{
				$br_overide = true;
				if ($verbose === true) {echo "br_overide now TRUE".PHP_EOL;}
				$br_waiter--;
				unset($ex_waiting[$br_waiter]);
				$ex_waiting = array_values($ex_waiting);
			}
			if ($br_index[$br_num] == $or_waiting[$or_waiter -1])
			{
				$or = true;
				if ($verbose === true) {echo "OR now TRUE".PHP_EOL;}
				$or_waiter--;
				unset($or_waiting[$or_waiter]);
				$or_waiting = array_values($or_waiting);
			}
			if ($br_index[$br_num] == $xor_waiting[$xor_waiter -1])
			{
				$xor = true;
				if ($verbose === true) {echo "XOR now TRUE".PHP_EOL;}
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
			if ($verbose === true)
			{
				echo "PRE ')' char elem removal char_num ".$char_num." br_num: ".$br_num.PHP_EOL;
				var_dump($char_br_num);
			}
			for ($k = 0; $k < count($char_br_num); $k++)
			{
				if ($verbose === true) {echo "k is: ".$k." and less than: ".count($char_br_num).PHP_EOL;}
				if ($char_br_num[$k] == $br_num + 1)
				{
					unset($char_br_num[$k]);
					$char_br_num = array_values($char_br_num);
					unset($char_index[$k]);
					$char_index = array_values($char_index);
					unset($char_ex[$k]);
					$char_ex = array_values($char_ex);
					$char_num--;
					if ($verbose === true)
					{
						echo "removing char elems at k = ".$k.PHP_EOL;
						echo "POST ')' char elem removal char_num ".$char_num." br_num: ".$br_num.PHP_EOL;
						var_dump($char_br_num);
					}
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
			if ($verbose === true)
			{
				echo "var_dump".PHP_EOL;
				var_dump($groups);
			}
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
			if ($char_num > 0 && $op_num > 0 && $char_br_num[$prev_char_num] == $br_num && $str[$i + 1] != ")")
			{
				if ($verbose === true)
				{
					echo "Check for '!' at ".$prev_char_num." and br_overide: ".$br_overide." then create group".PHP_EOL;
					var_dump($char_ex);
					var_dump($char_index);
				}
				//Create group according to state of ex and br_overide
				if ($char_ex[$prev_char_num] == false && $ex == false)
					$groups[] = [$char_index[$prev_char_num], $i];
				else if ($char_ex[$prev_char_num] == false && $ex == true)
					$groups[] = [$char_index[$prev_char_num], $i];
				else if ($char_ex[$prev_char_num] == true && $br_overide == false)
					$groups[] = [$char_index[$prev_char_num] -1, $i];
				else if ($char_ex[$prev_char_num] == true && $br_overide == true)
					$groups[] = [$char_index[$prev_char_num] -2, $i];
				if ($verbose === true)
				{
					echo "PRE char elem removal char_num ".$char_num." br_num: ".$br_num.PHP_EOL;
					var_dump($char_br_num);
				}
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
						unset($char_br_num[$k]);
						$char_br_num = array_values($char_br_num);
						unset($char_index[$k]);
						$char_index = array_values($char_index);
						unset($char_ex[$k]);
						$char_ex = array_values($char_ex);
						$char_num--;
						if ($verbose === true)
						{
							echo "removing char elems at k = ".$k.PHP_EOL;
							echo "POST char elem removal char_num ".$char_num." br_num: ".$br_num.PHP_EOL;
							var_dump($char_br_num);
						}
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
				if ($verbose === true)
				{
					echo "var_dump".PHP_EOL;
					var_dump($groups);
				}
			}
			else
			{
				if ($verbose === true) {echo "Does not meet the conditions for group".PHP_EOL;}
				$char_num++;
				$char_index[] = $i;
				$char_ex[] = $ex;
				$char_br_num[] = $br_num;
			}
			if ($char_num > 0 && $verbose === true)
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
				if ($verbose === true) {echo "Ex now FALSE".PHP_EOL;}
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
		if ($verbose === true)
		{
			echo "chars in: ".PHP_EOL;
			var_dump($in);
		}
		for ($k = 0; $k < iconv_strlen($str); $k++)
		{
			if ($in && array_search($k, $in) == false)
			{
				$groups[] = [0, iconv_strlen($str)];
				if ($verbose === true) {echo "Added whole group".PHP_EOL;}
				break;
			}
		}
	}
	else
	{
		$groups[] = [0, iconv_strlen($str)];
		if ($verbose === true) {echo "Added whole group".PHP_EOL;}
	}
	if ($verbose === true)
	{
		echo "str:".$str.PHP_EOL."END GROUPER Groups:".PHP_EOL;
		var_dump($groups);
	}
	if ($verbose === true) {echo "End GROUPER".PHP_EOL;}
	return $groups;
}

function expander($options, $group, $group_num)
{
	$or = false;
	$xor = false;
	$verbose = true;
	if ($verbose === true) {echo "In EXPANDER".PHP_EOL."Group ".$group_num." is :".$group.PHP_EOL;}
	//Go through current group looking for |, ^ and counting chars
	for ($i = 0; $i <= iconv_strlen($group); $i++)
	{
		if ($group[$i] == "|")
		{
			$or = true;
			$pos = $i;
		}
		else if ($group[$i] == "^")
		{
			$xor = true;
			$pos = $i;
		}
		else if (ctype_alpha($group[$i]))
		{
			if ($group[$i-1] == "!")
				$chars[] = ["!".$group[$i], $group[$i]];
			else
				$chars[] = [$group[$i], "!".$group[$i]];
		}
	}
	//For each char, copy each element currently in the array and add a true and false copy
	for ($i=0; $i<count($chars); $i++)
	{
		if ($g_opts)
		{
			if ($verbose === true) {echo "Expanding i=".$i."\n";}
			foreach ($g_opts as $g_opt)
			{
				$ng_opts[] = $g_opt." + ".$chars[$i][0];
				$ng_opts[] = $g_opt." + ".$chars[$i][1];
				if ($verbose === true) {var_dump($ng_opts);}
			}
			$g_opts = $ng_opts;
			unset($ng_opts);
		}
		else
		{
			$g_opts[] = $chars[$i][0];
			$g_opts[] = $chars[$i][1];
		}
	}
	if ($verbose === true)
	{
		echo "END EXPANDER".PHP_EOL;
		var_dump($g_opts);
	}
	return($g_opts);
}

function replacer($group, $x_groups, $i, $j , $options, $rplcd, &$rplcd_opt_ind)
{
	//Create Strings of all chars pre (rg_pre) and post (rg_post) group to replace (rg)
	if ($verbose === true)
	{
		echo "In REPLACER".PHP_EOL."group:";
		var_dump($group);
	}
	if ($rplcd != false)
	{
		$rg_diff = $group[1];
		$rg_pos = [$group[2][0]-$rg_diff, $group[2][1]-$rg_diff];
		$rg_len = $rg_pos[1] - $rg_pos[0] + 1;
		$rg_post = substr($group[0], $rg_pos[1]+1, iconv_strlen($group[0])-($rg_pos[1]+1));
		$rg_pre = substr($group[0], 0, $rg_pos[0]);
		$replace = substr($group[0], $rg_pos[0], $rg_len);
		$rg_check = $group[2];
	}
	else
	{
		$rg_pos = $x_groups[$j];
		$rg_len = $rg_pos[1] - $rg_pos[0] + 1;
		$rg_post = substr($group, $rg_pos[1]+1, iconv_strlen($group)-($rg_pos[1]+1));
		$rg_pre = substr($group, 0, $rg_pos[0]);
		$replace = substr($group, $rg_pos[0], $rg_len);
		$rg_check = $rg_pos;
	}
	if ($verbose === true) {echo "rg_pre:".$rg_pre."~".PHP_EOL."rg_post:".$rg_post."~".PHP_EOL."rpl:".$replace."~".PHP_EOL;}
	foreach ($options as $k => $opt)
	{
		//Check if each option originated from the group to be replaced
		if ($verbose === true) {echo "checking option i= ".$k.": ".$opt[0].PHP_EOL;}
		if ($opt[1] == $rg_check)
		{
			$opt_len = iconv_strlen($opt[0]);
			$diff = $rg_len - $opt_len;
			if ($verbose === true)
			{
				echo "replacing opt[1][0]=".$opt[1][0]." rg_pos[0]=".$rg_pos[0].PHP_EOL.$rg_pre.$opt[0].$rg_post.PHP_EOL;
				echo "Diff Calculated: ".$diff.PHP_EOL;
			}
			$rplcd_gs[] = [$rg_pre.$opt[0].$rg_post, $diff];
			$rplcd_opt_ind[] = $k;
		}
	}
	if ($verbose === true)
	{
		var_dump($rplcd_gs);
		echo "End REPLACER".PHP_EOL;
	}
	return $rplcd_gs;
}

function joiner($rule, $pre, $x_groups)
{
	if ($verbose === true)
	{
		echo "IN JOINER".PHP_EOL."rule: ".$rule.PHP_EOL."pre: ".$pre.PHP_EOL;
		var_dump($x_groups);
	}
	foreach ($x_groups as $i => $group_ind)
	{
		//Get group len and create group
		$group_len = $group_ind[1] - $group_ind[0] + 1;
		$group = substr($rule, $group_ind[0], $group_len);
		if ($verbose === true) {echo "Group ".$i." is :".$group.PHP_EOL;}
		$rplcd = false;
		//Check if any pervious groups are within this one, if so, replace with replacer function
		for ($j = 0; $j < $i; $j++)
		{
			if ($verbose === true) {echo "Cheking for inner groups j=".$j.PHP_EOL;}
			if ($group_ind[0] <= $x_groups[$j][0] && $group_ind[1] >= $x_groups[$j][1])
			{
				if ($verbose === true) {echo "Inner group found, replacing".PHP_EOL;}
				//If a previous replacement has been done, send rplcd_gs to replacer in place of current group
				if ($rplcd != false)
				{
					if ($verbose === true) {echo "Replacment has been done on this group already".PHP_EOL;}
					//unset old replacements
					$old_rplcd_gs = $rplcd_gs;
					unset($rplcd_gs);
					unset($rplcd_gs2);
					foreach ($old_rplcd_gs as $old_rplcd_grp)
					{
						if ($old_rplcd_grp[2] = $x_groups[$j])
							$rplcd_gs3[] = replacer($old_rplcd_grp, $x_groups, $i, $j, $options, $rplcd, $rplcd_opt_ind);
					}
					foreach ($rplcd_gs3 as $rplcd_g3)
					{
						foreach ($rplcd_g3 as $rplcd_g4)
							$rplcd_gs2[] = [$rplcd_g4[0], $rplcd_g4[1], $x_groups[$j]];
					}
					if ($verbose === true)
					{
						echo "After replacing old_rplcd_grp, rplcd_gs2 :".PHP_EOL;
						var_dump($rplcd_gs2);
					}
				}
				//If no replacement has been done, replace current group
				else
				{
					if ($verbose === true) {echo "No replacment has been done on this group".PHP_EOL;}
					$rplcd_gs2 = replacer($group, $x_groups, $i, $j , $options, $rplcd, $rplcd_opt_ind);
					$rplcd[] = true;
				}
				//add each returned option to rplcd_gs
				foreach ($rplcd_gs2 as $rplcd_g2)
					$rplcd_gs[] = [$rplcd_g2[0], $rplcd_g2[1], $x_groups[$j]];
			}
		}
		//If replacement was done on current group, expand every element in rplcd_gs & add to options array with group indexes
		if ($rplcd != false)
		{
			if ($verbose === true)
			{
				echo "POST replacer rplcd_gs at i=".$i.":".PHP_EOL;
				var_dump($rplcd_gs);
			}
			foreach ($rplcd_gs as $k => $r_group)
			{
				$new_options = expander($options, $r_group[0], $i);
				foreach ($new_options as $n_opt)
					$options[] = [$n_opt, $x_groups[$i]];
			}
			unset($rplcd_gs);
			$rplcd = false;
			foreach ($rplcd_opt_ind as $ind)
				unset($options[$ind]);
			array_values($options);
			unset($rplcd_opt_ind);
		}
		//If no replacement was done on current group, expand group & add to options array with group indexes
		else
		{
			if ($verbose === true) 
				echo "No replacements made for group ".$group.PHP_EOL;
			$new_options = expander($options, $group, $i);
			foreach ($new_options as $n_opt)
				$options[] = [$n_opt, $x_groups[$j]];
			if ($verbose === true)
			{
			echo "at the end of JOINER iteration ".$i." options has:".PHP_EOL;
			var_dump($options);
			}
		}	
	}
	return $options;
}

function expand_posibls($pos)
{
	foreach ($pos as $i => $rule)
	{
		$iaoi = false;
		if  (strpos($rule, '<') !== false)	//IFaoIF
		{
			$pre_end = strpos($rule, '<')-2;
			$con_start = strpos($rule, '>')+3;
			$iaoi = true;
		}
		else
		{
			$pre_end = strpos($rule, '=')-1;
			$con_start = strpos($rule, '=')+3;
		}
		echo "Pre: ".$pre_end.PHP_EOL."Con: ".$con_start.PHP_EOL;
		$preStr = substr($rule, 0, $pre_end);
		$conStr = substr($rule, $con_start, iconv_strlen($rule));
		echo "Pre: ".$preStr.PHP_EOL."Con: ".$conStr.PHP_EOL;
		$p_groups = grouper(trim($preStr));
		$c_groups = grouper(trim($conStr));
		/*echo "Pre Groups".PHP_EOL;
		var_dump($p_groups);
		echo "Con Groups".PHP_EOL;
		var_dump($c_groups);*/
		$pre = joiner($rule, $preStr, $p_groups);
		echo "herehello".PHP_EOL;
		echo $conStr.PHP_EOL;
		if (iconv_strlen($conStr) == 1)
			$con[] = $conStr;
		else
			$con = joiner($rule, $conStr, $c_groups);
		var_dump($con);
		if ($iaoi)
		{
			foreach ($con as $c)
			{
				foreach ($pre as $p)
					$posibilities[] = [$p[0]." <=> ".$c[0], $i];
			}
		}
		else
		{
			foreach ($con as $c)
			{
				foreach ($pre as $p)
					$posibilities[] = [$p[0]." => ".$c[0], $i];
			}
		}
		unset($con);
		echo "At the end of expand_posibls iteration $i, posibilities is:".PHP_EOL;
		var_dump($posibilities);
	}
	return $posibilities;
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

$rules = expand_rules($pre_rules);
$posibilities = expand_posibls($pre_posibls);
echo "EEEEEEEEEENNNNNNNNNNDDDDDDDDDD".PHP_EOL;
echo "Pre Rules".PHP_EOL;
var_dump($pre_rules);
echo "Rules".PHP_EOL;
var_dump($rules);
var_dump($pre_posibls);
echo "\nFacts: ".$facts.PHP_EOL."Queries: ".$queries.PHP_EOL;
?>
