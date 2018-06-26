<?php

$files = glob('tmp/*'); // get all file names
foreach($files as $file){ // iterate files
    if(is_file($file)){

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
        }
    }
}
