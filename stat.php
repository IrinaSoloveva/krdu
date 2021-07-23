<?
session_start();
if(!isset($_SESSION['user'])) {
  header('HTTP/1.1 401 Unauthorized');
  header('Location: /');
  exit;
}
$u=&$_SESSION['user'];
require_once('f.php');
?>
<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<title>ЭИПП | Статистика</title>

	<link rel="stylesheet" href="assets/fonts/css/all.css">
	<link href="assets/css/app.css" rel="stylesheet">
<style>
img.inspector { 
	max-height: 56px;
	max-width: 56px;
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
	        		<li><a href="stat.php" style="background-color:#666"><i class="fa fa-clipboard" style="margin-right: 9px;"></i>Статистика</a></li>
	        		<li><a href="chat.php"><i class="fa fa-cog" style="margin-right: 9px;"></i>Чат</a></li>
        		</ul>
        	</div>
				<div class="navbar-collapse collapse">
					<ul class="navbar-nav ml-auto">
						<li class="nav-item dropdown">
                <img src="profile_photos/<?=$u['n']?>.jpg?<?=time()?>" class="avatar img-fluid rounded-circle mr-1"/> <span style="color: #fff;"><?=fio_cut()?></span>
						</li>
					</ul>
				</div>
			</nav>




<main class="content">
				<div class="container-fluid p-0">

					<div class="row">





<div class="col-lg-6">
							<div class="card flex-fill">
								<div class="card-header">
									<h5 class="card-title mb-0">Статистика по кафедрам</h5>
								</div>
								<table class="table table-striped my-0">
									<thead>
										<tr>
											<th>Кафедра</th>
											<th>Сдавшие индивидуальный план</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>Кафедра информационной безопасности</td>
											<td class="d-none d-xl-table-cell">
												<div class="progress">
													<div class="progress-bar bg-info" role="progressbar" style="width: 43%;" aria-valuenow="43" aria-valuemin="0" aria-valuemax="100">43%</div>
												</div>
											</td>
										</tr>
										<tr>
											<td>Кафедра 1</td>
											<td class="d-none d-xl-table-cell">
												<div class="progress">
													<div class="progress-bar bg-info" role="progressbar" style="width: 27%;" aria-valuenow="27" aria-valuemin="0" aria-valuemax="100">27%</div>
												</div>
											</td>
										</tr>
										<tr>
											<td>Кафедра 2</td>
											<td class="d-none d-xl-table-cell">
												<div class="progress">
													<div class="progress-bar bg-info" role="progressbar" style="width: 22%;" aria-valuenow="22" aria-valuemin="0" aria-valuemax="100">22%</div>
												</div>
											</td>
										</tr>
										<tr>
											<td>Кафедра 3</td>
											<td class="d-none d-xl-table-cell">
												<div class="progress">
													<div class="progress-bar bg-info" role="progressbar" style="width: 16%;" aria-valuenow="16" aria-valuemin="0" aria-valuemax="100">16%</div>
												</div>
											</td>
										</tr>
										<tr>
											<td>Кафедра 4</td>
											<td class="d-none d-xl-table-cell">
												<div class="progress">
													<div class="progress-bar bg-info" role="progressbar" style="width: 15%;" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100">15%</div>
												</div>
											</td>
										</tr>
										<tr>
											<td>Кафедра 5</td>
											<td class="d-none d-xl-table-cell">
												<div class="progress">
													<div class="progress-bar bg-info" role="progressbar" style="width: 13%;" aria-valuenow="13" aria-valuemin="0" aria-valuemax="100">13%</div>
												</div>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>









						<div class="col-lg-6">
							<div class="w-100">
								<div class="row">
									<div class="col-sm-6">
										<div class="card flex-fill">
											<div class="card-header">
												<h5 class="card-title mb-0">На исправлении</h5>
											</div>
											<div class="card-body my-2">
												<div class="row d-flex align-items-center mb-4">
													<div class="col-8">
														<h2 class="d-flex align-items-center mb-0 font-weight-light">
															378
														</h2>
													</div>
													<div class="col-4 text-right">
														<span class="text-muted">32%</span>
													</div>
												</div>

												<div class="progress progress-sm shadow-sm mb-1">
													<div class="progress-bar bg-success" role="progressbar" style="width: 32%"></div>
												</div>
											</div>
										</div>
										
										
									</div>
									<div class="col-sm-6">
										<div class="card flex-fill">
											<div class="card-header">
												<h5 class="card-title mb-0">Не заполнили</h5>
											</div>
											<div class="card-body my-2">
												<div class="row d-flex align-items-center mb-4">
													<div class="col-8">
														<h2 class="d-flex align-items-center mb-0 font-weight-light">
															114
														</h2>
													</div>
													<div class="col-4 text-right">
														<span class="text-muted">18%</span>
													</div>
												</div>

												<div class="progress progress-sm shadow-sm mb-1">
													<div class="progress-bar bg-danger" role="progressbar" style="width: 82%"></div>
												</div>
											</div>
										</div>
										
									</div>




									<div class="col-sm-12">
										
										<div class="card">
								<div class="card-body h-100">

									<div class="media">
										<img src="assets/img/Frolova_S.A._seryy123.jpg" width="56" height="56" class="rounded-circle mr-3 inspector" alt="Ashley Briggs">
										<div class="media-body">
											<span class="badge badge-primary float-right">Объявление</span>
											<small class="float-right text-navy" style="margin-right: 12px;">12:07 24.02.2019</small>
											<p class="mb-2"><strong>Фролова С.А.</strong></p>

											<p>Внимание! <br />Здесь сотрудники учебного управления могут писать объявления. Например, о сроках сдачи индивидуальных планов.</p>
											</div>

										</div>
									</div>
							</div>
										
									</div>
								</div>
							</div>
						</div>
















					</div>
				</div>
			</main>



			
		</div>
	</div>

</body>



</html>