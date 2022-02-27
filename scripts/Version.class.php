<?php
	class Version {
		private $lex_ver;
		public function __construct($version) {
			preg_match('/(([0-9]+\\.)+)?[0-9]+/', $version, $out);
			if (count($out) < 1) {
				throw new Exception("Can't parse this version: ".$version);
			}
			$this->lex_ver = explode('.', $out[0]);
		}
		public function compareTo(Version $otherversion) {
			$c1 = count($this->lex_ver);
			$c2 = count($otherversion->lex_ver);
			$i = 0;

			for ( ; $i < min($c1,$c2); $i++) {
				if ((int)$this->lex_ver[$i] > (int)$otherversion->lex_ver[$i]) {
					return 1;
				} else if ((int)$this->lex_ver[$i] < (int)$otherversion->lex_ver[$i]) {
					return -1;
				}
			}
			if ($c1 == $c2) {
				return 0;
			} else {
				for ($j = $i; $j < max($c1, $c2); $j++) {
					if ((int)(($c1 > $c2 ? $this : $otherversion)->lex_ver[$j]) > 0) {
						return $c1 > $c2 ? 1 : -1;
					}
				}
				return 0;
			}
		}
		public function isGreater($otherversion) {
			return $this->compareTo($otherversion) > 0;
		}
		public function isLesser($otherversion) {
			return $this->compareTo($otherversion) < 0;
		}
		public function isEquals($otherversion) {
			return $this->compareTo($otherversion) == 0;
		}
		public function __toString() {
			return implode('.', $this->lex_ver);
		}
	}
