<?
session_start();
if(!isset($_SESSION['user'])) {
  header('HTTP/1.1 401 Unauthorized');
  header('Location: /');
  exit;
}
$u = &$_SESSION['user'];
require_once('f.php');

if(!($q = mysqli_connect('localhost', 'root', '', ''))) {
  printf("Невозможно подключиться к базе данных. Код ошибки: %s\n", mysqli_connect_error());
  exit;
}

$sem = (string)(int)$_GET['sem']>1 ? 2 : 1;
$t_name = $u['login'].'_'.date('Y').'_'.$sem;

if(isset($_GET['sendReport']) && (int)$_GET['sendReport'] === 1) {
	if($u['prava']==255) {
		mysqli_query($q, "UPDATE `bd_3`.`sogl` SET soglasovano='2',date2='".date('H:i:s d.m.Y')."' WHERE t_name='$t_name'");
	} else {
		mysqli_query($q, "UPDATE `bd_3`.`sogl` SET soglasovano='1' WHERE t_name='$t_name'");
	}
}

if(isset($_GET['saveReport']) && (int)$_GET['saveReport'] === 1) {
	if($res = mysqli_query($q, 'DROP TABLE if EXISTS `bd_3`.`'.$t_name.'`')) {
		var_dump($res);
		if($qwe = mysqli_fetch_assoc($res)){}
		mysqli_free_result($res);
	}
	if($res = mysqli_query($q, 'CREATE TABLE `bd_3`.`'.$t_name.'` (`n` VARCHAR(8) NOT NULL,`naim` VARCHAR(200),`plan` VARCHAR(8) NOT NULL,`fakt` VARCHAR(8),`srok` VARCHAR(60),`otmetka` VARCHAR(500)) ENGINE = InnoDB')) {
		var_dump($res);
		if($qwe = mysqli_fetch_assoc($res)){}
		mysqli_free_result($res);
	}
	$rows = explode('(||)', htmlspecialchars($_POST['table_data']));
	for ($j=0; $j<count($rows)-1; $j++) {
		$r = explode('|', $rows[$j]);
		$r[0] = (string)(int)$r[0];
		$r2 = implode('\',\'', $r);
		$abc = 'INSERT INTO `bd_3`.`'.$t_name."` (`n`, `naim`, `plan`, `fakt`, `srok`, `otmetka`) VALUES ('".$r2."')";
		var_dump($abc);
		if($res = mysqli_query($q, $abc)) {
			var_dump($res);
			if($qwe = mysqli_fetch_assoc($res)){}
			mysqli_free_result($res);
		}
	}
	$t_name = $u['login'].'_'.(string)$_GET['y'].'_'.(string)$_GET['sem'];
	if(mysqli_query($q, "SELECT 1 from `bd_3`.`sogl` WHERE t_name='$t_name'")->num_rows == 0) {
		mysqli_query($q, "INSERT INTO `bd_3`.`sogl` (`t_name`,`date`,`date2`,`date3`,`plan`,`fakt`,`soglasovano`) VALUES ('$t_name','".date('H:i:s d.m.Y')."','0','0','0','0','0')");
	} else {
		mysqli_query($q, "UPDATE `bd_3`.`sogl` SET date=".date('H:i:s d.m.Y')."' WHERE t_name='$t_name'");
	}
	if($res = mysqli_query($q, 'SELECT * FROM `bd_3`.`sogl` WHERE t_name='.$t_name)) {
		if($r = mysqli_fetch_assoc($res)) {
			var_dump($r);
		}
	}
	echo 'Сохранено!';
	exit;
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<title>СВИПП | Отчёт</title>

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
        		<ul>
	        		<li><a href="/"><i class="fa fa-edit" style="margin-right: 9px;"></i>Индивидуальный план</a></li>
	        		<li><a href="stat.php"><i class="fa fa-clipboard" style="margin-right: 9px;"></i>Статистика</a></li>
	        		<li><a href="chat.php"><i class="fa fa-cog" style="margin-right: 9px;"></i>Чат</a></li>
	        		<li><a href="#" style="background-color:#666"><i class="fa fa-copy" style="margin-right: 9px;"></i>Отчёт</a></li>
        		</ul>
        	</div>
				<div class="navbar-collapse collapse">
					<ul class="navbar-nav ml-auto">
						<li class="nav-item dropdown">
                <img src="profile_photos/<?=$u['photo_link']?$u['n']:'0'?>.jpg" class="avatar img-fluid rounded-circle mr-1"/> <span style="color: #fff;"><?=fio_cut()?></span>
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
                                        
            
                                        <h4 class="header-title m-t-0 m-b-30 text-center">Научно-исследовательская работа <?
$sogl = mysqli_fetch_assoc(mysqli_query($q, "SELECT soglasovano from `bd_3`.`sogl` WHERE t_name='$t_name'"))['soglasovano'];
switch($sogl) {
	case 0: break;
	case 1: echo '(на согласновании у НК)'; break;
	case 2: echo '(на согласовании у НИО)'; break;
	case 3: echo '(согласовано)'; break;
}?></h4>
            
                                        <div class="table-responsive">
                                            <table id="mainTable" class="table table-striped m-b-30" style="font-size:11px">
                                                <thead>
                                                    <tr>
                                                        <th style="display:none">№</th>
                                                        <th>Вид научно-исследовательской работы</th>
                                                        <th>Наименование, дисциплина (при наличии)</th>
                                                        <th>Планируемая работа</th>
                                                        <th>Фактическая работа</th>
                                                        <th>Срок выполнения</th>
                                                        <th>Нормы времени для расчета нагрузки за учебный год (в часах)</th>
                                                        <th>Отметка о выполнении с указанием дисциплины, № протокола, заседания кафедры или методической секции, даты конференции, семинара, рецинзирования, и т.д.</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tb" kol="0">
<?
$vid = explode('|', '|Выполнение научно-исследовательских работ  (за исключением работ, направленных на подготовку учебников, учебных пособий, лекций и т.д.):|- по планам кафедры;|- по планам вуза;|- по планам МВД России.|Подготовка учебников, учебных пособий, лекций, включая подготовку к изданию:|- написание нового учебника, монографии;|- переиздание учебника;|- написание нового учебного пособия, курса лекций, практикума, справочника;|- написание лекций для  издания;|- переиздание лекций;|- написание научных статей докладов, сообщений для научных конференций и семинаров.|Внедрение научных разработок в практическую деятельность ОВД и его авторское сопровождение.|Научное редактирование  рабочих  учебных программ, учебников, учебных пособий, монографий, курсов лекций, научных трудов и т.д.|Рецензирование рабочих учебных программ, учебно-методических материалов,  учебников, учебных пособий, монографий, курсов лекций, научных трудов, диссертаций, авторефератов, проектов, нормативных актов и т.д.|Научная командировка (стажировка)|Участие в работе:|- Ученого совета образовательного учреждения;|- диссертационных советов:|председатель, заместитель председателя, секретарь диссовета;|членство в диссовете|- научно-методического и научно-технического советов МВД России.|Участие в работе научных конференций, семинаров и т.п.|Проведение научных консультаций с докторантом|Руководство:|- слушательским научным обществом, |научным кружком;|- научно-исследовательской работой  слушателя  (НИРС) с представлением слушателем научных отчетов, докладов, статей, рефератов и т.п.|Разработка диссертационного исследования, утвержденного советом образовательного учреждения:|- кандидатского;|- докторского.|Организационно-методическая работа|Участие в работе Совета по научной деятельности');
if($res=mysqli_query($q, 'SELECT * from `bd_3`.`'.$t_name.'`')) {
	for($i=0; $i<$res->num_rows; $i++){
		$r = mysqli_fetch_assoc($res);
?>
                                                    <tr>
                                                        <td style="display:none"><?=$r['n']?></td>
                                                        <td><?=$vid[$r['n']]?></td>
                                                        <td><?=$r['naim']?></td>
                                                        <td><?=$r['plan']?></td>
                                                        <td><?=$r['fakt']?></td>
                                                        <td><?=$r['srok']?></td>
                                                        <td></td>
                                                        <td><?=$r['otmetka']?></td>
                                                        <td class="del_row" style="color:red" onclick="this.parentNode.parentNode.removeChild(this.parentNode)">x</td>
                                                    </tr>
<?
	}
}
?>
                                                    <!-- tr>
                                                        <td style="display:none">1</td>
                                                        <td>1 Разработка примерной программы дисциплины</td>
                                                        <td>йцу</td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td class="del_row" style="color:red" onclick="this.parentNode.parentNode.removeChild(this.parentNode)">x</td>
                                                    </tr -->
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th><strong>ИТОГО:</strong></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
<input autocomplete placeholder="Поиск..." style="width:155px" onkeyup="if(this.value!='')test(this.value)" id="s">&nbsp;&nbsp;<a onclick="ge('s').value='';test('')" style="color:#88e;cursor:pointer;font-size:17px;display:none" id="x"><b>x</b></a>
<div class="sel" id="sel1"></div>
<div class="sel" id="sel2"></div>
<div class="sel" id="sel3"></div>
<br>
<select name="val" onchange="addStr(this.value, this.options[this.value].text)" style="margin-bottom:20px;max-width:300px">
<option selected disabled>Выберите вид работы:</option>

<option value="1">Выполнение научно-исследовательских работ  (за исключением работ, направленных на подготовку учебников, учебных пособий, лекций и т.д.):</option>
<option value="2">- по планам кафедры;</option>
<option value="3">- по планам вуза;</option>
<option value="4">- по планам МВД России.</option>
<option value="5">Подготовка учебников, учебных пособий, лекций, включая подготовку к изданию:</option>
<option value="6">- написание нового учебника, монографии;</option>
<option value="7">- переиздание учебника;</option>
<option value="8">- написание нового учебного пособия, курса лекций, практикума, справочника;</option>
<option value="9">- написание лекций для  издания;</option>
<option value="10">- переиздание лекций;</option>
<option value="11">- написание научных статей докладов, сообщений для научных конференций и семинаров.</option>
<option value="12">Внедрение научных разработок в практическую деятельность ОВД и его авторское сопровождение.</option>
<option value="13">Научное редактирование  рабочих  учебных программ, учебников, учебных пособий, монографий, курсов лекций, научных трудов и т.д.</option>
<option value="14">Рецензирование рабочих учебных программ, учебно-методических материалов,  учебников, учебных пособий, монографий, курсов лекций, научных трудов, диссертаций, авторефератов, проектов, нормативных актов и т.д.</option>
<option value="15">Научная командировка (стажировка)</option>
<option value="16">Участие в работе:</option>
<option value="17">- Ученого совета образовательного учреждения;</option>
<option value="18">- диссертационных советов:</option>
<option value="19">председатель, заместитель председателя, секретарь диссовета;</option>
<option value="20">членство в диссовете</option>
<option value="21">- научно-методического и научно-технического советов МВД России.</option>
<option value="22">Участие в работе научных конференций, семинаров и т.п.</option>
<option value="23">Проведение научных консультаций с докторантом</option>
<option value="24">Руководство:</option>
<option value="25">- слушательским научным обществом, </option>
<option value="26">научным кружком;</option>
<option value="27">- научно-исследовательской работой  слушателя  (НИРС) с представлением слушателем научных отчетов, докладов, статей, рефератов и т.п.</option>
<option value="28">Разработка диссертационного исследования, утвержденного советом образовательного учреждения:</option>
<option value="29">- кандидатского;</option>
<option value="30">- докторского.</option>
<option value="31">Организационно-методическая работа</option>
<option value="32">Участие в работе Совета по научной деятельности</option>

</select>
<!--
$b = explode(PHP_EOL, $a);
for ($i=1; $i<count($b); $i++) {
 echo '<option value="' . $i . '">' . $b[$i] . '</option>' . PHP_EOL;
}
-->

                                            <div><a style="padding:7px" class="btn btn-success btn-sm float-right" href="#save" onclick="saveReport()">Сохранить</a></div>
                                            <div style="margin-right:100px"><a style="padding:7px" class="btn btn-success btn-sm float-right" href="#send" onclick="sendReport()">На проверку</a></div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div><!-- end col -->
                        </div>
                        <!-- end row -->


				</div>
			</main>

		</div>
	</div>
<script src="assets/js/app.js"></script>
<script src="assets/js/bootstrap.js"></script>
<script src="assets/js/dataTables.js"></script>
<script src="assets/js/dataTables_002.js"></script>
<script src="assets/js/detect.js"></script>
<script src="assets/js/fastclick.js"></script>
<script src="assets/js/jquery.js"></script>
<script src="assets/js/jquery_002.js"></script>
<script src="assets/js/jquery_003.js"></script>
<script src="assets/js/jquery_004.js"></script>
<script src="assets/js/jquery_005.js"></script>
<script src="assets/js/jquery_006.js"></script>
<script src="assets/js/jquery_007.js"></script>
<script src="assets/js/jquery_008.js"></script>
<script src="assets/js/jquery_009.js"></script>
<script src="assets/js/jquery_010.js"></script>
<script src="assets/js/mindmup-editabletable.js"></script>
<script src="assets/js/modernizr.js"></script>
<script src="assets/js/numeric-input-example.js"></script>
<script src="assets/js/responsive.js"></script>
<script src="assets/js/waves.js"></script>
<script src="assets/js/wow.js"></script>
<script>
ge=function(q){return document.getElementById(q);}
$('#mainTable').editableTableWidget();
db_all=['Выполнение научно-исследовательских работ  (за исключением работ, направленных на подготовку учебников, учебных пособий, лекций и т.д.):','- по планам кафедры;','- по планам вуза;','- по планам МВД России.','Подготовка учебников, учебных пособий, лекций, включая подготовку к изданию:','- написание нового учебника, монографии;','- переиздание учебника;','- написание нового учебного пособия, курса лекций, практикума, справочника;','- написание лекций для  издания;','- переиздание лекций;','- написание научных статей докладов, сообщений для научных конференций и семинаров.','Внедрение научных разработок в практическую деятельность ОВД и его авторское сопровождение.','Научное редактирование  рабочих  учебных программ, учебников, учебных пособий, монографий, курсов лекций, научных трудов и т.д.','Рецензирование рабочих учебных программ, учебно-методических материалов,  учебников, учебных пособий, монографий, курсов лекций, научных трудов, диссертаций, авторефератов, проектов, нормативных актов и т.д.','Научная командировка (стажировка)','Участие в работе:','- Ученого совета образовательного учреждения;','- диссертационных советов:','председатель, заместитель председателя, секретарь диссовета;','членство в диссовете','- научно-методического и научно-технического советов МВД России.','Участие в работе научных конференций, семинаров и т.п.','Проведение научных консультаций с докторантом','Руководство:','- слушательским научным обществом, ','научным кружком;','- научно-исследовательской работой  слушателя  (НИРС) с представлением слушателем научных отчетов, докладов, статей, рефератов и т.п.','Разработка диссертационного исследования, утвержденного советом образовательного учреждения:','- кандидатского;','- докторского.','Организационно-методическая работа','Участие в работе Совета по научной деятельности'];

function addStr(n, val) {
	ge('tb').innerHTML += '<tr><td style="display:none">' + n + '</td><td>' + val + '</td><td></td><td></td><td></td><td></td><td></td><td></td><td class="del_row" style="color:red" onclick="this.parentNode.parentNode.removeChild(this.parentNode)">x</td></tr>';
	document.getElementsByName('val')[0].selectedIndex=0;
	$('#mainTable').editableTableWidget();
}

//document.getElementsByClassName('sel').onclick=function(){alert(this.id.substr(3))}

function test(inp) {
	if(inp=='') {
		ge('x').style['display'] = 'none';
		for(i=1; i<4; i++) {
			ge('sel'+i).innerHTML = '';
			ge('sel'+i).onclick = '';
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
		ge('sel'+(i+1)).onclick = '';
	}
	return true;
}

function xhr() {
	var x;try{x=new ActiveXObject('Msxml2.XMLHTTP');} catch(e){try{x=new ActiveXObject('Microsoft.XMLHTTP');}catch(E){x=false;}}if(!x){x2=('onload' in new XMLHttpRequest())?XMLHttpRequest:XDomainRequest;x=new x2();}return x;
}

function saveReport() {
	a = document.getElementsByTagName('tr');
	str = '';
	for (i=1; i<a.length-1; i++) {
		b = a[i].getElementsByTagName('td');
		str += b[0].innerText + '|'+ b[2].innerText + '|'+ b[3].innerText + '|'+ b[4].innerText + '|'+ b[5].innerText + '|'+ b[7].innerText + '(||)';
	}
	console.log(str);

	var x = xhr();
	x.open('POST', location.pathname + location.search + '&saveReport=1', true);
	x.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	x.send('table_data=' + encodeURIComponent(str)); 
	x.onreadystatechange = function() {
		if (this.readyState != 4) return;
		if (x.status == 200) {
			//document.body.innerHTML=x.responseText;
			alert('ok');
		} else {
			alert('error');
		}
	}
}

function sendReport() {
	location.href = location.pathname + location.search + '&sendReport=1';
}
</script>

</body>

</html><?mysqli_close($q);?>