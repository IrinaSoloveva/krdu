<?
/*
if($t_sogl['soglasovano2']==0) switch($t_sogl['soglasovano']) {
	case 0: break;
	case 1: echo '(на согласновании у НК)'; break;
	case 2: echo '(на согласовании у методического отдела)'; break;
	case 3: echo '(на согласовании у первого заместителя)'; break;
	case 4: echo ''; break; // план утвержден 1-с заместителем
}
$u['prava']
0 user
255 НК
150 метод
300 1-й зам.
*/
define('DEBUG', 'false');
session_start();
if(!isset($_SESSION['user'])) {
  header('HTTP/1.1 401 Unauthorized');
  header('Location: /');
  exit;
}
$u = &$_SESSION['user'];
require_once('f.php');

// проверка прав на просмотр
switch($u['prava']) {
	case 0:case 255:case 300: break;
	default: if($u['kod']!=4)die('no privilegies'); break;
}

// подключение к базе
if(!($q = mysqli_connect('localhost', 'root', '', '')))
  die('Невозможно подключиться к базе данных. Код ошибки: '. mysqli_connect_error());

// определение дефолтной таблицы (логин_год_семестр)
$sem = (string)(int)$_GET['sem']>1 ? 2 : 1;
$y = (string)(int)$_GET['y'];
if($y<2019 || $y>date('Y'))
	die('incorrect year');
if($u['prava']>0)
	$t_user = mysqli_fetch_assoc(mysqli_query($q, "SELECT * FROM `main`.`users` WHERE login='".(string)($_GET['login']??$u['login'])."'"));
else
	$t_user = mysqli_fetch_assoc(mysqli_query($q, "SELECT * FROM `main`.`users` WHERE login='".$u['login']."'"));
if(!$t_user)
	die('error: no user server');

// определение стадии согласования таблицы
$t_name = $t_user['login'].'_'.$y.'_'.$sem;
$t_sogl = mysqli_fetch_assoc(mysqli_query($q, "SELECT * FROM `bd_4`.`sogl` WHERE t_name='".$t_name."'"));

// отправка на согласование нач. кафедры || НК->metod
if(isset($_POST['sendReport']) && (int)$_POST['sendReport']===1) {
	if($u['prava'] == 255)
		if($t_sogl['soglasovano']==4)
			mysqli_query($q, "UPDATE `bd_4`.`sogl` SET soglasovano2=2,u6=".$u['n'].",date6='".date('d.m.Y H:i:s')."',date6='".date('d.m.Y H:i:s')."' WHERE t_name='$t_name'");
		else
			mysqli_query($q, "UPDATE `bd_4`.`sogl` SET soglasovano=2,u2=".$u['n'].",date='".date('d.m.Y H:i:s')."',date2='".date('d.m.Y H:i:s')."' WHERE t_name='$t_name'");
	elseif($u['prava'] == 0)
		if($t_sogl['soglasovano']==4)
			mysqli_query($q, "UPDATE `bd_4`.`sogl` SET soglasovano2=1,date5='".date('d.m.Y H:i:s')."' WHERE t_name='$t_name'");
		else
			mysqli_query($q, "UPDATE `bd_4`.`sogl` SET soglasovano=1,date='".date('d.m.Y H:i:s')."' WHERE t_name='$t_name'");
	exit;
}

// согласование НК + отправка в metod
elseif(isset($_POST['sendReport']) && (int)$_POST['sendReport']===2 && $u['prava']==255) {
	if($t_sogl['soglasovano']==4)
		mysqli_query($q, "UPDATE `bd_4`.`sogl` SET soglasovano2=2,u6=".$u['n'].",date6='".date('d.m.Y H:i:s')."' WHERE t_name='$t_name'");
	else
		mysqli_query($q, "UPDATE `bd_4`.`sogl` SET soglasovano=2,u2=".$u['n'].",date2='".date('d.m.Y H:i:s')."' WHERE t_name='$t_name'");
	exit;
}

