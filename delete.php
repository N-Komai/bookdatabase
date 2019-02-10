<?php
//=======削除処理=======
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
//search.phpからIDを受け取る（受け取ったIDをもとにSQL命令文を作る）
$id = h($_POST["delete"],ENT_QUOTES);

//2. DB接続
//ここから作成したDB（booklist）に接続をしてデータを検索
try {
    $pdo = new PDO('mysql:dbname=booklist;charset=utf8;host=localhost','root','');
}catch (PDOException $e){
    exit('DbConnectError:'.$e->getMessage());
}

//結果表示用に、削除前にDBからデータを引き出しておく
$sql = 'SELECT * from my_book where id = '.$id;
$SQL_SET = $pdo->query($sql);
$result = $SQL_SET->fetch(PDO::FETCH_ASSOC);

$name = $result['name'];
$number = $result['number'];
$author1 = $result['author1'];
$author2 = $result['author2'];
$author3 = $result['author3'];
$company = $result['company'];
$isbn = $result['isbn'];

//削除用命令文を定義
$sql2 = 'DELETE FROM my_book WHERE id = '.$id;
$pdo->query($sql2);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="style.css">
    <title>アイテム削除</title>

</head>
<body onLoad="setTimeout('window.close()',5000)">

    <div class="box-result">
        <h2>以下のアイテムを削除しました。5秒後に自動でウィンドウが閉じます</h2>

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