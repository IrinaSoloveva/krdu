<?
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
	default: if($u['kod']!=1)die('no privilegies'); break;
}

// подключение к базе
if(!($q = mysqli_connect('localhost', 'root', '', '')))
  die('Невозможно подключиться к базе данных. Код ошибки: '. mysqli_connect_error());

// определение дефолтной таблицы (логин_год_n)
$y = (string)(int)$_GET['y'];
if($y<2019 || $y>date('Y'))
	die('incorrect year');

// определение стадии согласования таблицы
$t_name = $_GET['u'].'_'.$y.'_'.$_GET['n'];
$t_sogl = mysqli_fetch_assoc(mysqli_query($q, "SELECT * FROM `bd_1`.`sogl` WHERE t_name='".$t_name."'"));
if(!$t_sogl)
    die('no table: sql error');
require 'C:\OSPanel\modules\php\PHP_7.3-x64\vendor\autoload.php';
if(file_exists($f_path='1_otdel/'.$y.'/'.$_GET['u'].'/'.$_GET['n'].'.'.$_GET['f']))
    $file_excel = \PhpOffice\PhpSpreadsheet\IOFactory::load($f_path);
$t_user = mysqli_fetch_assoc(mysqli_query($q, "SELECT * FROM `main`.`users` WHERE login='".$_GET['u']."'"));
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
                                        <h5 style="text-align:right"><a href="#" onclick="window.close()" style="text-decoration:none">Вернуться</a></h5>
                                        <h4 class="header-title m-t-0 m-b-30 text-center">Учебная нагрузка<?
echo '<br>Пользователь: ',$t_user['f'],' ',$t_user['i'],' ',$t_user['o'],' ('.$t_user['login'],')';
echo '<br>Учебный год: ',$y,'-',$y+1;
echo $sogl;
?></h4>
                                        <div class="table-responsive">
                                            <table id="mainTable" class="table table-striped m-b-30" style="font-size:11px">
                                                <thead>
                                                    <tr>
                                                        <th>Форма обучения</th>
                                                        <th>Прочее</th>
                                                        <th>ЛК</th>
                                                        <th>ГЗ</th>
                                                        <th>ЗЧ</th>
                                                        <th>КЭ</th>
                                                        <th>ЭК</th>
                                                        <th>ГИА</th>
                                                        <th>Итого</th>
                                                        <th>Проверка КР</th>
                                                        <th>Руководство практикой</th>
                                                        <th>Руководство курсовой работой, практикумом</th>
                                                        <th>Руководство ВКР</th>
                                                        <th>Рецензирование ВКР</th>
                                                        <th>Рецензирование реферата</th>
                                                        <th>Научное руководство адъюнктами</th>
                                                        <th>Прием внеауд. чтения по ин. яз.</th>
                                                        <th>Текущие консультации</th>
                                                        <th>Итого</th>
                                                        <th>Всего</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tb" kol="0">
<?
$study_form = array('ОФО','ЗФО','Адъюн','ДПО','Прочее');
//$res = mysqli_query($q, 'SELECT * from `bd_2`.`'.$t_name.'`');
$row = 8;
foreach($study_form as $i) {
    $row++;
    //$res->num_rows
	//$r = mysqli_fetch_assoc($res);
?>
                                                    <tr>
                                                        <td><?=$i?></td>
                                                        <?for($col=5;$col<24;$col++)echo '<td>',$file_excel->getActiveSheet()->getCellByColumnAndRow($col,$row)->getCalculatedValue(),'</td>';?>
                                                    </tr>
<?
}
?>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th><strong>ИТОГО:</strong></th>
                                                        <?for($col=5;$col<24;$col++)echo '<th>',$file_excel->getActiveSheet()->getCellByColumnAndRow($col,14)->getCalculatedValue(),'</th>';?>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div><!-- end card -->
                            </div><!-- end col -->
                        </div>
                        <!-- end row -->
				</div>
			</main>

		</div>
	</div>
<!-- script src="assets/js/jq.js"></script>
<script src="assets/js/mindmup-editabletable.js"></script -->
<script>
ge = function(q){return document.getElementById(q);}
debug = <?=DEBUG?>;
</script>
</body>
</html>