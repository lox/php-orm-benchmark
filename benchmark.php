<?php

printf("%-30s%-30s\n", "Class", "INSERT");

foreach(glob('*/*.php') as $path)
{
	if(!preg_match('/^(lib)/', $path))
	{
		passthru("php $path");
	}
}

