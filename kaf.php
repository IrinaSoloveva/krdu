<?
session_start();
if(!isset($_SESSION['user'])) {
  header('HTTP/1.1 401 Unauthorized');
  header('Location: /');
  exit;
}
$u = &$_SESSION['user'];
if(!in_array($u['prava'],array(255,256)))
	exit;
require_once('f.php');

if(!($q = mysqli_connect('localhost', 'root', '', '')))
  die('Невозможно подключиться к базе данных. Код ошибки: '. mysqli_connect_error());

$rus = 'а б в г д е ё ж з и й к л м н о п р с т у ф х ц ч ш щ ъ ы ь э ю я';
$eng = 'a b v g d e e zh z i i k l m n o p r s t u f kh ts ch sh shch ie y  e iu ia';
// var_dump(explode(' ',$eng));

if((int)$_GET['save'] == 1) {
	$p = &$_POST;
	if($p['n']!=(int)$p['n'] || (int)$p['n']==0 || strlen($p['login'])<1 || (strlen($p['pass'])!=0 && strlen($p['pass'])<6) || strlen($p['f'])<2 || strlen($p['i'])<2 || strlen($p['o'])<2)
		die('Данные некорректны');
	$p = array_map('htmlspecialchars', $p);
	$pass = ($p['pass']=='')?'':(",pass='${p['pass']}'");
	if(mysqli_query($q, "UPDATE `main`.`users` SET login='$p[login]'$pass,f='$p[f]',i='$p[i]',o='$p[o]',doljnost='$p[doljnost]',uchen_st='$p[uchen_st]',zvanie='$p[zvanie]' WHERE n=$p[n]"))
		echo 'Сохранено!';
	else
		echo 'error: save server';
	exit;
}

elseif((int)$_GET['deact']==1 && strlen((string)$_POST['login'])>0) {
	if(mysqli_query($q, "UPDATE `main`.`users` SET active='0' WHERE login='".$_POST['login']."'"))
		echo '1';
	else
		echo 'error: deact server';
	exit;
}

elseif((int)$_GET['makeChief']==1 && strlen((string)$_POST['login'])>0) {
	if(	mysqli_query($q, "UPDATE `main`.`users` SET prava='0' WHERE login='".$u['login']."'") &&
			mysqli_query($q, "UPDATE `main`.`users` SET prava='".$u['prava']."' WHERE login='".$_POST['login']."'")) {
		$u['prava']=($u['kod']<5)?1:0;
		echo '1';
	}	else
		echo 'error: makeChief server';
	exit;
}

