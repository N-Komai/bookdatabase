<?php
//=======登録処理=======
//　〇ID/id...これは自動で入力される
//　〇書名/name
//　〇巻数/number　←int型なので注意！
//　〇著者名１/author1
//　〇著者名２/author2
//　〇著者名３/author3
//　〇ISBN（１３桁）/isbn　←bigint型。intより大きな整数が扱える、らしい。
//　〇出版社/company
//　〇登録日時/indate...これは自動で登録日時が入る
//===================
//　上記９項目でデータベース「booklist」を作成
//　テーブル「my_book」から、idとindate以外の７項目を処理に利用する

//0.XSS処理とSESSION_START
  function h($val){
    return htmlspecialchars($val,ENT_QUOTES);
  }
  session_start();

//1. POSTデータ取得
//index.phpからデータを受け取る（この受け取ったデータをもとにbindValueと結びつけるため）

$name = h($_POST["name"],ENT_QUOTES);
$author1 = h($_POST["author1"],ENT_QUOTES);
$author2 = h($_POST["author2"],ENT_QUOTES);
$author3 = h($_POST["author3"],ENT_QUOTES);
$company = h($_POST["company"],ENT_QUOTES);
$isbn = h($_POST["isbn"],ENT_QUOTES);

//巻数が空欄で送信された場合は「１」を自動で入力
if ($_POST['number'] == null){ 
    $number = 1;
}else{
    $number = h($_POST['number'],ENT_QUOTES);
}


//2. DB接続
//ここから作成したDB（booklist）に接続をしてデータを検索
try {
  $pdo = new PDO('mysql:dbname=booklist;charset=utf8;host=localhost','root','');
} catch (PDOException $e) {
  exit('DbConnectError:'.$e->getMessage());
}


//　$pdo…………　データベースへの接続情報

//３．データ登録SQL作成 
//テーブル名は「my_book」
//カラムは id,name,number,author1~3,company,isbn,indate
$stmt = $pdo->prepare("INSERT INTO my_book(id, name, number, author1, author2, author3, company, isbn,
indate )VALUES(NULL, :name, :number, :author1, :author2, :author3, :company, :isbn, sysdate())");
$stmt->bindValue(':name', $name, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':number', $number, PDO::PARAM_INT);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':author1', $author1, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':author2', $author2, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':author3', $author3, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':company', $company, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':isbn', $isbn, PDO::PARAM_INT);  //Integer（数値の場合 PDO::PARAM_INT)
$status = $stmt->execute();

//４．データ登録処理後
if($status==false){
  //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
  $error = $stmt->errorInfo();
  exit("QueryError:".$error[2]);
}else{

//５．index.phpへリダイレクト
//SESSIONを使うことで、indexに遷移したときに「登録完了」の文字列を表示する
  $_SESSION['insert_ok']= "true";
  header("Location: index.php");
  exit;

}
?>


