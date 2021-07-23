<?
define('DEBUG', 'false');
session_start();
if(!isset($_SESSION['user'])) {
  header('HTTP/1.1 401 Unauthorized');
  header('Location: /');
  exit;
}
$u=&$_SESSION['user'];
require_once('f.php');

// проверка прав на просмотр
switch($u['kod']) {
	case 1: break;
	default: if($u['prava']==0 || $u['prava']==255 || $u['kod']<6) break; else die('no privilegies');
}

// подключение к базе
if(!($q = mysqli_connect('localhost', 'root', '', '')))
  die('Невозможно подключиться к базе данных. Код ошибки: '. mysqli_connect_error());

// определение дефолтной таблицы (логин_год)
$y = (string)(int)$_GET['y'];
if($y<2019 || $y>date('Y'))
	die('incorrect year');
if($u['prava']>0)
	$t_user = mysqli_fetch_assoc(mysqli_query($q, "SELECT * FROM `main`.`users` WHERE login='".((string)$_GET['login']==''?$u['login']:(string)$_GET['login'])."'"));
else
	$t_user = mysqli_fetch_assoc(mysqli_query($q, "SELECT * FROM `main`.`users` WHERE login='".$u['login']."'"));
if(!$t_user)
	die('error: no user server');

// определение стадии согласования таблицы
$t_name = $t_user['login'].'_'.$y;
$t_sogl = mysqli_fetch_assoc(mysqli_query($q, "SELECT * FROM `bd_1`.`sogl` WHERE t_name='".$t_name."_1'"));
require 'C:\OSPanel\modules\php\PHP_7.3-x64\vendor\autoload.php';

// сохранение комментария + отправка на доработку
if(isset($_POST['sendComment']) && (int)$_POST['sendComment']===1 && strlen(trim((string)$_POST['comment']))>0) {
	if (isset($_POST['toTeacher']) && (int)$_POST['toTeacher']===1 && $u['prava']>0) {
		echo mysqli_query($q, "INSERT INTO `bd_1`.`comments` (`t_name`,`date`,`n`,`text`) VALUES ('$t_name','".date('d.m.Y H:i:s')."',".$u['n'].",'Таблица отправлена на доработку.\r\n".htmlspecialchars(trim((string)$_POST['comment']))."')");
		if($t_sogl['soglasovano']==4)
			echo mysqli_query($q, "UPDATE `bd_1`.`sogl` SET soglasovano2=0,date5=null,date6=null,date7=null,u6=null,u7=null WHERE t_name='${t_name}_1'");
		else
	echo mysqli_query($q, "UPDATE `bd_1`.`sogl` SET soglasovano=0,date=null,date2=null,date3=null,date4=null,u2=null,u3=null,u4=null WHERE t_name='${t_name}_1'");
	} else {
		echo mysqli_query($q, "INSERT INTO `bd_1`.`comments` (`t_name`,`date`,`n`,`text`) VALUES ('$t_name','".date('d.m.Y H:i:s')."',".$u['n'].",'".htmlspecialchars(trim((string)$_POST['comment']))."')");
	}
	exit;
}

