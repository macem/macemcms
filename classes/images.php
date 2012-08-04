<?php

/**
 * class Images
 * @author macem
 * @package macemCMS
*/

Class Images
{


	function exists ($path) {
		//echo getcwd().$path;
		if (is_dir (getcwd().$path) || file_exists (getcwd().$path)) {
			return '{"code":"500", "result":"File or folder exist"}';
		}
		return '{"code":"200", "result":"File doesnt exist"}';	
	}
	
	/**
	 * method connect to database
	 * @param $db - database: [mysql]
	*/
	function thumbnails ($path) {
		
		$folder = @dir (getcwd().$path);
		
		//echo $path;
		if (!$folder->path) return 'Folder "'.$path.'" doesn\'t exists!';
		
		$images = array();
		while (false !== ($entry = $folder->read()) ) {
			$file_path = $path.$entry;
			$io_path   = getcwd().$file_path;
			
			if ($entry != '.htaccess' && $entry != '..' && $entry != '.') {
		
				$row = array();
				$row['name'] = htmlspecialchars ($entry);
				$row['href'] = $file_path;
				$row['type'] = strtolower (substr ($entry, strpos ($entry, '.')+1));
				
				if (is_file (str_replace ('-thumbnail', '', $io_path))) {
					$row['info'] = getimagesize (str_replace ('-thumbnail', '', $io_path));
					$row['filesize'] = round ((int)filesize (str_replace ('-thumbnail', '', $io_path))/1024, 1);
				}

				// only thumbnails
				if (strchr ($entry, '-thumbnail')) {
					array_push ($images, $row);					
				}

			}
		}
		
		//print_r ($images);
		return $images;
	}

	/*
	 * method delete image and thumbnail from disk
	 * $thumbnail = true/no
	*/
	function deleteFolder ($path)	{
		
		if (is_dir (getcwd().$path)) {

			if (@rmdir (getcwd().$path)) {
				return "Folder został skasowane.";
			} else {
				return "Nie skasowano folderu, folder może zawierać pliki !.";
			}
			
		} else {
			return 'Folder niestnieje !.';
		}
	}
		
	/*
	 * method
	*/
	function addFolder ($path)	{
		
		if (!is_dir (getcwd().strtolower ($path)) ) {
			if (@mkdir (getcwd().strtolower ($path), 0755)) {
				return "folder was added.";
			} else {
				return "error: folder cannot be create, maybe You dont have permission !.";
			}
		} else {
			return "error: folder exists !.";
		}
	}
	
	/*
	 * method
	*/
	function getFile ($path, $mode='files')	{

		$link = getcwd().$path;
			
	    $filelist = '';
		$folderlist = '';
		
		if ($mode == 'folders') $folderlist = "<div><a href='".$path."' class='folder root' title='otwórz ".$path."'>.</a></div>";
		
		if ($folder = dir ($link) ) {
			while ( false !== ($entry = $folder->read() ) ) {
				$itemPath = $path.'/'.$entry;
				
				// files
				if ($mode == 'files' && $entry!="." && $entry!=".." && !is_dir ($link.'/'.$entry) && Images::supportFiles ($entry, 'image') ) {

					// init size
					$size = (int)filesize ($link."/".$entry);
					if ($size/1024 > 0) {
						$size = round ($size/1024, 1)." kb";
					} else if ($size/1024/1024 > 0) {
            			$size = round ($size/1024/1024, 1)." Mb";
					} else {
						$size .= " b";
					}
					// TODO $GLOBALS['config']['host']
					$filelist .= "<div><a href='".$itemPath."' class='delete' title='usuń obrazek : ".$entry."'>usuń</a><span class='size'>".$size."</span>";
					$filelist .= "<a href='".$itemPath."' class='file' title='wybierz'><img src='".HOST.$itemPath."'/></a><strong>".$entry."</strong></div>";
				}
				// folders
				if ($mode == 'folders' && $entry!="." && $entry!=".." && is_dir ($link.'/'.$entry) == 1 ) {
					$folderlist .= "<div><a href='".$itemPath."' title='usuń folder : ".$entry."' class='delete'>usuń</a>";
					$folderlist .= "<a href='".$itemPath."' class='folder' title='otwórz ".$itemPath."'>".$entry."</a></div>";
				}
				
			}
			$folder->close();
		}
		
		return $folderlist.$filelist;
	}
	
	/*
	 * method delete image and thumbnail from disk
	 * $thumbnail = true/no
	*/
	function delete ($path, $thumbnail)	{
		$imgPath = str_replace ('-thumbnail', '', $path);
		if (file_exists ($imgPath)) {
			if (@unlink ($imgPath)) {
				if ($thumbnail != 'no' && file_exists ($path)) {
					if (!@unlink ($path)) {
						    return "Nie skasowano miniatury zdjęcia";
					}
				}
				return true;
			} else {
				return "Nie skasowano zdjęcia";
			}
		}
	}
	
	/*
	 * method which add unikate sign to mysql_query
	 * $thumbnail = true/no
	*/
	function uploadFile ($path, $filter, $thumbnail)	{
		// TODO lowercase
		$pathpic = $path;
		
		if (!empty($_FILES['photo']['name'])) {		
			
			//sleep (10);
			
			$pathFile = $pathpic.basename (strtolower($_FILES['photo']['name']));
			$pos = strpos (basename($_FILES['photo']['name']), '.');
			$pathThumbnail = $pathpic.substr (strtolower($_FILES['photo']['name']), 0,$pos).'-thumbnail'.substr (strtolower($_FILES['photo']['name']), $pos, strlen ($_FILES['photo']['name']));
			
			$size = round($_SERVER['CONTENT_LENGTH'], 0); // Mb
			
			if ($size > 1048576) return "Za duży rozmiar pliku (".$size."kb), zmniejszy rozmiar pliku do max. ".($size/1024)."kb.";
			
			if (!Images::supportFiles ($pathFile, 'image')) return "To nie jest plik JPG,PNG,GIF.";
												
			if (!file_exists ($pathpic.$_FILES['photo']['name'])) {
				if (move_uploaded_file ($_FILES['photo']['tmp_name'], $pathFile) ) {
                    
					// thumbnail generate
					if ($thumbnail != 'no') {
						$thumbWidth = 200;
					      $img = imagecreatefromjpeg ($pathFile);
					      $width = imagesx ($img);
					      $height = imagesy ($img);
					      $new_width = $thumbWidth;
					      $new_height = floor ($height * ($thumbWidth / $width));
					      $x = 0;
					      $y = 0;
					      if ($new_height > 134) {
							$x = floor (($height-((134/$new_height)*$height))/2);
							$new_height = 134;
						} 
					      if ($new_height < 134) {
							$y = floor (($width-(($new_height/134)*$width))/2);
							$new_height = 134;
						} 
			
						$tmp_img = imagecreatetruecolor( $new_width, $new_height );
					
					      // copy and resize old image into new image
					      imagecopyresampled( $tmp_img, $img, 0, 0, $y, $x, $new_width, $new_height, $width-($y*2), $height-($x*2) );
					      
					      if ($filter == 'greyscale') { imagefilter ($tmp_img, IMG_FILTER_GRAYSCALE); }
					
					      // save thumbnail into a file
					      imagejpeg( $tmp_img, $pathThumbnail, 60);							
					}          

					return true;
					//}				} else {
				  //return "Nie dodano zdjecia, wystąpił błąd.";
				}
			} else {
		    		return "Plik z tą nazwą istnieje.";
			}
		} else {
			return "Nie wczytano pliku. Spróbuj ponownie.";
		}
	}

	// supported files for editor
	function supportFiles($filename, $type) {

		 $imageType = array(
	       '.gif'=>'GIF',
	       '.jpg'=>'JPG',
	       '.png'=>'PNG'
	   );
	   
	   $videoType = array(
	       '.avi'=>'AVI',
	       '.mpg'=>'MPG',
	       '.mov'=>'MOV'
	   );
	   
	   $flashType = array(
	       '.swf'=>'SWF'
	   );
	   
	   $excludeType = array(
	       '.js'=>'JS'
	   );

	   switch ($type) {
				case "video":
				  $selectType = $videoType;
					break;
				case "flash":
				  $selectType = $flashType;
					break;
				case "image":
				  $selectType = $imageType;
					break;
				default:
	          		foreach ($excludeType as $i => $extansion) {
		       			if (strpos(strtolower($filename), $i)) return false;
		   			}
					return true;
				  break;
		 }
		 
	   foreach ($selectType as $i => $extansion) {
	       if (strpos(strtolower($filename), $i)) return true;
	   }
     return false;
	}
	
}; // end class
?>