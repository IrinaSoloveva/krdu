<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<title>КрУ МВД России | ЭИПП - Авторизация</title>

<link rel="stylesheet" href="assets/fonts/css/all.css">
<link href="assets/css/app.css" rel="stylesheet">
</head>

<body>
	<main class="main h-100 w-100">
		<div class="container h-100">
			<div class="row h-100">
				<div class="col-sm-10 col-md-8 col-lg-6 mx-auto d-table h-100">
					<div class="d-table-cell align-middle">

						<div class="text-center mt-4">
              <!-- img style="height:200px" src="logo.jpg" -->
							<p class="lead" style="font-size:20px">
								Электронный индивидуальный план преподавателя
							</p>
						</div>
						<div class="card">
							<div class="card-body">
                <div class="text-center" style="display:none;color:#d00;font-size:16px;" id="login_err">Ошибка входа</div>
								<div class="m-sm-4">
									<form onsubmit="return false;" autocomplete method="post" action="/">
										<div class="form-group">
											<label>Логин</label>
											<input class="form-control form-control-lg" type="login" id="login" name="login" placeholder="Введите логин" autofocus>
										</div>
										<div class="form-group">
											<label>Пароль</label>
											<input class="form-control form-control-lg" type="pass" id="pass" name="pass" placeholder="Введите пароль">
										</div>
										<div class="text-center mt-3">
											<button id="auth_btn" onclick="auth()" class="btn btn-lg btn-primary" type="submit">Войти</button>
											<button type="button" class="btn btn-lg btn-primary" data-toggle="modal" data-target="#defaultModalPrimary">Забыл пароль</button>
									<div class="modal fade" id="defaultModalPrimary" tabindex="-1" role="dialog" aria-hidden="true">
										<div class="modal-dialog" role="document">
											<div class="modal-content">
												<div class="modal-header">
													<h5 class="modal-title">Забыли пароль?</h5>
													<button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
												</div>
												<div class="modal-body m-3">
													<p class="mb-0">В случае возникновения проблем со входом обратитесь к руководителю структурного подразделения</p>
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
												</div>
											</div>
										</div>
									</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</main>

<script src="md5.min.js"></script>
<script src="assets/js/app.js"></script>
<script>
$=function(q){return document.getElementById(q);}
function xhr(){
  var x;
  try {
    x = new ActiveXObject('Msxml2.XMLHTTP');
  } catch (e) {
    try {
      x = new ActiveXObject('Microsoft.XMLHTTP');
    } catch (E) {
      x = false;
    }
  }
  if (!x) {
    var x2 = ('onload' in new XMLHttpRequest()) ? XMLHttpRequest : XDomainRequest;
    var x = new x2();
  }
  return x;
}

function auth() {
  $('login_err').setAttribute('style', 'display:none;');
  $('auth_btn').setAttribute('style', 'background:#999;border-color:#999;');
  $('auth_btn').removeAttribute('onclick');
  $('login').setAttribute('readonly', 'true');
  $('pass').setAttribute('readonly', 'true');
  var x = xhr();
  x.open('POST', '/auth.php', true);
  x.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  x.send('login=' + $('login').value + '&pass=' + $('pass').value); 
  x.onreadystatechange = function() {
    if (this.readyState != 4) return;
    if (x.status == 200) {
      // document.body.innerHTML=x.responseText;
      document.forms[0].submit();
    } else {
      $('login_err').setAttribute('style', 'display:block;color:#d00;font-size:16px;');
      $('login_err').innerHTML=x.responseText;
      $('auth_btn').removeAttribute('style');
      $('auth_btn').setAttribute('onclick', 'auth()');
      $('login').removeAttribute('readonly');
      $('pass').removeAttribute('readonly');
    }
  }
}
</script>
</body>
</html>