elseif((int)$_GET['addUser']==1) {
	$p = &$_POST;
	if(strlen($p['pass'])<6 || strlen($p['f'])<2 || strlen($p['i'])<2 || strlen($p['o'])<2)
		die('Данные некорректны');
	$p = array_map('htmlspecialchars', $p);
	$n = 1+mysqli_query($q, "SELECT 1 FROM `main`.`users`")->num_rows;
	$f = mb_convert_case(mb_strtolower(trim($p['f'])), MB_CASE_TITLE, 'UTF-8');
	$i = mb_convert_case(mb_strtolower(trim($p['i'])), MB_CASE_TITLE, 'UTF-8');
	$o = mb_convert_case(mb_strtolower(trim($p['o'])), MB_CASE_TITLE, 'UTF-8');
	$login=strtoupper(str_replace(explode(' ',$rus),explode(' ',$eng),mb_strtolower(mb_substr($f,0,1)))).str_replace(explode(' ',$rus),explode(' ',$eng),mb_strtolower(mb_substr($f,1))).strtoupper(str_replace(explode(' ',$rus),explode(' ',$eng),mb_strtolower(mb_substr($i,0,1)).mb_strtolower(mb_substr($o,0,1))));
	$wtf = mysqli_query($q,'SELECT 1 FROM `main`.`users` WHERE login LIKE \''.$login.'%\'')->num_rows;
	if($wtf)
		$login .= (string)($wtf+1);
	if(!mysqli_query($q, "INSERT INTO `main`.`users` (`n`,`login`,`pass`,`f`,`i`,`o`,`kod`,`active`,`prava`,`doljnost`,`uchen_st`,`zvanie`) VALUES (".$n.",'$login','".$p['pass']."','$f','$i','$o',".$u['kod'].",1,".(($u['kod']<5)?1:0).",'".$p['doljnost']."','".$p['uchen_st']."','".$p['zvanie']."')"))
		echo 'error: addUser server';
	else
		mysqli_query($q, 'UPDATE `main`.`podrazdeleniya` SET kol_ludei=kol_ludei+1 WHERE kod='.$u['kod']);
	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<title>ЭИПП | Управление подразделением</title>

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
                <img src="profile_photos/<?=$u['n']?>.jpg?<?=time()?>" class="avatar img-fluid rounded-circle mr-1"/> <span style="color:#fff;"><?=fio_cut()?></span>
						</li>
					</ul>
				</div>
			</nav>


<main class="content">
<div class="container-fluid p-0">
<div class="row">
<div class="card flex-fill">
<div class="card-header">
	<h5 class="card-title mb-0 text-center"><?=$u['podr']?></h5>
</div>
<table class="table table-striped my-0">
<thead>
	<tr>
	<th>Логин</th>
	<th>ФИО</th>
	<th>Фото</th>
	<th>Должность</th>
	<th><a href="#addUser" data-toggle="modal" data-target="#addUser">Добавить преподавателя</a></th>
	</tr>
</thead>
<div class="modal fade" id="addUser" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog" role="document">
<div class="modal-content">
<div class="modal-header">
	<h5 class="modal-title">Добавление нового пользователя в подразделение</h5>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
</div>
<div class="modal-body m-3">
	<form id="addUser_form" method="post" action="<?=$_SERVER['PHP_SELF']?>?addUser=1">
		<!-- input style="display:none" required disabled name="addUser" value="1" -->
		<p class="mb-2"><span class="table_block">Пароль*:</span> <input required name="pass" maxlength="30" placeholder="******" type="password" pattern=".{6,}" size=40></p>
		<p class="mb-2"><span class="table_block">Пароль (повторите):</span> <input required name="pass2" maxlength="30" placeholder="******" type="password" pattern=".{6,}" size=40></p>
		<p class="mb-2"><span class="table_block">Фамилия:</span> <input required name="f" maxlength="20" placeholder="Иванов" pattern="[А-Яа-яЁё]{2,}" size=40></p>
		<p class="mb-2"><span class="table_block">Имя:</span> <input required name="i" maxlength="20" placeholder="Иван" pattern="[А-Яа-яЁё]{2,}" size=40></p>
		<p class="mb-2"><span class="table_block">Отчество:</span> <input required name="o" maxlength="20" placeholder="Иванович" pattern="[А-Яа-яЁё]{2,}" size=40></p>
		<p class="mb-2"><span class="table_block">Должность:</span> <input required name="doljnost" maxlength="60" placeholder="преподаватель" pattern=".{1,}" size=40></p>
		<p class="mb-2"><span class="table_block">Ученая степень, ученое звание:</span> <input name="uchen_st" maxlength="60" placeholder="доктор технических наук, профессор" size=40></p>
		<p class="mb-2"><span class="table_block">Специальное звание:</span> <input name="zvanie" maxlength="60" placeholder="генерал-лейтенант полиции" size=40></p>
		<p class="mb-0" style="font-size:13px">* мин. длина пароля 6 символов</p>
	</form>
</div>
<a id="addUser_btn_save" class="btn btn_save float-right" href="#save" onclick="addUser()">Сохранить</a>
<div class="modal-footer">
	<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
</div>
</div>
</div>
</div>
<tbody>
<?
if($res=mysqli_query($q, 'SELECT n,login,f,i,o,active,doljnost,uchen_st,zvanie FROM `main`.`users` WHERE kod='.$u['kod'])) {
while($r=mysqli_fetch_assoc($res)) {?>
	<tr>
	<td><?=$r['login']?></td>
	<td><?=$r['f']?> <?=$r['i']?> <?=$r['o']?></td>
	<td><img width="56" height="56" class="rounded-circle mr-3 inspector" alt src="/profile_photos/<?=$r['n']?>.jpg?<?=time()?>"<?
//if(!file_exists('./profile_photos/'.$r['n'].'.jpg'))
	echo ' title="Изменить фото" style="cursor:pointer;" id=photo_'.$r['n'].' onclick="$(\'uploadPhoto_'.$r['n'].'\').click()"><form method=post enctype="multipart/form-data" id="uploadPhotoForm_'.$r['n'].'" action="/up.php"><input type=hidden name=n value='.$r['n'].'><input name=f style="display:none;" type=file id=uploadPhoto_'.$r['n'].' onchange=uploadPhoto('.$r['n'].')></form>';
//else
//	echo '>';
?></td>
	<td><?=$r['doljnost']?></td>
	<td id="set_<?=$r['login']?>">
<?if($r['active'] == 1) {?>
		<a href="#edit" data-toggle="modal" data-target="#defaultModalPrimary<?=$r['login']?>">Изменить</a>
<?} else {?>
		Деактивирован
<?}?>
<?if($r['login'] != $u['login']) {?>
<?	if($r['active'] == 1) {?>
		| <a href="#makeChief" onclick="if(confirm('Вы уверены, что <?=$r['f']?> <?=$r['i']?> <?=$r['o']?> занимает должность начальника кафедры?'))makeChief('<?=$r['login']?>')">Назначить главным</a>
		| <a href="#deactivate" onclick="if(confirm('Операция необратима, вы не смоежете редактировать данного пользователя. Вы уверены?'))deactivate('<?=$r['login']?>')">Деактивировать</a>
<?	} else {/*?>
		| <a href="#deactivate" onclick="if(confirm('Вы уверены? Операция необратима'))activate('<?=$r['login']?>')">Активировать</a>
<?*/	}?>
<?}?>
	</td>
	</tr>
<div class="modal fade" id="defaultModalPrimary<?=$r['login']?>" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog" role="document">
<div class="modal-content">
<div class="modal-header">
	<h5 class="modal-title">Редактировани данных пользователя <?=$r['login']?></h5>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
</div>
<div class="modal-body m-3">
	<form id="user_<?=$r['n']?>">
		<input style="display:none" required disabled maxlength="6" name="n" value="<?=$r['n']?>">
		<p class="mb-2"><span class="table_block">Логин:</span> <input disabled required maxlength="30" name="login" value="<?=$r['login']?>" size=40></p>
		<p class="mb-2"><span class="table_block">Пароль*:</span> <input name="pass" maxlength="20" placeholder="******" type="password" size=40></p>
		<p class="mb-2"><span class="table_block">Фамилия:</span> <input required name="f" maxlength="20" value="<?=$r['f']?>" pattern="[А-Яа-яЁё]{2,}" size=40></p>
		<p class="mb-2"><span class="table_block">Имя:</span> <input required name="i" maxlength="20" value="<?=$r['i']?>" pattern="[А-Яа-яЁё]{2,}" size=40></p>
		<p class="mb-2"><span class="table_block">Отчество:</span> <input required name="o" maxlength="20" value="<?=$r['o']?>" pattern="[А-Яа-яЁё]{2,}" size=40></p>
		<p class="mb-2"><span class="table_block">Должность:</span> <input required name="doljnost" maxlength="60" value="<?=$r['doljnost']?>" placeholder="преподаватель" pattern=".{1,}" size=40></p>
		<p class="mb-2"><span class="table_block">Ученая степень, ученое звание:</span> <input name="uchen_st" maxlength="60" value="<?=$r['uchen_st']?>" placeholder="доктор технических наук, профессор" size=40></p>
		<p class="mb-2"><span class="table_block">Специальное звание:</span> <input name="zvanie" maxlength="60" value="<?=$r['zvanie']?>" placeholder="генерал-лейтенант полиции" size=40></p>
		<p class="mb-0" style="font-size:13px">* оставьте поле пустым, если пароль менять не нужно, пароль 6 символов и более</p>
	</form>
</div>
<a id="btn_save_<?=$r['n']?>" class="btn btn_save float-right" href="#save" onclick="saveEdit('<?=$r['n']?>')">Сохранить</a>
<div class="modal-footer">
	<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
</div>
</div>
</div>
</div>
<?
}
}
?>
	<!-- tr>
	<td>login</td>
	<td>Дудаев Асланбек Андарбекович</td>
	<td>фотка</td>
	<td>главный по Абхазии</td>
	<td>
		<a href="#">Назначить смотрящим всея Руси</a> |
		<a href="#">Понизить до рядового генерала</a>
	</td>
	</tr -->
</tbody>
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

addUser = function () {
	if($('addUser_form').checkValidity() && $('addUser_form').elements['pass'].value==$('addUser_form').elements['pass2'].value)
		$('addUser_form').submit();
	else
		alert('Вы где-то ошиблись');
}

saveEdit = function (q) {
	var x = xhr();
	x.open('POST', '/kaf.php?save=1', true);
	x.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	post_query = '';
	for(i=0; i<$('user_'+q).elements.length; i++) {
		post_query += $('user_'+q).elements[i].name + '=' + encodeURIComponent($('user_'+q).elements[i].value) + '&';
	}
	x.send(post_query); 
	x.onreadystatechange = function() {
		if (this.readyState != 4) return;
		$('btn_save_'+q).innerText = x.responseText;
		$('btn_save_'+q).setAttribute('onclick', '');
		// if (x.status == 200) { }
		setTimeout(() => (
											($('btn_save_'+q).innerText = 'Сохранить') & 
											($('btn_save_'+q).setAttribute('onclick', 'saveEdit(\''+q+'\')'))
											), 3000);
	}
}

deactivate = function (q) {
	var x = xhr();
	x.open('POST', '/kaf.php?deact=1', true);
	x.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	x.send('login=' + encodeURIComponent(q)); 
	x.onreadystatechange = function() {
		if (this.readyState != 4) return;
		if (x.responseText == '1')
			$('set_' + q).innerHTML = 'Деактивирован';
		else
			alert(x.responseText);
	}
}

makeChief = function (q) {
	var x = xhr();
	x.open('POST', '/kaf.php?makeChief=1', true);
	x.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	x.send('login=' + encodeURIComponent(q)); 
	x.onreadystatechange = function() {
		if (this.readyState != 4) return;
		if (x.responseText == '1')
			document.location.href = '/';
		else
			alert(x.responseText);
	}
}

uploadPhoto = function (q) {
	f = $('uploadPhoto_'+q).files[0];
	if(f.type.substr(0,5)!='image')
		return alert('Выберите изображение');
	fr = new FileReader();
	fr.readAsDataURL(f);
	fr.onload = function() {
		$('photo_'+q).src = fr.result;
	}
	$('uploadPhotoForm_'+q).submit();
}
</script>
</body>
</html>