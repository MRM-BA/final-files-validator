<?php
date_default_timezone_set('Africa/Lagos');
require_once 'classes/class.analizer.php';
/*
-	Chequeo de cantidad de imágenes y verificación de que no haya demás ni de menos.
-	Chequeo de la cantidad de archivos HTMLs
-	Las imágenes no deben tener caracteres raros ni espacios en los nombres de archivo.
-	Se debería poder controlar que los nombres de los HTMLs sean los que el cliente solicita (no aplica en todos los casos).
-	Se debería poder controlar que el nombre del archivo ZIP sea el que el cliente solicita.
-	Sería interesante poder procesar el archivo ZIP que se debe entregar (sin necesidad de descomprimirlo)
-	Hay casos en que tenemos 2 archivos ZIP comprimidos dentro de 1 solo ZIP (por requerimiento del cliente).

Consulta: Qué posibilidad habría de comprobar el archivo de FF directamente desde el SVN?
*/

 
/* Si el archivo se extrajo correctamente listamos los nombres de los
 * archivos que contenia de lo contrario mostramos un mensaje de error
*/
$analizer = new analizer($_POST['fileName']);
$analizer->unzip();
echo $analizer->analize();

?>