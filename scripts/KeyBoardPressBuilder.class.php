<?php
	class KeyBoardPressBuilder {
		protected $keys;
		protected $pressDown;
		protected $pressUp;

		private function __construct() {
			$this->keys = [];
			$this->pressDown = true;
			$this->pressUp = true;
		}
		public static function create() {
			return new static();
		}
		public function addKey(int $keyCode) {
			$this->keys[] = $keyCode;
			return $this;
		}
		public function setKeyDownMode(bool $doPress) {
			$this->pressDown = $doPress;
			return $this;
		}
		public function setKeyUpMode(bool $doPress) {
			$this->pressUp = $doPress;
			return $this;
		}
		public function press($sleep_timer = 0, $noReset = false) {
			exec('"' . __DIR__ . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . (static::is64Bits() ? 'keyboard' : 'keyboard32') . (PHP_OS == 'WINNT' ? '.exe' : null) . '" ' . ($this->pressUp ? '' : '-nku ') . ($this->pressDown ? '' : '-nkd ') . implode(' ', $this->keys));
			usleep($sleep_timer * 1000);
			if (!$noReset) {
				$this->keys = [];
				$this->pressDown = true;
				$this->pressUp = true;
			}
		}
		private function is64Bits() {
			return strlen(decbin(~0)) == 64;
		}
	}