<?
session_start();
if(!isset($_SESSION['user'])) {
  header('HTTP/1.1 401 Unauthorized');
  header('Location: /');
  exit;
}
$u = &$_SESSION['user'];
if($u['prava'] == 255)
	$kod = $u['kod'];
elseif($u['prava']>0)
	$kod = (int)$_GET['kod'];
else
	exit;
require_once('f.php');

if(!($q = mysqli_connect('localhost', 'root', '', '')))
  die('Невозможно подключиться к базе данных. Код ошибки: '. mysqli_connect_error());

if(isset($_GET['y'])) {
	$y = (int)$_GET['y'];
	if($y<2019 || $y>2090)
		die('error: incorrect year server');
} else
	$y = sem()==1?(int)date('Y'):(int)date('Y')-1;

$rus = 'а б в г д е ё ж з и й к л м н о п р с т у ф х ц ч ш щ ъ ы ь э ю я';
$eng = 'a b v g d e e zh z i i k l m n o p r s t u f kh ts ch sh shch ie y  e iu ia';
?>
<!DOCTYPE html>
<html lang="ru">

<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<title>ЭИПП | Статистика по кафедре</title>

<link rel="stylesheet" href="assets/fonts/css/all.css">
<link href="assets/css/app.css" rel="stylesheet">
<style>
img.inspector { 
	max-height: 56px;
	max-width: 56px;
}
.table_block {
	display: inline-block;
	width: 115px;
}
.btn_save {
	padding: 9px;
	border-top: 1px solid #dee2e6;
}
.btn_save:hover {
	background: #eef;
}

.table thead th {
	vertical-align: baseline;
	text-align: justify;
	border: 2px solid #dee2e6;
}
.table td {
	border: 1px solid #dee2e6;
}
</style>
</head>

<body>
	<div class="wrapper">
		<div class="main">
			<nav class="navbar navbar-expand navbar-light bg-white">
			<a class="sidebar-brand" href="/">
          <i class="align-middle fa fa-book" style="color:#51BAC0; margin-right:10px;"></i>
          <span class="align-middle">КРу МВД России</span>
        </a>
        	<div class="den">
        		<ul><?include('menu.php');?></ul>
        	</div>
				<div class="navbar-collapse collapse">
					<ul class="navbar-nav ml-auto">
						<li class="nav-item dropdown">
                <img src="/profile_photos/<?=$u['n']?>.jpg?<?=time()?>" class="avatar img-fluid rounded-circle mr-1" alt> <span style="color:#fff;"><?=fio_cut()?></span>
						</li>
					</ul>
				</div>
			</nav>


<main class="content">
<div class="container-fluid p-0">
<div class="row">
<div class="card flex-fill">
<div class="card-header">
	<h5 class="card-title mb-0 text-center"><form><?if($u['prava']==255)echo $u['podr']; else{?><select name=kod onchange="this.parentNode.submit()"><option disabled<?=(!$kod)?' selected':''?>>Выберите подразделение</option><?
$units = mysqli_query($q, 'SELECT * FROM `main`.`podrazdeleniya`');
while($unit=mysqli_fetch_assoc($units)) {
	if(in_array($unit['kod'],array(0,1,2,3,4,5))){continue;}
	if($unit['kod']==$kod)
		echo '<option value="'.$unit['kod'].'" selected>'.$unit['nazvanie'].'</option>';
	else
		echo '<option value="'.$unit['kod'].'">'.$unit['nazvanie'].'</option>';
}
?></select><?}?> <select name=y onchange="this.parentNode.submit()"><?
for($i=2019; $i<=date('Y'); $i++) {
	if($i==$y)
		echo '<option value="'.$i.'" selected>'.($i.'/'.($i+1)).'</option>';
	else
		echo '<option value="'.$i.'">'.($i.'/'.($i+1)).'</option>';
}
?></select></form></h5>
</div>
<?if($u['prava']!=255 && !isset($_GET['kod'])) die('</div></div></div></main></div></div></body></html>');?>
<table class="table table-striped my-0">
<thead>
	<tr>
	<th>ФИО</th>
	<th width=92>Фото</th>
