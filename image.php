<?php
ini_set('gd.jpeg_ignore_warning', true);

class oneImage {
	protected $imgResized;
	private $fileIn;
	private $imgWidth;
	private $imgHeight;
	private $imgResource;
	private $imgType;
	private $imgExt;
	private $watermarkFile = 'images/watermark.png';
	private $watermarkRatio = 0.5;

	public $jpegQuality = 75;
	public $error;

	public function __construct($file, $name = null, $create = true){
		if (is_array($file)){
			$this->fileIn = $file['tmp_name'];
			$this->imgName = $file['name'];
		}
		else {
			$this->fileIn = $file;
			$this->imgName = empty($name) ? substr($file, strrpos($file, '/')) : $name;
		}
		$this->data();
		if ($create and !$this->create()){
			return false;
		}
	}

	public function __destruct(){
		if (!empty($this->imgResource)){
			imagedestroy($this->imgResource);
		}
	}

	function copy($fileOut){
		if (!copy($this->fileIn, $fileOut . '.' . $this->imgExt)){
			$this->error = 'No resizing was attempted, but copying the image to "' . $fileOut . '" failed. The most likely cause of this is the folder permissions of the destination folder. Please make sure scripts have access to write in this folder.';

			return false;
		}

		return true;
	}

	function create(){
		if (!empty($this->imgResource)){
			return $this;
		}
		switch ($this->imgType){
			case 'image/gif':
				$this->imgResource = imagecreatefromgif($this->fileIn);
			break;
			case 'image/pjpeg':
			case 'image/jpeg':
			case 'image/jpg':
				$this->imgResource = imagecreatefromjpeg($this->fileIn);
			break;
			case 'image/png':
			case 'image/x-png':
				$this->imgResource = imagecreatefrompng($this->fileIn);
				imagealphablending($this->imgResource, true);
			break;
		}
		if (!$this->imgResource){
			$this->error = 'The image could not be processed, please try another image. If the error only occurs with one image (or set of similar images) your file(s) may be corrupted. Try opening it in an image editor and saving a new version.';

			return false;
		}

		return $this;
	}

	function crop($options){
		if (isset($options['ratio'])){
			$options['ratio'] = ratio_decimal($options['ratio']);
		}
		if (empty($options['ratio'])){
			$options['ratio'] = 1;
		}
		switch (true){
			case  ($options['ratio']<1):
				if ($this->imgHeight>$this->imgWidth){
					$options['newwidth'] = $this->imgWidth;
					$options['newheight'] = $this->imgWidth / $options['ratio'];
				}
				else {
					$options['newwidth'] = $this->imgHeight * $options['ratio'];
					$options['newheight'] = $this->imgHeight;
				}
			break;
			case ($options['ratio']>1):
				if ($this->imgWidth>$this->imgHeight){
					$options['newwidth'] = $this->imgHeight * $options['ratio'];
					$options['newheight'] = $this->imgHeight;
				}
				else {
					$options['newwidth'] = $this->imgWidth;
					$options['newheight'] = $this->imgWidth / $options['ratio'];
				}
			break;
			default:
				if (empty($options['newwidth'])){
					if ($this->imgWidth>$this->imgHeight){
						$options['newwidth'] = $this->imgHeight;
					}
					else {
						$options['newwidth'] = $this->imgWidth;
					}
				}
				if (empty($options['newheight'])){
					if ($this->imgWidth>$this->imgHeight){
						$options['newheight'] = $this->imgHeight;
					}
					else {
						$options['newheight'] = $this->imgWidth;
					}
				}
		}
		if ($options['scale']>1){
			$options['scale'] = $options['scale'] / $options['newheight'];
		}
		elseif ($options['height']>1) {
			$options['scale'] = $options['height'] / $options['newheight'];
		}
		elseif ($options['width']>1) {
			$options['scale'] = $options['width'] / $options['newwidth'];
		}
		$options['cropwidth'] = ceil($options['newwidth'] * $options['scale']);
		$options['cropheight'] = ceil($options['newheight'] * $options['scale']);
		if (empty($options['x'])){
			$options['x'] = 0;
		}
		if (empty($options['y'])){
			$options['y'] = 0;
		}
		$image_cropped = imagecreatetruecolor($options['cropwidth'], $options['cropheight']);
		if (!$image_cropped){
			$this->error = 'The cropped truecolor image could not be generated with dimensions ' . $options['cropwidth'] . ' x ' . $options['cropheight'] . 'px. Other variables are: ' . echo_array($options, true);

			return false;
		}
		if ($this->imgType=='image/png' or $this->imgType=='image/x-png'){
			if (!imagealphablending($image_cropped, false)){
				$this->error = 'Could not blend alpha state of new image.';

				return false;
			}
			if (!imagesavealpha($image_cropped, true)){
				$this->error = 'Could not save alpha state.';

				return false;
			}
		}
		if (!imagecopyresampled($image_cropped, $this->imgResource, 0, 0, $options['x'], $options['y'], $options['cropwidth'], $options['cropheight'], $options['newwidth'], $options['newheight'])){
			$this->error = 'Resampling the cropped image failed.';

			return false;
		}

		$options['file'] = $options['file'] . '.' . $this->imgExt;
		switch ($this->imgType){
			case 'image/gif':
				if (!imagegif($image_cropped, $options['file'])){
					$this->error = 'The cropped GIF could not be created in ' . $options['file'] . '.';

					return false;
				}
			break;
			case 'image/pjpeg':
			case 'image/jpeg':
			case 'image/jpg':
				if (!imagejpeg($image_cropped, $options['file'], $this->jpegQuality)){
					$this->error = 'The cropped JPEG could not be created in ' . $options['file'] . '.';

					return false;
				}
			break;
			case 'image/png':
			case 'image/x-png':
				if (!imagepng($image_cropped, $options['file'])){
					$this->error = 'The cropped PNG could not be created in ' . $options['file'] . '.';

					return false;
				}
			break;
		}
		if (!file_exists($options['file'])){
			$this->error = 'The cropped image was created but could not be found in ' . $options['file'] . '.';

			return false;
		}
		imagedestroy($image_cropped);
		if ($options['ext']){
			return $this->imgExt;
		}

		return $options['file'];
	}

