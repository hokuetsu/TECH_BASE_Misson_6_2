<?php
function h($s){
  return htmlspecialchars($s, ENT_QUOTES, 'utf-8');
}
session_start();

//DB内でPOSTされたメールアドレスを検索
try {
	$dsn = 'mysql:dbname=tb******db;host=localhost';
	$user = 'tb-*******';
	$password = '**********';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

  $stmt = $pdo->prepare('select * from DB_login where login_name = ?');
  $stmt->execute([$_POST['login_name']]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (\Exception $e) {
  echo $e->getMessage() . PHP_EOL;
}
//login_nameがDB内に存在しているか確認
if (!isset($row['login_name'])) {
  echo 'メールアドレス又はパスワードが間違っています1。';
  return false;
}
//パスワード確認後sessionにメールアドレスを渡す
if (password_verify($_POST['password'], $row['password'])) {
//password_verify ()ハッシュ化されたパスワードが一致しているかチェックする関数

  session_regenerate_id(true); //session_idを新しく生成し、置き換える
  $_SESSION['login_name'] = $row['login_name'];
  echo 'ログインしました';
  header('Location: fileshare_main.php');
} else {
  echo 'メールアドレス又はパスワードが間違っています2。';
  return false;
}
