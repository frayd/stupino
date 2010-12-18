<?php
//get path to gallery
$path = isset($_GET["p"]) ? $_GET["p"] : "";

echo "<data>" . chr(13);

$ignore = array('.', '..' );
// Directories to ignore when listing output. Many hosts
// will deny PHP access to the cgi-bin.
$dh = @opendir( $path . "/_thumbs" );
while( false !== ( $file = readdir( $dh ) ) ){
// Loop through the directory
	if( !in_array( $file, $ignore ) ){
	// Check that this file is not to be ignored
		// get images
		if( !is_dir( "$path/_thumbs/$file" ) ){
			//print out xml
			echo '<image file="' . $file . '" />' . chr(13);
		}
	}
}

closedir( $dh );
// Close the directory handle

echo '</data>';
?>