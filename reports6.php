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
	case 0:case 255: break;
	default: die('no privilegies'); break;
}

if(!($q = mysqli_connect('localhost', 'root', '', '')))
  die('Невозможно подключиться к базе данных. Код ошибки: '. mysqli_connect_error());

$y = (string)(int)$_POST['y'];
if($y<2019 || $y>date('Y'))
	die('incorrect year');
if($u['prava']==255)
	$t_name = (string)($_POST['login']??$u['login']).'_'.$y;
else
	$t_name = $u['login'].'_'.$y;

if(isset($_POST['approveSuggestion']) && (int)$_POST['approveSuggestion']===1 && $u['prava']==255)
		if(mysqli_query($q, "UPDATE `bd_6`.`sogl` SET soglasovano=2,u2=".$u['n'].",date2='".date('d.m.Y H:i:s')."' WHERE t_name='$t_name'"))
			echo 'Согласовано!';
		else
			echo 'error: approveSuggestion server';

elseif(isset($_POST['saveSuggestion']) && (int)$_POST['saveSuggestion']===1) {
	if(strlen((string)$_POST['suggestion'])<10 || strlen((string)$_POST['suggestion'])>3000)
		die('Вы очень много написали!');
	$text = htmlspecialchars(trim((string)$_POST['suggestion']));
	mysqli_query($q, "DELETE FROM `bd_6`.`sogl` WHERE t_name='$t_name'");
	mysqli_query($q, "INSERT INTO `bd_6`.`sogl` (`t_name`,`date0`,`text`,`soglasovano`,`u2`) VALUES ('$t_name','".date('d.m.Y H:i:s')."','$text',".($u['prava']==255?'2':'1').','.$u['n'].')');
	echo 'Сохранено!';
	exit;
}
?>