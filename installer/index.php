<?php
if($_GET['installer']=='')
{
	require('gui.php');
	$g = new gui();
	$g->Header();
	echo 'Welcome to OpenVoucher installer.<br><br>
	Before you proceed, make sure that you have at least two interfaces configured on your machine. The internal interface, where the guests are connected to, <b>must</b> have a static IPv4 address. 
	<br><br>
	I is <b>strongly recommended</b> that you don\'t connect the internal and external interfaces to the same switch, unless they are in different VLANs.<br><br>
	To proceed, select the operating system of the server:<br><br>
	<form action="'.$_SERVER['PHP_SELF'].'" method="get">
	<select name="installer" size="1" class="formstyle">
	<option value="debian">Debian</option>
	</select>
	<br><br>
	<input type="submit" value="Next" class="formstyle">
	</form>';
	$g->Footer();
}
if($_GET['installer']=='debian')
{
	header('Location: debian.php');
}
?>