// сохранение таблицы
elseif(isset($_POST['t_n']) && (int)$_POST['t_n']>0) {
$f = &$_FILES['f_'.$_POST['t_n']];
if($_POST['t_n']==1)
    $plan = 0;
else
    $plan = \PhpOffice\PhpSpreadsheet\IOFactory::load($f['tmp_name'])->getActiveSheet()->getCell('W14')->getCalculatedValue();
if($plan>900) {
    header('Location: ./reports1.php?y=' . (string)$_GET['y'] . '&login=' . (string)$_GET['login'].'&er=PLAN900');
    exit;
}
switch(strrchr($f['name'],'.')){
    case '.xlsx':
        case '.xls':
            case '.xlsm': break;
            default: vd($f);die('Неверное расширение');
}
if(!is_dir('1_otdel/'.$y))
	mkdir('1_otdel/'.$y);
if(!is_dir('1_otdel/'.$y.'/'.$u['login']))
	mkdir('1_otdel/'.$y.'/'.$u['login']);
move_uploaded_file($f['tmp_name'], '1_otdel/'.$y.'/'.$u['login'].'/'.$_POST['t_n'].strrchr($f['name'],'.'));

//$file_excel = \PhpOffice\PhpSpreadsheet\IOFactory::load('1_otdel/'.$y.'/'.$u['login'].'/'.$_POST['t_n'].strrchr($f['name'],'.'));
//$plan = $file_excel->getActiveSheet()->getCellByColumnAndRow(22,14)->getCalculatedValue();

mysqli_query($q, "DELETE FROM `bd_1`.`sogl` WHERE t_name LIKE '${t_name}_${_POST['t_n']}'");

mysqli_query($q, 'INSERT INTO `bd_1`.`sogl` (`t_name`,`date0`,`soglasovano`,`plan`) VALUES (\''.$t_name.'_'.$_POST['t_n']."','".date('d.m.Y H:i:s')."',0,'$plan')");

// echo 'Сохранено!';
// exit;
header('Location: ./reports1.php?y='.(string)$_GET['y'].'&login='.(string)$_GET['login']);
exit;
}

elseif((int)($_GET['del_t'])>0) {
$ff='1_otdel/'.$y.'/'.$t_user['login'].'/'.$_GET['del_t'];
if($_GET['del_t']==1) {
    mysqli_query($q, "DELETE FROM `bd_1`.`sogl` WHERE t_name LIKE '${t_name}_%'");
    for ($i=1; $i<17; $i++) {
        foreach (['.xls','.xlsm','.xlsx'] as $extension)
            if (file_exists('1_otdel/'.$y.'/'.$t_user['login'].'/'.$i.$extension))
                unlink('1_otdel/'.$y.'/'.$t_user['login'].'/'.$i.$extension);
    }
} else {
    mysqli_query($q, "DELETE FROM `bd_1`.`sogl` WHERE t_name='$t_name" . '_' . $_GET['del_t'] . "'");
    if (file_exists($ff . '.xls'))
        unlink($ff . '.xls');
    elseif (file_exists($ff . '.xlsx'))
        unlink($ff . '.xlsx');
    elseif (file_exists($ff . '.xlsm'))
        unlink($ff . '.xlsm');
}
header('Location: ./reports1.php?y='.(string)$_GET['y'].'&login='.(string)$_GET['login']);
}

elseif((int)($_POST['sendReport'])==1) {
	$t=(string)(int)$_POST['sendReport'];
	if($t_user['login']==$u['login']) {
		if($u['prava']==0)
			mysqli_query($q, "UPDATE `bd_1`.`sogl` SET date='".date('d.m.Y H:i:s')."',soglasovano=1 WHERE t_name='$t_name".'_'.$t."'");
		else
			mysqli_query($q, "UPDATE `bd_1`.`sogl` SET date2='".date('d.m.Y H:i:s')."',u2='".$u['n']."',soglasovano=2 WHERE t_name='$t_name".'_'.$t."'");
	} elseif($u['prava']==255)
		mysqli_query($q, "UPDATE `bd_1`.`sogl` SET date2='".date('d.m.Y H:i:s')."',u2='".$u['n']."',soglasovano=2 WHERE t_name='$t_name".'_'.$t."'");
	elseif($u['kod']==1)
        if($t_user['prava']==0)
            mysqli_query($q, "UPDATE `bd_1`.`sogl` SET date4='".date('d.m.Y H:i:s')."',u4='".$u['n']."',soglasovano=4 WHERE t_name='$t_name".'_'.$t."'");
        else
            mysqli_query($q, "UPDATE `bd_1`.`sogl` SET date3='".date('d.m.Y H:i:s')."',u3='".$u['n']."',soglasovano=3 WHERE t_name='$t_name".'_'.$t."'");
}

