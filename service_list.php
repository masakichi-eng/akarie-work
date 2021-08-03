<?php
   // 設定ファイルの読み込み

    require_once('./database_config.php');

    session_save_path('/home/m_kona/session/');

    session_start();

    $login_name=$_SESSION['employer_name'];

    $login_id=$_SESSION['employer_id'];

    try {

        //接続情報設定

        $dbh = new PDO("mysql:host=".DB_SERVER."; dbname=".DB_NAME."; charset=utf8", DB_ACCOUNT_ID , DB_ACCOUNT_PW);

        //エラー出力設定

        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

   //失敗した場合はエラー表示

    } catch (PDOException $e) {

        echo 'Connection failed: ' . $e->getMessage();

        exit;

    }

    // サービスを抽出するSQLを用意

    $stmt=$dbh->prepare('select name,time,price,service_id
        from service_master');

    //実行

    $stmt->execute();

    // 後ほどhtmlファイルで置き換えするための変数の初期化

    $service_line="";

    //実行結果を変数にセット

    if ($result = $stmt->fetchAll(PDO::FETCH_ASSOC)) {

        // if no one matched, move login

        foreach ($result as $row) {

           $service_id = $row['service_id'];

           $service_name = $row['name'];

           $service_time = $row['time'];

           $service_price = $row['price'];

           

           // 一覧用の値をセット

           $service_line.="<tr><td>".$service_id."</td><td>".$service_name."</td><td>".$service_time."</td><td><a href='./service_detail.php?targetID=".$service_id."'><button type='button'>詳細</button></a></td></tr>\n";

        }

        /* 結果セットを開放します */

        $result = null;

    }

    #statementオブジェクトを初期化

    $stmt = null;



    #DB接続情報を初期化

    $dbh = null;

    

    // htmlファイルが読み込める状態かどうかを確認する

    if(is_readable('./service_list.html')) {

    // ファイル内容を変数に取り込む

    $fp=fopen('./service_list.html','r');



    // ファイルの最後まで処理を行う

    while(!feof($fp)) {

       // 1行ずつファイルを読み込み変数にセット

       $line=fgets($fp);

       // データベースからセットする項目について置き換え（動的部分）

       // ログイン名

       $line1=str_replace("<###LOGINNAME###>",$service_name,$line);

       // 従業員リスト

       $lines=str_replace("<###SERVICELIST###>",$service_line,$line1);

       echo $lines;

    }

    fclose($fp);

    }

    exit();

?>
