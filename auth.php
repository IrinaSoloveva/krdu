<?
if(!($q=mysqli_connect('localhost','root','','main'))) {
   header('HTTP/1.1 401 Unauthorized');
   die('Невозможно подключиться к базе данных. Код ошибки: '. mysqli_connect_error());
}

if ($res = mysqli_query($q, 'SELECT * from users where login="'.htmlspecialchars($_POST['login']).'" and pass="'.htmlspecialchars($_POST['pass']).'"')) {
    if($row = mysqli_fetch_assoc($res)){
				if($row['active'] == 0) {
					header('HTTP/1.1 401 Unauthorized');
					echo 'Пользователь деактивирован';
        } else {
					session_start();
					$_SESSION['user'] = $row;
					$_SESSION['user']['podr'] = mysqli_fetch_assoc(mysqli_query($q, 'SELECT nazvanie FROM `podrazdeleniya` WHERE kod='.$row['kod']))['nazvanie'];
					//include_once('index.php');
					echo 'ok';
        }
    } else {
        header('HTTP/1.1 401 Unauthorized');
        echo 'Неверный логин/пароль';
    }
}

/*
DROP TABLE if EXISTS `qwe`;
CREATE TABLE `bd_metod`.`b_2019_1` ( `n` INT(8) NOT NULL , `naim` VARCHAR(200) NOT NULL , `shifr` VARCHAR(500) NOT NULL , `kol1` INT(8) NOT NULL , `kol2` INT(8) NOT NULL , `kol3` INT(8) NOT NULL , `kol4` INT(8) NOT NULL , `plan` INT(8) NOT NULL , `fakt` INT(8) NOT NULL , `otmetka` VARCHAR(500) NOT NULL ) ENGINE = InnoDB;

INSERT INTO `b_2019_1` (`n`, `naim`, `shifr`, `kol1`, `kol2`, `kol3`, `kol4`, `plan`, `fakt`, `otmetka`) VALUES ('1', '', '', '', '', '', '', '', '', '')
*/