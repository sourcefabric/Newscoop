<?php

if (function_exists('apc_store')) {
	exit(0);
} else {
	exit(1);
}

?>
