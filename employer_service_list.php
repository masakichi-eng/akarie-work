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

    // $stmt=$dbh->prepare('select employer_id,service_id,date,scheduled,offer_time,client_name,employer_service_id
    //     from employer_service_master');

    $stmt=$dbh->prepare('select employer_service_master.employer_id,employer_service_master.service_id,employer_service_master.date,employer_service_master.scheduled,employer_service_master.offer_time,employer_service_master.client_name,employer_service_master.employer_service_id,service_master.service_name,employer_master.name from employer_service_master,employer_master,service_master where employer_service_master.service_id=service_master.service_id and employer_service_master.employer_id=employer_master.employer_id order by employer_service_master.id');


    //実行

    $stmt->execute();

    // 後ほどhtmlファイルで置き換えするための変数の初期化

    $employer_service_line="";

    //実行結果を変数にセット

    if ($result = $stmt->fetchAll(PDO::FETCH_ASSOC)) {

        // if no one matched, move login

        foreach ($result as $row) {

           $employer_service_id = $row['employer_service_id'];
           
           $employer_name = $row['name'];
           
           $service_name= $row['service_name'];

           $employer_service_date = $row['date'];

           $employer_service_scheduled_time = $row['scheduled'];

           $employer_service_offer_time = $row['offer_time'];

           $employer_service_client_name = $row['client_name'];


           // 一覧用の値をセット

           $employer_service_line.="<tr><td>".$employer_name."</td><td>".$service_name."</td><td>".$employer_service_date."</td><td>".$employer_service_scheduled_time."</td><td>".$employer_service_offer_time."</td><td>".$employer_service_client_name."</td><td><a href='./employer_service_detail.php?targetID=".$employer_service_id."'><button type='button'>詳細</button></a></td></tr>\n";

        }

        /* 結果セットを開放します */

        $result = null;

    }

    #statementオブジェクトを初期化

    $stmt = null;



    #DB接続情報を初期化

    $dbh = null;


    // htmlファイルが読み込める状態かどうかを確認する

    if(is_readable('./employer_service_list.html')) {

    // ファイル内容を変数に取り込む

    $fp=fopen('./employer_service_list.html','r');



    // ファイルの最後まで処理を行う

    while(!feof($fp)) {

       // 1行ずつファイルを読み込み変数にセット

       $line=fgets($fp);

       // データベースからセットする項目について置き換え（動的部分）

       // ログイン名

       $line1=str_replace("<###LOGINNAME###>",$login_id,$line);

       // 従業員リスト

       $lines=str_replace("<###EMPLOYERSERVICELIST###>",$employer_service_line,$line1);

       echo $lines;

    }

    fclose($fp);

    }

    exit();

?>
