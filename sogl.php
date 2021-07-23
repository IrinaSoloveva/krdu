<?
session_start();
if(!isset($_SESSION['user'])) {
  header('HTTP/1.1 401 Unauthorized');
  header('Location: /');
  exit;
}
$u = &$_SESSION['user'];
if(!in_array($u['kod'],array(0,1,2,3,4,5)))
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
$eng = 'a b v g d e e zh z i i k l m n o p r s t u f kh TS CH SH SHCH IE Y  E IU IA';
?>
<!DOCTYPE html>
<html lang="ru">

<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<title>ЭИПП | Таблицы на согласовании</title>

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
                <img src="/profile_photos/<?=$u['n']?>.jpg?<?=time()?>" class="avatar img-fluid rounded-circle mr-1"> <span style="color:#fff;"><?=fio_cut()?></span>
						</li>
					</ul>
				</div>
			</nav>


<main class="content">
<div class="container-fluid p-0">
<div class="row">
<div class="card flex-fill">
<div class="card-header">
	<h5 class="card-title mb-0 text-center">Таблицы на согласовании</h5>
</div>
<table class="table table-striped my-0">
<thead>
	<tr>
	<th>ФИО</th>
	<th>Фото</th>
<?if($u['kod']==1) {?>
	<th>Учебная нагрузка</th>
<?}?>
<?if($u['kod']==2) {?>
	<th>Методическая работа</th>
<?}?>
<?if($u['kod']==3) {?>
	<th>Научно-исследовательская работа</th>
<?}?>
<?if($u['kod']==4) {?>
	<th>Воспитательная работа</th>
<?}?>
	<th>План</th>
	<th>Факт</th>
	<th>Предложения преподавателя</th>
	</tr>
</thead>
<tbody>
<?
$sem = 1;
switch($u['kod']) {
    case 1: $bd = mysqli_query($q, 'SELECT * FROM `bd_1`.`sogl` WHERE soglasovano=2 OR soglasovano2=2'); break;
	case 2: $bd = mysqli_query($q, 'SELECT * FROM `bd_2`.`sogl` WHERE soglasovano=2 OR soglasovano2=2'); break;
	case 3: $bd = mysqli_query($q, 'SELECT * FROM `bd_3`.`sogl` WHERE soglasovano=2 OR soglasovano2=2'); break;
	case 4: $bd = mysqli_query($q, 'SELECT * FROM `bd_4`.`sogl` WHERE soglasovano=2 OR soglasovano2=2'); break;
}
while($t=mysqli_fetch_assoc($bd)) {
	$r = mysqli_fetch_assoc(mysqli_query($q, 'SELECT * FROM `main`.`users` WHERE login=\''.strstr($t['t_name'],'_',true).'\''));
	list(,$y,$sem) = explode('_',$t['t_name']);
	$plan = &$t['plan'];
	$fakt = &$t['fakt'];
	$t_6 = mysqli_fetch_assoc(mysqli_query($q, 'SELECT * FROM `bd_6`.`sogl` WHERE t_name=\''.$r['login'].'_'.$y.'\''));
?>
	<tr<?=$r['active']?'':' style="background:#eee"'?>>
	<td><?=$r['f']?> <?=$r['i']?> <?=$r['o']?></td>
	<td><img src="/profile_photos/<?=$r['n']?>.jpg?<?=time()?>" width=56 height=56 class="rounded-circle mr-3 inspector" alt></td>
<?if($u['kod']==1) {?>
    <td class=><a href="/reports1.php?y=<?=$y?>&login=<?=$r['login']?>"><span><?
                if(!$t['date6']) echo 'Смотреть (план)';
                else echo 'Смотреть (факт)';
                ?></span></a></td>
<?}?>
<?if($u['kod']==2) {?>
	<td class=><a href="/reports2.php?sem=<?=$sem?>&y=<?=$y?>&login=<?=$r['login']?>"><span><?
if(!$t['date6']) echo 'Смотреть (план)';
else echo 'Смотреть (факт)';
?></span></a></td>
<?}?>
<?if($u['kod']==3) {?>
	<td><a href="/reports3.php?sem=<?=$sem?>&y=<?=$y?>&login=<?=$r['login']?>"><span><?
if(!$t['date6']) echo 'Смотреть (план)';
else echo 'Смотреть (факт)';
?></span></a></td>
<?}?>
<?if($u['kod']==4) {?>
	<td><a href="/reports4.php?sem=<?=$sem?>&y=<?=$y?>&login=<?=$r['login']?>"><span><?
if(!$t['date6']) echo 'Смотреть (план)';
else echo 'Смотреть (факт)';
?></span></a></td>
<?}?>
	<td class=""><?=$plan?></td>
	<td class=""><?=$fakt?></td>
	<td class=""><a href="#suggestions_<?=$r['login']?>" data-toggle="modal" data-target="#suggestions_<?=$r['login']?>"><?
switch($t_6['soglasovano']) {
	default: case null: echo 'Не заполнено'; break;
	case 1: echo 'Смотреть (на согласовании у НК)'; break;
	case 2: echo 'Смотреть (согласованы НК)'; break;
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
<p><?=$t_6['text']??'Ещё не готовы'?></p>
<div>Согласовано: <?if($t_6['date2']){$s=mysqli_fetch_assoc(mysqli_query($q,'SELECT f,i,o FROM `main`.`users` WHERE n='.$t_6['u2']));echo $s['f'].' '.$s['i'].' '.$s['o'];}else{echo '-';}?></div>
<div>Дата: <?=$t_6['date2']??'-'?></div>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
</div>
</div>
</div>
</div>
	</tr>
<?
}
?>


</tbody>										<table class="table my-0" border=1><tr style="border:0;"><td style="border:0">Обозначения:</td><td style="border:0">
&#128394; - на заполнении<br>
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