elseif((int)($_POST['sendReport'])==2) {
    $t=(string)(int)$_POST['sendReport'];
    if($t_user['login']==$u['login']) {
        if($u['prava']==0)
            mysqli_query($q, "UPDATE `bd_1`.`sogl` SET date5='".date('d.m.Y H:i:s')."',soglasovano2=1 WHERE t_name='$t_name".'_1'."'");
        else
            mysqli_query($q, "UPDATE `bd_1`.`sogl` SET date6='".date('d.m.Y H:i:s')."',u6='".$u['n']."',soglasovano2=2 WHERE t_name='$t_name".'_1'."'");
    } elseif($u['prava']==255)
        mysqli_query($q, "UPDATE `bd_1`.`sogl` SET date6='".date('d.m.Y H:i:s')."',u6='".$u['n']."',soglasovano2=2 WHERE t_name='$t_name".'_1'."'");
    elseif($u['kod']==1)
        mysqli_query($q, "UPDATE `bd_1`.`sogl` SET date7='".date('d.m.Y H:i:s')."',u7='".$u['n']."',soglasovano2=3 WHERE t_name='$t_name".'_1'."'");
}

elseif ((string)$_GET['form']=='1_sem') {
    $ff = '1_otdel/'.$y.'/'.$t_user['login'].'/';
    for($i=2; $i<6; $i++) {
        foreach(['.xls','.xlsx','.xlsm'] as $extension)
            if(file_exists($ff.$i.$extension))
                $m[] = \PhpOffice\PhpSpreadsheet\IOFactory::load($ff.$i.$extension)->getActiveSheet();
            //else
                //echo $ff.$i.$extension.PHP_EOL;
    }
    if(count($m) != 4)
        die('Ошибка, не все месяцы интерпретированы');

    foreach(['.xls','.xlsx','.xlsm'] as $extension)
        if(file_exists($ff.'2'.$extension))
        {copy($ff.'2'.$extension,$ff.'6.xlsx');break;}

    $sem1 = \PhpOffice\PhpSpreadsheet\IOFactory::load($ff.'6.xlsx');

    for ($i=9; $i<14; $i++) {
        $row_sum = 0;
        for ($j=5; $j<12; $j++){
            $cell_sum = 0;
            foreach($m as $k) {
                $cell_sum += $k->getCellByColumnAndRow($j, $i)->getCalculatedValue();
            }
            $sem1->getActiveSheet()->setCellValueByColumnAndRow($j, $i, $cell_sum);
            $row_sum += $cell_sum;
        }
        $sem1->getActiveSheet()->setCellValueByColumnAndRow(12, $i, $row_sum);
    }
    for ($i=9; $i<14; $i++) {
        $row_sum = 0;
        for ($j=13; $j<22; $j++){
            $cell_sum = 0;
            foreach($m as $k) {
                $cell_sum += $k->getCellByColumnAndRow($j, $i)->getCalculatedValue();
            }
            $sem1->getActiveSheet()->setCellValueByColumnAndRow($j, $i, $cell_sum);
            $row_sum += $cell_sum;
        }
        $sem1->getActiveSheet()->setCellValueByColumnAndRow(22, $i, $row_sum);
    }

    for($i=9; $i<14; $i++) {
        $a1 = $sem1->getActiveSheet()->getCellByColumnAndRow(12, $i)->getValue();
        $a2 = $sem1->getActiveSheet()->getCellByColumnAndRow(22, $i)->getValue();
        $sem1->getActiveSheet()->setCellValueByColumnAndRow(23, $i, $a1+$a2);
    }

    for($i=5; $i<24; $i++) {
        $column_sum = array();
        $column_sum[] = $sem1->getActiveSheet()->getCellByColumnAndRow($i, 9)->getValue();
        $column_sum[] = $sem1->getActiveSheet()->getCellByColumnAndRow($i,10)->getValue();
        $column_sum[] = $sem1->getActiveSheet()->getCellByColumnAndRow($i,11)->getValue();
        $column_sum[] = $sem1->getActiveSheet()->getCellByColumnAndRow($i,12)->getValue();
        $column_sum[] = $sem1->getActiveSheet()->getCellByColumnAndRow($i,13)->getValue();
        $sem1->getActiveSheet()->setCellValueByColumnAndRow($i, 14, array_sum($column_sum));
    }

    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($sem1, 'Xlsx');
    $writer->save($ff.'6.xlsx');
    //die('1 семестр сформирован');

	mysqli_query($q, 'INSERT INTO `bd_1`.`sogl` (`t_name`,`date0`,`soglasovano`,`plan`) VALUES (\''.$t_name.'_6'."','".date('d.m.Y H:i:s')."',0,'".$sem1->getActiveSheet()->getCellByColumnAndRow(23,14)."')");
}