// согласование метОдом + отправка на утверждение 1-му заму
elseif(isset($_POST['sendReport']) && (int)$_POST['sendReport']===3 && $u['kod']==4) {
	if($t_user['prava']==255)
	if($t_sogl['soglasovano']==4)
		echo mysqli_query($q, "UPDATE `bd_4`.`sogl` SET soglasovano2=3,u7=".$u['n'].",date7='".date('d.m.Y H:i:s')."' WHERE t_name='$t_name'");
	else
		echo mysqli_query($q, "UPDATE `bd_4`.`sogl` SET soglasovano=3,u3=".$u['n'].",date3='".date('d.m.Y H:i:s')."' WHERE t_name='$t_name'");
	else
	if($t_sogl['soglasovano']==4)
		echo mysqli_query($q, "UPDATE `bd_4`.`sogl` SET soglasovano2=3,u7=".$u['n'].",date7='".date('d.m.Y H:i:s')."' WHERE t_name='$t_name'");
	else
		echo mysqli_query($q, "UPDATE `bd_4`.`sogl` SET soglasovano=4,u4=".$u['n'].",date4='".date('d.m.Y H:i:s')."' WHERE t_name='$t_name'");
	exit;
}

// утверждение 1-м замом
elseif(isset($_POST['sendReport']) && (int)$_POST['sendReport']===4 && $u['prava']==300) {
	echo mysqli_query($q, "UPDATE `bd_4`.`sogl` SET soglasovano=4,u4=".$u['n'].",date4='".date('d.m.Y H:i:s')."' WHERE t_name='$t_name'");
	exit;
}

// сохранение комментария
elseif(isset($_POST['sendComment']) && (int)$_POST['sendComment']===1 && strlen(trim((string)$_POST['comment']))>0) {
	if (isset($_POST['toTeacher']) && (int)$_POST['toTeacher']===1 && $u['prava']>0) {
		echo mysqli_query($q, "INSERT INTO `bd_4`.`comments` (`t_name`,`date`,`n`,`text`) VALUES ('$t_name','".date('d.m.Y H:i:s')."',".$u['n'].",'Таблица отправлена на доработку.\r\n".htmlspecialchars(trim((string)$_POST['comment']))."')");
		if($t_sogl['soglasovano']==4)
			echo mysqli_query($q, "UPDATE `bd_4`.`sogl` SET soglasovano2=0,date5=null,date6=null,date7=null,u6=null,u7=null WHERE t_name='$t_name'");
		else
			echo mysqli_query($q, "UPDATE `bd_4`.`sogl` SET soglasovano=0,date=null,date2=null,date3=null,date4=null,u2=null,u3=null,u4=null WHERE t_name='$t_name'");
	} else {
		echo mysqli_query($q, "INSERT INTO `bd_4`.`comments` (`t_name`,`date`,`n`,`text`) VALUES ('$t_name','".date('d.m.Y H:i:s')."',".$u['n'].",'".htmlspecialchars(trim((string)$_POST['comment']))."')");
	}
	exit;
}