<?if($u['prava']==255 || $u['kod']==1 || $u['prava']==300) {?>
	<th>Учебная нагрузка</th>
<?}?>
<?if($u['prava']==255 || $u['kod']==2 || $u['prava']==300) {?>
	<th>Методическая работа</th>
<?}?>
<?if($u['prava']==255 || $u['kod']==3 || $u['prava']==300) {?>
	<th>Научно-исследовательская работа</th>
<?}?>
<?if($u['prava']==255 || $u['kod']==4 || $u['prava']==300) {?>
	<th>Воспитательная работа</th>
<?}?>
<?if($u['prava']==255 || $u['prava']==300) {?>
	<th>Другие виды работ</th>
<?}?>
	<th>План</th>
	<th>Факт</th>
	<th>Предложения преподавателя</th>
<?if($u['prava']==255 || $u['prava']==300) {?>
	<th>Заключение о качестве работы преподавателя</th>
<?}?>
	</tr>
</thead>
<tbody>
<?
$res = mysqli_query($q, 'SELECT * FROM `main`.`users` WHERE kod='.$kod);
while($r=mysqli_fetch_assoc($res)) {

$bd_1 = mysqli_fetch_assoc(mysqli_query($q, 'SELECT * FROM `bd_1`.`sogl` WHERE t_name=\''.$r['login'].'_'.$y.'_1\''));
$bd_2[0] = mysqli_fetch_assoc(mysqli_query($q, 'SELECT * FROM `bd_2`.`sogl` WHERE t_name=\''.$r['login'].'_'.$y.'_1\''));
$bd_2[1] = mysqli_fetch_assoc(mysqli_query($q, 'SELECT * FROM `bd_2`.`sogl` WHERE t_name=\''.$r['login'].'_'.$y.'_2\''));
$bd_3[0] = mysqli_fetch_assoc(mysqli_query($q, 'SELECT * FROM `bd_3`.`sogl` WHERE t_name=\''.$r['login'].'_'.$y.'_1\''));
$bd_3[1] = mysqli_fetch_assoc(mysqli_query($q, 'SELECT * FROM `bd_3`.`sogl` WHERE t_name=\''.$r['login'].'_'.$y.'_2\''));
$bd_4[0] = mysqli_fetch_assoc(mysqli_query($q, 'SELECT * FROM `bd_4`.`sogl` WHERE t_name=\''.$r['login'].'_'.$y.'_1\''));
$bd_4[1] = mysqli_fetch_assoc(mysqli_query($q, 'SELECT * FROM `bd_4`.`sogl` WHERE t_name=\''.$r['login'].'_'.$y.'_2\''));
$bd_5 = mysqli_fetch_assoc(mysqli_query($q, 'SELECT * FROM `bd_5`.`sogl` WHERE t_name=\''.$r['login'].'_'.$y.'\''));
$bd_6 = mysqli_fetch_assoc(mysqli_query($q, 'SELECT * FROM `bd_6`.`sogl` WHERE t_name=\''.$r['login'].'_'.$y.'\''));
$bd_7 = mysqli_fetch_assoc(mysqli_query($q, 'SELECT * FROM `bd_7`.`sogl` WHERE t_name=\''.$r['login'].'_'.$y.'\''));

switch($u['kod']) {
	case 1: $plan=$bd_1['plan']; $fakt=$bd_1['fakt']; break;
	case 2: $plan=$bd_2[0]['plan']+$bd_2[1]['plan']; $fakt=$bd_2[0]['fakt']+$bd_2[1]['fakt']; break;
	case 3: $plan=$bd_3[0]['plan']+$bd_3[1]['plan']; $fakt=$bd_3[0]['fakt']+$bd_3[1]['fakt']; break;
	case 4: $plan=$bd_4[0]['plan']+$bd_4[1]['plan']; $fakt=$bd_4[0]['fakt']+$bd_4[1]['fakt']; break;
	default: $plan=$bd_1['plan']+$bd_2[0]['plan']+$bd_2[1]['plan']+$bd_3[0]['plan']+$bd_3[1]['plan']+$bd_4[0]['plan']+$bd_4[1]['plan']+$bd_5['plan'];
						$fakt=$bd_1['fakt']+$bd_2[0]['fakt']+$bd_2[1]['fakt']+$bd_3[0]['fakt']+$bd_3[1]['fakt']+$bd_4[0]['fakt']+$bd_4[1]['fakt']+$bd_5['fakt']; break;
}
?>
	<tr<?=$r['active']?'':' style="background:#eee"'?>>
	<td><?=$r['f']?> <?=$r['i']?> <?=$r['o']?> (<?=$r['login']?>)</td>
	<td><img src="/profile_photos/<?=$r['n']?>.jpg?<?=time()?>" width="56" height="56" class="rounded-circle mr-3 inspector" alt></td>
<?if($u['prava']==255 || $u['kod']==1 || $u['prava']==300) {?>
	<td class="text-center"><a class=bulat href="/reports1.php?y=<?=$y?>&login=<?=$r['login']?>"><span><?
            if(!$bd_1['soglasovano2']) switch($bd_1['soglasovano']) {
                default: case null: echo '-'; break;
                case 0: echo '&#128394;'; break;
                case 1: echo '&#10068;'; break;
                case 2: echo '&#10069;'; break;
                case 3: echo '&#10071;'; break;
                case 4: echo '&#10004;'; break;
            } else switch($bd_1['soglasovano2']) {
                case 0: echo '&#10004;'; break;
                case 1: echo '&#10068;&#10068;'; break;
                case 2: echo '&#10069;&#10069;'; break;
                case 3: echo '&#9989;'; break;
            }
                ?></span></a></td>
<?}?>
<?if($u['prava']==255 || $u['kod']==2 || $u['prava']==300) {?>
	<td><a class=bulat href="/reports2.php?sem=1&y=<?=$y?>&login=<?=$r['login']?>"><span><?
if(!$bd_2[0]['soglasovano2']) switch($bd_2[0]['soglasovano']) {
	default: case null: echo '-'; break;
	case 0: echo '&#128394;'; break;
	case 1: echo '&#10068;'; break;
	case 2: echo '&#10069;'; break;
	case 3: echo '&#10071;'; break;
	case 4: echo '&#10004;'; break;
} else switch($bd_2[0]['soglasovano2']) {
	case 0: echo '&#10004;'; break;
	case 1: echo '&#10068;&#10068;'; break;
	case 2: echo '&#10069;&#10069;'; break;
	case 3: echo '&#9989;'; break;
}
?></span></a><a class=bulat href="/reports2.php?sem=2&y=<?=$y?>&login=<?=$r['login']?>"><span class="float-right"><?
if(!$bd_2[1]['soglasovano2']) switch($bd_2[1]['soglasovano']) {
	default: case null: echo '-'; break;
	case 0: echo '&#128394;'; break;
	case 1: echo '&#10068;'; break;
	case 2: echo '&#10069;'; break;
	case 3: echo '&#10071;'; break;
	case 4: echo '&#10004;'; break;
} else switch($bd_2[1]['soglasovano2']) {
	case 0: echo '&#10004;'; break;
	case 1: echo '&#10068;&#10068;'; break;
	case 2: echo '&#10069;&#10069;'; break;
	case 3: echo '&#9989;'; break;
}
?></span></a></td>
<?}?>
<?if($u['prava']==255 || $u['kod']==3 || $u['prava']==300) {?>
	<td><a class=bulat href="/reports3.php?sem=1&y=<?=$y?>&login=<?=$r['login']?>"><span><?
if(!$bd_3[0]['soglasovano2']) switch($bd_3[0]['soglasovano']) {
	default: case null: echo '-'; break;
	case 0: echo '&#128394;'; break;
	case 1: echo '&#10068;'; break;
	case 2: echo '&#10069;'; break;
	case 3: echo '&#10071;'; break;
	case 4: echo '&#10004;'; break;
} else switch($bd_3[0]['soglasovano2']) {
	case 0: echo '&#10004;'; break;
	case 1: echo '&#10068;&#10068;'; break;
	case 2: echo '&#10069;&#10069;'; break;
	case 3: echo '&#9989;'; break;
}
?></span></a><a class=bulat href="/reports3.php?sem=2&y=<?=$y?>&login=<?=$r['login']?>"><span class="float-right"><?
if(!$bd_3[1]['soglasovano2']) switch($bd_3[1]['soglasovano']) {
	default: case null: echo '-'; break;
	case 0: echo '&#128394;'; break;
	case 1: echo '&#10068;'; break;
	case 2: echo '&#10069;'; break;
	case 3: echo '&#10071;'; break;
	case 4: echo '&#10004;'; break;
} else switch($bd_3[1]['soglasovano2']) {
	case 0: echo '&#10004;'; break;
	case 1: echo '&#10068;&#10068;'; break;
	case 2: echo '&#10069;&#10069;'; break;
	case 3: echo '&#9989;'; break;
}
?></span></a></td>
<?}?>
<?if($u['prava']==255 || $u['kod']==4 || $u['prava']==300) {?>
	<td><a class=bulat href="/reports4.php?sem=1&y=<?=$y?>&login=<?=$r['login']?>"><span><?
if(!$bd_4[0]['soglasovano2']) switch($bd_4[0]['soglasovano']) {
	default: case null: echo '-'; break;
	case 0: echo '&#128394;'; break;
	case 1: echo '&#10068;'; break;
	case 2: echo '&#10069;'; break;
	case 3: echo '&#10071;'; break;
	case 4: echo '&#10004;'; break;
} else switch($bd_4[0]['soglasovano2']) {
	case 0: echo '&#10004;'; break;
	case 1: echo '&#10068;&#10068;'; break;
	case 2: echo '&#10069;&#10069;'; break;
	case 3: echo '&#9989;'; break;
}
?></span></a><a class=bulat href="/reports4.php?sem=2&y=<?=$y?>&login=<?=$r['login']?>"><span class="float-right"><?
if(!$bd_4[1]['soglasovano2']) switch($bd_4[1]['soglasovano']) {
	default: case null: echo '-'; break;
	case 0: echo '&#128394;'; break;
	case 1: echo '&#10068;'; break;
	case 2: echo '&#10069;'; break;
	case 3: echo '&#10071;'; break;
	case 4: echo '&#10004;'; break;
} else switch($bd_4[1]['soglasovano2']) {
	case 0: echo '&#10004;'; break;
	case 1: echo '&#10068;&#10068;'; break;
	case 2: echo '&#10069;&#10069;'; break;
	case 3: echo '&#9989;'; break;
}
?></span></a></td>
<?}?>
<?if($u['prava']==255 || $u['prava']==300) {?>
	<td class="text-center"><a class=bulat href="/reports5.php?y=<?=$y?>&login=<?=$r['login']?>"><?
if(!$bd_5['soglasovano2']) switch($bd_5['soglasovano']) {
	default: case null: echo '-'; break;
	case 0: echo '&#128394;'; break;
	case 1: echo '&#10068;'; break;
	case 2: echo '&#9989;'; break;
}
?></a></td>
<?}?>
	<td class="text-center"><?=$plan?></td>
	<td class="text-center"><?=$fakt?></td>
	<td class="text-center"><a href="#suggestions_<?=$r['login']?>" data-toggle="modal" data-target="#suggestions_<?=$r['login']?>" class=bulat><?
switch($bd_6['soglasovano']) {
	default: case null: echo '-'; break;
	case 1: echo '&#10068;'; break;
	case 2: echo '&#9989;'; break;
}
?></td>
<div class="modal fade" id="suggestions_<?=$r['login']?>" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog" role="document">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title">Предложения (<?=$r['f']?> <?=$r['i']?> <?=$r['o']?>)</h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
</div>
<div class="modal-body m-3">
<p><?=$bd_6['text']??'Ещё не готовы'?></p>
<div>Согласовано: <?if($bd_6['date2']){$s=mysqli_fetch_assoc(mysqli_query($q,'SELECT f,i,o FROM `main`.`users` WHERE n='.$bd_6['u2']));echo $s['f'].' '.$s['i'].' '.$s['o'];}else{echo '-';}?></div>
<div>Дата: <?=$bd_6['date2']??'-'?></div>
</div>
<?if($bd_6['soglasovano']==1 && $u['prava']==255){?><a id="suggestions_btn_save_<?=$r['login']?>" class="btn btn_save float-right" href="#saveSuggestions" onclick="approveSuggestion('<?=$r['login']?>')">Согласовать</a><?}?>
<div class="modal-footer">
<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
</div>
</div>
</div>
</div>
<?if($u['prava']==255 || $u['prava']==300) {?>
	<td class="text-center"><a href="#conclusion_<?=$r['login']?>" data-toggle="modal" data-target="#conclusion_<?=$r['login']?>" class=bulat><?
/*if($bd_2[0]['date7'] &&
		$bd_2[1]['date7'] &&
		$bd_3[0]['date7'] &&
		$bd_3[1]['date7'] &&
		$bd_4[0]['date7'] &&
		$bd_4[1]['date7'] &&
		$bd_5['date2'] &&
		$bd_6['date2'])*/
if($bd_7)
	echo '&#9989;';
else
	if($u['login']==$r['login'])
	echo 'Смотреть';
	else
	echo 'Изменить';?></a></td>
<div class="modal fade" id="conclusion_<?=$r['login']?>" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog" role="document">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title">Заключение о работе (<?=$r['f']?> <?=$r['i']?> <?=$r['o']?>)</h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
</div>
<div class="modal-body m-3">
<form id="conclusion_form_<?=$r['login']?>" method="post" action="/reports6.php">
<textarea required maxlength="3000" style="width:100%" rows="10" placeholder="<?=($r['prava']==0&&$u['prava']==255||$r['prava']==255&&$u['prava']==300)?'Напишите Ваше заключение о работе преподавателя по итогам учебного года...':'Заключение ещё не готово'?>" id="conclusionText_<?=$r['login']?>"><?=$bd_7['text']?></textarea>
</form>
<div>Заключение составил: <?if($bd_7){$s=mysqli_fetch_assoc(mysqli_query($q,'SELECT f,i,o FROM `main`.`users` WHERE n='.$bd_7['u0']));echo $s['f'].' '.$s['i'].' '.$s['o'];}else{echo '-';}?></div>
<div>Дата: <?=$bd_7['date0']??'-'?></div>
</div>
<?if(!$bd_7 && ($r['prava']==0&&$u['prava']==255||$r['prava']==255&&$u['prava']==300)){?><a id="conclusion_btn_save_<?=$r['login']?>" class="btn btn_save float-right" href="#saveConclusion" onclick="saveConclusion('<?=$r['login']?>')">Сохранить</a><?}?>
<div class="modal-footer">
<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
</div>
</div>
</div>
</div>
<?}?>
	</tr>
<?
}
/*
на заполнении
на согласовании (НК)
на согласовании (УО)
на утверждении
план утвержден
согласовано
*/
?>


