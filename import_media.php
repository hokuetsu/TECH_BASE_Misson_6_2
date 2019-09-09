<?php
session_start();
if (!isset($_SESSION["login_name"])){
echo "ログインしてください";

	header("Location: logout.php");
exit;
}
    if(isset($_GET["target"]) && $_GET["target"] !== ""){
        $target = $_GET["target"];
//GET変数　URL パラメータで現在のスクリプトに渡された変数の連想配列です。
/*
例1 $_GET の例

<?php
echo 'Hello ' . htmlspecialchars($_GET["name"]) . '!';
?>
ユーザーが http://example.com/?name=Hannes と入力したとします。

上の例の出力は、 たとえば以下のようになります。

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