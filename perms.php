<?php
//chmod('conzip/conzip/con_img.zip', 777);

$pathname = 'conzip/';

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($pathname));

$filemode = "0777"; // Set the permission

// Foreach item found set the permissions
foreach($iterator as $item) {
    chmod($item, $filemode);
}
?>