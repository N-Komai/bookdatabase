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
//　表示するためのテーブルについて、PHPでの文字列付け足し（.=）を使った方が、HTMLがすっきりして良かったかもしれない

//0.XSS処理
    function h($val){
        return htmlspecialchars($val,ENT_QUOTES);
    }

//1. POSTデータ取得
//index.phpからデータを受け取る（この受け取ったデータをもとに検索用のSQL命令文を作る）
//著者情報は１つだけ取得して、author1~3に代入することで、横断検索できるはず
    $name = h($_POST["name"],ENT_QUOTES);
    $number = h($_POST['number'],ENT_QUOTES);
    $author = h($_POST["author"],ENT_QUOTES);
    $company = h($_POST["company"],ENT_QUOTES);
    $isbn = h($_POST["isbn"],ENT_QUOTES);


//2. DB接続
//ここから作成したDB（booklist）に接続をしてデータを検索
    try {
        $pdo = new PDO('mysql:dbname=booklist;charset=utf8;host=localhost','root','');
    }catch (PDOException $e){
        exit('DbConnectError:'.$e->getMessage());
    }

//3．検索前準備
//$where[]配列を作成して、SQLの検索条件を入れていく
//最後にimplode-"and"で文字列に変換（$wheresql）し、
//SQL命令文を文字列として、"$sql"に入れてあげる
    $where = []; 

    if(isset($name)){ //書名の曖昧キーワード検索
        $where[] = "name LIKE '%".$name."%'";
    }

    if(isset($number)){ //巻数の検索（曖昧ナシ）。varchar型""で送信される可能性があるので、その場合は１以上（＝全タイトル対象）を入力
        if($number == ""){
            $where[] = "number >= 1";
        }else{
            $where[] = "number = ".$number;
        }
    }

    if(isset($company)){ //出版社名の曖昧検索
        $where[] = "company LIKE '%".$company."%'";
    }

    if(isset($author)){ //$authorを元に、DBのauthor1~3を"SELECT-LIKE-or"で曖昧・横断検索
        $where[] = "(author1 LIKE '%".$author."%' OR author2 LIKE '%".$author."%' OR author3 LIKE '%".$author."%')";
    }

    if(isset($isbn)){ //ISBNの曖昧検索。varchar型""で送信される可能性があるので、その場合は曖昧検索で全番号対象になるように入力
        if($isbn == ""){
            $where[] = "isbn LIKE '%%'";
        }else{
            $where[] = "isbn LIKE '%".$isbn."%'";
        }
    }

//4．SQL命令文を実行して、検索結果を取得する
//結果は$rowで取得した物を、$result[]配列に順番に入れ込んでいく
//$whereが空になることはない、はず。
    if(isset($where)){
        $whereSql = implode(' AND ', $where); //SQL命令文なので「AND」の前後には半角スペースが必須
        $sql = 'SELECT * FROM my_book WHERE '.$whereSql ;
        $SQL_SET = $pdo->query($sql);
        $result = [];
        while($row = $SQL_SET->fetch(PDO::FETCH_ASSOC)){ //FETCH_ASSOC、多分連想配列を作るということ？
            $result[] = $row; //配列内に連想配列を継ぎ足し
        }
    }

    //fetch：MtGのフェッチランドのフェッチ＝「取り出し」
    //ASSOCはAssociation？

//---動作確認用。後で消す
//echo $whereSql."<br>";
//echo $_POST["name"];
//echo $_POST["number"];
//echo $_POST["author"];
//echo $_POST["company"];
//echo $_POST["isbn"];
//var_dump($result);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="style.css">
    <title>検索結果</title>

    <!--[START] script/JavaScript -->
    <script type = "text/javascript">
    <!-- JS未対応ブラウザ対策用コメントアウト
    function deletecheck(){
        if(window.confirm('本当に削除してよろしいですか？')){ // 確認ダイアログを表示
            return true; // 「OK」時は送信を実行
        }
        else{ // 「キャンセル」時の処理
            window.alert('キャンセルされました'); // 警告ダイアログを表示
            return false; // 送信を中止
        }
    }
    //JS未対応対策ここまで-->
    </script>
    <!--[END] script/JavaScrpit -->

</head>
<body>

<main>
    <div class="box-result">
        <?php if(isset($result)): ?>
            <h2><?php echo count($result) ?>件見つかりました。</h2>

            <table>

                <!--[START] 要素のタグ表示用のテーブル--> 
                <thead>
                    <tr>
                        <th style="width:35%;">書名</th>
                        <th style="width:3%;">巻数</th>
                        <th style="width:10%;">著者名１</th>
                        <th style="width:10%;">著者名２</th>
                        <th style="width:10%;">著者名３</th>
                        <th style="width:13%;">出版社名</th>
                        <th style="width:13%;">ISBN</th>
                        <th style="width:3%;">修正</th>
                        <th style="width:3%;">削除</th>
                    </tr>
                </thead>
                <!--[END] 要素のタグ表示用のテーブル-->  
                
                <!--[START] 結果表示用のテーブル-->
                <tbody>
                <!--foreach文で、$resultの中身（$row）がある限り、中身を取り出して表示する-->
                    <?php foreach($result as $row): ?><!--HTMLの中でPHPを動かす方法らしい。{}の代わりに:とend〇〇;を使う-->
                        <tr>
                            <td><?php echo h($row['name']) ?></td>
                            <td class="value"><?php echo h($row['number']) ?></td>
                            <td class="value"><?php echo h($row['author1']) ?></td>
                            <td class="value"><?php echo h($row['author2']) ?></td>
                            <td class="value"><?php echo h($row['author3']) ?></td>
                            <td class="value"><?php echo h($row['company']) ?></td>
                            <td class="value"><?php echo h($row['isbn']) ?></td>
                            <td class="value">
                                <form method="post" target="_blank" action="update.php">
                                    <button input type="submit" name="update" value = "<?php echo h($row['id']) ?>">
                                    修正
                                    <!--ボタンのvalueをDBから拾ってきたIDにすることで、修正用PHPに飛ばしている-->
                                    </button>
                                </form>
                            </td>
                            <td class="value">
                                <form method="post" target="_blank" action="delete.php" onSubmit ="return deletecheck()">
                                    <button input type="submit" name="delete" value = "<?php echo h($row['id']) ?>">
                                    削除
                                    <!--ボタンのvalueをDBから拾ってきたIDにすることで、削除用PHPに飛ばしている-->
                                    </button>
                                </form>
                            </td>
                        </tr>                  
                    <?php endforeach; ?>
                </tbody>
                <!--[END] 結果表示用のテーブル-->  

            </table>

            <form class="close">
                <button input type="button"　value=" 閉じる " onClick="window.close();">
                閉じる
                </button>
            </form>

        <?php else: ?>
            <!--見つからなかったときの表示-->
            <p class="alert alert-danger">検索対象は見つかりませんでした。</p>
        <?php endif; ?>
        
    </div>
</main>

</body>
</html>