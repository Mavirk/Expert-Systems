<?php
function read_file($filename)
{
	if (file_exists($filename))
		$file = fopen($filename, "r");
	$i = 0;
	if ($file)
	{
		while (($line = fgets($file)) !== false)
		{
			if ($line[0] != "#")
		    {
		    	if ($line[0] == '=')
		    	{
		    		$facts = str_split(explode(" #", $line)[0]);
		    		array_shift($facts);
		    	}
		    	else if ($line[0] == '?')
		    	{
		    		$queries = str_split(explode(" #", $line)[0]);
		    		array_shift($queries);
		    	}
		    	else
		    	{
					$rules = explode(" #", $line)[0];
					$lines[$i] = $rules;
					$i++;
				}
			}
		}
		fclose($file);
	}
	else
		echo "Error reading file: ".$filename.PHP_EOL;
		$data = array('rules' => $lines, 'facts' => $facts, 'queries' => $queries, );
		return ($data);
}
$data = read_file($argv[1]);
var_dump($data);
?>