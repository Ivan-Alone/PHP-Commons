<?php
	class Teletype {
		public static function print($text, $speed = 10) {
			$b = KeyBoardPressBuilder::create();
			$keyCodes = (new ReflectionClass(new KeyBoard()))->getConstants();
			
			for ($i = 0; $i < strlen($text); $i++) {
				$ch = substr($text, $i, 1);
				$id = ord($ch);
				
				if (($id >= ord('A') && $id <= ord('Z')) || ($id >= ord('a') && $id <= ord('z')) || ($id >= ord('0') && $id <= ord('9'))) {
					if (!($id >= ord('0') && $id <= ord('9')) && strtoupper($ch) == $ch) {
						$b->addKey(KeyBoard::VK_SHIFT);
					}
					$b->addKey($keyCodes['VK_'.strtoupper($ch)]);
				} elseif ($ch == ' ') {
					$b->addKey(KeyBoard::VK_SPACE);
				} elseif ($ch == "\n") {
					$b->addKey(KeyBoard::VK_RETURN);
				} else {
					switch ($ch) {
						case '!':
							$b->addKey(KeyBoard::VK_SHIFT);
							$b->addKey(KeyBoard::VK_1);
							break;
						case '@':
							$b->addKey(KeyBoard::VK_SHIFT);
							$b->addKey(KeyBoard::VK_2);
							break;
						case '#':
							$b->addKey(KeyBoard::VK_SHIFT);
							$b->addKey(KeyBoard::VK_3);
							break;
						case '$':
							$b->addKey(KeyBoard::VK_SHIFT);
							$b->addKey(KeyBoard::VK_4);
							break;
						case '%':
							$b->addKey(KeyBoard::VK_SHIFT);
							$b->addKey(KeyBoard::VK_5);
							break;
						case '^':
							$b->addKey(KeyBoard::VK_SHIFT);
							$b->addKey(KeyBoard::VK_6);
							break;
						case '&':
							$b->addKey(KeyBoard::VK_SHIFT);
							$b->addKey(KeyBoard::VK_7);
							break;
						case '*':
							$b->addKey(KeyBoard::VK_SHIFT);
							$b->addKey(KeyBoard::VK_8);
							break;
						case '(':
							$b->addKey(KeyBoard::VK_SHIFT);
							$b->addKey(KeyBoard::VK_9);
							break;
						case ')':
							$b->addKey(KeyBoard::VK_SHIFT);
							$b->addKey(KeyBoard::VK_0);
							break;
						case '-':
							$b->addKey(KeyBoard::VK_OEM_MINUS);
							break;
						case '=':
							$b->addKey(KeyBoard::VK_OEM_PLUS);
							break;
						case '_':
							$b->addKey(KeyBoard::VK_SHIFT);
							$b->addKey(KeyBoard::VK_OEM_MINUS);
							break;
						case '+':
							$b->addKey(KeyBoard::VK_SHIFT);
							$b->addKey(KeyBoard::VK_OEM_PLUS);
							break;
						case '[':
							$b->addKey(KeyBoard::VK_OEM_4);
							break;
						case ']':
							$b->addKey(KeyBoard::VK_OEM_6);
							break;
						case '{':
							$b->addKey(KeyBoard::VK_SHIFT);
							$b->addKey(KeyBoard::VK_OEM_4);
							break;
						case '}':
							$b->addKey(KeyBoard::VK_SHIFT);
							$b->addKey(KeyBoard::VK_OEM_6);
							break;
						case ';':
							$b->addKey(KeyBoard::VK_OEM_1);
							break;
						case ':':
							$b->addKey(KeyBoard::VK_SHIFT);
							$b->addKey(KeyBoard::VK_OEM_1);
							break;
						case '\'':
							$b->addKey(KeyBoard::VK_OEM_7);
							break;
						case '"':
							$b->addKey(KeyBoard::VK_SHIFT);
							$b->addKey(KeyBoard::VK_OEM_7);
							break;
						case '\\':
							$b->addKey(KeyBoard::VK_OEM_5);
							break;
						case '|':
							$b->addKey(KeyBoard::VK_SHIFT);
							$b->addKey(KeyBoard::VK_OEM_5);
							break;
						case ',':
							$b->addKey(KeyBoard::VK_OEM_COMMA);
							break;
						case '.':
							$b->addKey(KeyBoard::VK_OEM_PERIOD);
							break;
						case '<':
							$b->addKey(KeyBoard::VK_SHIFT);
							$b->addKey(KeyBoard::VK_OEM_COMMA);
							break;
						case '>':
							$b->addKey(KeyBoard::VK_SHIFT);
							$b->addKey(KeyBoard::VK_OEM_PERIOD);
							break;
						case '/':
							$b->addKey(KeyBoard::VK_OEM_2);
							break;
						case '?':
							$b->addKey(KeyBoard::VK_SHIFT);
							$b->addKey(KeyBoard::VK_OEM_2);
							break;
						case '`':
							$b->addKey(KeyBoard::VK_OEM_3);
							break;
						case '~':
							$b->addKey(KeyBoard::VK_SHIFT);
							$b->addKey(KeyBoard::VK_OEM_3);
							break;
					}
				}
				
				$b->press($speed);
			}
		}
	}