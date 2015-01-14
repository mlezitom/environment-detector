<?php
/**
 * mlezitom/environmentDetector
 *
 * @author Tomas Mleziva
 */
class EnvironmentDetector {
    
    public $data;
    public $domain;
    public $ip;
    
    const SECTION_PRODUCTION = "production";
    const SECTION_DEVELOPMENT = "development";
    const SECTION_LOCALHOST = "localhost";
    
    public function __construct($neon = NULL) {
        $this->domain = $_SERVER['HTTP_HOST'];
        $this->ip = $_SERVER['REMOTE_ADDR'];
		if($neon) {
			$this->loadNeon($neon);
		}
    }
    
    public function loadNeon($neonFile) {
        if(!is_file($neonFile)) {
            throw new \Nette\Application\ApplicationException(sprintf("Environment configuration file '%s' not found.", $neonFile));
        }
        
        $this->data = Nette\Neon\Neon::decode(file_get_contents($neonFile));
    }
    
    public function isDevel() {
        return $this->is(self::SECTION_DEVELOPMENT);
    }
    
    public function isProduction() {
        return $this->is(self::SECTION_PRODUCTION);
    }
    
    public function isLocalhost() {
        return $this->is(self::SECTION_LOCALHOST);
    }
    
    private function is($mode) {
        if(is_string($this->data[$mode])) {
            return ($this->domain == $this->data[$mode] || $this->ip == $this->data[$mode]);
        }
        
        if(is_array($this->data[$mode])) {
            foreach($this->data[$mode] as $item) {
                if($this->domain == $item || $this->ip == $item) {
                    return TRUE;
                }
            }
            return ($this->domain == $this->data[$mode]);
        }
        else {
            throw new \Nette\InvalidArgumentException("Invalid data in evironment conf file - section " . $mode);
        }
        return false;
    }
    
    public function getMode() {
        if($this->isLocalhost()) {
            return self::SECTION_LOCALHOST;
        }
        if($this->isDevel()) {
            return self::SECTION_DEVELOPMENT;
        }
        return self::SECTION_PRODUCTION;
    }
}
