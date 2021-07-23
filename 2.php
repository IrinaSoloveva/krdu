<?
$q=mysqli_connect('localhost','root','','');
/*
if($res=mysqli_query($q, "SELECT soglasovano from `bd_metod`.`sogl` WHERE t_name='b_2019_1'")) {
	var_dump($res);
	echo mysqli_query($q, "SELECT soglasovano from `bd_metod`.`sogl` WHERE t_name='b_2019_1'")->num_rows;
	if($r=mysqli_fetch_assoc($res)) {
		var_dump($r);
	}
}
*/
$t_name='b_2019_2';
	if(mysqli_query($q, "SELECT 1 from `bd_metod`.`sogl` WHERE t_name='$t_name'")->num_rows == 0) {
		$w="INSERT INTO `bd_metod`.`sogl` (`t_name`,`date`,`date2`,`date3`,`plan`,`fakt`,`soglasovano`) VALUES ('$t_name','".date('H:i:s d.m.Y')."','','0','0','0','0')";
		var_dump($w);
	} else {
		echo '2';
	}
/*
if($res=mysqli_query($q, "UPDATE `bd_metod`.`sogl` SET plan='2' WHERE t_name='b_2019_1'")) {
	var_dump($res);
	if($r=mysqli_fetch_assoc($res)) {
		var_dump($r);
	}
}
*/
mysqli_close($q);