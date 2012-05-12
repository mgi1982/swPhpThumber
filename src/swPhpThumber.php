<?php
die(var_dump($_GET));
$im = file_get_contents($_REQUEST['img']); 
header('Content-type: ' . image_type_to_mime_type(exif_imagetype($_REQUEST['img']))); 
echo $im; 