elseif ((string)$_GET['form']=='2_sem') {
    $ff = '1_otdel/'.$y.'/'.$t_user['login'].'/';
    for($i=7; $i<15; $i++) {
        foreach(['.xls','.xlsx','.xlsm'] as $extension)
            if(file_exists($ff.$i.$extension))
                $m[] = \PhpOffice\PhpSpreadsheet\IOFactory::load($ff.$i.$extension)->getActiveSheet();
            //else
                //echo $ff.$i.$extension.PHP_EOL;
    }
    if(count($m) != 8)
        die('Ошибка, не все месяцы интерпретированы');

    foreach(['.xls','.xlsx','.xlsm'] as $extension)
        if(file_exists($ff.'2'.$extension))
        {copy($ff.'7'.$extension,$ff.'15.xlsx');break;}

    $sem1 = \PhpOffice\PhpSpreadsheet\IOFactory::load($ff.'15.xlsx');

    for ($i=9; $i<14; $i++) {
        $row_sum = 0;
        for ($j=5; $j<12; $j++){
            $cell_sum = 0;
            foreach($m as $k) {
                $cell_sum += $k->getCellByColumnAndRow($j, $i)->getCalculatedValue();
            }
            $sem1->getActiveSheet()->setCellValueByColumnAndRow($j, $i, $cell_sum);
            $row_sum += $cell_sum;
        }
        $sem1->getActiveSheet()->setCellValueByColumnAndRow(12, $i, $row_sum);
    }
    for ($i=9; $i<14; $i++) {
        $row_sum = 0;
        for ($j=13; $j<22; $j++){
            $cell_sum = 0;
            foreach($m as $k) {
                $cell_sum += $k->getCellByColumnAndRow($j, $i)->getCalculatedValue();
            }
            $sem1->getActiveSheet()->setCellValueByColumnAndRow($j, $i, $cell_sum);
            $row_sum += $cell_sum;
        }
        $sem1->getActiveSheet()->setCellValueByColumnAndRow(22, $i, $row_sum);
    }

    for($i=9; $i<14; $i++) {
        $a1 = $sem1->getActiveSheet()->getCellByColumnAndRow(12, $i)->getValue();
        $a2 = $sem1->getActiveSheet()->getCellByColumnAndRow(22, $i)->getValue();
        $sem1->getActiveSheet()->setCellValueByColumnAndRow(23, $i, $a1+$a2);
    }

    for($i=5; $i<24; $i++) {
        $column_sum = array();
        $column_sum[] = $sem1->getActiveSheet()->getCellByColumnAndRow($i, 9)->getValue();
        $column_sum[] = $sem1->getActiveSheet()->getCellByColumnAndRow($i,10)->getValue();
        $column_sum[] = $sem1->getActiveSheet()->getCellByColumnAndRow($i,11)->getValue();
        $column_sum[] = $sem1->getActiveSheet()->getCellByColumnAndRow($i,12)->getValue();
        $column_sum[] = $sem1->getActiveSheet()->getCellByColumnAndRow($i,13)->getValue();
        $sem1->getActiveSheet()->setCellValueByColumnAndRow($i, 14, array_sum($column_sum));
    }

    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($sem1, 'Xlsx');
    $writer->save($ff.'15.xlsx');
    //die('2 семестр сформирован');

    mysqli_query($q, 'INSERT INTO `bd_1`.`sogl` (`t_name`,`date0`,`soglasovano`,`plan`) VALUES (\''.$t_name.'_15'."','".date('d.m.Y H:i:s')."',0,'".$sem1->getActiveSheet()->getCellByColumnAndRow(23,14)."')");
}

