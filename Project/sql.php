<?php
	session_start();

	$database = mysqli_connect("localhost","root","usbw","project");
	$errors = [];
	/*
	$newpw = md5("69");
	$query = "INSERT INTO docent (naam, ww) VALUES ('van Dijk', '$newpw')";
	mysqli_query($database, $query);
	*/
	//Dit hoort bij code.php
	if(!isset($_POST['code'])) {$code = 0;};
	if(isset($_POST['code'])) {$code = $_POST['code'];};

	if($code != 0) 
	{
		$query = "SELECT code FROM code WHERE 	code = '$code'";
		$result = mysqli_query($database, $query);
		$result = mysqli_fetch_assoc($result);

		if($result) 
		{
			$query2 = "SELECT klas FROM code WHERE 	code = '$code'";
			$_SESSION['klas'] = mysqli_query($database, $query2)->fetch_object()->klas;
			$query3 = "DELETE FROM `code` WHERE code = '$code'";
			mysqli_query($database, $query3);
			$_SESSION['klasConfirm'] = true;
			header('location: login.php');
		}

		else 
		{
			array_push($errors, "Oeps! deze code bestaat niet...");
		}
	}
	
	//Dit hoort bij vragen.php
	function getQuestion($id)
	{
		$database = mysqli_connect("localhost","root","usbw","project");
		$query = "SELECT Vraag FROM vragen WHERE ID = '$id'";
		$result = mysqli_query($database, $query)->fetch_object()->Vraag;
		
		echo $result;
	}
	
	function storeAnswer($llnr, $question, $answer)
	{
		$database = mysqli_connect("localhost","root","usbw","project");
		$query = "INSERT INTO `antwoorden`(`llnr`, `ID`, `antwoord`) VALUES ('$llnr','$question','$answer')";
		mysqli_query($database, $query);
	}
	
	
	//Dit hoort bij login.php
	function getKlasNames($id) 
	{
		$database = mysqli_connect("localhost","root","usbw","project");
		$query = mysqli_query($database,"SELECT `vn`, `tv`, `an`, `llnr` FROM `leerlingen` WHERE klas = '$id'");
		
		$_SESSION['namen'] = array();
		
		while($row  = mysqli_fetch_assoc($query))
		{
			$_SESSION['namen'][]  = $row;
		}
	}
	
	//Dit hoort bij docentlogin.php
	if(isset($_POST['docentLogin'])) 
	{
		if(!isset($_POST['naam'])) {$_POST['naam'] = "";};
		if(!isset($_POST['ww'])) {$_POST['ww'] = "";};
		
		$naam = mysqli_real_escape_string($database,$_POST['naam']);
		$ww = mysqli_real_escape_string($database,$_POST['ww']);

		if(empty($naam)) 
		{
			array_push($errors, "Vul je naam in");
		}
		if(empty($ww)) 
		{
			array_push($errors, "Vul je wachtwoord in");
		}
		if(count($errors) == 0 ) 
		{
			$ww = md5($ww);
			$query = "SELECT * FROM docent WHERE naam = '". $naam . "' AND ww = '" . $ww . "'";
			$results = mysqli_query($database, $query);

			if(mysqli_num_rows($results)) 
			{
				$_SESSION['naam'] = $naam;
				$_SESSION['succes'] = "ingelogd";
				header('location: docenthome.php');
			}
			else
			{
				array_push($errors, "Foute naam of wachtwoord");
			}
		}
	}
	
	//Dit hoort bij docentcode.php
	function makeCode($klas)
	{
		$database = mysqli_connect("localhost","root","usbw","project");
		$query = "SELECT `klas` FROM leerlingen WHERE klas = '$klas'";
		$classLength = mysqli_query($database, $query)->num_rows;
		$codes = [];
		
		for($i = 0; $i < $classLength; $i++)
		{
			$codesNotSame = true;
			
			while($codesNotSame)
			{
				$codesNotSame = false;
				$code = "";
				
				for ($j = 0; $j <= 5; $j++)
				{
					$code .= rand(0, 9);
				}
				
				for ($k = 0; $k < count($codes); $k++)
				{
					if ($code == $codes[$k])
					{
						$codesNotSame = true;
					}
				}
			}
			
			array_push($codes, $code);
		}
		$_SESSION['codes'] =  $codes;
		for ($i = 0; $i < $classLength; $i++)
		{
			$query1 = "INSERT INTO `code` (`code`, `klas`) VALUES ('$codes[$i]', '$klas');";
			mysqli_query($database, $query1);
		}
	}
	
	function destroyCodes($klas)
	{
		$database = mysqli_connect("localhost","root","usbw","project");
		$query = "DELETE `code` FROM code WHERE klas = '$klas'";
		mysqli_query($database, $query);
		
	}
	function getKlassen()
	{
		$database = mysqli_connect("localhost","root","usbw","project");
		$query = mysqli_query($database,"SELECT DISTINCT klas FROM leerlingen ORDER BY klas");
		
		$_SESSION['docentKlassen'] = array();
		
		while($row  = mysqli_fetch_assoc($query))
		{
			$_SESSION['docentKlassen'][]  = $row;
		}
		
	}
	//Dit hoort bij docentleerlingen.php
	function getResults($klas)
	{
		$database = mysqli_connect("localhost","root","usbw","project");
		$query = "SELECT antwoorden.*, vragen.Soort, vragen.Categorie FROM `antwoorden`, `vragen` WHERE antwoorden.ID = vragen.ID";
		
		
	}
	
?>