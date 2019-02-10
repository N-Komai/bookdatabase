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

//0.XSS処理
  function h($val){
    return htmlspecialchars($val,ENT_QUOTES);
  }

//1. POSTデータ取得
//update.phpからデータを受け取る（この受け取ったデータをもとにbindValueと結びつけるため）
//巻数が空欄で送信された場合は「１」を自動で入力
$id = h($_POST["id"],ENT_QUOTES);
$name = h($_POST["name"],ENT_QUOTES);
$author1 = h($_POST["author1"],ENT_QUOTES);
$author2 = h($_POST["author2"],ENT_QUOTES);
$author3 = h($_POST["author3"],ENT_QUOTES);
$company = h($_POST["company"],ENT_QUOTES);
$isbn = h($_POST["isbn"],ENT_QUOTES);
if ($_POST['number'] == null){ 
    $number = 1;
}else{
    $number = h($_POST['number'],ENT_QUOTES);
}
//ここまではinsert.phpとやっていることがほぼ同じ


//2. DB接続
//作成したDB（booklist）に接続をしてデータを検索して上書きしていく
try {
  $pdo = new PDO('mysql:dbname=booklist;charset=utf8;host=localhost','root','');
} catch (PDOException $e) {
  exit('DbConnectError:'.$e->getMessage());
}

//３．データ修正SQL作成 
//テーブル名は「my_book」
//カラムは id,name,number,author1~3,company,isbn,indate
$stmt = $pdo->prepare("UPDATE my_book SET name = :name, number = :number, author1 = :author1, author2 = :author2, author3 = :author3,
 company = :company, isbn = :isbn, indate = sysdate() WHERE id = :id");

//$stmtに修正用のSQL命令文を入力（UPDATE）。送られてきたidを元に全情報を上書きする

$stmt->bindValue(':name', $name, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':number', $number, PDO::PARAM_INT);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':author1', $author1, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':author2', $author2, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':author3', $author3, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':company', $company, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':isbn', $isbn, PDO::PARAM_INT);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':id', $id, PDO::PARAM_INT);  //Integer（数値の場合 PDO::PARAM_INT)
$status = $stmt->execute();

//４．データ登録処理後
if($status==false){
  //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
  $error = $stmt->errorInfo();
  exit("QueryError:".$error[2]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="style.css">
    <title>修正完了</title>
</head>
<body onLoad="setTimeout('window.close()',5000)">

    <div class="box-result">
        <h2>以下の内容に更新しました。5秒後に自動でウィンドウが閉じます</h2>

        <table>
        <!--[START] 要素のタグ表示用のテーブル--> 
            <thead>
                <tr>
                    <th style="width:35%;">書名</th>
                    <th style="width:5%;">巻数</th>
                    <th style="width:10%;">著者名１</th>
                    <th style="width:10%;">著者名２</th>
                    <th style="width:10%;">著者名３</th>
                    <th style="width:15%;">出版社名</th>
                    <th style="width:15%;">ISBN</th>
                </tr>
            </thead>
        <!--[END] 要素のタグ表示用のテーブル-->  
                
        <!--[START] 結果表示用のテーブル-->
            <tbody>
                 <tr>
                    <td><?php echo h($name) ?></td>
                    <td class="value"><?php echo h($number) ?></td>
                    <td class="value"><?php echo h($author1) ?></td>
                    <td class="value"><?php echo h($author2) ?></td>
                    <td class="value"><?php echo h($author3) ?></td>
                    <td class="value"><?php echo h($company) ?></td>
                    <td class="value"><?php echo h($isbn) ?></td>
                </tr>                  
            </tbody>
        <!--[END] 結果表示用のテーブル-->  
         </table>

        <form class="close">
            <button input type="button"　value=" 閉じる " onClick="window.close();">
            閉じる
            </button>
        </form>
        
    </div>
    
</body>
</html>
