<?php
   // 設定ファイルの読み込み

    require_once('./database_config.php');

    //従業員一覧から遷移した場合

    if(isset($_GET['targetID'])) {

        $target_user_id=$_GET['targetID'];

    } else {

    //新規登録の場合

        $target_user_id="";

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

    $employer_account = '';

    $employer_password = '';

    $employer_account_id = '';

    $employer_name = '';

    $employer_name_kana = '';

    $employer_birth_date = '';

    $employer_department = '';

    $employer_position = '';

    $employer_address = '';

    if($target_user_id != "") {

        //SQL構文を作成

        // 従業員情報を抽出（従業員マスタ、役職マスタ、部署マスタより）

        $stmt = $dbh->prepare('select employer_master.account account,employer_master.password password, employer_master.employer_id employer_id,employer_master.name name,employer_master.name_kana name_kana,employer_master.birth_date birth_date,department_master.department_id department_id,position_master.position_id position_id,employer_master.address address from employer_master,department_master,position_master where employer_master.department_id=department_master.department_id and employer_master.position_id = position_master.position_id and employer_master.id = :target_user_id');

        //変数紐づけ

        $stmt->bindParam(":target_user_id",$target_user_id,PDO::PARAM_INT);

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
        $employer_account = $result['account'];

        $employer_password = $result['password'];

        $employer_account_id = $result['employer_id'];

        $employer_name = $result['name'];

        $employer_name_kana = $result['name_kana'];

        $employer_birth_date = $result['birth_date'];

        $employer_department = $result['department_id'];

        $employer_position = $result['position_id'];

        $employer_address = $result['address'];

        /* 結果セット、statementオブジェクトを初期化 */

        $result = null;

        $stmt = null;

    }



    //部署マスタからデータ取得

    $stmt = $dbh->prepare('select * from department_master order by department_id');



    //実行

    $stmt->execute();

    //  部署マスタにレコードが存在しない場合はエラー

    if($stmt->rowCount() < 1) {

       echo "Error: 部署マスタにレコードが存在しません";

       exit;

    }


    //プルダウン対応

    $line_department="";


    //実行結果を変数にセット

    $result = $stmt->fetchAll(PDO::FETCH_NUM);

    foreach($result as $row) {

       $department_id = $row[0];

       $department_name = $row[1];

       if($department_id==$employer_department) {

           $line_department .= "<option value='".$department_id."' selected>".$department_name."</option>\n";

       } else {

           $line_department .= "<option value='".$department_id."'>".$department_name."</option>\n";

       }

    }

    /* 結果セット、statementオブジェクトを初期化 */

    $result = null;

    $stmt = null;



    //役職マスタからデータ取得

    $stmt = $dbh->prepare('select * from position_master order by position_id');



    //実行

    $stmt->execute();



    //  部署マスタにレコードが存在しない場合はエラー

    if($stmt->rowCount() < 1) {

       echo "Error: 役職マスタにレコードが存在しません";

       exit;

    }



    //プルダウン対応

    $line_position="";



    //実行結果を変数にセット

    $result = $stmt->fetchAll(PDO::FETCH_NUM);

    foreach($result as $row) {

       $position_id = $row[0];

       $position_name = $row[1];

       if($position_id==$employer_position) {

          $line_position .= "<option value='".$position_id."' selected>".$position_name."</option>\n";

       } else {

          $line_position .= "<option value='".$position_id."'>".$position_name."</option>\n";

       }

    }



    /* 結果セット、statementオブジェクトを初期化 */

    $result = null;

    $stmt = null;





    //新規画面からの遷移か従業員一覧画面からの遷移かで新規、更新、削除の>オプションの状態を変える

    if($target_user_id != "") {

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

    if(is_readable('./employerdetail.html')) {

    // ファイル内容を変数に取り込む

    $fp=fopen('./employerdetail.html','r');



    // ファイルの最後まで処理を行う

    while(!feof($fp)) {

       // 1行ずつファイルを読み込み変数にセット

       $line=fgets($fp);

       // データベースからセットする項目について置き換え（動的部分）

       // ログイン名

       $line=str_replace("<###LOGINNAME###>",$login_name,$line);

       // 従業員詳細情報

       $line=str_replace("<###ACCOUNT###>",$employer_account,$line);

       $line=str_replace("<###PASSWORD###>",$employer_password,$line);

       $line=str_replace("<###ACCOUNTID###>",$employer_account_id,$line);

       $line=str_replace("<###NAME###>",$employer_name,$line);

       $line=str_replace("<###NAMEKANA###>",$employer_name_kana,$line);

       $line=str_replace("<###BIRTHDATE###>",$employer_birth_date,$line);

       $line=str_replace("<###DEPARTMENT###>",$line_department,$line);

       $line=str_replace("<###POSITION###>",$line_position,$line);

       $line=str_replace("<###ADDRESS###>",$employer_address,$line);

       $line=str_replace("<###EMPLOYERID###>",$target_user_id,$line);

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
