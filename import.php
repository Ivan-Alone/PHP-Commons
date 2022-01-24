//<?php
	if (!defined('_COMMONS_REPO')) {
		define('_COMMONS_REPO', 'https://raw.githubusercontent.com/ivan-alone/php-commons/master/scripts');
	}
	if (!defined('_COMMONS_DIR')) {
		define('_COMMONS_DIR', 'common_scripts');
	}
	
	if (!file_exists('import.php')) {
		$data = substr(@file_get_contents(_COMMONS_REPO.'/../import.php')), 2);
		if (strlen($data) > 0) {
			file_put_contents('import.php', $data));
		}
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