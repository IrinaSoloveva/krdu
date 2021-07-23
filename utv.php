<?
session_start();
if(!isset($_SESSION['user'])) {
  header('HTTP/1.1 401 Unauthorized');
  header('Location: /');
  exit;
}
$u = &$_SESSION['user'];
if(!in_array($u['kod'],array(0,5)))
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


if(isset($_GET['approve']) && $_GET['approve']==1) {
	mysqli_query($q, "UPDATE `bd_1`.`sogl` SET date4='".date('d.m.Y H:i:s')."',u4='".$u['n']."',soglasovano=4 WHERE t_name='".$_GET['login'].'_'.$_GET['y']."_1'");
	mysqli_query($q, "UPDATE `bd_2`.`sogl` SET date4='".date('d.m.Y H:i:s')."',u4='".$u['n']."',soglasovano=4 WHERE t_name='".$_GET['login'].'_'.$_GET['y']."_1'");
	mysqli_query($q, "UPDATE `bd_2`.`sogl` SET date4='".date('d.m.Y H:i:s')."',u4='".$u['n']."',soglasovano=4 WHERE t_name='".$_GET['login'].'_'.$_GET['y']."_2'");
	mysqli_query($q, "UPDATE `bd_3`.`sogl` SET date4='".date('d.m.Y H:i:s')."',u4='".$u['n']."',soglasovano=4 WHERE t_name='".$_GET['login'].'_'.$_GET['y']."_1'");
	mysqli_query($q, "UPDATE `bd_3`.`sogl` SET date4='".date('d.m.Y H:i:s')."',u4='".$u['n']."',soglasovano=4 WHERE t_name='".$_GET['login'].'_'.$_GET['y']."_2'");
	mysqli_query($q, "UPDATE `bd_4`.`sogl` SET date4='".date('d.m.Y H:i:s')."',u4='".$u['n']."',soglasovano=4 WHERE t_name='".$_GET['login'].'_'.$_GET['y']."_1'");
	mysqli_query($q, "UPDATE `bd_4`.`sogl` SET date4='".date('d.m.Y H:i:s')."',u4='".$u['n']."',soglasovano=4 WHERE t_name='".$_GET['login'].'_'.$_GET['y']."_2'");
}
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
	text-align: center;
	border: 2px solid #dee2e6;
}
.table td {
	border: 1px solid #dee2e6;
}
.table tr {
    border: 2px solid #dee2e6;
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
	<h5 class="card-title mb-0 text-center">Таблицы на утверждении</h5>
</div>
<table class="table table-striped my-0">
<thead>
	<tr>
	<th>ФИО</th>
	<th>Фото</th>
    <th>Семестр</th>
	<th>Учебная нагрузка</th>
	<th>Методическая работа</th>
	<th>Научно-исследовательская работа</th>
	<th>Воспитательная работа</th>
	<th>План</th>
    <th>Утверждение плана</th>
	</tr>
</thead>
<tbody>
<?
$users = mysqli_query($q, 'SELECT * FROM `main`.`users` WHERE prava=255');

while($user=mysqli_fetch_assoc($users)) {
    $t1 = mysqli_query($q, 'SELECT * FROM `bd_1`.`sogl` WHERE soglasovano=3 AND t_name="'.$user['login'].'_'.$y.'_1"');
    $t2[0] = mysqli_query($q, 'SELECT * FROM `bd_2`.`sogl` WHERE soglasovano=3 AND t_name="'.$user['login'].'_'.$y.'_1"');
    $t2[1] = mysqli_query($q, 'SELECT * FROM `bd_2`.`sogl` WHERE soglasovano=3 AND t_name="'.$user['login'].'_'.$y.'_2"');
    $t3[0] = mysqli_query($q, 'SELECT * FROM `bd_3`.`sogl` WHERE soglasovano=3 AND t_name="'.$user['login'].'_'.$y.'_1"');
    $t3[1] = mysqli_query($q, 'SELECT * FROM `bd_3`.`sogl` WHERE soglasovano=3 AND t_name="'.$user['login'].'_'.$y.'_2"');
    $t4[0] = mysqli_query($q, 'SELECT * FROM `bd_4`.`sogl` WHERE soglasovano=3 AND t_name="'.$user['login'].'_'.$y.'_1"');
    $t4[1] = mysqli_query($q, 'SELECT * FROM `bd_4`.`sogl` WHERE soglasovano=3 AND t_name="'.$user['login'].'_'.$y.'_2"');
    if(in_array(0, array($t1->num_rows, $t2[0]->num_rows, $t3[0]->num_rows, $t4[0]->num_rows))) continue;
    if(in_array(0, array($t2[1]->num_rows, $t3[1]->num_rows, $t4[1]->num_rows))) continue;

    $plan1 = mysqli_fetch_assoc($t1)['plan'];
    $plan[0] = mysqli_fetch_assoc($t2[0])['plan'] + mysqli_fetch_assoc($t3[0])['plan'] + mysqli_fetch_assoc($t4[0])['plan'];
    $plan[1] = mysqli_fetch_assoc($t2[1])['plan'] + mysqli_fetch_assoc($t3[1])['plan'] + mysqli_fetch_assoc($t4[1])['plan'];
?>
        <tr<?=$user['active']?' style="background:#fff"':' style="background:#eee"'?>>
            <td rowspan=2><?=$user['f'].' '.$user['i'].' '.$user['o'].' ('.$user['login'].')'?></td>
            <td rowspan=2><img class=text-center src="/profile_photos/<?=$user['n']?>.jpg?<?=time()?>" width=56 height=56 class="rounded-circle mr-3 inspector" alt></td>
            <td class="text-center" style="margin:0;padding:0">Семестр 1</td>
            <td rowspan=2><a href="/reports1.php?y=<?=$y?>&login=<?=$user['login']?>"><span>Смотреть</span></a> (<?=$plan1?>)</td>
            <td class><a href="/reports2.php?sem=1&y=<?=$y?>&login=<?=$user['login']?>"><span>Смотреть</span></a></td>
            <td><a href="/reports3.php?sem=1&y=<?=$y?>&login=<?=$user['login']?>"><span>Смотреть</span></a></td>
            <td><a href="/reports4.php?sem=1&y=<?=$y?>&login=<?=$user['login']?>"><span>Смотреть</span></a></td>
            <td><?=$plan[0]?></td>
            <td rowspan=2 style="text-align:center"><a href="?approve=1&y=<?=$y?>&login=<?=$user['login']?>">Утвердить</a></td>
        </tr>
        <tr<?=$user['active']?' style="background:#fff"':' style="background:#eee"'?>>
            <td class=text-center style="margin:0;padding:0">Семестр 2</td>
            <td class><a href="/reports2.php?sem=2&y=<?=$y?>&login=<?=$user['login']?>"><span>Смотреть</span></a></td>
            <td><a href="/reports3.php?sem=2&y=<?=$y?>&login=<?=$user['login']?>"><span>Смотреть</span></a></td>
            <td><a href="/reports4.php?sem=2&y=<?=$y?>&login=<?=$user['login']?>"><span>Смотреть</span></a></td>
            <td><?=$plan[1]?></td>
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
	if(!confirm('План работы преподавателя на учебный год будет утвержден. Вы уверены?'))
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