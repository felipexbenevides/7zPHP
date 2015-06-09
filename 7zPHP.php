<?php 
/**
 * Simple class for compression in 7z format
 * AES 256 bits
 * 
 * @author benevides and rodrigues  
 * @version beta
 * @copyright  2015
 * @access public */ 


class PHP7z{
	/*
    ** define 7z format
    */
	const ADD = 'a';
	const F_7Z  = '7z';
	const REMOVE = 'remove';
	const NO_VERIFY = 'no_verify';
	const MD5_BIN = '42badc1d2f03a8b1e4875740d3d49336';
	const SPACE = '<br><br>';
	/*
    ** define variables
    */	
	protected $_source;
	protected $_dest;
	protected $_pass;
	protected $_type;
	protected $_bin;
	protected $_debug;
	protected $_log;
	private $new_dir_flag;
	

	function __construct(){

		date_default_timezone_set('America/Sao_Paulo');
		if(file_exists('7z.exe'))
			$this->_bin = '7z.exe';
		$this->_type = '-t7z';
		$this->_pass = '';
		$this->_dest = '';
		$this->findBin();
	}	
	private function findBin(){
		if(file_exists(__DIR__.'/7zPHP.exe'))
			$this->setBin(__DIR__.'/7zPHP.exe');		
		if(file_exists(__DIR__.'/bin/7zPHP.exe'))
			$this->setBin(__DIR__.'/bin/7zPHP.exe');	
	}
	private function fileTest($source){
		try{	  
			if(!file_exists($source)){
				throw new Exception('Arquivo não existe!');	
			}
		}catch(Exception $e){
			echo $e->getMessage();
			return false;
		}			
		return true;	
	}
	private function integrityCheck($source, $md5){
		if($this->fileTest($source) && md5_file($source) == $md5)
			return true;
		else
			return false;
	}
	public function setSource($source){
		$source = str_replace("\\",'/',$source);
		if($this->fileTest($source))
			$this->_source = pathinfo($source);
		else
			$source = null;
	}
	public function setDest($dest){
		$dest = str_replace('\\','/',$dest);
		if(!file_exists(dirname($dest))){
			$this->new_dir_flag = true;
		}
		$this->_dest = ($dest);
		return $this;		
	}	
	public function setDebug($debug){
		$this->_debug = $debug;
	}		
	public function setPass($pass){
		if(isset($pass)){
			$this->_pass = "-p".$pass;
		}else{
			$this->_pass = "";
		}
	}	
	public function setType($type){
	}		
	public function setBin($bin){
		$bin = str_replace("\\",'/',$bin);
		if(!file_exists($bin)){
			return false;
		}
		$this->_bin = ($bin);
		$this->_bin = pathinfo($this->_bin);
		$this->_bin['real'] = $this->_bin['dirname'].'/'.$this->_bin['basename'];
		return $this;			
	}
	public function compress($source = '', $dest = '', $pass = '', $remove = false, $verify = true){
		if(!($this->integrityCheck($this->_bin['real'],self::MD5_BIN)))
			return false;
		if($source == '')
			$source = $this->_source;
		else
			$source = pathinfo($source);
		if($pass == '')
			$pass = $this->_pass;
		else
			$pass = '-p'.$pass;
		if(!isset($source['extension'])){
				$sfile = false;
		}else{
			$sfile = true;
		}
		
		if($dest == '' or $dest == NULL){
			if($this->_dest == '' or $this->_dest == NULL)
				$dest = NULL;
			else
				$dest = pathinfo($this->_dest);
		}else{
			$dest = pathinfo($dest);
		}
		if ($dest != NULL){
			if(!isset($dest['extension'])){
					$dfile = false;
					if($sfile)
						$dest = $dest['dirname'].'/'.$dest['basename'].'/'.$source['filename'];
					else
						$dest = $dest['dirname'].'/'.$dest['basename'].'/'.$source['basename'];
			}else{
				$dfile = true;
				$dest = $dest['dirname'].'/'.$dest['basename'];
			}	
		}else{
			if($sfile){
				$dest = $source['dirname'].'/'.$source['filename'];	
			}else{
				$dest = $source['dirname'].'/'.$source['basename'];
			}
		}
		$op = self::ADD;
		$type = $this->_type;	
		$cmd =$this->_bin['real'] .' '. $op .' '. $type .' '. $pass .' '. $dest .' '. $source['dirname'].'/'.$source['basename'];
		exec($cmd, $output, $return);
		$info = $this->debugHeader();
		$info .= self::SPACE . $cmd;
		
		$info .= self::SPACE;
		foreach ($output as $value) {
			$info .= $value;
			$info .= "<br>";
			
		}		
		if($this->_debug)
			echo $info;
		$arquivo = fopen("log.html", "ab");
		fwrite($arquivo, $info."\r\n");
		fclose($arquivo);
		return $return;
	}
	private function debugHeader(){
		$header = '- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -';
		$header .= '<br>' . date('d/m/Y  - H\hm\m\i\n - e');
		return $header;
	}
}
?>