	function data(){
		list($this->imgWidth, $this->imgHeight, $this->imgType) = getimagesize($this->fileIn);
		$this->imgType = image_type_to_mime_type($this->imgType);
		if (!empty($this->imgName)){
			$dot = strrpos($this->imgName, '.');
			$this->imgExt = strtolower(substr($this->imgName, ($dot+1)));
			$this->imgExt = strtolower($this->imgExt);
		}

		return $this;
	}

	function ext(){
		return $this->imgExt;
	}

	function resize($options){
		$options = $this->resizeCalc($options);
		$this->resizeImg($options);

		return $options;
	}

	function resizeImg($options){
		$this->imgResized = imagecreatetruecolor($options['width'], $options['height']);
		if (!$this->imgResized){
			$this->error = 'The truecolor image could not be generated.';

			return false;
		}
		if (!imagecopyresampled($this->imgResized, $this->imgResource, 0, 0, 0, 0, $options['width'], $options['height'], $this->imgWidth, $this->imgHeight)){
			$this->error = 'The resampled copy could not be made.';

			return false;
		}
		if (!$this->alphaBlend($this->imgResized)){
			return false;
		}

		return $this;
	}

	function alphaBlend($image){
		if ($this->imgType = 'image/png' or $this->imgType = 'image/x-png'){
			if (!imagealphablending($image, false)){
				$this->error = 'Could not blend alpha state of new image.';

				return false;
			}
			if (!imagesavealpha($image, true)){
				$this->error = 'Could not save alpha state.';

				return false;
			}
		}

		return $this;
	}

	function resizeCalc($options){
		if ($options['enlarge']!=1){
			if ($options['width']>$this->imgWidth){
				$options['width'] = $this->imgWidth;
			}
			if ($options['height']>$this->imgHeight){
				$options['height'] = $this->imgHeight;
			}
		}
		if ($options['strict']!=1 and $options['height'] and $options['width']){
			if ($this->imgWidth>$options['width'] and $this->imgHeight>$options['height']){
				if (($this->imgHeight / $options['height'])>($this->imgWidth / $options['width'])){
					$options['width'] = false;
				}
				else {
					$options['height'] = false;
				}
			}
			elseif ($this->imgHeight>$options['height']) {
				$options['width'] = false;
			}
			elseif ($this->imgWidth>$options['width']) {
				$options['height'] = false;
			}
		}
		if (empty($options['height'])){
			$options['height'] = ($this->imgHeight / $this->imgWidth) * $options['width'];
		}
		if (!$options['width']){
			$options['width'] = ($this->imgWidth / $this->imgHeight) * $options['height'];
		}

		return $options;
	}

