<?php
	$dsn = 'mysql:dbname=tb******db;host=localhost';
	$user = 'tb-*******';
	$password = '**********';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

function h($s){
  return htmlspecialchars($s, ENT_QUOTES, 'utf-8');
}

session_start();
/*sessionとは
コンピューター側のサーバー上に一時的にデータを保存する仕組みのこと
今回はログイン情報を入れておくのに使う(簡単にアクセスされるとまずいため)
セキュリティ的に比較的安全な仕組みを使う)
参考
https://techacademy.jp/magazine/4970
*/
//ログイン済みの場合
if (isset($_SESSION['EMAIL'])) {
	echo 'ようこそ' .  h($_SESSION['EMAIL']) . "さん<br>";
	echo "<a href='fileshare_main.php'>サービスに戻る</a><br>";
	echo "<a href='logout.php'>ログアウトはこちら</a>";//リンク先をを適宜変えておく
	exit(1);
}

 ?>

<!DOCTYPE html>
<html lang="ja">
 <head>
   <meta charset="utf-8">
   <title>ファイル共有</title>
 </head>
 <body>
   <h1>ファイル共有サイト</h1>
   <h3>ログイン</h3>
   <form  action="login.php" method="post">
     <label for="login_name">ID</label>
     <input type="text" name="login_name">
     <label for="password">password</label>
     <input type="password" name="password">
     <button type="submit">ログイン</button>
   </form>
   <h3>初めての方はこちら</h3>
   <form action="new_register.php" method="post">
     <label for="login_name">ID</label>
     <input type="text" name="login_name">
     <label for="password">password</label>
     <input type="password" name="password">
     <button type="submit">新規登録</button>
<p>
     ※IDは半角英数字のみを利用できます</br>
     ※※パスワードは半角英数字をそれぞれ１文字以上含んだ、８文字以上で設定してください
   </form>
 </body>
</html>
