<?php

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
if (isset($_SESSION['login_name'])) {
	echo 'ようこそ' .  h($_SESSION['login_name']) . "さん<br>";
	echo "<a href='fileshare_main.php'>サービスに戻る</a><br>";
	echo "<a href='logout.php'>ログアウトはこちら</a>";//リンク先をを適宜変えておく
	exit(1);
}


//IDの判定(いまはやらない)
if (preg_match("/^[a-zA-Z0-9]+$/",$_POST['login_name'])){	
$login_name = $_POST['login_name'];
}else {
  echo 'IDは半角英数字で設定してください<br>';
  echo "<a href='signUp.php'>ログイン画面に戻る</a>";
  return false;
}
//パスワードの正規表現
if (preg_match('/\A(?=.*?[a-z])(?=.*?\d)[a-z\d]{8,100}+\z/i', $_POST['password'])) {
  $password_login = password_hash($_POST['password'], PASSWORD_DEFAULT);
//password_hash ()パスワードを外部から簡単に見られないように暗号化する関数
//DBの呼び出し設定にも$passwordが使われていたので_loginを付けた
} else {
  echo 'パスワードは半角英数字をそれぞれ1文字以上含んだ8文字以上で設定してください<br>';
  echo "<a href='signUp.php'>ログイン画面に戻る</a>";
  return false;
}
//登録処理

	$dsn = 'mysql:dbname=tb******db;host=localhost';
	$user = 'tb-*******';
	$password = '**********';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
//DB内のメールアドレスを取得
$stmt = $pdo->prepare("select login_name from DB_login where login_name = ?");
$stmt->execute([$login_name]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
//DB内のメールアドレスと重複していない場合、登録する。
if (!isset($row['login_name'])) {
  $stmt = $pdo->prepare("insert into DB_login(login_name, password) value(?, ?)");
  $stmt->execute([$login_name, $password_login]);
  echo "登録完了</br>";
  echo "<a href='signUp.php'>ログイン画面に戻る</a><br>";	
} else {
  echo '既に登録されたIDです。';
  return false;
}
/*
もっと短く書く方法　
try {
  $stmt = $pdo->prepare("insert into DB_login(email, password) value(?, ?)");
  $stmt->execute([$email, $password]);
  echo '登録完了';
} catch (\Exception $e) {
  echo '登録済みのメールアドレスです。';
}

*/