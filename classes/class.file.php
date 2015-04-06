<?php
class file
 {
 
  private $fileName;
  private $path;
  private $dir;
  private $ext;
  private $qty;
  private $size;
  private $html;

	function __construct($fileName)
	 {
	 $this->fileName = $fileName;
	 }

	
	public function getFileName(){
		return $this->fileName;
	}
	
	private function getTotalFiles(){
		
	}
	
	public function getExt(){
		$parts = explode('.',  $this->fileName);
		@$this->ext = $parts[1];
		return $this->ext;
	}
 
 
 }
 
 ?>