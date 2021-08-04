<?php
   // 設定ファイルの読み込み

    require_once('./database_config.php');

    //従業員一覧から遷移した場合

    if(isset($_GET['targetID'])) {

        $target_service_id=$_GET['targetID'];

    } else {

    //新規登録の場合

        $target_service_id="";

    }

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

    //各入力変数を初期化

    $service_name = '';

    $service_time = '';

    $service_price = '';

    $service_id = '';



    if($target_service_id != "") {

        //SQL構文を作成

        // サービスを抽出

        $stmt=$dbh->prepare('select service_master.service_name service_name,service_master.time time, service_master.price price,service_master.service_id service_id
        from service_master where service_master.id = :target_service_id');

        //変数紐づけ

        $stmt->bindParam(":target_service_id",$target_service_id,PDO::PARAM_INT);

        //実行

        $stmt->execute();

        //取得したレコードの数が１かどうか

        if($stmt->rowCount() != 1) {

            echo "Error: データが特定できませんでした";

            exit;

        }

        //実行結果を変数にセット(1件)

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        //変数にテーブルの値をセット

        $service_name = $result['service_name'];

        $service_time = $result['time'];

        $service_price = $result['price'];

        $service_id = $result['service_id'];



        /* 結果セット、statementオブジェクトを初期化 */

        $result = null;

        $stmt = null;

    }






    //新規画面からの遷移か従業員一覧画面からの遷移かで新規、更新、削除の>オプションの状態を変える

    if($target_service_id != "") {

        //従業員一覧からの時

        $suboption1 = "checked";

        $suboption2= "";

        $suboption3 = "";

    } else {

        //新規の時

        $suboption1 = "disabled";

        $suboption2= "checked";

        $suboption3 = "disabled";

    }



    // htmlファイルが読み込める状態かどうかを確認する

    if(is_readable('./service_detail.html')) {

    // ファイル内容を変数に取り込む

    $fp=fopen('./service_detail.html','r');



    // ファイルの最後まで処理を行う

    while(!feof($fp)) {

       // 1行ずつファイルを読み込み変数にセット

       $line=fgets($fp);

       // データベースからセットする項目について置き換え（動的部分）

       // ログイン名

       $line=str_replace("<###LOGINNAME###>",$login_name,$line);

       // 従業員詳細情報

       $line=str_replace("<###NAME###>",$service_name,$line);

       $line=str_replace("<###TIME###>",$service_time,$line);

       $line=str_replace("<###PRICE###>",$service_price,$line);

       $line=str_replace("<###SERVICEID###>",$service_id,$line);

       $line=str_replace("<###SUBOPTION1###>",$suboption1,$line);

       $line=str_replace("<###SUBOPTION2###>",$suboption2,$line);

       $line=str_replace("<###SUBOPTION3###>",$suboption3,$line);

       //  1行ずつ出力

       echo $line;

    }

    fclose($fp);

    }

    exit();

?>
