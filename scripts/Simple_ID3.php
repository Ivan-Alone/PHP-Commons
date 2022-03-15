<?php
	spl_autoload_register(function($class) {
		$namespace = 'ru\\ivan_alone\\simple_id3\\';
		$len = strlen($namespace);
		if (strtolower($namespace) == strtolower(substr($class, 0, $len))) {
			import($class . '.class.php', 'https://raw.githubusercontent.com/Ivan-Alone/Simple_ID3/master/');
		}
	});
