<?php
class gui
{
	public function Header()
	{
		echo '<html>
		<head>
		<link rel="stylesheet" href="style.css">
		<title>OpenVoucher</title>
		</head>
		<body>
		<table width="100%" border="0" cellspacing="0">
		<tr class="tableheader">
		<td colspan="3">&nbsp;</td>
		</tr><tr>
		<td width="75px"><img src="logo-small.png"></td>
		<td align="center">
		<div class="middle">OpenVoucher - Installer</div>
		</td>
		<td width="75px"><img src="logo-small.png"></td>
		</tr>
		<tr class="tableheader">
		<td colspan="3">&nbsp;</td>
		</tr>
		</table>
		<br><ul><a href="'.$_SERVER['PHP_SELF'].'?step=reset">Reset all entered data and start from beginning</a></ul><br>';
	}
	
	public function Footer()
	{
		echo '</body></html>';
	}
}
?>