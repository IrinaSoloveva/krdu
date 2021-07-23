<?
$p['f']='Колесникова';
$p['i']='Е';
$p['o']='Ф';
$rus = 'а б в г д е ё ж з и й к л м н о п р с т у ф х ц ч ш щ ъ ы ь э ю я';
$eng = 'a b v g d e e zh z i i k l m n o p r s t u f kh ts ch sh shch ie y  e iu ia';

	$f = mb_convert_case(mb_strtolower(trim($p['f'])), MB_CASE_TITLE, 'UTF-8');
	$i = mb_convert_case(mb_strtolower(trim($p['i'])), MB_CASE_TITLE, 'UTF-8');
	$o = mb_convert_case(mb_strtolower(trim($p['o'])), MB_CASE_TITLE, 'UTF-8');
	$login=strtoupper(str_replace(explode(' ',$rus),explode(' ',$eng),mb_strtolower(mb_substr($f,0,1)))).str_replace(explode(' ',$rus),explode(' ',$eng),mb_strtolower(mb_substr($f,1))).strtoupper(str_replace(explode(' ',$rus),explode(' ',$eng),mb_strtolower(mb_substr($i,0,1)).mb_strtolower(mb_substr($o,0,1))));
	echo $login;