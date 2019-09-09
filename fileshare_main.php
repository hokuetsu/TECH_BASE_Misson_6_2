<?php
session_start();
if (!isset($_SESSION["login_name"])){
echo "ログインしてください";

	header("Location: logout.php");
exit;
}
try{
	$dsn = 'mysql:dbname=tb******db;host=localhost';
	$user = 'tb-*******';
	$password = '**********';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
//ユーザー名に合わせてDBを作成する
	$DB_name = "DB_".$_SESSION["login_name"];
	$DB_create = "CREATE TABLE IF NOT EXISTS ".$DB_name;
	$sql = $DB_create	
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY," //idというカラムを作った　INTは整数
	. "title TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,"
	. "fname TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,  "
	. "extension TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,"
	. "raw_data LONGBLOB NOT NULL,"
	. "time DATETIME"
	.");";
	$stmt = $pdo->query($sql);
//ファイルアップロードがあったとき
	if (isset($_FILES['upfile']['error']) && is_int($_FILES['upfile']['error']) && $_FILES["upfile"]["name"] !== ""){
	//エラーチェック
		switch ($_FILES['upfile']['error']) {
		case UPLOAD_ERR_OK: // OK
			break;
		case UPLOAD_ERR_NO_FILE:   // 未選択
			throw new RuntimeException('ファイルが選択されていません', 400);
		case UPLOAD_ERR_INI_SIZE:  // php.ini定義の最大サイズ超過
		throw new RuntimeException('ファイルサイズが大きすぎます', 400);
		default:
			throw new RuntimeException('その他のエラーが発生しました', 500);
	}

            //画像・動画をバイナリデータにする．
		$raw_data = file_get_contents($_FILES['upfile']['tmp_name']);
		//タイトルを設定する
		$title = ($_POST['title']); 
		//ファイル名が未入力のときは弾く設定
		if(empty($title)){
		echo "タイトルが入力されていません．<br/>";
		echo ("<a href=\"fileshare_main.php\">戻る</a><br/>");
		exit(1);
		}
           $tmp = pathinfo($_FILES["upfile"]["name"]);
            $extension = $tmp["extension"];
            if($extension === "jpg" || $extension === "jpeg" || $extension === "JPG" || $extension === "JPEG"){
                $extension = "jpeg";
            }
            elseif($extension === "png" || $extension === "PNG"){
                $extension = "png";
            }
            elseif($extension === "gif" || $extension === "GIF"){
                $extension = "gif";
            }
            elseif($extension === "mp4" || $extension === "MP4"){
                $extension = "mp4";
            }
            elseif($extension === "pdf" || $extension === "PDF"){
                $extension = "pdf";
            }
            else{
                echo "非対応ファイルです．<br/>";
                echo ("<a href=\"fileshare_main.php\">戻る</a><br/>");
                exit(1);
            }

            //DBに格納するファイルネーム設定
            //サーバー側の一時的なファイルネームと取得時刻を結合した文字列にsha256をかける．
            $date = getdate();
            $fname = $_FILES["upfile"]["tmp_name"].$date["year"].$date["mon"].$date["mday"].$date["hours"].$date["minutes"].$date["seconds"];
            $fname = hash("sha256", $fname);
		//投稿時間を記録する
		$time =  date("Y/m/d H:i:s");
		//画像・動画をDBに格納．
		$sql ="INSERT INTO ".$DB_name." (title, fname, extension, raw_data, time) VALUES (:title, :fname, :extension, :raw_data, :time);";
		$stmt = $pdo->prepare($sql);
		$stmt -> bindValue(":title",$title, PDO::PARAM_STR);
		$stmt -> bindValue(":fname",$fname, PDO::PARAM_STR);
		$stmt -> bindValue(":extension",$extension, PDO::PARAM_STR);
		$stmt -> bindValue(":raw_data",$raw_data, PDO::PARAM_STR);
		$stmt -> bindValue(':time', $time , PDO::PARAM_STR);
		$stmt -> execute();

	}

}	catch(PDOException $e){
	echo("<p>500 Inertnal Server Error</p>");
	exit($e->getMessage());
	}
?>

<!DOCTYPE HTML>

<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>ファイル共有サイト</title>
</head>

<body>
<h1>ファイル共有しよう！</h1>

<?php echo "こんにちは ".$_SESSION["login_name"]."さん"; ?>  <a href='logout.php'>ログアウトはこちら</a>
<p>
    <form action="fileshare_main.php" enctype="multipart/form-data" method="post">
        <label>画像/動画アップロード</label>
        <input type="file" name="upfile">
	<p>
	<!-- 追加要素 ファイルタイトルの個別設定 -->
        <label>ファイル名</label>
        <input type="text" name="title">
        
	<input type="submit" value="アップロード">
    </form>
<p>
	使い方
	<ul>
	<li>ファイル選択ボタンを押し、アップしたいファイルを選ぼう </li>
	<li>タイトルを入力しよう </li>
	<li>下の表に反映されるぞ </li>
	<li>「プレビュー」でアップした画像や動画、文章を見ることができるぞ</li>
	<li>「ダウンロード」でアップした画像や動画、文章をダウンロードすることができるぞ</li>
	</ul>
	<p>
        ※画像はjpeg方式，png方式，gif方式に対応しています．動画はmp4方式のみ対応しています．文書はpdf方式のみ対応しています。<br>
	※※動画のアップロードについてはまだ実装が十分にできていません(m__m) (あべ)
       <p>
    <?php
    //DBから取得して表示する．
    $sql = "SELECT * FROM ".$DB_name." ORDER BY id;";
    $stmt = $pdo->prepare($sql);
    $stmt -> execute();
    while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
	
        //動画と画像と文書で場合分け
        $target = $row["fname"];
	$id = $row["id"];
	$title2 = $row["title"];
        if($row["extension"] == "mp4"){
	echo $id." ";
	echo $title2.".".$row["extension"]." ";
	echo (
		"<a href='import_media.php?target=$target' target='_blank' rel='noopener noreferrer'>動画プレビュー</a>"
		." ".date("Y/m/d H:i:s",strtotime($row["time"]))
		." "."<a href='download_media.php?target=$target'>動画をダウンロード</a>"
		." "."<a href='delete.php?target=$target'>動画を削除</a>"
	);
	echo ("<hr/>"); 
       }
        elseif($row["extension"] == "jpeg" || $row["extension"] == "png" || $row["extension"] == "gif"){
	echo $id." ";
	echo $title2.".".$row["extension"]." ";
	echo (
		"<a href='import_media.php?target=$target' target='_blank' rel='noopener noreferrer'>画像プレビュー</a>"
		." ".date("Y/m/d H:i:s",strtotime($row["time"]))
		." "."<a href='download_media.php?target=$target'>画像をダウンロード</a>"
		." "."<a href='delete.php?target=$target'>画像を削除</a>"
	);
	echo ("<hr/>");
        }
 	elseif($row["extension"] == "pdf" ){
	echo $id." ";
	echo $title2.".".$row["extension"]." ";
	echo (
		"<a href='import_media.php?target=$target' target='_blank' rel='noopener noreferrer'>文書プレビュー</a>"
		." ".date("Y/m/d H:i:s",strtotime($row["time"]))
		." "."<a href='download_media.php?target=$target'>文書をダウンロード</a>"
		." "."<a href='delete.php?target=$target'>文書を削除</a>"
	);
 	echo ("<hr/>");
        }
    }
?>

</body>
</html>