elseif ((string)$_GET['form']=='year') {
    $ff = '1_otdel/'.$y.'/'.$t_user['login'].'/';
    if(file_exists($ff.'6.xlsx'))
        $m[] = \PhpOffice\PhpSpreadsheet\IOFactory::load($ff.'6.xlsx')->getActiveSheet();
    else
        die('Отсутствует 1 семестр');
    if(file_exists($ff.'15.xlsx'))
        $m[] = \PhpOffice\PhpSpreadsheet\IOFactory::load($ff.'15.xlsx')->getActiveSheet();
    else
        die('Отсутствует 2 семестр');
    copy($ff.'15.xlsx',$ff.'16.xlsx');

    $year = \PhpOffice\PhpSpreadsheet\IOFactory::load($ff.'16.xlsx');

    for ($i=9; $i<15; $i++) {
        for ($j=5; $j<24; $j++){
            $cell_sum = 0;
            foreach($m as $k)
                $cell_sum += $k->getCellByColumnAndRow($j, $i)->getCalculatedValue();
            $year->getActiveSheet()->setCellValueByColumnAndRow($j, $i, $cell_sum);
        }
    }

    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($year, 'Xlsx');
    $writer->save($ff.'16.xlsx');
    //die('год сформирован');

    $fakt = $year->getActiveSheet()->getCellByColumnAndRow(23,14)->getCalculatedValue();
    mysqli_query($q, $kek="UPDATE `bd_1`.`sogl` SET fakt='$fakt' WHERE t_name='${t_name}_1'");
	
	mysqli_query($q, 'INSERT INTO `bd_1`.`sogl` (`t_name`,`date0`,`soglasovano`,`plan`) VALUES (\''.$t_name.'_16'."','".date('d.m.Y H:i:s')."',0,'".$year->getActiveSheet()->getCellByColumnAndRow(23,14)."')");
}

$t_sogl = mysqli_fetch_assoc(mysqli_query($q, "SELECT * FROM `bd_1`.`sogl` WHERE t_name='".$t_name."_1'"));

