<?php
   // 設定ファイルの読み込み

    require_once('./database_config.php');

    //従業員一覧から遷移した場合

    if(isset($_GET['targetID'])) {

        $target_employer_service_id=$_GET['targetID'];

    } else {

    //新規登録の場合

        $target_employer_service_id="";

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

    $employer_id = '';

    $e_service_id = '';

    $employer_service_date = '';

    $employer_service_scheduled_time = '';

    $employer_service_offer_time = '';

    $employer_service_client_name = '';

    $employer_service_id = '';



    if($target_employer_service_id != "") {

        //SQL構文を作成

        // サービスを抽出

        $stmt=$dbh->prepare('select employer_service_master.employer_id employer_id,employer_service_master.service_id service_id, employer_service_master.date service_date, employer_service_master.scheduled scheduled_time,employer_service_master.offer_time offer_time,employer_service_master.client_name client_name,employer_service_master.employer_service_id employer_service_id
        from employer_service_master where employer_service_master.employer_service_id = :target_employer_service_id');

        //変数紐づけ

        $stmt->bindParam(":target_employer_service_id",$target_employer_service_id,PDO::PARAM_INT);

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

        $employer_id = $result['employer_id'];

        $e_service_id = $result['service_id'];

        $employer_service_date = $result['service_date'];

        $employer_service_scheduled_time = $result['scheduled_time'];

        $employer_service_offer_time = $result['offer_time'];

        $employer_service_client_name = $result['client_name'];

        $employer_service_id = $result['employer_service_id'];



        /* 結果セット、statementオブジェクトを初期化 */

        $result = null;

        $stmt = null;

    }


        //サービスマスタからデータ取得

        $stmt = $dbh->prepare('select * from service_master order by service_id');



        //実行
    
        $stmt->execute();
    
        //  サービスマスタにレコードが存在しない場合はエラー
    
        if($stmt->rowCount() < 1) {
    
           echo "Error: サービスマスタにレコードが存在しません";
    
           exit;
    
        }
    
    
        //プルダウン対応
    
        $line_service="";
    
    
        //実行結果を変数にセット
    
        $result = $stmt->fetchAll(PDO::FETCH_NUM);
    
        foreach($result as $row) {

    
           $service_id = $row[0];
    
           $service_name = $row[1];
    
           if($service_id==$e_service_id) {
    
               $line_service .= "<option value='".$service_id."' selected>".$service_name."</option>\n";
    
           } else {
    
               $line_service .= "<option value='".$service_id."'>".$service_name."</option>\n";
    
           }
    
        }
    
        /* 結果セット、statementオブジェクトを初期化 */
    
        $result = null;
    
        $stmt = null;






    //新規画面からの遷移か従業員一覧画面からの遷移かで新規、更新、削除の>オプションの状態を変える

    if($target_employer_service_id != "") {

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

    if(is_readable('./helper_employer_service_detail.html')) {

    // ファイル内容を変数に取り込む

    $fp=fopen('./helper_employer_service_detail.html','r');



    // ファイルの最後まで処理を行う

    while(!feof($fp)) {

       // 1行ずつファイルを読み込み変数にセット

       $line=fgets($fp);

       // データベースからセットする項目について置き換え（動的部分）

       // ログイン名

       $line=str_replace("<###LOGINNAME###>",$login_name,$line);

       // 従業員詳細情報

       $line=str_replace("<###SERVICEID###>",$line_service,$line);

       $line=str_replace("<###EMPLOYERSERVICEID###>",$employer_service_id,$line);

       $line=str_replace("<###SERVISEDATE###>",$employer_service_date,$line);

       $line=str_replace("<###SCHEDULEDTIME###>",$employer_service_scheduled_time,$line);

       $line=str_replace("<###OFFERTIME###>",$employer_service_offer_time,$line);

       $line=str_replace("<###CLIENTNAME###>",$employer_service_client_name,$line);

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
