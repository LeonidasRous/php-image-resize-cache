<?php
// HERE IS THE PATH TO IMAGE FOLDER
$media_folder = '../media/';
// HERE IS THE PATH TO TMP FOLDER
$tmp_folder = 'tmp/';
// GET THE IMAGE PATH FROM URL AND CHECK IF IMAGE EXISTS
$filename = $media_folder.$_REQUEST['file'];
if( !file_exists( $filename ) )
{
    // THIS IS THE DEFAULT IMAGE
    $default_image = 'no_image.png';
    $filename = $media_folder . $default_image;
}
//if image is svg
if($ext == 'svg'){
    header('Content-type: image/svg+xml');
    $file = file_get_contents($filename);
    echo $file;
    exit;
}
$aa = getimagesize( $filename );
$width = $aa[0];
$height = $aa[1];
$mime = $aa['mime'];
$ext = explode('/' , $mime)[1];
// HEADERS FROM CONTENT TYPE AND CACHE-CONTROL AND BROWSER CACHING
header('Pragma: public');
header('Cache-Control: max-age=604800');
header('Expires: '. gmdate('D, d M Y H \G\M\T', time() + 186400));
header('Content-type: '.$mime);
// HERE IS THE TEMPORARY IMAGE NAME THAT USED TO SAVE THE
$tmpimagename = explode('/' , $_REQUEST['file'] );
$tmpimagename = implode('_' , $tmpimagename );
$tmpimagename = $_REQUEST['hsize'] . '_' . $_REQUEST['wsize'] . '_' . $tmpimagename;
// CHECKING IF IMAGE EXISTS OR USE {img_url}?decache=1 TO REMAKE THE IMAGE
if( file_exists( $tmp_folder . $tmpimagename ) && ( !isset( $_REQUEST['decache'] ) || ( isset( $_REQUEST['decache'] ) && $_REQUEST['decache'] != 1 ) ) )
{
    // GET TMP IMAGE AND DISPLAY IT
    $file = file_get_contents( $tmp_folder . $tmpimagename );
    echo $file;
    exit;
}


/*
 *
 *  CALCULATE THE SIZE OF THE IMAGE
 *
 *  IF `hsize` AND `wsize` ARE BIGGER THAN ZERO THEN THE IMAGE CROPED TO EXACTLY THIS SIZE
 *  ELSE IF WIDTH IS ZERO THEN THE HEIGHT OF THE IMAGE WILL BE THE SETTED SIZE AND THE OTHER WILL BE CALCULATED WITH THE IMAGE RATIO
 *  ELSE IF HEIGHT IS ZERO THEN THE WIDTH OF THE IMAGE WILL BE THE SETTED SIZE AND THE OTHER WILL BE CALCULATED WITH THE IMAGE RATIO
 *  ELSE IF BOTH OF THEM IS ZERO THE IMAGE WILL BE TO HIS ORIGINAL SIZE
 *
 * */

$b=1;
$new_width = $width * $b;
$new_height = $height * $b;
$cropImage = 0;
if( $_REQUEST['hsize'] > 0 && $_REQUEST['wsize'] > 0 )
{
    $cropImage = 1;
    $thumb_width = $_REQUEST['wsize'];
    $thumb_height = $_REQUEST['hsize'];

    $original_aspect = $width / $height;
    $thumb_aspect = $thumb_width / $thumb_height;

    if ( $original_aspect >= $thumb_aspect )
    {
        // If image is wider than thumbnail (in aspect ratio sense)
        $new_height = $thumb_height;
        $new_width = $width / ($height / $thumb_height);
    }
    else
    {
        // If the thumbnail is wider than the image
        $new_width = $thumb_width;
        $new_height = $height / ($width / $thumb_width);
    }

}
else if( $_REQUEST['hsize'] > 0 && $_REQUEST['wsize'] == 0 )
{

    $a = 100 * $_REQUEST['hsize'];
    $b = $a / $height;
    $b = $b / 100;
    $new_width = $width * $b;
    $new_height = $height * $b;

}
else if( $_REQUEST['wsize'] > 0 && $_REQUEST['hsize'] == 0 )
{

    $a = 100 * $_REQUEST['wsize'];
    $b = $a / $width;
    $b = $b / 100;
    $new_width = $width * $b;
    $new_height = $height * $b;

}

// Resample
if( $cropImage == 1 ) {
    $image_p = imagecreatetruecolor($thumb_width, $thumb_height);
} else {
    $image_p = imagecreatetruecolor($new_width, $new_height);
}
imagealphablending( $image_p , false );
$transparency = imagecolorallocatealpha( $image_p, 0, 0, 0, 127);
imagefill( $image_p, 0, 0, $transparency );
imagesavealpha( $image_p, true );
$ext = strtolower( $ext );
switch ( $ext ) {
    case 'jpg':
    case 'jpeg':
        $image = imagecreatefromjpeg( $filename );
        break;
    case 'gif':
        $image = imagecreatefromgif( $filename );
        break;
    case 'png':
        $image = imagecreatefrompng( $filename );
        break;
    default:
        $image = false;
        break;
}
if( $cropImage == 1 ) {
    imagecopyresampled($image_p, $image, 0 - ($new_width - $thumb_width) / 2, 0 - ($new_height - $thumb_height) / 2, 0, 0, $new_width, $new_height, $width, $height);
} else {
    imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
}
// Output
switch ( $ext ) {
    case 'jpg':
    case 'jpeg':
        imagejpeg($image_p, $tmp_folder . $tmpimagename , 85);
        break;
    case 'gif':
        imagegif($image_p, $tmp_folder . $tmpimagename );
        break;
    case 'png':
        imagepng($image_p, $tmp_folder . $tmpimagename );
        break;
    default:
        $image = false;
        break;
}

$file = file_get_contents( $tmp_folder . $tmpimagename );
echo $file;
