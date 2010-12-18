<?php
require_once("config.php");

require_once('phpThumb/ThumbLib.inc.php');

makeThumb("$data_folder", $thumb_width, $thumb_height, $crop_to_fit);

function makeThumb( $path = '.', $width = 100, $height = 100, $crop = false){
    $ignore = array('.', '..', '_thumbs' );
    // Directories to ignore.
    $dh = @opendir( $path );
    // Open the directory to the handle $dh
    while( false !== ( $file = readdir( $dh ) ) ){
    // Loop through the directory

        if( !in_array( $file, $ignore ) ){
        // Check that this file is not to be ignored
            
            if( is_dir( "$path/$file" ) ){
            // Its a directory					
				makeThumb("$path/$file", $width, $height, $crop);
            } else {
			// Its a file
				$file_parts = explode(".",$file);
				$file_type = array_pop($file_parts); //returns file extension. e.g. jpg, gif, png, txt, etc.
				
				if(eregi('(jpg|jpeg|gif|png)$', $file_type)){
					//file is an image
					
					if(!file_exists("$path/_thumbs")){
					// there is no _thumbs folder. make one.
						mkdir("$path/_thumbs", 0777);
						echo "Made $path/_thumbs folder.<br />";
					}
					
					if(!file_exists("$path/_thumbs/$file")){
						// thumbnail for file does not exist. make one.
						$thumb = PhpThumbFactory::create("$path/$file");
						
						// do your manipulations
						if($crop){
							$thumb->adaptiveResize($width, $height);
						} else {
							$thumb->resize($width, $height);
						}
						$thumb->save("$path/_thumbs/$file");
						echo("thumbnail created:<br /><img src=\"$path/_thumbs/$file\">");
					}
				}
			}
        }
    }
	
    closedir( $dh );
    // Close the directory handle
}
?>