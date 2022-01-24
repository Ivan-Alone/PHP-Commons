//<?php
	if (!defined('_COMMONS_ENTRY')) {
		define('_COMMONS_ENTRY', 'import.php');
	}
	if (!defined('_COMMONS_REPO')) {
		define('_COMMONS_REPO', 'https://raw.githubusercontent.com/ivan-alone/php-commons/master/scripts');
	}
	if (!defined('_COMMONS_DIR')) {
		define('_COMMONS_DIR', 'common_scripts');
	}
	
	if (!file_exists(_COMMONS_ENTRY)) {
		$data = substr(@file_get_contents(_COMMONS_REPO.'/../import.php'), 2);
		if (strlen($data) > 0) {
			@file_put_contents(_COMMONS_ENTRY, $data);
		}
	}
	
	if (!defined('_COMMONS_NO_WARNING') && strtolower(basename(__FILE__)) != strtolower(_COMMONS_ENTRY) && file_exists(_COMMONS_ENTRY)) {
		echo '[Info] PHP Commons script stored, replace [ eval(file_get_contents(\'https://is.gd/AXa2Ej\')); ] to [ include \''._COMMONS_ENTRY.'\'; ] for safety'.PHP_EOL;
	}
	
	function import($module_name) {
		if (!file_exists(_COMMONS_DIR)) {
			mkdir(_COMMONS_DIR);
		}
		if (!file_exists(_COMMONS_DIR . DIRECTORY_SEPARATOR . basename($module_name))) {
			$data = @file_get_contents(_COMMONS_REPO.'/'.basename($module_name));
			if (strlen($data) > 0) {
				file_put_contents(_COMMONS_DIR . DIRECTORY_SEPARATOR . basename($module_name), $data);
			}
		}
		include(_COMMONS_DIR . DIRECTORY_SEPARATOR . basename($module_name));
	}