function viktor($t_n) {
global $u,$y,$t_user,$q;
$ff='1_otdel/'.$y.'/'.$t_user['login'].'/'.$t_n;
$x=($t_user['login']==$u['login'])?' | <a href="/reports1.php?del_t='.$t_n.'&y='.$_GET['y'].'">Удалить</a>':'';
$bd_1 = mysqli_fetch_assoc(mysqli_query($q, 'SELECT * FROM `bd_1`.`sogl` WHERE t_name=\''.$t_user['login'].'_'.$y.'_1\''));
if($bd_1['soglasovano']==4)
    $x = '';

if(file_exists($ff.'.xls'))
    if($t_n==1)
        echo '<a target=_blank href="'.$ff.'.xls'.'">Скачать</a> | <a target=_blank href="reports1_show_full.php?y='.$y.'&u='.$t_user['login'].'&f=xls">Просмотр</a>',$x;
    else
        echo '<a target=_blank href="reports1_show.php?y='.$y.'&u='.$t_user['login'].'&n='.$t_n.'&f=xls">Просмотр</a> | <a target=_blank href="'.$ff.'.xls'.'">Скачать</a>',' (',(\PhpOffice\PhpSpreadsheet\IOFactory::load($ff.'.xls')->getActiveSheet()->getCell('W14')->getCalculatedValue()),')',$x;
elseif(file_exists($ff.'.xlsx'))
    if($t_n==1)
        echo '<a target=_blank href="'.$ff.'.xlsx'.'">Скачать</a> | <a target=_blank href="reports1_show_full.php?y=\'.$y.\'&u=\'.$t_user[\'login\'].\'&f=xls">Просмотр</a>\'',$x;
    else
        echo '<a target=_blank href="reports1_show.php?y='.$y.'&u='.$t_user['login'].'&n='.$t_n.'&f=xlsx">Просмотр</a> | <a target=_blank href="'.$ff.'.xlsx'.'">Скачать</a>',' (',(\PhpOffice\PhpSpreadsheet\IOFactory::load($ff.'.xlsx')->getActiveSheet()->getCell('W14')->getCalculatedValue()),')',$x;
elseif(file_exists($ff.'.xlsm'))
    if($t_n==1)
        echo '<a target=_blank href="'.$ff.'.xlsm'.'">Скачать</a> | <a target=_blank href="reports1_show_full.php?y=\'.$y.\'&u=\'.$t_user[\'login\'].\'&f=xls">Просмотр</a>\'',$x;
    else
        echo '<a target=_blank href="reports1_show.php?y='.$y.'&u='.$t_user['login'].'&n='.$t_n.'&f=xlsm">Просмотр</a> | <a target=_blank href="'.$ff.'.xlsm'.'">Скачать</a>',' (',(\PhpOffice\PhpSpreadsheet\IOFactory::load($ff.'.xlsm')->getActiveSheet()->getCell('W14')->getCalculatedValue()),')',$x;
elseif($u['kod']==1 || $u['prava']==300 || ($u['prava']==255 && $t_user['login']!=$u['login']))
	echo '<div>Таблица не загружена</div>';
elseif($t_n==6)
    if(mysqli_query($q, 'SELECT * FROM `bd_1`.`sogl` WHERE t_name=\''.$t_user['login'].'_'.$y.'_5\'')->num_rows==1)
        echo '<a href="reports1.php?y='.$y.'&form=1_sem">Сформировать</a>';
    else
        echo '-';
elseif($t_n==15)
    if(mysqli_query($q, 'SELECT * FROM `bd_1`.`sogl` WHERE t_name=\''.$t_user['login'].'_'.$y.'_14\'')->num_rows==1)
        echo '<a href="reports1.php?y='.$y.'&form=2_sem">Сформировать</a>';
    else
        echo '-';
elseif($t_n==16)
    if(mysqli_query($q, 'SELECT * FROM `bd_1`.`sogl` WHERE t_name=\''.$t_user['login'].'_'.$y.'_15\'')->num_rows==1)
        echo '<a href="reports1.php?y='.$y.'&form=year">Сформировать</a>';
    else
        echo '<div>-</div>';
else
	echo '<form method=post enctype="multipart/form-data" action=""><input type=hidden name=t_n value='.$t_n.'><input type=file name=f_'.$t_n.' onchange="this.parentNode.submit()"></form>';
}

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
.pointer:hover {
	cursor:pointer;
	background: rgba(220,220,220, 0.5)
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
                <img alt src="profile_photos/<?=$u['n']?>.jpg?<?=time()?>" class="avatar img-fluid rounded-circle mr-1"/> <span style="color: #fff;"><?=fio_cut()?></span>
						</li>
					</ul>
				</div>
			</nav>
<?
$bd[]=null;
for($i=1;$i<19;$i++) {
	$bd[]=mysqli_fetch_assoc(mysqli_query($q, 'SELECT * FROM `bd_1`.`sogl` WHERE t_name=\''.$t_name.'_'.$i.'\''));
}
?>
			<main class="content">
				<div class="container-fluid p-0">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h3 class="text-center">Пользователь: <?=$t_user['f'].' '.$t_user['i'].' '.$t_user['o'].' ('.$t_user['login'].')'.'<br>Учебный год: '.$y.'-'.($y+1)?></h3>
                                        <h4 class="header-title m-t-0 m-b-30">1.1 Планируемая учебная нагрузка <?
if($bd[1]['date4'])
	echo '(согласовано)';
elseif($bd[1]['date3'])
	echo '(на утверждении)';
elseif($bd[1]['date2']) {
	if($u['kod']==1)
		echo '<a class="btn btn-success btn-sm" onclick="sendReport(\'1\')" href="#">Согласовать</a>';
	else
		echo '(на согласовании (УО))';
} elseif($bd[1]['date']) {
	if($u['login']!=$t_user['login'] && $u['prava']==255)
		echo '<a class="btn btn-success btn-sm" onclick="sendReport(\'1\')" href="#">Согласовать</a>';
	else
		echo '(на согласовании (НК))';
}elseif($bd[1]['date0'] && $u['login']==$t_user['login']) echo '<a class="btn btn-success btn-sm" onclick="sendReport(\'1\')" href="#">На согласование</a>';
?></h4>

                                        <form></form>
                                        <div>
                                            <table class="table m-b-0">
                                                <thead>
                                                    <tr>
                                                        <th>Период</th>
                                                        <th>Таблица</th>
                                                    </tr>
                                                </thead>
                                                <tbody style="font-size:17px">
                                                    <tr>
                                                        <td>Учебный год — развёрнутая таблица</td>
                                                        <td class="pointer"><?viktor(1)?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>1 семестр — сокращённая таблица</td>
                                                        <td class="pointer"><?viktor(17)?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>2 семестр — сокращённая таблица</td>
                                                        <td class="pointer" onnoclick="if(this.firstElementChild.innerText=='Скачать')this.firstElementChild.click()"><?viktor(18)?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Учебный год — сокращённая таблица</td>
                                                        <td class="pointer" onnoclick="if(this.firstElementChild.innerText=='Скачать')this.firstElementChild.click()"><?viktor(19)?></td>
                                                    </tr>
                                                </tbody>
                                            </table>


                                        </div>
                                        <h4 class="header-title m-t-0 m-b-30">1.2 Фактически выполненная учебная нагрузка <?
if($bd[1]['date7'])
    echo '(согласовано)';
elseif($bd[1]['date6'])
    if($u['kod']==1)
        echo '<a class="btn btn-success btn-sm" onclick="sendReport(\'2\')" href="#">Согласовать</a>';
    else
        echo '(на согласовании (УО))';
elseif($bd[1]['date5']) {
    if($u['prava']==255)
        echo '<a class="btn btn-success btn-sm" onclick="sendReport(\'2\')" href="#">Согласовать</a>';
    else
        echo '(на согласовании (НК))';
} elseif($bd[1]['date4']) {
    if($u['login']==$t_user['login'])
        echo '<a class="btn btn-success btn-sm" onclick="sendReport(\'2\')" href="#">На согласование</a>';
    else
        echo '(на заполнении преподавателем)';
} else echo '(План не согласован)'
?></h4>
                                        <div>
                                            <table class="table m-b-0">
                                                <thead>
                                                    <tr>
                                                        <th>Период</th>
                                                        <th>Таблица</th>
                                                    </tr>
                                                </thead>
                                                <tbody style="font-size:17px">
                                                    <tr>
                                                        <td>Сентябрь</td>
                                                        <td class="pointer"><?viktor(2)?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Октябрь</td>
                                                        <td class="pointer"><?viktor(3)?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Ноябрь</td>
                                                        <td class="pointer"><?viktor(4)?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Декабрь</td>
                                                        <td class="pointer"><?viktor(5)?></td>
                                                    </tr>
                                                    <tr style="background:#f0f0f0">
                                                        <td>1 семестр</td>
                                                        <td class="pointer"><?viktor(6)?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Январь</td>
                                                        <td class="pointer"><?viktor(7)?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Февраль</td>
                                                        <td class="pointer"><?viktor(8)?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Март</td>
                                                        <td class="pointer"><?viktor(9)?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Апрель</td>
                                                        <td class="pointer"><?viktor(10)?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Май</td>
                                                        <td class="pointer"><?viktor(11)?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Июнь</td>
                                                        <td class="pointer"><?viktor(12)?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Июль</td>
                                                        <td class="pointer"><?viktor(13)?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Август</td>
                                                        <td class="pointer"><?viktor(14)?></td>
                                                    </tr>
                                                    <tr style="background:#f0f0f0">
                                                        <td>2 семестр</td>
                                                        <td class="pointer"><?viktor(15)?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Учебный год</td>
                                                        <td class="pointer"><?viktor(16)?></td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                        </div>
                                    </div>
                                </div><!-- end card -->
<div class="card">
	<div class="card-header">
		<h5 class="card-title mb-0 text-center">Комментарии к таблице</h5>
	</div>
	<div class="card-body h-100">
<?
$res = mysqli_query($q, 'SELECT * from `bd_1`.`comments` WHERE t_name=\''.$t_name.'\'');
for($i=0; $i<$res->num_rows; $i++) {
	$r = mysqli_fetch_assoc($res);
	$user = mysqli_fetch_assoc(mysqli_query($q, 'SELECT * from `main`.`users` WHERE n='.$r['n']));
?>
		<div class="media">
			<img src="/profile_photos/<?=$user['n']?>.jpg" width="56" height="56" class="inspector rounded-circle mr-2" alt>
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
				<div class="col-sm-10"><!--label for="comment"></label --><textarea class="form-control" id="comment" placeholder="Текст комментария" rows="3" maxlength="900"></textarea></div>
			</div>
			<div class="form-group row">
				<label class="col-form-label col-sm-1 text-sm-right"></label>
				<div class="col-sm-10">
					<button type="button" class="btn btn-primary" onclick="sendComment()">Отправить</button>
<?
if(($u['prava']==255 && $t_sogl['soglasovano']==1) ||
			($u['kod']==1 && $t_sogl['soglasovano']==2) ||
            ($u['prava']==300 && $t_sogl['soglasovano']==3) ||
			($u['prava']==255 && $t_sogl['soglasovano2']==1) ||
			($u['kod']==1 && $t_sogl['soglasovano2']==2)) {?>
					<button type="button" class="btn btn-primary" onclick="if(ge('comment').value.length<5)alert('Заполните заменчание');else sendComment('1')" style="float:right">Отправить на доработку</button>
<?}?>
				</div>
			</div>
		</form>
	</div>
</div>
                            </div><!-- end col -->
                        </div><!-- end row -->
				</div>
			</main>
		</div>
	</div>
	
<script src="assets/js/app.js"></script>
<script src="assets/js/jq.js"></script>
<script src="assets/js/mindmup-editabletable.js"></script>
<!--suppress JSPotentiallyInvalidConstructorUsage -->
    <script>
//$('#mainTable').editableTableWidget().numericInputExample().find('td:first').focus();
ge = function(q){return document.getElementById(q);};
xhr = function(){let x;try{x=new ActiveXObject('Msxml2.XMLHTTP');} catch(e){try{x=new ActiveXObject('Microsoft.XMLHTTP');}catch(E){x=false;}}if(!x){x=new XMLHttpRequest();}return x;};
debug = <?=DEBUG?>;

sendComment = function (q) {
    let x = xhr();
    x.open('POST', location.pathname + location.search, true);
	x.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	x.send('sendComment=1&comment=' + encodeURIComponent(ge('comment').value) + (q?'&toTeacher=1':'')); 
	x.onreadystatechange = function() {
		if (this.readyState !== 4) return;
		if (x.status === 200) {
			console.log(x.responseText);
			if(!debug)
				document.location.reload();
		} else {
			alert('error: sendComment js');
		}
	}
};

sendReport = function (q) {
    let x = xhr();
    x.open('POST', location.pathname + location.search, true);
	x.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	x.send('sendReport=' + q); 
	x.onreadystatechange = function() {
		if (this.readyState !== 4) return;
		if (x.status === 200) {
			console.log(x.responseText);
			//alert('Таблица отправлена на проверку');
			if(!debug)
				document.location.reload();
		} else {
			alert('error: sendReport(' + q + ') js');
		}
	}
}

/*
downloads = document.getElementsByClassName('pointer');
for(i=0; i<downloads.length; i++)downloads[i].addEventListener("click", function() {
    if(this.firstElementChild.innerText=='Просмотр')this.firstElementChild.click();
});
 */
</script>
</body>
</html>