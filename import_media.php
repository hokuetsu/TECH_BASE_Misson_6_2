<?php
session_start();
if (!isset($_SESSION["login_name"])){
echo "���O�C�����Ă�������";

	header("Location: logout.php");
exit;
}
    if(isset($_GET["target"]) && $_GET["target"] !== ""){
        $target = $_GET["target"];
//GET�ϐ��@URL �p�����[�^�Ō��݂̃X�N���v�g�ɓn���ꂽ�ϐ��̘A�z�z��ł��B
/*
��1 $_GET �̗�

<?php
echo 'Hello ' . htmlspecialchars($_GET["name"]) . '!';
?>
���[�U�[�� http://example.com/?name=Hannes �Ɠ��͂����Ƃ��܂��B

��̗�̏o�͂́A ���Ƃ��Έȉ��̂悤�ɂȂ�܂��B

Hello Hannes!
*/
    }
    else{
        header("Location: fileshare_main.php");
    }
    $MIMETypes = array(
        'png' => 'image/png',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'mp4' => 'video/mp4',
	'pdf' => 'application/pdf'
    );
    try {
	$dsn = 'mysql:dbname=tb******db;host=localhost';
	$user = 'tb-*******';
	$password = '**********';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	$DB_name = "DB_".$_SESSION["login_name"];
	$sql = "SELECT * FROM ".$DB_name." WHERE fname = :target;";
	$stmt = $pdo->prepare($sql);
	$stmt -> bindValue(":target", $target, PDO::PARAM_STR);
	$stmt -> execute();
	$row = $stmt -> fetch(PDO::FETCH_ASSOC);
	header("Content-Type: ".$MIMETypes[$row["extension"]]);
	echo ($row["raw_data"]);

    }
    catch (PDOException $e) {
        echo("<p>500 Inertnal Server Error</p>");
        exit($e->getMessage());
    }
?>