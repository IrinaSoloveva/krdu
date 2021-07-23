<?
session_start();
if(!isset($_SESSION['user'])) {
  header('HTTP/1.1 401 Unauthorized');
  header('Location: /');
  exit;
}
$u = &$_SESSION['user'];
require_once('f.php');

if(!($q=mysqli_connect('localhost', 'root', '', '')))
  die('Невозможно подключиться к базе данных. Код ошибки: '. mysqli_connect_error());

if(is_string($_POST['text']) && strlen($_POST['text'])>1)
	mysqli_query($q, "INSERT INTO `main`.`chat` (`n`,`date`,`text`) VALUES (".$u['n'].",'".date('Y-m-d H:i:s')."','".htmlspecialchars(trim((string)$_POST['text']))."')");

?>
<!DOCTYPE html>
<html lang=ru>
<head>
<meta charset=utf-8>
<meta http-equiv=X-UA-Compatible content="IE=edge">
<meta name=viewport content="width=device-width,initial-scale=1,shrink-to-fit=no">

<title>ЭИПП | Чат</title>

<link rel=stylesheet href="/assets/fonts/css/all.css">
<link rel=stylesheet href="/assets/css/app.css">
<style>
img.inspector {
  max-width: 56px;
  max-height: 56px;
}
</style>
</head>

<body>
<div class=wrapper>
<div class=main>
<nav class="navbar navbar-expand navbar-light bg-white">
	<a class=sidebar-brand href=/>
		<i class="align-middle fa fa-book" style="color:#51BAC0;margin-right:10px;"></i>
		<span class=align-middle>КРу МВД России</span>
	</a>
	<div class=den>
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
<main class=content>
<div class="container-fluid p-0">
<div class=row>

<div class=col-3>
	<div class="card mb-3">
		<div class=card-header><h5 class="card-title mb-0">Инструкция по использованию</h5></div>
		<div class="card-body text-center">
			<a class="btn btn-primary btn-sm" href="/instr.pdf" target=_blank>Скачать инструкцию</a>
		</div>
	</div>
</div>

<div class=col-9>
	<div class=card>
		<div class=card-header><h5 class="card-title mb-0">Чат</h5></div>
		<div class="card-body h-100">
			<div class=media>
				<img src="/profile_photos/0.jpg" width=56 height=56 class="rounded-circle mr-2 inspector" alt=Булат title=Булат>
				<div class=media-body>
					<strong>Разработчик</strong>
					<small class=text-muted>00:00:00 01.01.2020</small>
					<div class="border text-muted p-2 mt-1">Здесь</div>
				</div>
			</div>
<?
$chat = mysqli_query($q, 'SELECT * FROM `main`.`chat` ORDER BY date DESC');

while($c=mysqli_fetch_assoc($chat)) {
$cc = mysqli_fetch_assoc(mysqli_query($q, 'SELECT f,i,o FROM `main`.`users` WHERE n='.$c['n']));
?>
			<hr>
			<div class=media>
				<img src="/profile_photos/<?=$c['n']?>.jpg" width=56 height=56 class="rounded-circle mr-2 inspector" alt>
				<div class=media-body>
					<strong><?=$cc['f']?> <?=$cc['i']?> <?=$cc['o']?></strong>
					<small class=text-muted><?=date('H:i:s d.m.Y',strtotime($c['date']))?></small>
					<div class="border text-muted p-2 mt-1"><?=$c['text']?></div>
					</div>
			</div>
<?
}
?>
		</div>
	</div>

	<div class=card>
		<!-- div class=card-header>
			<h5 class=card-title>Заголовок</h5>
			<h6 class="card-subtitle text-muted">Замученный текст</h6>
		</div -->
		<div class=card-body>
		<form method=post>
			<div class="form-group row">
				<label class="col-form-label col-sm-1 text-sm-right"></label>
				<div class="col-sm-10"><textarea required maxlength=3000 name=text class=form-control placeholder=Текст rows=3></textarea></div>
			</div>
			<div class="form-group row">
				<div class="col-sm-11 ml-sm-auto"><button type=submit class="btn btn-primary float-right">Отправить</button></div>
			</div>
		</form>
		</div>
	</div>
</div>

</div>
</div>
</main>			
</div>
</div>
</body>
<script src="/assets/js/app.js"></script>
</html>