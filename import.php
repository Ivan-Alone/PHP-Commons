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
		if (!is_array(@$_SERVER['PHP_COMMONS_IMPORTED'])) $_SERVER['PHP_COMMONS_IMPORTED'] = [];
		if (!file_exists(_COMMONS_DIR)) {
			mkdir(_COMMONS_DIR);
		}
		if (in_array(strtolower(basename($module_name)), $_SERVER['PHP_COMMONS_IMPORTED'])) {
			return;
		}
		if (!file_exists(_COMMONS_DIR . DIRECTORY_SEPARATOR . basename($module_name))) {
			$dependencies = @json_decode(file_get_contents(_COMMONS_REPO.'/'.basename($module_name).'.deps'));
			if (is_array($dependencies)) {
				file_put_contents(_COMMONS_DIR . DIRECTORY_SEPARATOR . basename($module_name).'.deps', json_encode($dependencies));
				foreach ($dependencies as $dep) {
					switch (strtolower($dep->type)) {
						case 'bin':
							if (!is_array($dep->files)) {
								$dep->files = [$dep->files];
							}
							foreach ($dep->files as $file) {
								$f_data = @file_get_contents(_COMMONS_REPO.'/'.$file);
								if (strlen($f_data) > 0) {
									@mkdir(_COMMONS_DIR . DIRECTORY_SEPARATOR . pathinfo($file, PATHINFO_DIRNAME), 0777, true);
									file_put_contents(_COMMONS_DIR . DIRECTORY_SEPARATOR . $file, $f_data);
								}
							}
							break;
						case 'php':
							import($dep->import);
					}
				}
			}
			$data = @file_get_contents(_COMMONS_REPO.'/'.basename($module_name));
			if (strlen($data) > 0) {
				file_put_contents(_COMMONS_DIR . DIRECTORY_SEPARATOR . basename($module_name), $data);
			}
		}
		$deps_info = _COMMONS_DIR . DIRECTORY_SEPARATOR . basename($module_name) . '.deps';
		if (file_exists($deps_info) && is_array($deps = @json_decode(file_get_contents($deps_info)))) {
			foreach ($deps as $dep) {
				switch (strtolower($dep->type)) {
					case 'php':
						import($dep->import);
				}
			}
		}
		include(_COMMONS_DIR . DIRECTORY_SEPARATOR . basename($module_name));
		array_push($_SERVER['PHP_COMMONS_IMPORTED'], strtolower(basename($module_name)));
	}
