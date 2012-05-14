<?php
if(!file_exists($_REQUEST['img'])) {
    header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
    exit();
}

$im = file_get_contents($_REQUEST['img']);

class swPhpThumber {
    private $_image;
    private $_image_type;

    public function load($filename) {
        $image_info = getimagesize($filename);
        $this->_image_type = $image_info[2];
        switch($this->_image_type) {
            case IMAGETYPE_JPEG:
                $this->_image = imagecreatefromjpeg($filename);
                break;
            case IMAGETYPE_GIF:
                $this->_image = imagecreatefromgif($filename);
                break;
            case IMAGETYPE_PNG:
                $this->_image = imagecreatefrompng($filename);
                break;
        }
    }
    
    public function __construct($image = null) {
        if(null!=$image && file_exists($image)) {
            $this->load($image);
        }
    }

    public function save($filename, $image_type = null, $compression=75, $permissions=null) {
        if(null === $image_type) {
            $image_type = IMAGETYPE_JPEG;
        }
        switch ($image_type) {
            case IMAGETYPE_JPEG:
                imagejpeg($this->_image,$filename,$compression);
                break;
            case IMAGETYPE_GIF:
                imagegif($this->_image,$filename);
                break;
            case IMAGETYPE_PNG:
                imagepng($this->_image,$filename);
                break;
        }
        if( $permissions != null) {
            chmod($filename,$permissions);
        }
    }

    public function __toString() {
        return $this->output($this->_image_type);
    }
    
    public function output($image_type = null) {
        if(null === $image_type) {
            $image_type = IMAGETYPE_JPEG;
        }
        switch($image_type) {
            case IMAGETYPE_JPEG:
                imagejpeg($this->_image);
                break;
            case IMAGETYPE_GIF:
                imagegif($this->_image);
                break;
            case IMAGETYPE_PNG:
                imagepng($this->_image);
                break;
        }
    }

    public function getWidth() {
        return imagesx($this->_image);
    }

    public function getHeight() {
        return imagesy($this->_image);
    }

    public function resizeToHeight($height, $dont_scale = false) {
        if($dont_scale) {
            $width = $this->getWidth();
        } else {
            $ratio = $height / $this->getHeight();
            $width = $this->getWidth() * $ratio;
        }
        $this->resize($width,$height);
    }

    public function resizeToWidth($width, $dont_scale = false) {
        if($dont_scale) {
            $height = $this->getHeight();
        } else {
            $ratio = $height / $this->getHeight();
            $height = $this->getHeight() * $ratio;
        }
        $this->resize($width,$height);
    }

    public function scale($scale) {
        $width = $this->getWidth() * $scale/100;
        $height = $this->getheight() * $scale/100;
        $this->resize($width,$height);
    }

    public function resize($width,$height) {
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

$img = new swPhpThumber($_REQUEST['img']);
if(isset($_REQUEST['w']) && isset($_REQUEST['h'])) {
    $img->resize($_REQUEST['w'], $_REQUEST['h']);
} elseif(isset($_REQUEST['w'])) {
    $img->resizeToWidth($_REQUEST['w'], $dont_scale);
} elseif(isset($_REQUEST['h'])) {
    $img->resizeToHeight($_REQUEST['h'], $dont_scale);
}
echo $img;
