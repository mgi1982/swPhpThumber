<?php
$im = file_get_contents($_REQUEST['img']);
if(!file_exists($_REQUEST['img'])) {
	header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
	exit();
}

class SimpleImage {
	private $_image;
	private $_image_type;

	function load($filename) {

		$image_info = getimagesize($filename);
		$this->_image_type = $image_info[2];
		if( $this->_image_type == IMAGETYPE_JPEG ) {
			$this->_image = imagecreatefromjpeg($filename);
		} elseif( $this->_image_type == IMAGETYPE_GIF ) {
			$this->_image = imagecreatefromgif($filename);
		} elseif( $this->_image_type == IMAGETYPE_PNG ) {
			$this->_image = imagecreatefrompng($filename);
		}
	}
	 
	function save($filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null) {

		if( $image_type == IMAGETYPE_JPEG ) {
			imagejpeg($this->_image,$filename,$compression);
		} elseif( $image_type == IMAGETYPE_GIF ) {
			imagegif($this->_image,$filename);
		} elseif( $image_type == IMAGETYPE_PNG ) {
			imagepng($this->_image,$filename);
		}
		if( $permissions != null) {
			chmod($filename,$permissions);
		}
	}
	 
	function output($image_type=IMAGETYPE_JPEG) {
		if( $image_type == IMAGETYPE_JPEG ) {
			imagejpeg($this->_image);
		} elseif( $image_type == IMAGETYPE_GIF ) {
			imagegif($this->_image);
		} elseif( $image_type == IMAGETYPE_PNG ) {
			imagepng($this->_image);
		}
	}
	
	function getWidth() {
		return imagesx($this->_image);
	}

	function getHeight() {
		return imagesy($this->_image);
	}
	
	function resizeToHeight($height, $dont_scale = false) {
		if($dont_scale) {
			$width = $this->getWidth();
		} else {
			$ratio = $height / $this->getHeight();
			$width = $this->getWidth() * $ratio;
		}
		$this->resize($width,$height);
	}

	function resizeToWidth($width, $dont_scale = false) {
		if($dont_scale) {
			$height = $this->getHeight();
		} else {
			$ratio = $height / $this->getHeight();
			$height = $this->getHeight() * $ratio;
		}
		$this->resize($width,$height);
	}

	function scale($scale) {
		$width = $this->getWidth() * $scale/100;
		$height = $this->getheight() * $scale/100;
		$this->resize($width,$height);
	}

	function resize($width,$height) {
		$new_image = imagecreatetruecolor($width, $height);
		imagecopyresampled($new_image, $this->_image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
		$this->_image = $new_image;
	}
}

header('Content-type: ' . image_type_to_mime_type(exif_imagetype($_REQUEST['img'])));
if(isset($_REQUEST['scale']) 
	&& ($_REQUEST['scale'] == 'false' || $_REQUEST['scale'] == 'no')) {
	$dont_scale = true;
} else {
	$dont_scale = false;
}

$img = new SimpleImage();
$img->load($_REQUEST['img']);
if(isset($_REQUEST['w']) && isset($_REQUEST['h'])) {
	$img->resize($_REQUEST['w'], $_REQUEST['h']);
} elseif(isset($_REQUEST['w'])) {
	$img->resizeToWidth($_REQUEST['w'], $dont_scale);
} elseif(isset($_REQUEST['h'])) {
	$img->resizeToHeight($_REQUEST['h'], $dont_scale);
}
echo $img->output();
