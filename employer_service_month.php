<?php
   // 設定ファイルの読み込み

    require_once('./database_config.php');

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


    $stmt=$dbh->prepare('select DATE_FORMAT(employer_service_master.date, "%Y年%m月"),employer_service_master.employer_id,employer_master.name,SUM(employer_service_master.offer_time) FROM employer_service_master, employer_master where employer_service_master.employer_id=employer_master.employer_id GROUP BY employer_service_master.employer_id,DATE_FORMAT(employer_service_master.date, "%Y年%m月")' );


    //実行

    $stmt->execute();

    // 後ほどhtmlファイルで置き換えするための変数の初期化

    $employer_service_line="";

    //実行結果を変数にセット

    if ($result = $stmt->fetchAll(PDO::FETCH_ASSOC)) {

        // if no one matched, move login

        foreach ($result as $row) {

           $employer_id = $row['employer_id'];
           
           $employer_name = $row['name'];

           $employer_service_date = $row['DATE_FORMAT(employer_service_master.date, "%Y年%m月")'];

           $employer_service_offer_time = $row['SUM(employer_service_master.offer_time)'];

           // 一覧用の値をセット

           $employer_service_line.="<tr><td>".$employer_id."</td><td>".$employer_name."</td><td>".$employer_service_date."</td><td>".$employer_service_offer_time."</td></tr>\n";
        }

        /* 結果セットを開放します */

        $result = null;

    }

    #statementオブジェクトを初期化

    $stmt = null;



    #DB接続情報を初期化

    $dbh = null;


    // サービス総合計時間を求めるSQL

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


    $stmt=$dbh->prepare('select SUM(offer_time) FROM employer_service_master' );


    //実行

    $stmt->execute();

    // 後ほどhtmlファイルで置き換えするための変数の初期化

    $employer_service_all_time="";



    //実行結果を変数にセット

    if ($result = $stmt->fetchAll(PDO::FETCH_ASSOC)) {

        // if no one matched, move login
        foreach ($result as $row) {

            $employer_service_all_time = $row['SUM(offer_time)'];

        }

        /* 結果セットを開放します */

        $result = null;

    }

    #statementオブジェクトを初期化

    $stmt = null;



    #DB接続情報を初期化

    $dbh = null;


    // htmlファイルが読み込める状態かどうかを確認する

    if(is_readable('./employer_service_month.html')) {

    // ファイル内容を変数に取り込む

    $fp=fopen('./employer_service_month.html','r');



    // ファイルの最後まで処理を行う

    while(!feof($fp)) {

       // 1行ずつファイルを読み込み変数にセット

       $line=fgets($fp);

       // データベースからセットする項目について置き換え（動的部分）

       // ログイン名

       $line1=str_replace("<###LOGINNAME###>",$login_id,$line);

       // 従業員リスト

       $lines=str_replace("<###EMPLOYERSERVICEMONTH###>",$employer_service_line,$line1);

       $line2=str_replace("<###EMPLOYERSERVICEALLTIME###>",$employer_service_all_time,$lines);

       echo $line2;

    }

    fclose($fp);

    }

    exit();

?>
