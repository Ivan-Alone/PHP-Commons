<?php

    class Watchdog {
        private $timeout = 0;
        private $pid, $proc_handle = null, $handles;
        
        const WDT_EXE = 'watchdog';
        
        public function __construct(int $timeout = 60, bool $use_default_popen = false) {
            $this->timeout = $timeout;
            $this->pid = getmypid();
            
            $wdt_exe = implode(DIRECTORY_SEPARATOR, [__DIR__, 'bin', 'wdt', static::WDT_EXE ]);
            
            $arch = static::getSystemArch();
            if ($arch != 'x86') {
                $wdt_exe .= '.' . $arch;
            }
            
            if (!static::isSystemX64()) {
                $wdt_exe .= '32';
            }
            
            if (static::isWindows()) {
                $wdt_exe .= '.exe';
            } elseif (!static::isLinux()) {
                $wdt_exe .= '.' . strtolower(PHP_OS);
            }
            
            $cmd = ['"' . $wdt_exe . '"', $this->pid, $this->timeout ];
            
            if (!$use_default_popen) {
                $cmd[] = '--silent';
            }
            
            $cmd = implode(' ', $cmd);
            
            if (!file_exists($wdt_exe)) {
                throw new Exception('Main WDT executable not found: ' . $wdt_exe . " !");
            } else {
                if ($use_default_popen) {
                    $this->proc_handle = popen($cmd, 'w');
                    $this->handles     = [ $this->proc_handle ];
                } else {
                    $this->proc_handle = proc_open($cmd, [ ['pipe', 'r'], ['pipe', 'w'], ['pipe', 'w'] ], $this->handles);
                }
            }
        }
        
        public function reset() {
            static $i = 0;
            
            if (!$this->proc_handle || !$this->handles || count($this->handles) < 1) {
                return false;
            }
            
            $char = static::WDT_EXE[ $i++ % strlen(static::WDT_EXE) ];
            
            return fwrite($this->handles[0], $char);
        }
        
        public function disable() {
            if (count($this->handles) > 1) {
                foreach ($this->handles ?? [] as $pipe) {
                    fclose($pipe);
                }
                proc_close($this->proc_handle);
            } else {
                pclose($this->proc_handle);
            }
            $this->proc_handle = null;
            $this->handles     = null;
        }
        
        private static function isSystemX64() {
            return PHP_INT_SIZE * 8 >= 64;
        }
        
        private static function isWindows() {
            return PHP_OS == 'WINNT';
        }
        
        private static function isLinux() {
            return PHP_OS == 'Linux';
        }
        
        private static function getSystemArch() {
            if (static::isWindows()) {
                $reg = static::exec('reg query "HKLM\\Hardware\\Description\\System\\CentralProcessor\\0"');
                $lines = array_map(function($z) { return array_map('strtoupper', explode('    ', trim($z))); }, explode("\n", $reg));
                
                foreach ($lines as $test) {
                    if (count($test) >= 3 && $test[0] == 'IDENTIFIER') {
                        switch (explode(' ', $test[2])[0]) {
                            case 'ARM64':
                            case 'ARMV8':
                                return 'arm';
                            case 'X86':
                            case 'AMD64':
                                return 'x86';
                        }
                    }
                }
                return 'unknown';
            } else {
                $type = trim(static::exec('uname -m'));
                return trim(strtolower(explode('_', $type)[0]));
            }
        }
    
        private static function exec($cmd) {
            $buf = '';
            $p = popen($cmd, 'r');
            while ($b = fread($p, 10)) {
                $buf .= $b;
            }
            pclose($p);
            
            return $buf;
        }
    }