<?php
$tmp_folder = 'tmp/';

$search = '*';

if(isset($_REQUEST['start_with'])){
    $search = $_REQUEST['start_with'] . $search;
}

if(isset($_REQUEST['end_with'])){
    $search .= $_REQUEST['end_with'];
}

$files = glob($tmp_folder . $search ); // get all file names

$deletedFiles = 0;
$allFiles = 0;
foreach($files as $file){ // iterate files
    if(is_file($file)){
        $allFiles++;
        $a=date("d-m-Y H:i:s", filemtime($file));

        if(isset($_REQUEST['clear']) && $_REQUEST['clear']=='all'){
            $b=date("d-m-Y H:i:s");
        } else {
            $b=date("d-m-Y H:i:s", strtotime('-1 month'));
        }

        $diff=date_diff(date_create($a),date_create($b));

        if($diff->days>0 && $diff->invert==0){
            echo "$file was last modified: " . date ("d-m-Y H:i:s", filemtime($file));
            echo "<br/>";
            echo "<br/>".date("d-m-Y H:i:s",strtotime('-1 month'));
            echo "<br/>";
            echo "<br/>";
            unlink($file); // delete file
            $deletedFiles++;
        }
    }
}

echo "<h1>CLEAR TMP FILES</h1>";
echo "<h3>{$deletedFiles} / {$allFiles} FILES CLEARED...</h3>";
