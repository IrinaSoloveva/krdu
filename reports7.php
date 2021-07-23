<?
session_start();
if(!isset($_SESSION['user'])) {
  header('HTTP/1.1 401 Unauthorized');
  header('Location: /');
  exit;
}
$u = &$_SESSION['user'];
require_once('f.php');

switch($u['prava']) {
	case 255:case 300: break;
	default: die('no privilegies'); break;
}

if(!($q = mysqli_connect('localhost', 'root', '', '')))
  die('Невозможно подключиться к базе данных. Код ошибки: '. mysqli_connect_error());

$y = (string)(int)$_POST['y'];
if($y<2019 || $y>date('Y'))
	die('incorrect year');
if($u['prava']==255 && strstr($t_name,'_',true)!=$u['login'] || $u['prava']==300)
	$t_name = ((string)$_POST['login']??$u['login']).'_'.$y;
else
	die('Нет прав');

if(isset($_POST['saveConclusion']) && (int)$_POST['saveConclusion']===1) {
	if(strlen((string)$_POST['conclusion'])<10 || strlen((string)$_POST['conclusion'])>3000)
		die('Вы очень много написали!');
	$text = htmlspecialchars(trim((string)$_POST['conclusion']));
	mysqli_query($q, "DELETE FROM `bd_7`.`sogl` WHERE t_name='$t_name'");
	mysqli_query($q, "INSERT INTO `bd_7`.`sogl` (`t_name`,`date0`,`text`,`u0`) VALUES ('$t_name','".date('d.m.Y H:i:s')."','$text',".$u['n'].')');
	echo 'Сохранено!';
	exit;
}
?>