// сохранение таблицы
elseif(isset($_POST['saveReport']) && (int)$_POST['saveReport']===1) {
	mysqli_query($q, 'DROP TABLE if EXISTS `bd_4`.`'.$t_name.'`');
	mysqli_query($q, 'CREATE TABLE `bd_4`.`'.$t_name.'` (`n` VARCHAR(8) NOT NULL,`naim` VARCHAR(200),`plan` VARCHAR(8) NOT NULL DEFAULT \'0\',`fakt` VARCHAR(8),`srok` VARCHAR(50),`otmetka` VARCHAR(500)) ENGINE = InnoDB');
	$rows = explode('(||)', htmlspecialchars($_POST['table_data']));
	$plan = (float)0;
	$fakt = (float)0;
	for ($j=0; $j<count($rows)-1; $j++) {
		$r = explode('|', $rows[$j]);
		$r[0] = (string)(int)$r[0]; // n
		$r[2] = (float)str_replace(',', '.', (string)$r[2]); // plan
		$plan += $r[2];
		$r[3] = (float)str_replace(',', '.', (string)$r[3]); // fakt
		$fakt += $r[3];
		$r2 = implode("','", $r);
		mysqli_query($q, 'INSERT INTO `bd_4`.`'.$t_name."` (`n`,`naim`,`plan`,`fakt`,`srok`,`otmetka`) VALUES ('".$r2."')");
	}
	if($t_sogl['date4']) {
		mysqli_query($q, "UPDATE `bd_4`.`sogl` SET date0='".date('d.m.Y H:i:s')."',plan='$plan',fakt='$fakt',date5=null,date6=null,date7=null,soglasovano2=null WHERE t_name='$t_name'");
	} else {
		if($t_sogl)
			mysqli_query($q, "DELETE FROM `bd_4`.`sogl` WHERE t_name='$t_name'");
		mysqli_query($q, "INSERT INTO `bd_4`.`sogl` (`t_name`,`date0`,`plan`,`fakt`,`soglasovano`) VALUES ('$t_name','".date('d.m.Y H:i:s')."','$plan','$fakt',0)");
	}
	echo 'Сохранено!';
	exit;
}
$t_sogl = mysqli_fetch_assoc(mysqli_query($q, "SELECT * FROM `bd_4`.`sogl` WHERE t_name='".$t_name."'"));
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<title>ЭИПП | Отчёт</title>

