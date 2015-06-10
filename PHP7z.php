<?php 
/**
 * Simple class for compression in 7z format
 * AES 256 bits
 * 
 * @author BRS
 * @version beta
 * @copyright  2015
 * @access public */ 


class PHP7z{
	/*
    ** define constants
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
	
	/**
	 * PATH of source file
	 *
	 * @var string
	 * @access private
	 */	
	protected $_source;
	
	/**
	 * PATH of destination file
	 *
	 * @var string
	 * @access private
	 */		
	 
	protected $_dest;
	
	/**
	 * Password
	 *
	 * @var string
	 * @access private
	 */	
	protected $_pass;
	
	/**
	 * Type of compression
	 *
	 * @var string
	 * @access private
	 */	
	protected $_type;
	
	/**
	 * PATH of bin PHP7z file
	 *
	 * @var string
	 * @access private
	 */	
	protected $_bin;
	
	/**
	 * Debug flag
	 *
	 * @var boolean
	 * @access private
	 */		
	protected $_debug;
	
	/**
	 * Log flag
	 *
	 * @var boolean
	 * @access private
	 */			
	protected $_log;
	
	/**
	 * Verify flag
	 *
	 * @var boolean
	 * @access private
	 */			
	protected $_verify;
	
	/**
	 * PATH of include file
	 *
	 * @var string
	 * @access private
	 */			
	protected $_include_file;	
	
	
	/**
	 * New dir. flag
	 *
	 * @var boolean
	 * @access private
	 */		
	private $new_dir_flag;
	

	/**
	* Constructor - if you're not using the class statically
	*
	* @return void
	*/
	function __construct(){
		$this->findBin();
		date_default_timezone_set('America/Sao_Paulo');
		$this->_type = '-t7z';
		$this->_pass = '';
		$this->_dest = '';
		$this->_log = true;
		$this->_verify = true;
		$this->_include_file['real']= ' -i!warning.txt';
	}	
	
	
	/**
	* Finder - search for executable file
	*
	* @return void
	*/	
	private function findBin(){
		if(file_exists(__DIR__.'/PHP7z.exe'))
			$this->setBin(__DIR__.'/PHP7z.exe');		
		if(file_exists(__DIR__.'/bin/PHP7z.exe'))
			$this->setBin(__DIR__.'/bin/PHP7z.exe');	
	}
	
	/**
	* Test File
	*
	* @return boolean
	*/	
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
	
	
	/**
	* Test of  integrity
	*
	* @return boolean
	*/		
	private function integrityCheck($source, $md5){
		if($this->fileTest($source) && md5_file($source) == $md5)
			return true;
		else
			return false;
	}
	
	
	/**
	* Set source file
	*
	* @return void
	*/		
	public function setSource($source){
		$source = str_replace("\\",'/',$source);
		if($this->fileTest($source))
			$this->_source = pathinfo($source);
		else
			$source = NULL;
	}
	
	
	/**
	* Set destination file
	*
	* @return void
	*/	
	public function setDest($dest){
		$dest = str_replace('\\','/',$dest);
		if(!file_exists(dirname($dest))){
			$this->new_dir_flag = true;
		}
		$this->_dest = ($dest);
		return $this;		
	}	
	
	/**
	* Set include file
	*
	* @return void
	*/		
	public function setIncludeFile($file){
		if($file == false){
			$this->_include_file['real'] = '';	
			return;
		}
		$file = str_replace("\\",'/',$file);
		if($this->fileTest($file))
			$this->_include_file = pathinfo($file);
		else
			$file = null;
		$this->_include_file['real'] = ' -i!'.$this->_include_file['dirname'].'/'.$this->_include_file['basename'];			
	}	
	
	
	/**
	* Set debug flag
	*
	* @return void
	*/	
	public function setDebug($debug){
		$this->_debug = $debug;
	}		
	
	
	/**
	* Set log flag
	*
	* @return void
	*/		
	public function setLog($log){
		$this->_log = $log;
	}		
	
	
	/**
	* Set password 
	*
	* @return void
	*/		
	public function setPass($pass){
		if($pass != '' && $pass != NULL && $pass != ' '){
			$this->_pass = "-p".$pass;
		}else{
			$this->_pass = "";
		}
	}
	
	
	/**
	* Set verify 
	*
	* @return void
	*/		
	public function setVerify($verify){
		$this->_verify = $verify;
	}	

	
	/**
	* Set type 
	*
	* @return void
	*/		
	public function setType($type){
	}	


	/**
	* Set bin 
	*
	* @return void
	*/		
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
	
	/**
	* Compression 
	*
	* @param string $source Source path
	* @param string $dest Destination path
	* @param string $pass Password
	* @param boolean $remove Remove flag
	* @param boolean $verify Verify flag
	*
	* @return string
	*/	
	public function compress($source = '', $dest = '', $pass = '', $remove = false, $verify = true){
		if(!($this->integrityCheck($this->_bin['real'],self::MD5_BIN)))
			return false;
		if($source == '')
			$source = $this->_source;
		else
			$source = pathinfo($source);
		if($source == NULL)
			return 0;
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
		$cmd =$this->_bin['real'] .' '. $op .' '. $type .' '. $pass .' '. $dest .' '. $source['dirname'].'/'.$source['basename']. $this->_include_file['real'];
		exec($cmd, $output, $return);
		$info = $this->debugHeader();
		$info .= self::SPACE.'<b>CMD COMPRESS: '.$cmd.'</b>';
		$info .= self::SPACE;
		foreach ($output as $value) {
			$info .= $value;
			$info .= "<br>";
			
		}		
		if($this->_debug)
			echo $info;
		$this->log($info);
		$ext = '';
		if($this->_verify){
			unset($output);
			$verifyFile = pathinfo($dest);
			
			if(isset($verifyFile['extension'])){
				if($verifyFile['extension'] != '' && $verifyFile['extension'] != NULL && $verifyFile['extension']!= '.' && $verifyFile['extension'] != '/'){
					$ext = '';
				}else{	
					$ext = '.7z';
				}	
			}else{
				$ext = '.7z';
			}
			$cmd =$this->_bin['real'] .' '. 't' .' '. $pass .' '. $dest . $ext .' *';
			$info = self::SPACE.'<b>CMD TEST: '.$cmd.'</b>';
			exec($cmd, $output, $return);
			foreach ($output as $value) {
				$info .= $value;
				$info .= self::SPACE;
			}	
			if($this->_debug)
				echo $info;
			$this->log($info);			
		}		
		return $return;
	}


	/**
	* Set log 
	*
	* @return void
	*/	
	private function log($info){
		if($this->_log){
			$arquivo = fopen("log.html", "ab");
			fwrite($arquivo, $info."\r\n");
			fclose($arquivo);
		}
	}
	
	/**
	* Debug header 
	*
	* @return void
	*/	
	private function debugHeader(){
		$header = '- - - - - - - - - - - - - - - - - - - - - - '.
		'- - - - - - - - - - - - - - - - - - - - - - - - - - - -'.
		' - - - - - - - - - - - - - - - - - -';
		$header .= '<br>' . date('d/m/Y  - H\hm\m\i\n - e');
		return $header;
	}
}

$object = new PHP7z();
$object->setSource('C:\Users\Felipe\Documents\PHP7z');
$object->setIncludeFile('');
$object->setPass('assinare');
$object->compress();



?>
