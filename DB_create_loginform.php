<?php
	$dsn = 'mysql:dbname=tb******db;host=localhost';
	$user = 'tb-*******';
	$password = '**********';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
$sql = "CREATE TABLE IF NOT EXISTS DB_login"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY," //idというカラムを作った　INTは整数
	. "login_name varchar(255) unique,"
	. "password varchar(255) "
	.");";
	$stmt = $pdo->query($sql);
$sql = 'SELECT * FROM DB_login';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		echo $row['id'].',';
		echo $row['login_name'].',';
		echo $row['password'].'</br>';

	}
?>