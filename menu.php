
<?if($u['kod']<6) {
    if(strpos($_SERVER['PHP_SELF'],'report')) {?>
							<li><a href="<?=$_SERVER['REQUEST_URI']?>" style="background-color:#666;"><i class="fa fa-edit" style="margin-right:9px;"></i>Индивидуальный план</a></li>
<?}} else {?>
    <li><a href="/"<?=(strpos($_SERVER['PHP_SELF'],'reports')>0)?' style="background-color:#666;"':''?>><i class="fa fa-edit" style="margin-right:9px;"></i>Индивидуальный план</a></li>
<?}?>
<?if($u['prava']>0) {?>
							<li><a href="kaf_stat.php"<?=($_SERVER['PHP_SELF']=='/kaf_stat.php')?' style="background-color:#666;"':''?>><i class="fa fa-clipboard" style="margin-right:9px;"></i>Статистика</a></li>
<?}?>
							<li><a href="chat.php"<?=($_SERVER['PHP_SELF']=='/chat.php')?' style="background-color:#666;"':''?>><i class="fa fa-cog" style="margin-right:9px;"></i>Чат</a></li>
<?if($u['prava']==255 || $u['prava']==256) {?>
							<li><a href="kaf.php"<?=($_SERVER['PHP_SELF']=='/kaf.php')?' style="background-color:#666;"':''?>><i class="fa fa-cog" style="margin-right:9px;"></i>Мое подразделение</a></li>
<?}?>
<?
$x=false;
switch($u['kod']) {
    case 1: $x=mysqli_query($q,'SELECT 1 from `bd_1`.`sogl` WHERE soglasovano=2 OR soglasovano2=2')->num_rows; break;
	case 2: $x=mysqli_query($q,'SELECT 1 from `bd_2`.`sogl` WHERE soglasovano=2 OR soglasovano2=2')->num_rows; break;
	case 3: $x=mysqli_query($q,'SELECT 1 from `bd_3`.`sogl` WHERE soglasovano=2 OR soglasovano2=2')->num_rows; break;
	case 4: $x=mysqli_query($q,'SELECT 1 from `bd_4`.`sogl` WHERE soglasovano=2 OR soglasovano2=2')->num_rows; break;
}
if($x!==false) {
?>
							<li><a href="sogl.php"<?=($_SERVER['PHP_SELF']=='/sogl.php')?' style="background-color:#666;"':''?>><i class="fa fa-copy" style="margin-right:9px;"></i>Ждут проверки (<?=$x?>)</a></li>
<?}?>
<?
$x=0;
if($u['prava']==300) {
    $xx = mysqli_query($q, 'SELECT * FROM `main`.`users` WHERE prava=255');
    while($xxx=mysqli_fetch_assoc($xx))
        if(mysqli_query($q, $ee=('SELECT * FROM `bd_2`.`sogl` WHERE soglasovano=3 AND t_name LIKE \''.$xxx['login'].'_'.$y.'_%\''
                            .' UNION SELECT * FROM `bd_3`.`sogl` WHERE soglasovano=3 AND t_name LIKE \''.$xxx['login'].'_'.$y.'_%\''
                            .' UNION SELECT * FROM `bd_1`.`sogl` WHERE soglasovano=3 AND t_name LIKE \''.$xxx['login'].'_'.$y.'_1\''
                            .' UNION SELECT * FROM `bd_4`.`sogl` WHERE soglasovano=3 AND t_name LIKE \''.$xxx['login'].'_'.$y.'_%\''))->num_rows==7)
            $x++;
?>
							<li><a href="utv.php"<?=($_SERVER['PHP_SELF']=='/sogl.php')?' style="background-color:#666;"':''?>><i class="fa fa-copy" style="margin-right:9px;"></i>На утверждении (<?=$x?>)</a></li>
<?	unset($bd_2,$bd_3,$bd_4,$x,$xx,$xxx);
}?>
