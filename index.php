<?php
//=========================================================
//=======ファイル構成=======
//　〇index.php
//　　このファイル。全体のトップページ。
//　〇insert.php
//　　登録処理。処理完了後に自動でindexに戻る。
//　〇search.php
//　　検索処理。結果を別ウィンドウで表示する。
//~~~以下のphpはsearch.phpから【修正】【削除】をするときに使用する
//　〇delete.php
//　　削除処理。searchでアラートを出した後、削除OKしたら飛ぶページ。
//　　消したアイテムの情報を表示して5秒後に閉じる
//
//　〇update.php
//　〇end.php
//　　更新処理。update.phpは入力用フォーム。
//　　入力・送信するとend.phpで結果を表示して5秒後に閉じる
//~~~以下のPHPは特に意味は無い。確認が楽なので作った。
//　〇datacheck.php
//　　データベースの情報を全部取ってくる。表示は汚い。
//=========================================================
//=======DB情報=======
//　〇ID/id...これは自動で入力される
//　〇書名/name
//　〇巻数/number　←int型なので注意！
//　〇著者名１/author1
//　〇著者名２/author2
//　〇著者名３/author3
//　〇ISBN（１３桁）/isbn　←bigint型。intより大きな整数が扱える、らしい。
//　〇出版社/company
//　〇登録日時/indate...これは自動で登録日時が入る
//
//　上記９項目でデータベース「booklist」を作成
//　テーブル「my_book」から、idとindate以外の７項目を処理に利用する
//=========================================================

//SESSION変数で「登録完了」か否かを判定して、最下段の文字の表示非表示を切り替える
    session_start();

    if(!isset($_SESSION['insert_ok'])){
        $display = 'style="display:none;"';
    }elseif(isset($_SESSION['insert_ok'])){
        if($_SESSION['insert_ok'] == "true"){
            $display = '';
            $_SESSION['insert_ok'] = "";
        }else{
            $display = 'style="display:none;"';
        }
    }
//PHPここまで。
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href = "style.css">
    <title>蔵書データベース</title>

    <!--[START] script/JavaScript -->
    <script type = "text/javascript">
    <!-- JS未対応ブラウザ対策用コメントアウト
    /* [登録機能／check()]……書名と著者名１は最低入力していないとsubmitで送信できないように設定 */
        function check() {
            if(document.plusdata.name.value == ""){
                if(document.plusdata.author1.value == ""){
                    alert("書名は必ず入力してください。\n著者名は最低１人入力してください");
                    return false;
                }else{
                    alert("書名は必ず入力してください");
                    return false;
                }
            }
            if(document.plusdata.author1.value == ""){
                if(document.plusdata.name.value == ""){
                    alert("書名は必ず入力してください。\n著者名は最低１人入力してください");
                    return false;
                }else{
                    alert("著者名は最低１名入力してください");
                    return false;
                }
            }
        }

    /* [検索機能／check2()]……どれか１項目は最低入力していないとsubmitで送信できないように設定 */
        function checksearch() {
            if(document.search.name.value == "" && document.search.number.value == "" && document.search.author.value == "" && 
            document.search.company.value == "" && document.search.isbn.value == ""){
                alert("どれか１項目は入力してください");
                return false;
            }
        }
    //JS未対応対策ここまで-->
    </script>
    <!--[END] script/JavaScrpit -->

</head>
<body>

<main id="main">

    <h1>蔵書データベース</h1>

    <div class="box-form">
    <!-- [START] box-form -->
        <div class="box" style="background: lightblue;">
            <form method="post" action="insert.php" name="plusdata" onSubmit ="return check()">
                <h1>登録フォーム</h1>
                
                <dl><dt>書　　名：</dt><dd><input type="text" name="name"></dd></dl>
                <dl><dt>巻　　数：</dt><dd><input type="text" name="number"></dd></dl>
                <dl><dt>著者名１：</dt><dd><input type="text" name="author1"></dd></dl>
                <dl><dt>著者名２：</dt><dd><input type="text" name="author2"></dd></dl>
                <dl><dt>著者名３：</dt><dd><input type="text" name="author3"></dd></dl>
                <dl><dt>出版社名：</dt><dd><input type="text" name="company"></dd></dl>
                <dl><dt>ＩＳＢＮ：</dt><dd><input type="text" name="isbn"></dd></dl>
                
                <button input type="submit" name="func" value = "登録">
                    登録
                </button>

            </form>
        </div>

        <div class="box" style="background: pink;">
            <form method="post" target="_blank" action="search.php" name="search" onSubmit ="return checksearch()">
                <h1>検索フォーム</h1>
                
                <dl><dt>書　　名：</dt><dd><input type="text" name="name"></dd></dl>
                <dl><dt>巻　　数：</dt><dd><input type="text" name="number"></dd></dl>
                <dl><dt>著　　者：</dt><dd><input type="text" name="author"></dd></dl>
                <dl><dt>出版社名：</dt><dd><input type="text" name="company"></dd></dl>
                <dl><dt>ＩＳＢＮ：</dt><dd><input type="text" name="isbn"></dd></dl>
                
                <button input type="submit" name="func" value = "検索">
                    検索
                </button>
            </form>
        </div>
    <!-- [END] box-form -->
    </div>

    <div>
        <form class="close" action="datacheck.php" target="_blanck">
            <button input type="submit">
                全データ表示（チェック用）
            </button>
        </form>
    </div>

    <div <?= $display ?>>
        <h3>登録に成功しました</h3>
    </div>

</main>

</body>
</html>