	function store($options){
		$copy = null;
		if (empty($options['height']) and empty($options['width'])){
			$copy = true;
		}
		else {
			$options = $this->resizeCalc($options);
			if ($options['width']==$this->imgWidth and $options['height']==$this->imgHeight){
				$copy = true;
			}
		}
		if ($copy){
			if (!$this->copy($options['file'])){
				return false;
			}

			// we return false here but do not set $error
			return true;
		}

		$this->resizeImg($options);

		$options['file'] = $options['file'] . '.' . $this->imgExt;
		switch ($this->imgType){
			case 'image/gif':
				if (!imagegif($this->imgResized, $options['file'])){
					$this->error = 'The GIF could not be created in ' . $options['file'] . '.';

					return false;
				}
			break;
			case 'image/pjpeg':
			case 'image/jpeg':
			case 'image/jpg':
				if (!imagejpeg($this->imgResized, $options['file'], $this->jpegQuality)){
					$this->error = 'The JPEG could not be created in ' . $options['file'] . '.';

					return false;
				}
			break;
			case 'image/png':
			case 'image/x-png':
				if (!imagepng($this->imgResized, $options['file'])){
					$this->error = 'The PNG could not be created in ' . $options['file'] . '.';

					return false;
				}
			break;
		}
		imagedestroy($this->imgResized);

		return $options['file'];
	}

	function storeInit($options){
		// if theres no width or height specified we just save it straight away

		return $options;
	}

	function valid(){
		switch ($this->imgType){
			case 'image/gif':
			case 'image/pjpeg':
			case 'image/jpeg':
			case 'image/jpg':
			case 'image/png':
			case 'image/x-png':
			break;
			default:
				$this->error = 'You have uploaded an invalid image type.';

				return false;
		}

		return $this;
	}

	function watermark($options){
		if (!empty($options['watermark'])){
			$this->watermarkFile = $options['watermark'];
		}
		if (!empty($options['watermark-ratio'])){
			$this->watermarkRatio = $options['watermark-ratio'];
		}

		$watermark = imagecreatefrompng($this->watermarkFile);
		imagealphablending($watermark, true);
		$watermark_width = imagesx($watermark);
		$watermark_height = imagesy($watermark);

		$watermark_resized_width = $this->imgWidth * $this->watermarkRatio;
		$watermark_resized_height = $this->imgHeight * $this->watermarkRatio;
		$watermark_resized = imagecreatetruecolor($watermark_resized_width, $watermark_resized_height);
		imagealphablending($watermark_resized, false);
		imagesavealpha($watermark_resized, true);
		imagecopyresampled($watermark_resized, $watermark, 0, 0, 0, 0, $watermark_resized_width, $watermark_resized_height, $watermark_width, $watermark_height);
		imagedestroy($watermark);

		// copy the image to watermark it or resize it
		if (!empty($options['width']) or !empty($options['height'])){
			$options = $this->resize($options);
			$image_watermarked = $this->imgResized;
		}
		else {
			$image_watermarked = $this->imgResource;
			$options['width'] = $this->imgWidth;
			$options['height'] = $this->imgHeight;
		}
		$dest_x = ($options['width']->imgWidth-$watermark_resized_width) / 2;
		$dest_y = ($options['height']-$watermark_resized_height) / 2;
		imagecopymerge_alpha($image_watermarked, $watermark_resized, $dest_x, $dest_y, 0, 0, $watermark_resized_width, $watermark_resized_height);
		imagedestroy($watermark_resized);
		if (!$this->alphaBlend($image_watermarked)){
			return false;
		}

		$options['file'] = $options['file'] . '.' . $this->imgExt;
		switch ($this->imgType){
			case 'image/gif':
				if (!imagegif($image_watermarked, $options['file'])){
					$this->error = 'The GIF could not be created.';

					return false;
				}
			break;
			case 'image/pjpeg':
			case 'image/jpeg':
			case 'image/jpg':
				if (!imagejpeg($image_watermarked, $options['file'], $this->jpegQuality)){
					$this->error = 'The JPEG could not be created.';

					return false;
				}
			break;
			case 'image/png':
			case 'image/x-png':
				if (!imagepng($image_watermarked, $options['file'])){
					$this->error = 'The PNG could not be created.';

					return false;
				}
			break;
		}
		imagedestroy($image_watermarked);

		return $options['file'];
	}
}


function image_crop($p, &$error){
	$image = new oneImage($p['image'], $p['thumb']);
	$p['file'] = substr_before($p['thumb'], '.');
	$result = $image->crop($p);
	if (empty($result)){
		$error = $image->error;
	}

	return $result;
}

