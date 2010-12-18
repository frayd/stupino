<?php
require_once("config.php");

//creates xml data for navigation structure of fig
echo '<root name="Home" path="' . $data_folder . '">' . chr(13);

getDirectory("$data_folder");

function getDirectory( $path = '.'){
    $ignore = array('.', '..' );
    // Directories to ignore when listing output. Many hosts
    // will deny PHP access to the cgi-bin.
    $dh = @opendir( $path );
	
    // Open the directory to the handle $dh
    while( false !== ( $file = readdir( $dh ) ) ){
    // Loop through the directory
        if( !in_array( $file, $ignore ) ){
        // Check that this file is not to be ignored
            // show the directory tree.
            if( is_dir( "$path/$file" ) ){
            // Its a directory, is it a folder or a gallery
				if(!file_exists("$path/$file/_thumbs")){					
					//directory is a folder
					echo '<folder path="' . $file . '">' . chr(13);
					getDirectory("$path/$file");
					echo '</folder>' . chr(13);
				}
            }
        }
    }
	
	$dh = @opendir( $path );
	while( false !== ( $file = readdir( $dh ) ) ){
    // Loop through the directory
        if( !in_array( $file, $ignore ) ){
        // Check that this file is not to be ignored
            // show the directory tree.
            if( is_dir( "$path/$file" ) ){
            // Its a directory, is it a folder or a gallery
				if(file_exists("$path/$file/_thumbs")){
					//the directory has a folder for thumbnails, it's a gallery
					echo '<gallery path="' . $file . '" />' . chr(13);
				}
            }
        }
    }
	
    closedir( $dh );
    // Close the directory handle
}

echo '</root>';

?>
