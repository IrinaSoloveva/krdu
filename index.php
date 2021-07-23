<?php
session_start();
if(!isset($_SESSION['user'])) {
  require_once('login.php');
  exit;
}

$u = &$_SESSION['user'];
require_once('f.php');

if(!($q=mysqli_connect('localhost', 'root', '', '')))
  die('Невозможно подключиться к базе данных. Код ошибки: '. mysqli_connect_error());

if(isset($_GET['y'])) {
	$y = (int)$_GET['y'];
	if($y<2019 || $y>2090)
		die('error: incorrect year server');
} else
	$y = sem()==1?(int)date('Y'):(int)date('Y')-1;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<title>КРу МВД России | ЭИПП</title>

<link rel="stylesheet" href="assets/fonts/css/all.css">
<link href="assets/css/app.css" rel="stylesheet">
<style>
img.inspector {
  max-width: 56px;
  max-height: 56px;
}
.btn_save {
	padding: 9px;
	border-top: 1px solid #dee2e6;
}
.btn_save:hover {
	background: #eef;
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
                <img src="profile_photos/<?=$u['n']?>.jpg?<?=time()?>" width="56" height="56" class="avatar inspector img-fluid rounded-circle mr-1"/> <span style="color: #fff;"><?=fio_cut()?></span>
						</li>
					</ul>
				</div>
		</nav>

		<main class="content">
				<div class="container-fluid p-0">
					<div class="row">
						<div class="col-md-4 col-xl-3">
							<div class="card mb-3">
								<div class="card-header">
									<h5 class="card-title mb-0 text-center">Информация о пользователе</h5>
									<a href="logout.php"><div class="text-muted text-center" style="font-size:13px">Выйти</div></a>
								</div>
								<div class="card-body text-center">
									<img src="/profile_photos/<?=$u['n']?>.jpg?<?=time()?>" alt class="img-fluid rounded-circle mb-2" width=128 height=128 title="Изменить фото" style="cursor:pointer;" onclick="$('uploadPhoto').click()"><form method=post enctype="multipart/form-data" id="uploadPhotoForm" action="/up.php"><input type=hidden name=n value=<?=$u['n']?>><input name=f style="display:none;" type=file id=uploadPhoto onchange="$('uploadPhotoForm').submit()"></form>
									<h5 class="card-title mb-0"><?=$u['f']?> <?=$u['i']?> <?=$u['o']?></h5>
									<div class="text-muted mb-2"><?=$u['podr']?></div>
								</div>
								<hr class="my-0" />
								<div class="card-body">
									<ul class="list-unstyled mb-0">
										<li class="mb-1"> <span class="badge badge-primary mr-1 my-1">Должность:</span> <?=$u['doljnost']?></li>
										<li class="mb-1"><span class="badge badge-primary mr-1 my-1">Учёная степень, ученое звание:</span> <?=$u['uchen_st']?></li>
										<li class="mb-1"><span class="badge badge-primary mr-1 my-1">Специальное звание:</span> <?=$u['zvanie']?></li>
									</ul>
								</div>
							</div>
							<div class="card mb-3">
								<div class="card-header">
									<h5 class="card-title mb-0 text-center">Инструкция по использованию</h5>
								</div>
								<div class="card-body text-center">
									<a class="btn btn-primary btn-sm" href="instr.pdf" target="_blank">Скачать инструкцию</a>
								</div>
							</div>
						</div>
<?if(!in_array($u['kod'],array(1,2,3,4,5))) {?>
						<div class="col-md-8 col-xl-9">
							<div class="card">
								<div class="card-header">
									<h4 class="card-title" style="display:inline-block;">
										Индивидуальный план работы преподавателя на <select onchange="location.href='/?y='+this.value;"><?
for($i=2019; $i<=date('Y'); $i++) {
	if($i==$y) {
		echo '<option value="'.$i.'" selected>'.($i.'/'.($i+1)).'</option>';
	} else {
		echo '<option value="'.$i.'">'.($i.'/'.($i+1)).'</option>';
	}
}
?></select> учебный год</h4>
<?
$bd_1_1 = mysqli_fetch_assoc(mysqli_query($q, "SELECT * FROM `bd_1`.`sogl` WHERE t_name='".$u['login']."_".$y."_1'"));
$bd_1_17 = mysqli_fetch_assoc(mysqli_query($q, "SELECT * FROM `bd_1`.`sogl` WHERE t_name='".$u['login']."_".$y."_17'"));
$bd_1_18 = mysqli_fetch_assoc(mysqli_query($q, "SELECT * FROM `bd_1`.`sogl` WHERE t_name='".$u['login']."_".$y."_18'"));
//$bd_1_16 = mysqli_fetch_assoc(mysqli_query($q, "SELECT * FROM `bd_1`.`sogl` WHERE t_name='".$u['login']."_".$y."_16'"));
$bd_2[0] = mysqli_fetch_assoc(mysqli_query($q, "SELECT * FROM `bd_2`.`sogl` WHERE t_name='".$u['login']."_".$y."_1'"));
$bd_2[1] = mysqli_fetch_assoc(mysqli_query($q, "SELECT * FROM `bd_2`.`sogl` WHERE t_name='".$u['login']."_".$y."_2'"));
$bd_3[0] = mysqli_fetch_assoc(mysqli_query($q, "SELECT * FROM `bd_3`.`sogl` WHERE t_name='".$u['login']."_".$y."_1'"));
$bd_3[1] = mysqli_fetch_assoc(mysqli_query($q, "SELECT * FROM `bd_3`.`sogl` WHERE t_name='".$u['login']."_".$y."_2'"));
$bd_4[0] = mysqli_fetch_assoc(mysqli_query($q, "SELECT * FROM `bd_4`.`sogl` WHERE t_name='".$u['login']."_".$y."_1'"));
$bd_4[1] = mysqli_fetch_assoc(mysqli_query($q, "SELECT * FROM `bd_4`.`sogl` WHERE t_name='".$u['login']."_".$y."_2'"));
$bd_5 = mysqli_fetch_assoc(mysqli_query($q, "SELECT * FROM `bd_5`.`sogl` WHERE t_name='".$u['login']."_".$y."'"));
$bd_6 = mysqli_fetch_assoc(mysqli_query($q, "SELECT * FROM `bd_6`.`sogl` WHERE t_name='".$u['login']."_".$y."'"));
$bd_7 = mysqli_fetch_assoc(mysqli_query($q, "SELECT * FROM `bd_7`.`sogl` WHERE t_name='".$u['login']."_".$y."'"));
?>
										<span class="float-right">Статус журнала: <span class="badge badge-<?=($bd_7)?'success">':'danger">не '?>заполнен</span>
								</div>
								<table class="table table-bordered">
									<thead>
										<tr>
											<th style="width:auto;">Наименование</th>
											<th style="width:10%;">План (ч.)</th>
											<th style="width:10%;">Факт (ч.)</th>
											<th style="width:18%;">Дата изменения</th>
											<th style="width:53px;" class="text-center">&#128100;</th>
											<th style="width:10%;">Действие</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>1. Учебная нагрузка <span class="float-right">год:</span></td>
											<td><div><?=($bd_1_17['plan']+$bd_1_17['plan'])??'-'?></div></td>
											<td><div><?=($bd_1_18['fakt']+$bd_1_18['fakt'])??'-'?></div></td>
											<td><div><?=(max($bd_1_1['date0'],$bd_1_17['date0'],$bd_1_18['date0'])==0?'-':(max($bd_1_1['date0'],$bd_1_17['date0'],$bd_1_18['date0'])))?></div></td>
											<td class="text-center"><div><?
                                                    if(!$bd_1_1['soglasovano2']) switch($bd_1_1['soglasovano']) {
                                                        default: case null: echo '-'; break;
                                                        case 0: echo '&#128394;'; break; // ручка
                                                        case 1: echo '&#10068;'; break; // белый ?
                                                        case 2: echo '&#10069;'; break; // белый !
                                                        case 3: echo '&#10071;'; break; // красный !
                                                        case 4: echo '&#10004;'; break; // галочка
                                                    } else switch($bd_1_1['soglasovano2']) {
                                                        case 0: echo '&#10004;'; break; // галочка
                                                        case 1: echo '&#10068;&#10068;'; break;
                                                        case 2: echo '&#10069;&#10069;'; break;
                                                        case 3: echo '&#9989;'; break;
                                                    }
                                                    ?></div></td>
											<td class="table-action text-center">
												<a href="reports1.php?y=<?=$y?>"><span class="badge badge-info">Изменить</span></a>
											</td>
										</tr>
										<tr>
											<td>2. Методическая работа <span class="float-right">1 семестр:<br>2 семестр:</span></td>
											<td><div><?=$bd_2[0]['plan']??'-'?></div><div><?=$bd_2[1]['plan']??'-'?></div></td>
											<td><div><?=$bd_2[0]['fakt']??'-'?></div><div><?=$bd_2[1]['fakt']??'-'?></div></td>
											<td><div><?=$bd_2[0]['date0']??'-'?></div><div><?=$bd_2[1]['date0']??'-'?></div></td>
											<td class="text-center"><div><?
if(!$bd_2[0]['soglasovano2']) switch($bd_2[0]['soglasovano']) {
	default: case null: echo '-'; break;
	case 0: echo '&#128394;'; break; // ручка
	case 1: echo '&#10068;'; break; // белый ?
	case 2: echo '&#10069;'; break; // белый !
	case 3: echo '&#10071;'; break; // красный !
	case 4: echo '&#10004;'; break; // галочка
} else switch($bd_2[0]['soglasovano2']) {
	case 0: echo '&#10004;'; break; // галочка
	case 1: echo '&#10068;&#10068;'; break;
	case 2: echo '&#10069;&#10069;'; break;
	case 3: echo '&#9989;'; break;
}
?></div><div><?
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
?></div></td>
											<td class="table-action text-center">
												<a href="reports2.php?y=<?=$y?>&sem=1<?=$t_sogl['date4']?'&fakt=1':''?>"><span class="badge badge-info">Изменить</span></a>
												<a href="reports2.php?y=<?=$y?>&sem=2<?=$t_sogl['date4']?'&fakt=1':''?>"><span class="badge badge-info">Изменить</span></a>
											</td>
										</tr>
										<tr>
											<td>3. Научно-исследовательская работа <span class="float-right">1 семестр:<br>2 семестр:</span></td>
											<td><div><?=$bd_3[0]['plan']??'-'?></div><div><?=$bd_3[1]['plan']??'-'?></div></td>
											<td><div><?=$bd_3[0]['fakt']??'-'?></div><div><?=$bd_3[1]['fakt']??'-'?></div></td>
											<td><div><?=$bd_3[0]['date0']??'-'?></div><div><?=$bd_3[1]['date0']??'-'?></div></td>
											<td class="text-center"><div><?
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
?></div><div><?
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
?></div></td>
											<td class="table-action text-center">
												<a href="reports3.php?y=<?=$y?>&sem=1"><span class="badge badge-info">Изменить</span></a>
												<a href="reports3.php?y=<?=$y?>&sem=2"><span class="badge badge-info">Изменить</span></a>
											</td>
										</tr>
										<tr>
											<td>4. Воспитательная работа <span class="float-right">1 семестр:<br>2 семестр:</span></td>
											<td><div><?=$bd_4[0]['plan']??'-'?></div><div><?=$bd_4[1]['plan']??'-'?></div></td>
											<td><div><?=$bd_4[0]['fakt']??'-'?></div><div><?=$bd_4[1]['fakt']??'-'?></div></td>
											<td><div><?=$bd_4[0]['date0']??'-'?></div><div><?=$bd_4[1]['date0']??'-'?></div></td>
											<td class="text-center"><div><?
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
?></div><div><?
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
?></div></td>
											<td class="table-action text-center">
												<a href="reports4.php?y=<?=$y?>&sem=1"><span class="badge badge-info">Изменить</span></a>
												<a href="reports4.php?y=<?=$y?>&sem=2"><span class="badge badge-info">Изменить</span></a>
											</td>
										</tr>
										<tr>
											<td>5. Другие виды работ <span class="float-right">год:</span></td>
											<td><?=$bd_5['plan']??'-'?></td>
											<td><?=$bd_5['fakt']??'-'?></td>
											<td><?=$bd_5['date0']??'-'?></td>
											<td class="text-center"><?
if(!$bd_5['soglasovano2']) switch($bd_5['soglasovano']) {
	default: case null: echo '-'; break;
	case 0: echo '&#128394;'; break;
	case 1: echo '&#10068;'; break;
	case 2: echo '&#9989;'; break;
}
?></td>
											<td class="table-action text-center">
												<a href="reports5.php?y=<?=$y?>"><span class="badge badge-info">Изменить</span></a>
											</td>
										</tr>
										<tr>
											<td><span class="float-right"><b>ИТОГО:</b></span></td>
											<td><?=$bd_1_1['plan']+$bd_2[0]['plan']+$bd_3[0]['plan']+$bd_4[0]['plan']	
														+$bd_2[1]['plan']+$bd_3[1]['plan']+$bd_4[1]['plan']+$bd_5['plan']?></td>
											<td><?=$bd_1_1['fakt']+$bd_2[0]['fakt']+$bd_3[0]['fakt']+$bd_4[0]['fakt']	
														+$bd_2[1]['fakt']+$bd_3[1]['fakt']+$bd_4[1]['fakt']+$bd_5['fakt']?></td>
											<td></td>
										</tr>
										<tr>
											<td>6. Предложения преподавателя</td>
											<td style="background:#eee"></td>
											<td style="background:#eee"></td>
											<td><?=$bd_6['date0']??'-'?></td>
											<td class="text-center"><?
switch($bd_6['soglasovano']) {
	default: case null: echo '-'; break;
	case 0: echo '&#128394;'; break;
	case 1: echo '&#10068;'; break;
	case 2: echo '&#9989;'; break;
}
?></td>
											<td class="table-action text-center">
												<a href="#suggestions" data-toggle="modal" data-target="#suggestions"><span class="badge badge-info">Изменить</span></a>
											</td>
<div class="modal fade" id="suggestions" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog" role="document">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title">Добавление предложений</h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
</div>
<div class="modal-body m-3">
<form id="suggestions_form" method="post" action="/reports6.php">
	<textarea required maxlength="3000" style="width:100%" rows="10" placeholder="Напишите Ваши предложения по итогам учебного года..." id="suggestion" pattern=".{10,}"><?=$bd_6['text']?></textarea>
</form>
<div>Согласовано: <?if($bd_6['date2']){$s=mysqli_fetch_assoc(mysqli_query($q,'SELECT f,i,o FROM `main`.`users` WHERE n='.$bd_6['u2']));echo $s['f'].' '.$s['i'].' '.$s['o'];}else{echo '-';}?></div>
<div>Дата: <?=$bd_6['date2']??'-'?></div>	
</div>
<?if($bd_6['soglasovano']!=2){?><a id="suggestions_btn_save" class="btn btn_save float-right" href="#saveSuggestions" onclick="saveSuggestion()">Сохранить</a><?}?>
<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button></div>
</div>
</div>
</div>
										</tr>
										<tr>
											<td>7. Заключение о качестве работы преподавателя</td>
											<td style="background:#eee"></td>
											<td style="background:#eee"></td>
											<td><?=$bd_7['date0']??'-'?></td>
											<td class="text-center"><?=$bd_7['date0']?'&#9989;':'-';?></td>
											<td class="table-action text-center">
												<a href="#conclusion" data-toggle="modal" data-target="#conclusion"><span class="badge badge-info">Смотреть</span></a>
											</td>
<div class="modal fade" id="conclusion" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog" role="document">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title">Ознакомление с заключением</h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
</div>
<div class="modal-body m-3">
<p><?=$bd_7['text']??'Ещё не готово...'?></p>
<div>Заключение составил: <?if($bd_7){$s=mysqli_fetch_assoc(mysqli_query($q,'SELECT f,i,o FROM `main`.`users` WHERE n='.$bd_7['u0']));echo $s['f'].' '.$s['i'].' '.$s['o'];}else{echo '-';}?></div>
<div>Дата: <?=$bd_7['date0']??'-'?></div>
</div>
<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button></div>
</div>
</div>
</div>
										</tr>
										<tr>
											<td colspan="6"><table><tr><td style="border:0">Обозначения:</td><td style="border:0">
&#128394; - на заполнении<br>
<?if($u['prava']==0) {?>
&#10068; - на согласовании начальником кафедры<br>
<?}?>
&#10069; - на согласовании в отделе<br>
<?if($u['prava']==255) {?>
&#10071; - на утверждении<br>
<?}?>
&#10004; - планируемая работа прошла проверку, ИПП на заполнении фактически выполненных работ<br>
&#10068;&#10068; - на согласовании фактически выполненных работ начальником кафедры<br>
&#10069;&#10069; - на согласовании фактически выполненных работ в отделе<br>
&#9989; - согласовано</td></tr></table></td>
										</tr>
									</tbody>
								</table>
							</div>
<!-- div class="card">
	<div class="card-body h-100">
		<div class="media">
			<img src="assets/img/Frolova_S.A._seryy123.jpg" width="56" height="56" class="rounded-circle mr-3 inspector" alt>
			<div class="media-body">
				<span class="badge badge-danger float-right">Замечание</span>
				<small class="float-right text-navy" style="margin-right:12px;">12:07 24.02.2019</small>
				<p class="mb-2"><strong>Фролова С.А.</strong></p>
				<p>Здесь сотрудники учебного управления могут оставлять найденные замечание по заполнению плана. Преподаватель сможет отправить журнал на проверку исправления замечаний при нажатии на кнопку "Замечание исправлено". Сотрудник учебного управления также получит об этом уведомление.</p>
				<a class="btn btn-success btn-sm float-right" href="#">Замечание исправлено</a>
			</div>
		</div>
	</div>
</div> -->
						</div>
<?}else echo '<div class="col-md-4 col-xl-9"><img src="https://vuzopedia.ru/storage/app/uploads/public/5a3/391/da9/5a3391da99b5e104315791.jpg" style="margin-left:80px;opacity:0.8" height="97%"></div>';?>
					</div>
				</div>
		</main>
	</div>
</div>

<script src="assets/js/app.js"></script>
<!-- script src="md5.min.js"></script -->
<script>
$ = function(q){return document.getElementById(q);}
xhr = function(){var x;try{x=new ActiveXObject('Msxml2.XMLHTTP');} catch(e){try{x=new ActiveXObject('Microsoft.XMLHTTP');}catch(E){x=false;}}if(!x){x2=('onload' in new XMLHttpRequest())?XMLHttpRequest:XDomainRequest;x=new x2();}return x;}

saveSuggestion = function () {
	if(!$('suggestions_form').checkValidity())
		return console.log('error: not valid form js');
	if($('suggestion').value.length<10)
		return alert('Напишите немного больше');
	var x = xhr();
	x.open('POST', '/reports6.php', true);
	x.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	x.send('saveSuggestion=1&y=<?=$y?>&suggestion=' + encodeURIComponent($('suggestion').value)); 
	x.onreadystatechange = function() {
		if (this.readyState != 4) return;
		$('suggestions_btn_save').innerText = x.responseText;
		$('suggestions_btn_save').setAttribute('onclick', '');
		// if (x.status == 200) { }
		setTimeout(() => (
											($('suggestions_btn_save').innerText = 'Сохранить') & 
											($('suggestions_btn_save').setAttribute('onclick', 'saveSuggestion()'))
											), 3000);
	}
}
</script>
</body>
</html>