function image_crop_im($p, &$error){
	$img = new Imagick($p['image']);
	$img->cropThumbnailImage($p['width'], $p['height']);
	if (!$img->writeImage($p['thumb'])){
		$error = 'No resizing was attempted, but copying the image to "' . $p['file'] . '" failed. The most likely cause of this is the folder permissions of the destination folder. Please make sure scripts have access to write in this folder.';

		return false;
	}

	return $img;
}

function image_ext($img_type){
	switch ($img_type){
		case 'image/gif':
			return 'gif';
		break;
		case 'image/pjpeg':
		case 'image/jpeg':
		case 'image/jpg':
			return 'jpg';
		break;
		case 'image/png':
		case 'image/x-png':
			return 'png';
		break;
	}

	return false;
}

function image_save($id, $params, &$error = null){
	switch ($params['type']){
		case 'copy':
			$p['file'] = $params['out'] . $id . '.' . $params['ext'];
			if (!copy($params['in'], $p['file'])){
				$error = 'Copying the image to "' . $p['file'] . '" failed. The most likely cause of this is the folder permissions of the destination folder. Please make sure scripts have access to write in this folder.';

				return false;
			}
		break;
		case 'crop':
			$image = ['image' => $params['in'], 'thumb' => $params['out'] . $id . '.' . $params['ext']];
			if (is_array($params['size'])){
				$image = array_merge($image, $params['size']);
			}
			elseif (function_exists($params['size'])) {
				$params['size']($image);
			}
			if ($params['imagick']){
				if (!image_crop_im($image, $error)){
					return false;
				}
			}
			else {
				if (!image_crop($image, $error)){
					return false;
				}
			}
		break;
		case 'store':
			$image = ['temp' => $params['in'], 'file' => $params['out'] . $id, 'ext' => $params['ext']];
			if (is_array($params['size'])){
				$image = array_merge($image, $params['size']);
			}
			elseif (function_exists($params['size'])) {
				$params['size']($image);
			}
			if ($params['imagick']){
				if (!image_store_im($image, $error)){
					return false;
				}
			}
			else {
				if (!image_store($image, $error)){
					return false;
				}
			}
		break;
		default:
			$error = 'You must specify an image save type.';

			return false;
	}

	return true;
}

function image_store_init(&$p, &$error){
	$p['file+ext'] = $p['file'] . '.' . $p['ext'];
	// if theres no width or height specified we just save it straight away
	if (empty($p['height']) && empty($p['width'])){
		$copy = true;
	}
	else {
		$copy = false;
		list($p['imgwidth'], $p['imgheight'], $p['img-type']) = getimagesize($p['temp']);
		if (empty($p['imgwidth']) or empty($p['imgheight'])){
			$error = 'The image does not have valid dimensions.';

			return false;
		}
		$p['img-type'] = image_type_to_mime_type($p['img-type']);
		if ($p['enlarge']!=1){
			if ($p['width']>$p['imgwidth']){
				$p['width'] = $p['imgwidth'];
			}
			if ($p['height']>$p['imgheight']){
				$p['height'] = $p['imgheight'];
			}
		}
		if ($p['strict']!=1 and $p['height'] and $p['width']){
			if ($p['imgwidth']>$p['width'] and $p['imgheight']>$p['height']){
				if (($p['imgheight'] / $p['height'])>($p['imgwidth'] / $p['width'])){
					$p['width'] = false;
				}
				else {
					$p['height'] = false;
				}
			}
			elseif ($p['imgheight']>$p['height']) {
				$p['width'] = false;
			}
			elseif ($p['imgwidth']>$p['width']) {
				$p['height'] = false;
			}
		}
		if (!$p['height']){
			$p['height'] = ($p['imgheight'] / $p['imgwidth']) * $p['width'];
		}
		if (!$p['width']){
			$p['width'] = ($p['imgwidth'] / $p['imgheight']) * $p['height'];
		}
		if ($p['width']==$p['imgwidth'] and $p['height']==$p['imgheight']){
			$copy = true;
		}
	}
	if ($copy){
		if (!copy($p['temp'], $p['file+ext'])){
			$error = 'No resizing was attempted, but copying the image to "' . $p['file+ext'] . '" failed. The most likely cause of this is the folder permissions of the destination folder. Please make sure scripts have access to write in this folder.';

			return false;
		}

		// we return false here but do not set $error
		return false;
	}

	return true;
}