<link rel="stylesheet" href="assets/fonts/css/all.css">
<link href="assets/css/app.css" rel="stylesheet">
<style>
.sel {
	margin-left: 160px;
}
.sel:hover {
	cursor: pointer;
	background: rgba(70, 100, 225, 0.3);
}
.del_row:hover {
	background: rgba(200, 10, 10, 0.1);
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
          <i class="align-middle fa fa-book" style="color: #51BAC0; margin-right: 10px;"></i>
          <span class="align-middle">КРу МВД России</span>
        </a>
        	<div class="den">
        		<ul><?include('menu.php');?></ul>
        	</div>
				<div class="navbar-collapse collapse">
					<ul class="navbar-nav ml-auto">
						<li class="nav-item dropdown">
                <img src="profile_photos/<?=$u['n']?>.jpg?<?=time()?>" class="avatar img-fluid rounded-circle mr-1" alt> <span style="color: #fff;"><?=fio_cut()?></span>
						</li>
					</ul>
				</div>
			</nav>

			<main class="content">
				<div class="container-fluid p-0">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-body">
                                        <!-- h5 style="text-align:right"><a href="#" onclick="history.back()" style="text-decoration:none">Вернуться</a></h5 -->
                                        <h4 class="header-title m-t-0 m-b-30 text-center">Воспитательная работа <?
$sogl = '';
if($t_user['prava']==255) {
	if($t_sogl['date3']) {
		$s = mysqli_fetch_assoc(mysqli_query($q,'SELECT f,i,o FROM `main`.`users` WHERE n='.$t_sogl['u3']));
		$sogl = PHP_EOL.'<br>План согласован: '.$s['f'].' '.$s['i'].' '.$s['o'].' (МО); ';
	}
	if($t_sogl['date4']) {
		$s = mysqli_fetch_assoc(mysqli_query($q,'SELECT f,i,o FROM `main`.`users` WHERE n='.$t_sogl['u4']));
		$sogl .= 'Утвержден: '.$s['f'].' '.$s['i'].' '.$s['o'].' (1-й заместитель)';
	}
	if($t_sogl['date7']) {
		$s = mysqli_fetch_assoc(mysqli_query($q,'SELECT f,i,o FROM `main`.`users` WHERE n='.$t_sogl['u7']));
		$sogl .= PHP_EOL.'<br>Факт согласован: '.$s['f'].' '.$s['i'].' '.$s['o'].' (МО)';
	}
} else {
	if($t_sogl['date2']) {
		$s = mysqli_fetch_assoc(mysqli_query($q,'SELECT f,i,o FROM `main`.`users` WHERE n='.$t_sogl['u2']));
		$sogl = PHP_EOL.'<br>План согласован: '.$s['f'].' '.$s['i'].' '.$s['o'].' (НК)';
	}
	if($t_sogl['date4']) {
		$s = mysqli_fetch_assoc(mysqli_query($q,'SELECT f,i,o FROM `main`.`users` WHERE n='.$t_sogl['u4']));
		$sogl .= ', '.$s['f'].' '.$s['i'].' '.$s['o'].' (МО)';
	}
	if($t_sogl['date6']) {
		$s = mysqli_fetch_assoc(mysqli_query($q,'SELECT f,i,o FROM `main`.`users` WHERE n='.$t_sogl['u6']));
		$sogl .= PHP_EOL.'<br>Факт согласован: '.$s['f'].' '.$s['i'].' '.$s['o'].' (НК)';
	}
	if($t_sogl['date7']) {
		$s = mysqli_fetch_assoc(mysqli_query($q,'SELECT f,i,o FROM `main`.`users` WHERE n='.$t_sogl['u7']));
		$sogl .= ', '.$s['f'].' '.$s['i'].' '.$s['o'].' (МО)';
	}
}

if($t_sogl['soglasovano2']==0) switch($t_sogl['soglasovano']) {
	case 0: break;
	case 1: echo '(на согласновании у НК)'; break;
	case 2: echo '(на согласовании у ВО)'; break;
	case 3: echo '(на утверждении)'; break;
	case 4: echo ''; break; // план утвержден 1-с заместителем
} else switch($t_sogl['soglasovano2']) {
	case 0: break;
	case 1: echo '(на согласновании у НК)'; break;
	case 2: echo '(на согласовании у ВО)'; break;
	case 3: echo '(согласовано)'; break;
}
echo '<br>Пользователь: ',$t_user['f'],' ',$t_user['i'],' ',$t_user['o'],' ('.$t_user['login'],')';
echo '<br>Учебный год: ',$y,'-',$y+1;
echo $sogl;
?></h4>
                                        <div class="table-responsive">
                                            <table id="mainTable" class="table table-striped m-b-30" style="font-size:11px">
                                                <thead>
                                                    <tr>
                                                        <th style="display:none">№</th>
                                                        <th>Вид воспитательной работы</th>
                                                        <th>Наименование, дисциплина (при наличии)</th>
                                                        <th<?=$t_sogl['date4']?' bgcolor="#ccc" style="cursor:default"':''?>>Планируемая работа</th>
                                                        <th<?=!$t_sogl['date4']?' bgcolor="#ccc" style="cursor:default"':''?>>Фактическая работа</th>
                                                        <th>Срок выполнения</th>
                                                        <th>Нормы времени в часах для расчета нагрузки на одного преподавателя</th>
                                                        <th>Отметка о выполнении с указанием дисциплины, № протокола, заседания кафедры или методической секции, даты конференции, семинара, рецинзирования, и т.д.</th>
<?if($u['prava']==0){?>
                                                        <th></th>
<?}?>
                                                    </tr>
                                                </thead>
                                                <tbody id="tb" kol="0">
<?
$vid = array('','1 Работа в качестве куратора учебного взвода','2 Проведение мероприятий по пропаганде ЗОЖ, мероприятий по программам «Антиспид», «Антинарко» и др. среди курсантов и слушателей','3 Проведение с курсантами и слушателями тематических мероприятий, посвященных памятным датам, значимым историческим событиям и гос. праздникам ','4 Проведение бесед, лекций с курсантами и слушателями по профилактике нарушений служебной дисциплины и законности','5 Организация встреч курсантов и слушателей с выпускниками университета','6 Организация встреч курсантов и слушателей с практическими работниками органов внутренних дел','7 Организация встреч курсантов и слушателей с ветеранами университета, ветеранами ОВД и ветеранами Великой Отечественной войны (ВОВ)','8 Организация культурных выходов курсантов и слушателей (экскурсии, посещение театров, выставок, музеев)','9 Организация конкурсов, викторин, соревнований ненаучной направленности среди курсантов и слушателей','10 Руководство кружком, студией ненаучной направленности, спортивной секцией','11 Подготовка материала и участие в проведении занятий по морально-психологической подготовке (МПП)','12 Проведение профориентационной работы','13 Работа в рамках юридической клиники');
$norma = array('','до 60 часов в год','до 16 часов','до 16 часов','до 16 часов','до 10 часов','до 10 часов','до 10 часов','до 20 часов','до 20 часов','до 60 часов в год','до 16 часов','до 20 часов','до 30 часов в год');
$res = mysqli_query($q, 'SELECT * from `bd_4`.`'.$t_name.'`');
for($i=0; $i<$res->num_rows; $i++) {
	$r = mysqli_fetch_assoc($res);
?>
                                                    <tr>
                                                        <td style="display:none"><?=$r['n']?></td>
                                                        <td><?=$vid[$r['n']]?></td>
                                                        <td><?=$r['naim']?></td>
                                                        <td<?=$t_sogl['date4']?' bgcolor="#ccc" style="cursor:default"':''?>><?=$r['plan']?></td>
                                                        <td<?=!$t_sogl['date4']?' bgcolor="#ccc" style="cursor:default"':''?>><?=$r['fakt']?></td>
                                                        <td><?=$r['srok']?></td>
                                                        <td><?=$norma[$r['n']]?></td>
                                                        <td><?=$r['otmetka']?></td>
<?if($u['prava']==0){?>
                                                        <td<?=$t_sogl['date3']?'>':' class="del_row" style="color:red" onclick="this.parentNode.parentNode.removeChild(this.parentNode);itog();">x'?></td>
<?}?>
                                                    </tr>
<?
}
?>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th style="display:none"></th>
                                                        <th><strong>ИТОГО:</strong></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
<?if($u['prava']==0){?>
                                                            <th></th>
<?}?>
                                                    </tr>
                                                </tfoot>
                                            </table>
<input autocomplete placeholder="Поиск..." style="width:155px" onkeyup="if(this.value!='')test(this.value)" id="s">&nbsp;&nbsp;<a onclick="ge('s').value='';test('')" style="color:#88e;cursor:pointer;font-size:17px;display:none" id="x"><b>x</b></a>
<div class="sel" id="sel1"></div>
<div class="sel" id="sel2"></div>
<div class="sel" id="sel3"></div>
<br>
<select name="val" onchange="addStr(this.value, this.options[this.value].text)" style="margin-bottom:20px;max-width:100%;">
<option selected disabled>Выбрать вид работы</option>
<option value="1">1 Работа в качестве куратора учебного взвода</option>
<option value="2">2 Проведение мероприятий по пропаганде ЗОЖ, мероприятий по программам «Антиспид», «Антинарко» и др. среди курсантов и слушателей</option>
<option value="3">3 Проведение с курсантами и слушателями тематических мероприятий, посвященных памятным датам, значимым историческим событиям и гос. праздникам </option>
<option value="4">4 Проведение бесед, лекций с курсантами и слушателями по профилактике нарушений служебной дисциплины и законности</option>
<option value="5">5 Организация встреч курсантов и слушателей с выпускниками университета</option>
<option value="6">6 Организация встреч курсантов и слушателей с практическими работниками органов внутренних дел</option>
<option value="7">7 Организация встреч курсантов и слушателей с ветеранами университета, ветеранами ОВД и ветеранами Великой Отечественной войны (ВОВ)</option>
<option value="8">8 Организация культурных выходов курсантов и слушателей (экскурсии, посещение театров, выставок, музеев)</option>
<option value="9">9 Организация конкурсов, викторин, соревнований ненаучной направленности среди курсантов и слушателей</option>
<option value="10">10 Руководство кружком, студией ненаучной направленности, спортивной секцией</option>
<option value="11">11 Подготовка материала и участие в проведении занятий по морально-психологической подготовке (МПП)</option>
<option value="12">12 Проведение профориентационной работы</option>
<option value="13">13 Работа в рамках юридической клиники</option>
</select>
<?if($t_user['login']==$u['login'] && $t_sogl['soglasovano2']<3) {?>
                                            <div><a style="padding:7px" class="btn btn-success btn-sm float-right" href="#saveReport" onclick="saveReport(this)">Сохранить</a></div>
<?}?>
<?if($t_sogl['date0'] && $t_user['login']==$u['login'] && ((!is_null($t_sogl['soglasovano']) && $t_sogl['soglasovano']==0) || ($t_sogl['soglasovano']==4 && $t_sogl['soglasovano2']==0))) {?>
                                            <div style="margin-right:100px"><a style="padding:7px" class="btn btn-success btn-sm float-right" href="#sendReport" onclick="sendReport('1')">На проверку<?=$t_sogl['soglasovano']==4?' (фактической работы)':''?></a></div>
<?}?>
<?if($u['prava']==255 && ($t_sogl['soglasovano']==1 || $t_sogl['soglasovano2']==1)) {?>
                                            <div style="margin-right:100px"><a style="padding:7px" class="btn btn-success btn-sm float-right" href="#forApproval" onclick="sendReport('2')">Согласовать</a></div>
<?}?>
<?if($u['kod']==4 && ($t_sogl['soglasovano']==2 || $t_sogl['soglasovano2']==2)) {?>
                                            <div style="margin-right:100px"><a style="padding:7px" class="btn btn-success btn-sm float-right" href="#forApproval" onclick="sendReport('3')">Согласовать</a></div>
<?}?>
<?/*if($u['prava']==300 && $t_sogl['soglasovano']==3) {?>
                                            <div style="margin-right:100px"><a style="padding:7px" class="btn btn-success btn-sm float-right" href="#forApproval" onclick="sendReport('4')">Утвердить</a></div>
<?}*/?>
                                        </div>
                                    </div>
                                </div><!-- end card -->
<div class="card">
	<div class="card-header">
		<h5 class="card-title mb-0 text-center">Комментарии к таблице</h5>
	</div>
	<div class="card-body h-100">
<?
$res = mysqli_query($q, 'SELECT * from `bd_4`.`comments` WHERE t_name=\''.$t_name.'\'');
for($i=0; $i<$res->num_rows; $i++) {
	$r = mysqli_fetch_assoc($res);
	$user = mysqli_fetch_assoc(mysqli_query($q, 'SELECT * from `main`.`users` WHERE n='.$r['n']));
?>
		<div class="media">
			<img src="profile_photos/<?=$user['n']?>.jpg" width="56" height="56" class="inspector rounded-circle mr-2" alt>
			<div class="media-body">
				<strong><?=$user['f'],' ',$user['i'],' ',$user['o']?></strong>
				<small class="text-muted"><?=$r['date']?></small>
				<div class="border text-muted p-2 mt-1"><?=nl2br($r['text'])?></div>
			</div>
		</div>
		<hr>
<?
}
?>
	</div>
</div>
<?/*if($u['prava']>0 && strstr($t_name,'_',true)!=$u['login']) {*/?>
<div class="card">
	<div class="card-header text-center">
		<h5 class="card-title">Оставить комментарий</h5>
		<h6 class="card-subtitle text-muted">Комментарий будет виден проверяющему, начальнику кафедры и преподавателю</h6>
	</div>
	<div class="card-body">
		<form>
			<div class="form-group row">
				<label class="col-form-label col-sm-1 text-sm-right"></label>
				<div class="col-sm-10"><textarea class="form-control" id="comment" placeholder="Текст комментария" rows="3" maxlength="900"></textarea></div>
			</div>
			<div class="form-group row">
				<label class="col-form-label col-sm-1 text-sm-right"></label>
				<div class="col-sm-10">
					<button type="button" class="btn btn-primary" onclick="sendComment()">Отправить</button>
<?if(($u['prava']==255 && $t_sogl['soglasovano']==1) ||
			($u['kod']==4 && $t_sogl['soglasovano']==2) ||
            ($u['prava']==300 && $t_sogl['soglasovano']==3) ||
			($u['prava']==255 && $t_sogl['soglasovano2']==1) ||
			($u['kod']==4 && $t_sogl['soglasovano2']==2)) {?>
					<button type="button" class="btn btn-primary" onclick="sendComment('1')" style="float:right">Отправить на доработку</button>
<?}?>
				</div>
			</div>
		</form>
	</div>
</div>
                            </div><!-- end col -->
                        </div>
                        <!-- end row -->
				</div>
			</main>

		</div>
	</div>
<script src="assets/js/jq.js"></script>
<script src="assets/js/mindmup-editabletable.js"></script>
<script>
ge = function(q){return document.getElementById(q);}
xhr = function(){var x;try{x=new ActiveXObject('Msxml2.XMLHTTP');} catch(e){try{x=new ActiveXObject('Microsoft.XMLHTTP');}catch(E){x=false;}}if(!x){x2=('onload' in new XMLHttpRequest())?XMLHttpRequest:XDomainRequest;x=new x2();}return x;}
db_all = ['1 Работа в качестве куратора учебного взвода','2 Проведение мероприятий по пропаганде ЗОЖ, мероприятий по программам «Антиспид», «Антинарко» и др. среди курсантов и слушателей','3 Проведение с курсантами и слушателями тематических мероприятий, посвященных памятным датам, значимым историческим событиям и гос. праздникам ','4 Проведение бесед, лекций с курсантами и слушателями по профилактике нарушений служебной дисциплины и законности','5 Организация встреч курсантов и слушателей с выпускниками университета','6 Организация встреч курсантов и слушателей с практическими работниками органов внутренних дел','7 Организация встреч курсантов и слушателей с ветеранами университета, ветеранами ОВД и ветеранами Великой Отечественной войны (ВОВ)','8 Организация культурных выходов курсантов и слушателей (экскурсии, посещение театров, выставок, музеев)','9 Организация конкурсов, викторин, соревнований ненаучной направленности среди курсантов и слушателей','10 Руководство кружком, студией ненаучной направленности, спортивной секцией','11 Подготовка материала и участие в проведении занятий по морально-психологической подготовке (МПП)','12 Проведение профориентационной работы','13 Работа в рамках юридической клиники'];
db_norma = ['до 60 часов в год','до 16 часов','до 16 часов','до 16 часов','до 10 часов','до 10 часов','до 10 часов','до 20 часов','до 20 часов','до 60 часов в год','до 16 часов','до 20 часов','до 30 часов в год'];
fakt_only = <?=$t_sogl['date4']?'true':'false'?>;
debug = <?=DEBUG?>;

non_clickable_col = function () {
	a = document.getElementsByTagName('tr');
	str = '';
	if(fakt_only)
		for (i=1; i<a.length-1; i++)
			a[i].getElementsByTagName('td')[3].removeAttribute('tabIndex');
	else
		for (i=1; i<a.length-1; i++)
			a[i].getElementsByTagName('td')[4].removeAttribute('tabIndex');
}

itog = function () {
	a = document.getElementsByTagName('tr');
	itog_plan = 0.0;
	itog_fakt = 0.0;
	for (i=1; i<a.length-1; i++) {
		b = a[i].getElementsByTagName('td');
		itog_plan += parseFloat(b[3].innerText.replace(',', '.'));
		itog_fakt += parseFloat(b[4].innerText.replace(',', '.'));
	}
	itog_str = a[a.length-1].getElementsByTagName('th');
	itog_str[3].innerText = itog_plan;
	itog_str[4].innerText = itog_fakt;
}

addStr = function (n, val) {
	ge('tb').innerHTML += '<tr><td style="display:none">' + n + '</td><td>' + val + '</td><td></td><td' + (fakt_only?' bgcolor="#ccc" style="cursor:default"':'') + '>0</td><td' + (!fakt_only?' bgcolor="#ccc" style="cursor:default"':'') + '>0</td><td></td><td>' + db_norma[n-1] + '</td><td></td><td class="del_row" style="color:red" onclick="this.parentNode.parentNode.removeChild(this.parentNode);itog();">x</td></tr>';
	document.getElementsByName('val')[0].selectedIndex=0;
	$('#mainTable').editableTableWidget();
	non_clickable_col();
	$('#mainTable td').on('change', function(){itog()});
	ge('s').value='';
	test('');
}

//document.getElementsByClassName('sel').onclick=function(){alert(this.id.substr(3))}

test = function (inp) {
	if(inp=='') {
		ge('x').style['display'] = 'none';
		for(i=1; i<4; i++) {
			ge('sel'+i).innerHTML = '';
			ge('sel'+i).removeAttribute('onclick');
		}
		return false;
	}
	ge('x').style['display'] = 'inline';
	sel = 0;
	for(i=0; i<db_all.length; i++) {
		if(db_all[i].toUpperCase().indexOf(inp.toUpperCase())>-1) {
			sel += 1;
			ge('sel'+sel).innerHTML = db_all[i];
			ge('sel'+sel).setAttribute('onclick', 'addStr('+(i+1)+',db_all['+i+']);');
			if(sel==3) break;
		}
	}
	for(i=sel; i<3; i++) {
		ge('sel'+(i+1)).innerHTML = '';
		ge('sel'+(i+1)).removeAttribute('onclick');
	}
	return true;
}

saveReport = function (q) {
	// q.setAttribute('style','background-color:#ccc; border-color:#ccc; cursor:progress;');
	q.style['background-color'] = '#ccc';
	q.style['border-color'] = '#ccc';
	q.style['cursor'] = 'progress';
	q.style['box-shadow'] = 'none';

	a = document.getElementsByTagName('tr');
	str = '';
	for (i=1; i<a.length-1; i++) {
		b = a[i].getElementsByTagName('td');
		str += + b[0].innerText + '|'+ b[2].innerText + '|'+ b[3].innerText + '|'+ b[4].innerText + '|'+ b[5].innerText + '|'+ b[7].innerText + '(||)';
	}
	console.log(str);

	var x = xhr();
	x.open('POST', location.pathname + location.search, true);
	x.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	x.send('saveReport=1&table_data=' + encodeURIComponent(str)); 
	x.onreadystatechange = function() {
		if (this.readyState != 4) return;
		if (x.status == 200) {
			q.innerHTML='ОК!';
			console.log(x.responseText);
			//alert('Таблица сохранена');
			//if(fakt_only)
			if(!debug)
				document.location.reload(true);
		} else {
			alert('error: saveReport js');
		}
	}
}

sendReport = function (q) {
	var x = xhr();
	x.open('POST', location.pathname + location.search, true);
	x.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	x.send('sendReport=' + q); 
	x.onreadystatechange = function() {
		if (this.readyState != 4) return;
		if (x.status == 200) {
			console.log(x.responseText);
			//alert('Таблица отправлена на проверку');
			if(!debug)
				document.location.reload(true);
		} else {
			alert('error: sendReport(' + q + ') js');
		}
	}
}

sendComment = function (q) {
	var x = xhr();
	x.open('POST', location.pathname + location.search, true);
	x.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	x.send('sendComment=1&comment=' + encodeURIComponent(ge('comment').value) + (q?'&toTeacher=1':'')); 
	x.onreadystatechange = function() {
		if (this.readyState != 4) return;
		if (x.status == 200) {
			console.log(x.responseText);
			if(!debug)
				document.location.reload(true);
		} else {
			alert('error: sendComment js');
		}
	}
}

$('#mainTable').editableTableWidget();
non_clickable_col();
itog();
$('#mainTable td').on('change', function(){itog()});
</script>
</body>
</html>