</tbody>										<table class="table my-0" border=1><tr style="border:0;"><td style="border:0">Обозначения:</td><td style="border:0">
&#128394; - на заполнении<br>
                ❔ - на согласовании начальником кафедры<br>
                &#10069; - на согласовании в отделе<br>
&#10071; - на утверждении<br>
&#10004; - планируемая работа прошла проверку, ИПП на заполнении фактически выполненных работ<br>
&#10068;&#10068; - на согласовании фактически выполненных работ начальником кафедры<br>
&#10069;&#10069; - на согласовании фактически выполненных работ в отделе<br>
&#9989; - согласовано</td></tr></table>
</table>
</div>
</div>
</div>
</main>
</div>
</div>

<script src="assets/js/app.js"></script>
<script>
$ = function(q){return document.getElementById(q);}
xhr = function(){var x;try{x=new ActiveXObject('Msxml2.XMLHTTP');} catch(e){try{x=new ActiveXObject('Microsoft.XMLHTTP');}catch(E){x=false;}}if(!x){x2=('onload' in new XMLHttpRequest())?XMLHttpRequest:XDomainRequest;x=new x2();}return x;}

approveSuggestion = function (q) {
	var x = xhr();
	x.open('POST', '/reports6.php', true);
	x.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	x.send('approveSuggestion=1&y=<?=$y?>&login=' + q); 
	x.onreadystatechange = function() {
		if (this.readyState != 4) return;
		$('suggestions_btn_save_' + q).innerText = x.responseText;
		$('suggestions_btn_save_' + q).setAttribute('onclick', '');
	}
}

saveConclusion = function (q) {
	if(!confirm('Статус ИПП изменится на "заполненный", заключение нельзя будет изменить. Вы уверены?'))
		return false;
	if(!$('conclusion_form_'+q).checkValidity())
		return console.log('error: not valid form js');
	if($('conclusionText_'+q).value.length<10)
		return alert('Напишите немного больше');
	var x = xhr();
	x.open('POST', '/reports7.php', true);
	x.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	x.send('saveConclusion=1&y=<?=$y?>&login=' + q + '&conclusion=' + encodeURIComponent($('conclusionText_'+q).value)); 
	x.onreadystatechange = function() {
		if (this.readyState != 4) return;
		$('conclusion_btn_save_' + q).innerText = x.responseText;
		$('conclusion_btn_save_' + q).setAttribute('onclick', '');
		/*
		setTimeout(() => (
											($('conclusion_btn_save_' + q).innerText = 'Сохранить') & 
											($('conclusion_btn_save_' + q).setAttribute('onclick', 'saveConclusion(\''+q+'\')'))
											), 3000);
		*/
	}
}
</script>
</body>
</html>