<?php
session_start();
if (!isset($_SESSION["login_name"])){
echo "ログインしてください";

	header("Location: logout.php");
exit;
}
	if(isset($_GET["target"]) && $_GET["target"] !== ""){
	$target = $_GET["target"];
	}else{
        header("Location: fileshare_main.php");
	}
try{
	$dsn = 'mysql:dbname=tb******db;host=localhost';
	$user = 'tb-*******';
	$password = '**********';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	//削除する　リンクの時点でfnameに変数が入っていることに注目
		$DB_name = "DB_".$_SESSION["login_name"];
		$sql = 'delete from '.$DB_name.' where fname=:target';
		$stmt = $pdo->prepare($sql);
		$stmt -> bindValue(":target", $target, PDO::PARAM_STR);
		$stmt->execute();
	//削除できたのでindexに戻る
	echo "削除しました"."<br/>";
		echo ("<a href=\"fileshare_main.php\">戻る</a>");

}catch (PDOException $e){
		echo("<p>500 Inertnal Server Error</p>");
		exit($e->getMessage());
}
?>
