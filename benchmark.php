<?php

printf("%-30sAUTHOR_INSERT\tBOOK_INSERT\n", "Class");

foreach(glob('*/*.php') as $path)
{
	if(!preg_match('/^(lib)/', $path))
	{
		passthru("php $path");
	}
}

