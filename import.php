<?php
    define('_COMMONS_DIR_DEFAULT', 'common_scripts');
	if (!defined('_COMMONS_ENTRY')) {
		define('_COMMONS_ENTRY', 'import.php');
	}
	if (!defined('_COMMONS_REPO')) {
		define('_COMMONS_REPO', 'https://raw.githubusercontent.com/ivan-alone/php-commons/master/scripts');
	}
	if (!defined('_COMMONS_DIR')) {
		define('_COMMONS_DIR', _COMMONS_DIR_DEFAULT);
	}

	if ((defined('_COMMONS_SAFE_MODE') || isset($_SERVER['SAFE_MODE'])) && !file_exists(_COMMONS_ENTRY)) {
		$data = substr(@file_get_contents(_COMMONS_REPO.'/../import.php'), 2);
		if (strlen($data) > 0) {
			@file_put_contents(_COMMONS_ENTRY, $data);
		}
	}

	if ((defined('_COMMONS_SAFE_MODE') || isset($_SERVER['SAFE_MODE'])) && strtolower(basename(__FILE__)) != strtolower(_COMMONS_ENTRY) && file_exists(_COMMONS_ENTRY)) {
		echo '[Info] Safe mode enabled. PHP Commons script stored, replace [ eval(file_get_contents(\'https://is.gd/AXa2Ej\')); ] to [ include \''._COMMONS_ENTRY.'\'; ] for safety'.PHP_EOL;
	}
	
	function import($module_name, $_REPO = _COMMONS_REPO) {
		$module_name_parts = [];
		foreach (explode('/', str_replace('\\', '/', $module_name)) as $node) {
			$node = trim($node);
			if (!$node) continue;
			foreach ([':','*','?','"','<','>','|','+'] as $forbidden_char) {
				if (strpos($node, $forbidden_char) !== false) continue 2;
			}
			array_push($module_name_parts, $node);
		}
		
		$module_name_dynamic = implode('/', $module_name_parts);
		$module_name_static = str_replace('/', '_', $module_name_dynamic);

        $_COMMONS_DIR = _COMMONS_DIR == _COMMONS_DIR_DEFAULT ? (__DIR__ ? __DIR__ . DIRECTORY_SEPARATOR : '') . _COMMONS_DIR : _COMMONS_DIR;
		if (!is_array(@$_SERVER['PHP_COMMONS_IMPORTED'])) $_SERVER['PHP_COMMONS_IMPORTED'] = [];
		if (!file_exists($_COMMONS_DIR)) {
			mkdir($_COMMONS_DIR);
		}
		if (in_array(strtolower($module_name_static), $_SERVER['PHP_COMMONS_IMPORTED'])) {
			return;
		}
		if (!file_exists($_COMMONS_DIR . DIRECTORY_SEPARATOR . $module_name_static)) {
			$dependencies = @json_decode(file_get_contents($_REPO.'/'.$module_name_dynamic.'.deps'));
			if (is_array($dependencies)) {
				file_put_contents($_COMMONS_DIR . DIRECTORY_SEPARATOR . $module_name_static.'.deps', json_encode($dependencies));
				foreach ($dependencies as $dep) {
					switch (strtolower($dep->type)) {
						case 'bin':
							if (!is_array($dep->files)) {
								$dep->files = [$dep->files];
							}
							foreach ($dep->files as $file) {
								$f_data = @file_get_contents($_REPO.'/'.$file);
								if (strlen($f_data) > 0) {
									@mkdir($_COMMONS_DIR . DIRECTORY_SEPARATOR . pathinfo($file, PATHINFO_DIRNAME), 0777, true);
									file_put_contents($_COMMONS_DIR . DIRECTORY_SEPARATOR . $file, $f_data);
								}
							}
							break;
						case 'php':
							import($dep->import, $dep->from ?? $_REPO);
					}
				}
			}
			$data = @file_get_contents($_REPO.'/'.$module_name_dynamic);

			if (strlen($data) > 0) {
				file_put_contents($_COMMONS_DIR . DIRECTORY_SEPARATOR . $module_name_static, $data);
			}
		}
		$deps_info = $_COMMONS_DIR . DIRECTORY_SEPARATOR . $module_name_static . '.deps';
		if (file_exists($deps_info) && is_array($deps = @json_decode(file_get_contents($deps_info)))) {
			foreach ($deps as $dep) {
				switch (strtolower($dep->type)) {
					case 'php':
						import($dep->import, $dep->from ?? $_REPO);
				}
			}
		}
		include($_COMMONS_DIR . DIRECTORY_SEPARATOR . $module_name_static);
		array_push($_SERVER['PHP_COMMONS_IMPORTED'], strtolower($module_name_static));
	}
