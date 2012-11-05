<?php

printf("%-30s%-15s%-15s%-15s%-15s%-15s\n", "Class", "INSERT", "PK_SEARCH", "ENUMERATE", "SEARCH", "N+1");

foreach(glob('*/*.php') as $path)
{
	if(!preg_match('/^(lib)/', $path))
	{
		passthru("php $path");
	}
}

