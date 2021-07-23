<?
function sem() {
	return ((int)date('n')<9)?2:1;
}
function y() {
	return (int)date('y'); //19
}
function vd(&$q) {
	var_dump($q);
}
function fio_cut($f=null, $i=null, $o=null) {
	if(!$f) { global $u; $f=&$u['f']; $im=&$u['i']; $o=&$u['o'];}
	return $f.' '.mb_substr($im,0,1).'.'.mb_substr($o,0,1).'.';
}