function image_create($p, &$error = null){
	switch ($p['img-type']){
		case 'image/gif':
			$image_resource = imagecreatefromgif($p['temp']);
		break;
		case 'image/pjpeg':
		case 'image/jpeg':
		case 'image/jpg':
			$image_resource = imagecreatefromjpeg($p['temp']);
		break;
		case 'image/png':
		case 'image/x-png':
			$image_resource = imagecreatefrompng($p['temp']);
		break;
	}
	if (empty($image_resource)){
		$error = 'The image could not be processed, please try another image. If the error only occurs with one image (or set of similar images) your file(s) may be corrupted. Try opening it in an image editor and saving a new version.';

		return false;
	}

	return $image_resource;
}

function image_store($p, &$error){
	if (!image_store_init($p, $error)){
		// if it returns false we don't need to resize; only if $error is set do we return an error
		// otherwise we assume the image has been copied succesfully
		return empty($error) ? $p['file+ext'] : false;
	}
	$image_resource = image_create($p, $error);
	if (!$image_resource){
		return false;
	}

	$image_resized = imagecreatetruecolor($p['width'], $p['height']);
	if (!$image_resized){
		$error = 'When resizing, the truecolor image could not be generated.';

		return false;
	}
	switch ($p['img-type']){
		case 'image/gif':
			if (!imagecopyresampled($image_resized, $image_resource, 0, 0, 0, 0, $p['width'], $p['height'], $p['imgwidth'], $p['imgheight'])){
				$error = 'When resizing a GIF the resampled copy could not be made.';

				return false;
			}
			if (!imagegif($image_resized, $p['file+ext'])){
				$error = 'When resizing a GIF the image could not be created in ' . $p['file+ext'] . '.';

				return false;
			}
		break;
		case 'image/pjpeg':
		case 'image/jpeg':
		case 'image/jpg':
			if (!imagecopyresampled($image_resized, $image_resource, 0, 0, 0, 0, $p['width'], $p['height'], $p['imgwidth'], $p['imgheight'])){
				$error = 'When resizing a JPG the resampled copy could not be made.';

				return false;
			}
			if (!imagejpeg($image_resized, $p['file+ext'], 75)){
				$error = 'When resizing a JPG the image could not be created in ' . $p['file+ext'] . '.';

				return false;
			}
		break;
		case 'image/png':
		case 'image/x-png':
			if (!imagealphablending($image_resized, false)){
				$error = 'When resizing, could not blend alpha state of new image.';

				return false;
			}
			if (!imagesavealpha($image_resized, true)){
				$error = 'When resizing, could not save alpha state.';

				return false;
			}
			if (!imagealphablending($image_resource, true)){
				$error = 'When resizing, could not blend alpha state of resource image.';

				return false;
			}
			if (!imagecopyresampled($image_resized, $image_resource, 0, 0, 0, 0, $p['width'], $p['height'], $p['imgwidth'], $p['imgheight'])){
				$error = 'When resizing a PNG the resampled copy could not be made.';

				return false;
			}
			if (!imagepng($image_resized, $p['file+ext'])){
				$error = 'When resizing a PNG the image could not be created in ' . $p['file+ext'] . '.';

				return false;
			}
		break;
	}
	if (!file_exists($p['file+ext'])){
		$error = 'The resized file was created but could not be found at ' . $p['file+ext'] . '.';

		return false;
	}
	imagedestroy($image_resource);
	imagedestroy($image_resized);

	return $p['file+ext'];
}

function image_store_im($p, &$error = null){
	if (!image_store_init($p, $error)){
		// if it returns false we don't need to resize; only if $error is set do we return an error
		// otherwise we assume the image has been copied succesfully
		return empty($error);
	}
	$img = new Imagick($p['temp']);
	$img->resizeImage($p['width'], $p['height']);
	if (!$img->writeImage($p['file'] . '.' . $p['ext'])){
		$error = 'No resizing was attempted, but copying the image to "' . $p['file'] . '" failed. The most likely cause of this is the folder permissions of the destination folder. Please make sure scripts have access to write in this folder.';

		return false;
	}
}

function image_types($check){
	$types = ['jpg', 'png', 'jpeg'];
	if (in_array(strtolower($check), $types)){
		return true;
	}

	return false;
}

