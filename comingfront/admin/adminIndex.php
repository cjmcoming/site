<?php
	require_once('incSet.php');
	require_once('incFunction.php');

	try {
	    $conn = new PDO('mysql:host='.$data['server'].'; port='.$data['port'].'; dbname='.$data['dbname'], $data['user'], $data['password']);
	    echo "Connected successfully";
	}catch(PDOException $e){
	    echo $e->getMessage();
	}
?>





