<?php

	class Network {
		private $cookie_path;
		private $proxy_data;
		
		private $latest_curl_info;
		
		private $defaultUserAgent, $advancedInfoOptions = null;
		
		public function __construct($cookie_path, $proxy_data = null) {
			$this->defaultUserAgent = null;
			@mkdir(pathinfo($cookie_path, PATHINFO_DIRNAME), 0777, true);
			$this->cookie_path = $cookie_path;
			$this->proxy_data = $proxy_data;
		}
	
		public function GetQuery($url, $header_plus = [], $noDecodeJSON = false) {
			return $this->Request([
				CURLOPT_URL => $url
			], $header_plus, $noDecodeJSON);
		}
		
		public function PostQuery($url, $par_array = [], $header_plus = [], $noDecodeJSON = false) {
			return $this->Request([
				CURLOPT_URL => $url,
				CURLOPT_POST => 1,
				CURLOPT_POSTFIELDS => $par_array
			], $header_plus, $noDecodeJSON);
		}
		
		public function Request($curl_opt_array, $header_plus = [], $noDecodeJSON = false) {
			$curl_handle = curl_init();
			if ($this->proxy_data != null) {
				curl_setopt($curl_handle, CURLOPT_PROXY, $this->proxy_data);
			}
			
			foreach ($curl_opt_array as $id => $value) {
				curl_setopt($curl_handle, $id, $value);
			}
		
			$header = [
				'User-Agent' => $this->defaultUserAgent != null ? $this->defaultUserAgent : 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:98.0) Gecko/20100101 Firefox/98.0', 
				'Accept' => '*/*', 
				'Accept-Language' => 'ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3', 
				'Connection' => 'keep-alive'
			];
			foreach ($header_plus as $name => $value) {
				$header[$name] = $value;
			}
			
			curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, 0); 
			curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $this->compileHeader($header, ['Accept']));
			curl_setopt($curl_handle, CURLOPT_COOKIEJAR, $this->cookie_path); 
			curl_setopt($curl_handle, CURLOPT_COOKIEFILE, $this->cookie_path); 
			
			$data = curl_exec($curl_handle);
			
			$this->latest_curl_info = curl_getinfo($curl_handle, $this->advancedInfoOptions);
			
			curl_close($curl_handle);
			
			if ($noDecodeJSON) return $data;
			
			$json = json_decode($data, true);
			
			if (is_array($json)) return $json;
			
			return false;
		}
		
		public function applyInfoOptions($options = null) {
			$this->advancedInfoOptions = $options;
		}
		
		public function setDefaultAgent(string $userAgent) {
			$this->defaultUserAgent = $userAgent;
		}
		
		public function getLatestInfo() {
			return $this->latest_curl_info;
		}
		
		private function compileHeader($header_array, $remove_array) {
			$header = [];
			foreach($remove_array as $val) $header[] = $val.':';
			foreach($header_array as $key => $val) $header[] = $key . ': ' . $val;
			return $header;
		}
	}
	
	class CurlCookies {
		private $cookies;
		private $cookies_file;
		
		public function __construct($filename) {
			$this->cookies_file = $filename;
			$this->reload();
		}
		
		public function getValidValue($key) {
			foreach ($this->cookies as $domain) {
				foreach ($domain as $name => $cookie) {
					if ($key == $name && $cookie['value'] != null && $cookie['value'] != '""') {
						return $cookie['value'];
					}
				}
			}
			return null;
		}
		
		public function addCookie($domain, $name, $value, $expiration = -1, $path = '/', $flag = 'TRUE', $secure = 'FALSE') {
			if ($expiration == -1) $expiration = time() + 3600;
			$cookie = $domain . "\t" . $flag . "\t" . $path . "\t" . $secure . "\t" . $expiration . "\t" . $name . "\t" . $value . "\r\n";
			$updated = @file_get_contents($this->cookies_file).$cookie;
			file_put_contents($this->cookies_file, $updated);
			$this->cookies = $this->extractCookies($updated);
		}
		
		public function reload() {
			$this->cookies = $this->extractCookies(@file_get_contents($this->cookies_file));
		}
		
		public function extractCookies($string) {
			$cookies = [];
			$lines = explode("\n", $string);
			foreach ($lines as $line) {
				if (isset($line[0]) && substr_count($line, "\t") == 6) {
					$tokens = explode("\t", $line);
					$tokens = array_map('trim', $tokens);
					$cookie = [];
					$cookie['flag'] = $tokens[1];
					$cookie['path'] = $tokens[2];
					$cookie['secure'] = $tokens[3];
					$cookie['expiration'] = $tokens[4];
					$cookie['value'] = $tokens[6];
					$cookies[$tokens[0]][$tokens[5]] = $cookie;
				}
			}
			return $cookies;
		}
	}