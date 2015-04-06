<?php
//Creamos un objeto de la clase ZipArchive()
$enzipado = new ZipArchive();
 
//Abrimos el archivo a descomprimir
$enzipado->open('ejemplo.zip');
 
//Extraemos el contenido del archivo dentro de la carpeta especificada
$extraido = $enzipado->extractTo("carpeta/");
 
/* Si el archivo se extrajo correctamente listamos los nombres de los
 * archivos que contenia de lo contrario mostramos un mensaje de error
*/
if($extraido == TRUE){
 for ($x = 0; $x < $enzipado->numFiles; $x++) {
 $archivo = $enzipado->statIndex($x);
 echo 'Extraido: '.$archivo['name'].'</br>';
 }
 echo $enzipado->numFiles ." archivos descomprimidos en total";
}
else {
 'Ocurrió un error y el archivo no se pudó descomprimir';
}
?>