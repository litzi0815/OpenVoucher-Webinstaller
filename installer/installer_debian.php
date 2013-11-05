<?php
include('gui.php');
class InstallerDebian
{
	private $step;
	private $g;
	
	function __construct()
	{
		session_start();
		$g = new gui();
		if(trim($_GET['step'])=='' || (!is_numeric($_GET['step']) && $_GET['step']!='reset'))
		{
			$this->step=1;
		} else {
			$this->step=$_GET['step'];
		}
		
		if($this->step==1) $this->FormSysdata();
		if($this->step==2) $this->ProcessSysdata();
		
		if($this->step=='reset') $this->ResetAll();
	}
	
	private function ResetAll()
	{
		$_SESSION=array();
		echo 'All data has been reset.<br><br><a href="'.$_SERVER['PHP_SELF'].'">Go back</a>';
		$g->Footer();
		die();
	}
	
	private function CheckPost($elements)
	{
		$checkres=true;
		foreach($elements as $element)
		{
			if($checkres)
			{
				if(trim($_POST[$element])=='')
				{
					$checkres=false;
				}
			}
		}
		return $checkres;
	}
	
	private function WritePostToSession($elements)
	{
		foreach($elements as $element)
		{
			$_SESSION[$element]=$_POST[$element];
		}
	}
	
	private function FormSysdata()
	{
		$g->Header();
		echo '<form action="'.$_SERVER['PHP_SELF'].'?step=2" method="post">
		On which user is apache running? <input type="text" name="apacheuser" class="formstyle" size="20" value="www-data"><br>
		Path to iptables: <input type="text" name="iptables" class="formstyle" size="20" value="/sbin/iptables"><br>
		Path to arp: <input type="text" name="arp" class="formstyle" size="20" value="/usr/sbin/arp"><br>
		Path to temp dir: <input type="text" name="tempdir" class="formstyle" size="20" value="/var/tmp/"><br>
		Path to www-root: <input type="text" name="wwwroot" class="formstyle" size="20" value="/var/www/"><br><br>
		Which interface are the guests connected to? <input type="text" name="if-internal" class="formstyle" size="20" value="eth0"><br>
		Which interface is connected to the internet? <input type="text" name="if-external" class="formstyle" size="20" value="eth1"><br>
		What\'s your internal IP (guest interface)? <input type="text" name="ip-internal" class="formstyle" size="20" value="10.0.0.1"><br><br>
		<input type="submit" value="Next">
		</form>';
		$g->Footer();
	}
	
	private function ProcessSysdata()
	{
		$g->Header();
		if(!$this->CheckPost(array('apacheuser','iptables','arp','tempdir','wwwroot','if-internal','if-external','ip-internal')))
		{
			echo 'Not all required fields were filled out.';
			$g->Footer();
			die();
		}
		$this->WritePostToSession(array('apacheuser','iptables','arp','tempdir','wwwroot','if-internal','if-external','ip-internal'));
		
		echo 'Before we proceed, please make sure you have the following requirements installed:
		<br>
		<ul>
		<li>sudo</li>
		<li>iptables</li>
		<li>A running MySQL server (on this or another machine)</li>
		</ul>
		<br>
		Please also make sure that:
		<br>
		<ul>
		<li>The user running apache is allowed to write into the www-root (or is owner of that directory)</li>
		<li>The user running apache is allowed to run iptables without password using sudo</li>
		</ul>
		<br>
		<form action="'.$_SERVER['PHP_SELF'].'" method="get">
		<input type="hidden" name="step" value="3">
		<input type="submit" class="formstyle" value="Next">
		</form>';
		$g->Footer();
	}
}
?>