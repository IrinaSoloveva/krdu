<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<title>ЭИПП | Ошибка</title>

<link rel="stylesheet" href="assets/fonts/css/all.css">
<link href="assets/css/app.css" rel="stylesheet">
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
	        		<li><a href="/" style="background-color:#666;"><i class="fa fa-edit" style="margin-right:9px;"></i>Индивидуальный план</a></li>
<?if($u['prava']==255) {?>
	        		<li><a href="kaf_stat.php"><i class="fa fa-clipboard" style="margin-right:9px;"></i>Статистика</a></li>
<?}?>
	        		<!-- li><a href="reports.php"><i class="fa fa-copy" style="margin-right:9px;"></i>Отчёты</a></li -->
	        		<li><a href="chat.php"><i class="fa fa-cog" style="margin-right:9px;"></i>Чат</a></li>
<?if($u['prava']==255) {?>
	        		<li><a href="kaf.php"><i class="fa fa-cog" style="margin-right:9px;"></i>Моя кафедра</a></li>
<?}?>
        		</ul>
        	</div>
				<div class="navbar-collapse collapse">
					<ul class="navbar-nav ml-auto">
						<li class="nav-item dropdown">
                <img src="profile_photos/<?=$u['n']?>.jpg" class="avatar img-fluid rounded-circle mr-1"/> <span style="color: #fff;"><?=fio_cut()?></span>
						</li>
					</ul>
				</div>
	</nav>

	<main class="content">
		<div class="container-fluid p-0">
			<div class="row">
				<div class="col-12">
					<div class="card mb-3">
						<div class="card-header"><h5 class="card-title mb-0 text-center">Ошибка</h5></div>
						<div class="card-body text-center"><?=$e?></div>
					</div>
				</div>
			</div>
		</div>
	</main>
</div>
</div>
</body>
</html>