function image_valid($file, &$error){
	$image['temp'] = $file['tmp_name'];
	$max_upload_size = get_max_upload_size();
	if ($image['size']>$max_upload_size){
		$error = 'The image you have chosen to upload is too large, it must be under ' . strip_end($max_upload_size, 3) . 'kB.';

		return false;
	}
	$dot = strrpos($file['name'], '.');
	$image['ext'] = strtolower(substr($file['name'], ($dot+1)));
	if (image_types($image['ext'])){
		return $image;
	}
	else {
		$error = 'The image you have uploaded is the wrong file type (' . $image['ext'] . ') - it must be a JPG or PNG file.';

		return false;
	}
}

function image_watermark(&$p, &$error = null){
	if (empty($p['imgwidth'])){
		list($p['imgwidth'], $p['imgheight'], $p['img-type']) = getimagesize($p['temp']);
		$p['img-type'] = image_type_to_mime_type($p['img-type']);
	}

	if (empty($p['watermark'])){
		$p['watermark'] = 'images/watermark.png';
	}
	$watermark = imagecreatefrompng($p['watermark']);
	imagealphablending($watermark, true);
	$watermark_width = imagesx($watermark);
	$watermark_height = imagesy($watermark);

	$watermark_resized_width = $p['imgwidth'] * $p['watermark-ratio'];
	$watermark_resized_height = $p['imgheight'] * $p['watermark-ratio'];
	$watermark_resized = imagecreatetruecolor($watermark_resized_width, $watermark_resized_height);
	imagealphablending($watermark_resized, false);
	imagesavealpha($watermark_resized, true);
	imagecopyresampled($watermark_resized, $watermark, 0, 0, 0, 0, $watermark_resized_width, $watermark_resized_height, $watermark_width, $watermark_height);
	imagedestroy($watermark);

	$image = image_create($p);
	$dest_x = ($p['imgwidth']-$watermark_resized_width) / 2;
	$dest_y = ($p['imgheight']-$watermark_resized_height) / 2;
	imagecopymerge_alpha($image, $watermark_resized, $dest_x, $dest_y, 0, 0, $watermark_resized_width, $watermark_resized_height);
	$p['file'] = $p['file'] . '.' . $p['ext'];
	switch ($p['img-type']){
		case 'image/gif':
			imagegif($image, $p['file']);
		break;
		case 'image/pjpeg':
		case 'image/jpeg':
		case 'image/jpg':
			imagejpeg($image, $p['file'], 75);
		break;
		case 'image/png':
		case 'image/x-png':
			imagealphablending($image, false);
			imagesavealpha($image, true);
			imagepng($image, $p['file']);
		break;
	}
	imagedestroy($image);
	imagedestroy($watermark_resized);
}

function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct = 100, $trans = null){
	$dst_w = imagesx($dst_im);
	$dst_h = imagesy($dst_im);

	// bounds checking
	$src_x = max($src_x, 0);
	$src_y = max($src_y, 0);
	$dst_x = max($dst_x, 0);
	$dst_y = max($dst_y, 0);
	if ($dst_x+$src_w>$dst_w){
		$src_w = $dst_w-$dst_x;
	}
	if ($dst_y+$src_h>$dst_h){
		$src_h = $dst_h-$dst_y;
	}

	for ($x_offset = 0; $x_offset<$src_w; $x_offset++){
		for ($y_offset = 0; $y_offset<$src_h; $y_offset++){
			// get source & dest color
			$src_color = imagecolorsforindex($src_im, imagecolorat($src_im, $src_x+$x_offset, $src_y+$y_offset));
			$dst_color = imagecolorsforindex($dst_im, imagecolorat($dst_im, $dst_x+$x_offset, $dst_y+$y_offset));
			// apply transparency
			if (is_null($trans) or ($src_color!==$trans)){
				$src_a = $src_color['alpha'] * $pct / 100;
				$src_a = 127-$src_a;
				$dst_a = 127-$dst_color['alpha'];
				$dst_r = ($src_color['red'] * $src_a+$dst_color['red'] * $dst_a * (127-$src_a) / 127) / 127;
				$dst_g = ($src_color['green'] * $src_a+$dst_color['green'] * $dst_a * (127-$src_a) / 127) / 127;
				$dst_b = ($src_color['blue'] * $src_a+$dst_color['blue'] * $dst_a * (127-$src_a) / 127) / 127;
				$dst_a = 127-($src_a+$dst_a * (127-$src_a) / 127);
				$color = imagecolorallocatealpha($dst_im, $dst_r, $dst_g, $dst_b, $dst_a);
				if (!imagesetpixel($dst_im, $dst_x+$x_offset, $dst_y+$y_offset, $color)){
					return false;
				}
				imagecolordeallocate($dst_im, $color);
			}
		}
	}

	return true;
}
