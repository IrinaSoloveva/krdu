<?
if(!($q=mysqli_connect('localhost','root','',''))) {
	header('HTTP/1.1 401 Unauthorized');
	printf("Невозможно подключиться к базе данных. Код ошибки: %s\n", mysqli_connect_error());
	exit;
}


$f='Уразгильдеев';
$i='Булат';
$o='Мандаринович';

$rus = 'а б в г д е ё ж з и й к л м н о п р с т у ф х ц ч ш щ ъ ы ь э ю я';
$eng = 'a b v g d e e zh z i i k l m n o p r s t u f kh TS CH SH SHCH IE Y  E IU IA';
//var_dump(explode(' ',$eng));

$login=ucfirst(str_replace(explode(' ',$rus),explode(' ',$eng),mb_strtolower($f))).strtoupper(str_replace(explode(' ',$rus),explode(' ',$eng),mb_strtolower(mb_substr($i,0,1)).mb_strtolower(mb_substr($o,0,1))));
//echo $login;

//echo mb_convert_case(str_replace(explode(' ',$rus),explode(' ',$eng),mb_strtolower($a)), MB_CASE_TITLE, 'UTF-8');
//echo mb_strtolower($a);



//$td_2 = mysqli_fetch_assoc(mysqli_query($q, "SELECT * FROM `bd_metod`.`sogl` WHERE t_name='IvanovII_2019_1' OR t_name='IvanovII_2019_2'"));
//var_dump($td_2);
$rus = 'а б в г д е ё ж з и й к л м н о п р с т у ф х ц ч ш щ ъ ы ь э ю я';
$eng = 'a b v g d e e zh z i i k l m n o p r s t u f kh TS CH SH SHCH IE Y  E IU IA';
$f='Йцу';
$i='Йцу';
$o='Йцу';

//echo ucfirst(str_replace(explode(' ',$rus),explode(' ',$eng),mb_strtolower($f))).strtoupper(str_replace(explode(' ',$rus),explode(' ',$eng),mb_strtolower(mb_substr($i,0,1)).mb_strtolower(mb_substr($o,0,1))));
//var_dump(file_exists('./profile_photos/1.jpg'));

$t_6 = mysqli_fetch_assoc(mysqli_query($q, 'SELECT * FROM `bd_6`.`sogl` WHERE t_name=\'b_2019\''));

if($t_6)echo 'qwe';
var_dump($_SERVER);







