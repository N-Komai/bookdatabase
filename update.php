<?php
//=======修正処理=======
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

//1. DB接続
//ここから作成したDB（booklist）に接続をしてデータを検索
try {
    $pdo = new PDO('mysql:dbname=booklist;charset=utf8;host=localhost','root','');
}catch (PDOException $e){
    exit('DbConnectError:'.$e->getMessage());
}

//2. POSTデータ取得
//search.phpからデータを受け取る（この受け取ったデータをもとにSQL命令文を作る）
//送られてきたid（ボタンのvalueに仕込んだ）を元に情報を撮っていく
$id = h($_POST["update"],ENT_QUOTES);

//送られてきたIDに合致する書籍情報をDBから引き出す
$sql = 'SELECT * FROM my_book WHERE id = '.$id;
$SQL_SET = $pdo->query($sql);
$result = $SQL_SET->fetch(PDO::FETCH_ASSOC);

$name = $result['name'];
$number = $result['number'];
$author1 = $result['author1'];
$author2 = $result['author2'];
$author3 = $result['author3'];
$company = $result['company'];
$isbn = $result['isbn'];

//チェック用の表示命令
//var_dump($result);
//echo $name."<br>";
//echo $number."<br>";
//echo $author1."<br>";
//echo $author2."<br>";
//echo $author3."<br>";
//echo $company."<br>";
//echo $isbn."<br>";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="style.css">
    <title>内容修正</title>

    <!--[START] script/JavaScript -->
    <script type = "text/javascript">
    <!-- JS未対応ブラウザ対策用コメントアウト
    /* [登録機能／check()]……書名と著者名１は最低入力していないとsubmitで送信できないように設定 */
        function check() {
            if(document.update.name.value == ""){
                if(document.update.author1.value == ""){
                    alert("書名は必ず入力してください。\n著者名は最低１人入力してください");
                    return false;
                }else{
                    alert("書名は必ず入力してください");
                    return false;
                }
            }
            if(document.update.author1.value == ""){
                if(document.update.name.value == ""){
                    alert("書名は必ず入力してください。\n著者名は最低１人入力してください");
                    return false;
                }else{
                    alert("著者名は最低１名入力してください");
                    return false;
                }
            }
        }
    //JS未対応対策ここまで-->
    </script>
    <!--[END] script/JavaScrpit -->

</head>
<body>

<main id="main">

        <h1>アイテム情報修正</h1>

<div class="box-form">
    <!-- [START] box-form -->

    <div class="box" style="background: lightgray;">        
            <h2>修正前</h2>
            <dl style="display:none;"><dt>管理ID：</dt><dd><input type="text" name="id" value="<?= $id ?>" readonly></dd></dl>     
            <dl><dt>書　　名：</dt><dd><input type="text" name="name" value="<?= $name ?>"readonly></dd></dl>
            <dl><dt>巻　　数：</dt><dd><input type="text" name="number"  value="<?= $number ?>" readonly></dd></dl>
            <dl><dt>著者名１：</dt><dd><input type="text" name="author1"  value="<?= $author1 ?>" readonly></dd></dl>
            <dl><dt>著者名２：</dt><dd><input type="text" name="author2"  value="<?= $author2 ?>" readonly></dd></dl>
            <dl><dt>著者名３：</dt><dd><input type="text" name="author3"  value="<?= $author3 ?>" readonly></dd></dl>
            <dl><dt>出版社名：</dt><dd><input type="text" name="company"  value="<?= $company ?>" readonly></dd></dl>
            <dl><dt>ＩＳＢＮ：</dt><dd><input type="text" name="isbn"  value="<?= $isbn ?>" readonly></dd></dl>
    </div>

    <div class="box" style="background: lightcoral;">        
        <form method="post" action="end.php" name="update" onSubmit ="return check()">
            <h2>修正後</h2>
            <dl style="display:none;"><dt>管理ID：</dt><dd><input type="text" name="id" value="<?= $id ?>" readonly></dd></dl>     
            <dl><dt>書　　名：</dt><dd><input type="text" name="name" value="<?= $name ?>"></dd></dl>
            <dl><dt>巻　　数：</dt><dd><input type="text" name="number"  value="<?= $number ?>"></dd></dl>
            <dl><dt>著者名１：</dt><dd><input type="text" name="author1"  value="<?= $author1 ?>"></dd></dl>
            <dl><dt>著者名２：</dt><dd><input type="text" name="author2"  value="<?= $author2 ?>"></dd></dl>
            <dl><dt>著者名３：</dt><dd><input type="text" name="author3"  value="<?= $author3 ?>"></dd></dl>
            <dl><dt>出版社名：</dt><dd><input type="text" name="company"  value="<?= $company ?>"></dd></dl>
            <dl><dt>ＩＳＢＮ：</dt><dd><input type="text" name="isbn"  value="<?= $isbn ?>"></dd></dl>
            
            <button input type="submit" name="update" value = "修正">
                修正
            </button>
        </form>           
    </div>

</main>

</body>
</html>