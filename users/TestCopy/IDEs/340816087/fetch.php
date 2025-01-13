<?php
$file_path = 'content.txt';
$fileContents = file_get_contents($file_path);
if (file_exists($file_path)) {
    echo $fileContents;
} else {
	echo $fileContents;
}
?>
