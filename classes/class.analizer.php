<?php
ini_set('display_errors', true);
error_reporting(E_ALL);
$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_HEADER, 0);
require_once 'config.php';
require_once 'includes/simple_html_dom.php';
require_once 'class.file.php';

class analizer
 {
 
  private $fileName;
  private $appPath;
  private $uploadsPath;
  private $dir;
  private $ext;
  private $qty;
  private $size;
  private $html;
  private $images;
  private $htmlfile;
  private $zipfile;
  private $extraido;
  private $enzipado;
  private $fecha;
  private $file;
  private $uploadsDir;
  private $dirsToDelete;
  private $zipName;
  private $tree;
  private $level;
  private $folder = 0;
  private $directories;
  private $imgIcon = '<img src="img/img-icon.png" /> ';
  private $folderIcon = '<img src="img/folder-icon.png" /> ';
  private $rarIcon = '<img src="img/zip-icon.png" /> ';
  private $zipIcon = '<img src="img/zip-icon.png" /> ';
  private $alertIcon = '<img src="img/alert-icon.png" /> ';
  private $htmlIcon = '<img src="img/html-icon.png" /> ';
  private $imagenNoEnHtml = array();
  private $badImgFilename = array();

	function __construct($fileName)
	 {
		$this->appPath = DOCUMENT_ROOT; //from config.php
		$this->uploadsDir = UPLOADS_DIR; //from config.php
		$this->fileName = $fileName;
		$this->zipName = $this->fileName;
		$this->fecha = date("Y-m-d");
		$this->uploadsPath = $this->appPath.'/'.$this->uploadsDir.'/'.$this->fileName; //directory where zip file was upload
		$this->dirsToDelete = array();
		$this->result = '';
		$this->level = '<img src="img/level1.png" />';
		$this->tree = '<img src="img/zip-icon.png" /> '.$fileName."<br>";
	 }
	 
	public function unzip(){
		
		$this->file = pathinfo($this->uploadsPath);
		//Creamos un objeto de la clase ZipArchive()
		$this->enzipado = new ZipArchive();
		//Abrimos el archivo a descomprimir
		$this->enzipado->open($this->uploadsPath);

		//Extraemos el contenido del archivo dentro de la carpeta especificada
		$this->extraido = $this->enzipado->extractTo(UNZIP_DIR.'/'.$this->file['filename']."/");
		@chmod($this->appPath.'/'.UNZIP_DIR.'/'.$this->file['filename'], 0777);
		
		if(is_dir($this->appPath.'/'.UNZIP_DIR.'/'.$this->file['filename'].'/images')){
			@chmod($this->appPath.'/'.UNZIP_DIR.'/'.$this->file['filename'].'/images', 0777);
		}
		if(is_dir($this->appPath.'/'.UNZIP_DIR.'/'.$this->file['filename'].'/img')){
			@chmod($this->appPath.'/'.UNZIP_DIR.'/'.$this->file['filename'].'/img', 0777);
		}
		
		$this->directories = glob($this->appPath.'/'.UNZIP_DIR.'/'.$this->file['filename'] . '/*' , GLOB_ONLYDIR);

		$this->dirsToDelete[] = $this->file['filename'];

	}
	
	public function loadFiles($file, $ext){
		
		switch($ext){
			case 'jpg':
				$this->images[] = $file;
				$filenameimg = explode('/', $file);
				if(!preg_match('/(^[a-zA-Z0-9]+([a-zA-Z\_0-9\.-]*))$/', $filenameimg[1])){
				$this->badImgFilename[] = $file;
				}
			break;
			case 'gif':
				$this->images[] = $file;
				$filenameimg = explode('/', $file);
				if(!preg_match('/(^[a-zA-Z0-9]+([a-zA-Z\_0-9\.-]*))$/', $filenameimg[1])){
				$this->badImgFilename[] = $file;
				}
			break;
			case 'png':
				$this->images[] = $file;
				$filenameimg = explode('/', $file);
				if(!preg_match('/(^[a-zA-Z0-9]+([a-zA-Z\_0-9\.-]*))$/', $filenameimg[1])){
				$this->badImgFilename[] = $file;
				}
			break;
			case 'html':
				$this->htmlfile[] = $file;
			break;
			case 'htm':
				$this->htmlfile[] = $file;
			break;

		}
		
	}
	
	public function getCountImages($dir){
	
	$i = 0; 
	$dir = $dir.'/';
    if ($handle = opendir($dir)) {
        while (($file = readdir($handle)) !== false){
            if (!in_array($file, array('.', '..')) && !is_dir($dir.$file)) 
                $i++;
        }
    }
    // prints out how many were in the directory
    return $i;
	
	}
	
	public function getImages(){
		
		return $this->images;
		
	}
	
	public function getHtmlsFile(){
		
		return $this->htmlfile;
		
	}
	
	public function checkImages($dirname, $htmls, $images){
		$imgs = '<div id="checkImages">';
		if($htmls > 0){
			
			//recorre cada html encontrado en el array $htmls
			foreach ($htmls as $htmlname){
				$imgsOk = array();
				$imgsKo = array();
				$inclusionesOk = 0;
				$inclusionesKo = 0;
		
				$html = file_get_html($this->appPath.'/'.UNZIP_DIR.'/'.$this->file['filename'].'/'.$htmlname);
				$imgs .= '<div class="html-analize">';
				$imgs .= '<h4><img src="img/html-icon.png" /> ' . $htmlname . '</h4>';
				
				//recorre cada insercion de imagen en el html y la busca en el array de imagenes $images
				foreach($html->find('img') as $element) {
					$imagesSource[] = $element->src;
					if(in_array($element->src, $images)){
						$inclusionesOk++;
						if(!in_array($element->src, $imgsOk)){
							$imgsOk[] = $element->src;
						}
					}else{
						$inclusionesKo++;
						if(!in_array($element->src, $imgsKo)){
							$imgsKo[] = $element->src;
						}
					}
					
				}
				//$imagesSource; //array con inserciones en el html
				//$images; //array con las imagenes en el directorio

				
				$imagenesConsistentes = count($imgsOk);
				$imagenesOkStr = implode("<br>", $imgsOk);
				
				//imagenes que no estan en la carpeta images 
				$imagenesInconsistentes = count($imgsKo);
				$imagenesKoStr = '<ol>';
				foreach($imgsKo as $imgKo){
					$imagenesKoStr .= '<li><span>'.$imgKo.'</span></li>';
				}
				$imagenesKoStr .= '</ol>';
				
				$inclusionesTotales = $inclusionesOk + $inclusionesKo;
				$imgs .= '<p>'.$this->imgIcon.' Images instances in html ('.$inclusionesTotales.')</p> 
				<p><img src="img/correct.png" /> Correct images instances ('.$inclusionesOk.')</p>';
				if($inclusionesKo >= 1){
				$imgs .= '<p><img src="img/incorrect.png" /> Incorrect images instances. Image file not found in directory ('.$inclusionesKo.')</p>';
				$imgs .= '<h5>Images not found ('.$imagenesInconsistentes.')</h5>';
				$imgs .= $imagenesKoStr;
				}
				$imgs .= '</div>';
			}
			
		//recorre cada imagen del directorio y la busca en el array de imagenes insertadas en el html
				foreach($images as $image) {
					if(!in_array($image, $imagesSource)){
						$this->imagenNoEnHtml[] = $image;
					}
				}
			
		if(count($this->imagenNoEnHtml) >= 1){
		
				/*foreach ($htmls as $htmlname){
					$html = file_get_html($this->appPath.'/'.UNZIP_DIR.'/'.$this->file['filename'].'/'.$htmlname);
					foreach($html->find('img') as $element) {
						$element->src;
					}
				}*/
				
				$imagenesSobrantes = '<ol>';
				foreach($this->imagenNoEnHtml as $residueImg){
					$imagenesSobrantes .= '<li><span>'.$residueImg.'</span></li>';
				}
				$imagenesSobrantes .= '</ol>';
				$imgs .= '<p class="residueImgs"><img src="img/incorrect.png" /> Residue images. Not used in htmls file ('.count($this->imagenNoEnHtml).') </p>';
				$imgs .= $imagenesSobrantes;
		}
		
		if(count($this->badImgFilename) >= 1){
				$imgs .= '<p class="invalidImgs"><img src="img/incorrect.png"> Invalid filename images </p>';
				
				$imgs .= '<ol>';
				foreach($this->badImgFilename as $invalidImg){
				$imgs .= '<li>'.$invalidImg.'</li>';
				}
				$imgs .= '</ol>';
			}
		
		}
		
			$imgs .= '</div>';
		return $imgs;
	
	}
	


	
	public function reanalize($zips){
		$this->imagenNoEnHtml = array();
		$this->badImgFilename = array();
		$this->folder = 0;
		$this->level = '<img src="img/level2.png" />';
		foreach($zips as $zip){
			$this->zipName = $zip;
			$this->uploadsPath = $zip;
			$this->unzip();
			$name = explode('/', $zip);
			$this->tree .= '<img src="img/level1.png" />'.$this->rarIcon.end($name)."<br>";
			$this->zipName = end($name);
			$this->images = array();
			$this->htmlfile= array();
			$this->analize();
		}
		
	}
	
	public function analize(){

		if($this->extraido == TRUE){
		
			 for ($x = 0; $x < $this->enzipado->numFiles; $x++) {
				$archivo = $this->enzipado->statIndex($x);
				$file = new file($archivo['name']);
				
				If (!preg_match("/.gif/i", $archivo['name'])&&!preg_match("/.jpg/i", $archivo['name'])&&!preg_match("/.png/i", $archivo['name'])) {
					If (preg_match("/.html/i", $archivo['name'])) {
					
						$this->tree .= $this->level.$this->htmlIcon.' ' .$archivo['name']."<br>";
					}else{
						If (!is_dir($this->appPath.'/'.UNZIP_DIR.'/'.$this->file['filename'].'/'.$archivo['name'])&&!preg_match("/.zip/i", $archivo['name'])&&!preg_match("/.rar/i", $archivo['name'])) {
							$this->tree .= $this->level.$this->alertIcon.$archivo['name']."<br>";
						}
					}
				}
				
				if($file->getExt()=='zip' || $file->getExt()=='rar'){
					$zips[] = $this->appPath.'/'.UNZIP_DIR.'/'.$this->file['filename'].'/'.$archivo['name'];				
				}else{
					
					$this->loadFiles($file->getFileName(), $file->getExt());
				}
			 }
				
				  $this->result .= '<h3>'.$this->rarIcon.' '.$this->zipName.'</h3>';
				  //analize images on files
				  $this->result .= $this->checkImages($this->file['filename'], $this->getHtmlsFile(), $this->getImages());

			 
			 
			}	
			else {
			
				  $this->result .= 'Ocurrió un error y el archivo no se pudó descomprimir';
			}
			
			
			foreach($this->directories as $directory){
					$dirName = explode('/', $directory);
					$dirName = end($dirName);
					$this->tree .= $this->level.$this->folderIcon.$dirName." (".$this->getCountImages('descomprimido/'.$this->file['filename'].'/'.$dirName).")<br>";
				}
				
			if(isset($zips) && $zips > 0){
				$this->result .= $this->reanalize($zips);
			}
			
			return $this->tree.$this->result;
			
	}
	
	private function rrmdir($carpeta) {
		foreach(glob($carpeta . "/*") as $archivos_carpeta)
		{
		//si es un directorio volvemos a llamar recursivamente 
		if (is_dir($archivos_carpeta))
		$this->rrmdir($archivos_carpeta);
		else//si es un archivo lo eliminamos
		unlink($archivos_carpeta);
		} 
		//if($carpeta == '/var/www/html/mrm/finalfiles/descomprimido/' || $carpeta == '/var/www/html/mrm/finalfiles/uploads/'){
		if($carpeta == 'E:\wwwroot\Stage.mrm.com.ar\mrm\tools\final-files-validator\descomprimido' || $carpeta == 'E:\wwwroot\Stage.mrm.com.ar\mrm\tools\final-files-validator\uploads'){
		
		}else{
		rmdir($carpeta);
		}
		
		}
					
	function __destruct()
	 {	
		$carpeta = $this->appPath.'/descomprimido/';
		$carpeta2 = $this->appPath.'/uploads/';

		$this->rrmdir($carpeta);
		$this->rrmdir($carpeta2);

	 }
 
 }
 
 ?>