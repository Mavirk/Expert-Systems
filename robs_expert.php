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
		    		printf("test");
		    		$queries = str_split(explode(" #", $line)[0]);
		    		array_shift($queries);
		    	}
		    	else
		    	{
					$rules = explode(" #", $line);
					$lines[$i] = $rules[0];
					$i++;
				}
			}
		}
		fclose($file);
	}
	else
		echo "Error reading file: ".$filename.PHP_EOL;
	var_dump($facts);
	var_dump($queries);
	var_dump($lines);
		return ($lines);
}
$lines = read_file($argv[1]);
?>