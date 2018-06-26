<?php
	// The file
//	include 'admin/common/common.php';
	$filename = '../media/';
	$filename = $filename.$_REQUEST['file'];
	if(!file_exists($filename)){
		$filename='../media/files/1/TV  Photo archive/tv_photos_980_400/enimerwsi/skailogo mikro.jpg';
	}
	$percent = 0.5; // percentage of resize
//echo $filename;
    // Content type
	$ext=explode('.',$_REQUEST['file']);
	$ext=$ext[count($ext)-1];

	$aa=getimagesize($filename);
	//print_r($aa);
	$width=$aa[0];
	$height=$aa[1];
	$mime=$aa[mime];

	header('Pragma: public');
	header('Cache-Control: max-age=604800');
	header('Expires: '. gmdate('D, d M Y H \G\M\T', time() + 186400));
	header('Content-type: '.$mime);

	$tmpimagename=explode('/',$_REQUEST['file']);
	$tmpimagename=implode('_',$tmpimagename);
	$tmpimagename=$_REQUEST[hsize].'_'.$_REQUEST[wsize].'_'.$tmpimagename;
	if(file_exists('tmp/'.$tmpimagename) && !isset($_REQUEST['decache'])){
		$file=file_get_contents('tmp/'.$tmpimagename);
		echo $file;
		exit();
	}
	// Get new dimensions
	//	list($width, $height,$a,$b,$c,$d,$e,$f,$mime) = getimagesize($filename);

	//exit();
	if($_REQUEST[hsize]>0 && $_REQUEST[wsize]>0) {
		$new_width = $_REQUEST[wsize];
		$new_height = $_REQUEST[hsize];
	} else if($_REQUEST[hsize]>0 && $_REQUEST[wsize]==0){
	    if ($width > $height) {
	        $a = 100 * $_REQUEST[hsize];
	        $b = $a / $width;
	        $b = $b / 100;
	    } else {
	        $a = 100 * $_REQUEST['hsize'];
	        $b = $a / $height;
	        $b = $b / 100;
	    }
	    $new_width = $width * $b;
		$new_height = $height * $b;
	} else {
	    $b=1;
		$new_width = $width * $b;
		$new_height = $height * $b;
	}

	// Resample
	$image_p = imagecreatetruecolor($new_width, $new_height);
	imagealphablending($image_p, false);
	$transparency = imagecolorallocatealpha($image_p, 0, 0, 0, 127);
	imagefill($image_p, 0, 0, $transparency);
	imagesavealpha($image_p, true);

	$ext = strtolower($ext);

	switch ($ext) {
		case 'jpg':
		case 'jpeg':
			$image = imagecreatefromjpeg($filename);
			break;
		case 'gif':
			$image = imagecreatefromgif($filename);
			break;
		case 'png':
			$image = imagecreatefrompng($filename);
			break;
		default:
			$image = false;
			break;
	}

	imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

	// Output
	switch ($ext) {
		case 'jpg':
		case 'jpeg':
			imagejpeg($image_p, "tmp/$tmpimagename", 85);
			break;
		case 'gif':
			imagegif($image_p, "tmp/$tmpimagename");
			break;
		case 'png':
			imagepng($image_p, "tmp/$tmpimagename");
			break;
		default:
			$image = false;
			break;
	}
	$file=file_get_contents('tmp/'.$tmpimagename);
	echo $file;
?>