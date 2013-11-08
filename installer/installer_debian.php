<?php
include('gui.php');
class InstallerDebian
{
	private $step;
	private $g;
	
	function __construct()
	{
		define('INSTALLERVERSION','0.100');
		session_start();
		$this->g = new gui(INSTALLERVERSION);
		if(trim($_GET['step'])=='' || (!is_numeric($_GET['step']) && $_GET['step']!='reset'))
		{
			$this->step=1;
		} else {
			$this->step=$_GET['step'];
		}
		
		if($this->step==1) $this->FormSysdata();
		if($this->step==2) $this->ProcessSysdata();
		if($this->step==3) $this->CheckRequirements();
		if($this->step==4) $this->StartDownload();
		if($this->step==5) $this->CheckDownload();
		if($this->step==6) $this->Decompress();
		
		if($this->step=='reset') $this->ResetAll();
	}
	
	private function ResetAll()
	{
		$this->g->Header();
		$_SESSION=array();
		echo 'All data has been reset.<br><br><a href="'.$_SERVER['PHP_SELF'].'">Go back</a>';
		$this->g->Footer();
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
	
	private function GetExitCode($command)
	{
		exec($command,$buffer,$exitcode);
		return $exitcode;
	}
	
	private function CommandExists($command)
	{
		if($this->GetExitCode($command)==127)
		{
			return false;
		} else {
			return true;
		}
	}
	
	private function PathWritable($path)
	{
		if($this->GetExitCode('touch '.$path.'test.tmp')==0)
		{
			if($this->GetExitCode('rm '.$path.'test.tmp')==0)
			{
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
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
		$this->g->Header();
		echo '<form action="'.$_SERVER['PHP_SELF'].'?step=2" method="post">
		On which user is apache running? <input type="text" name="apacheuser" class="formstyle" size="20" value="www-data"><br>
		Path to iptables: <input type="text" name="iptables" class="formstyle" size="20" value="/sbin/iptables"><br>
		Path to arp: <input type="text" name="arp" class="formstyle" size="20" value="/usr/sbin/arp"><br>
		Path to temp dir: <input type="text" name="tempdir" class="formstyle" size="20" value="/var/tmp/"><br>
		Path to www-root: <input type="text" name="wwwroot" class="formstyle" size="20" value="/var/www/"><br><br>
		Which interface are the guests connected to? <input type="text" name="if-internal" class="formstyle" size="20" value="eth0"><br>
		Which interface is connected to the internet? <input type="text" name="if-external" class="formstyle" size="20" value="eth1"><br>
		What\'s your internal IP (guest interface)? <input type="text" name="ip-internal" class="formstyle" size="20" value="10.0.0.1"><br><br>
		<input type="submit" value="Next" class="formstyle">
		</form>';
		$this->g->Footer();
	}
	
	private function ProcessSysdata()
	{
		$this->g->Header();
		if(!$this->CheckPost(array('apacheuser','iptables','arp','tempdir','wwwroot','if-internal','if-external','ip-internal')))
		{
			echo 'Not all required fields were filled out.';
			$this->g->Footer();
			die();
		}
		$this->WritePostToSession(array('apacheuser','iptables','arp','tempdir','wwwroot','if-internal','if-external','ip-internal'));
		
		echo 'Before we proceed, please make sure you have the following requirements installed:
		<br>
		<ul>
		<li>sudo</li>
		<li>iptables</li>
		<li>wget (for downloading the latest version)</li>
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
		$this->g->Footer();
	}
	
	private function CheckRequirements()
	{
		$this->g->Header();
		if(!$this->CommandExists('sudo'))
		{
			echo 'sudo wasn\'t found. Make sure that the user running your webserver can run it using the command &quot;sudo&quot.';
			$this->g->Footer();
			die();
		}
		if(!$this->CommandExists('wget'))
		{
			echo 'wget wasn\'t found. Make sure that the user running your webserver can run it.';
			$this->g->Footer();
			die();
		}
		if(!$this->CommandExists($_SESSION['iptables']))
		{
			echo 'iptables wasn\'t found. Make sure that the user running your webserver can run it using the command &quot;'.$_SESSION['iptables'].'&quot.';
			$this->g->Footer();
			die();
		}
		if(!$this->PathWritable($_SESSION['wwwroot']))
		{
			echo 'Can\'t write to '.$_SESSION['wwwroot'].'. Please check your permissions.';
			$this->g->Footer();
			die();
		}
		if($this->GetExitCode('sudo '.$_SESSION['iptables'].' --list')!=0)
		{
			echo 'Can\'t run iptables. Make sure that the user running your webserver can run it using the command &quot;'.$_SESSION['iptables'].'&quot.';
			$this->g->Footer();
			die();
		}
		echo 'Plase specify your MySQL settings now.<br><br>
		<form action="'.$_SERVER['PHP_SELF'].'?step=4" method="post">
		Hostname: <input type="text" name="mysql_host" size="20" class="formstyle" value="localhost"><br>
		Username: <input type="text" name="mysql_user" size="20" class="formstyle"><br>
		Password: <input type="password" name="mysql_pwd" size="20" class="formstyle"><br>
		Database: <input type="text" name="mysql_db" size="20" class="formstyle" value="openvoucher"><br><br>
		<input type="submit" value="Next" class="formstyle">
		</form>';
		$this->g->Footer();
	}
	
	private function StartDownload()
	{
		$this->g->Header();
		shell_exec('wget -O /var/tmp/ov_latest.tar.gz http://sourceforge.net/projects/openvoucher/files/latest/download?source=files > /dev/null 2>&1 &');
		echo 'The download has been started.<br><br>
		<form action="'.$_SERVER['PHP_SELF'].'" method="get">
		<input type="hidden" name="step" value="5">
		<input type="submit" class="formstyle" value="Next">
		</form>';
		$this->g->Footer();
	}
	
	private function CheckDownload()
	{
		$this->g->Header();
		if(preg_match("/wget/i",shell_exec('ps')))
		{
			echo 'It seems that the download hasn\'t finished yet. Please <a href="'.$_SERVER['PHP_SELF'].'?step=5">reload</a> to see if it has finished now.';
		} else {
			echo 'The download has finished. Click &quot;Next&quot; to decompress.<br><br>
			<form action="'.$_SERVER['PHP_SELF'].'" method="get">
			<input type="hidden" name="step" value="6">
			<input type="submit" class="formstyle" value="Next">
			</form>';
		}
		$this->g->Footer();
	}
	
	private function Decompress()
	{
		$this->g->Header();
		exec('tar -xf /var/tmp/ov_latest.tar.gz -C '.$_SESSION['wwwroot'],$buffer,$exitcode);
		if($exitcode==0)
		{
			echo 'The software has been decompressed.';
		} else {
			echo 'Couldn\'t decompress the software. Please do it manually, then click &quot;Next&quot;.';
		}
		echo '<br><br><form action="'.$_SERVER['PHP_SELF'].'" method="get">
		<input type="hidden" name="step" value="7">
		<input type="submit" class="formstyle" value="Next">
		</form>';
		$this->g->Footer();
	}
}
?>