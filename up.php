<?
$f = &$_FILES['f'];
if($f && substr($f['type'],0,5)=='image' && (int)$_POST['n']>0 && (int)$_POST['n']<10000) {
	$f_path = 'profile_photos\\'.$_POST['n'].'.jpg';
	if(file_exists($f_path))
		rename($f_path, 'profile_photos/'.$_POST['n'].'_'.time().'.jpg');
	switch(getimagesize($f['tmp_name'])[2]) {
		case IMAGETYPE_JPEG:
			$a=imagejpeg(imagecreatefromjpeg($f['tmp_name']),$f_path,100); break;
		case IMAGETYPE_GIF:
			$a=imagegif(imagecreatefromgif($f['tmp_name']),$f_path); break;
		case IMAGETYPE_PNG:
			$a=imagepng(imagecreatefrompng($f['tmp_name']),$f_path); break;
		default:
			die('Что-то пошла не так'); break;
	}
	//move_uploaded_file($f['tmp_name'], 'profile_photos/'.$n.'.jpg');
}
header('HTTP/1.1 301 Moved Permanently');
header('Location: /'.(strpos($_SERVER['HTTP_REFERER'],'kaf')?'kaf.php':''));
?>