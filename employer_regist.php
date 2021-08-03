<?php

    // 設定ファイルの読み込み

    require_once('./database_config.php');

    //従業員一覧から遷移した場合

    if(isset($_GET['targetID'])) {

      $target_employer_id=$_GET['targetID'];

    } else {

    //新規登録の場合

        $target_employer_id="";

    }

    //  セッション処理

    session_save_path('/home/m_kona/session/');

    session_start();

    $login_name=$_SESSION['employer_name'];

    $login_id=$_SESSION['employer_id'];

    // POST PARAMETER

    $employer_account=$_POST['employer_account'];

    $employer_password=$_POST['employer_password'];
    
    $employer_id=$_POST['employer_id'];

    $employer_name=$_POST['employer_name'];

    $employer_name_kana=$_POST['employer_name_kana'];

    $employer_birth_date=$_POST['employer_birth_date'];

    $department=$_POST['department'];

    $position=$_POST['position'];

    $address=$_POST['address'];

    $submit_option=$_POST['submit_option'];



    // データベースに接続

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



    //新規画面からの遷移か従業員一覧画面からの遷移かで新規、更新、削除の>オプションの状態を変える

    if($target_employer_id != "") {

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







    //情報削除

    if($submit_option=='3') {

        $stmt = $dbh->prepare('delete from employer_master where id=:employer_id');

        //変数紐づけ

        $stmt->bindParam(":employer_id",$employer_id,PDO::PARAM_INT);

        //削除なので従業員IDなし

        $target_employer_id="";

    //情報変更

    } else if($submit_option=='1') {

        $stmt = $dbh->prepare('update employer_master set account=:account,password=:password,name=:name,name_kana=:name_kana,birth_date=:birth_date,department_id=:department_id,position_id=:position_id,address=:address where id=:employer_id');

        //変数紐づけ

        $stmt->bindParam(":account",$employer_account,PDO::PARAM_STR);

        $stmt->bindParam(":password",$employer_password,PDO::PARAM_STR);
        
        $stmt->bindParam(":name",$employer_name,PDO::PARAM_STR);

        $stmt->bindParam(":name_kana",$employer_name_kana,PDO::PARAM_STR);

        $stmt->bindParam(":birth_date",$employer_birth_date,PDO::PARAM_STR);

        $stmt->bindParam(":department_id",$department,PDO::PARAM_STR);

        $stmt->bindParam(":position_id",$position,PDO::PARAM_STR);

        $stmt->bindParam(":address",$address,PDO::PARAM_STR);

        $stmt->bindParam(":employer_id",$employer_id,PDO::PARAM_INT);

        //従業員IDをセット

        $target_employer_id=$employer_id;



    //  新規追加

    } else {

        //  id と employer_idは自動的に付与（+1）

        $stmt=$dbh->prepare('select max(id)+1 id,max(employer_id)+1 employer_id from employer_master');

        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $new_id=$result['id'];

        $new_employer_id=$result['employer_id'];

        $stmt = null;


        $stmt=$dbh->prepare('insert into employer_master(id,account,password,name,name_kana,birth_date,employer_id,department_id,position_id,address) values(:id,:account,:password,:name,:name_kana,:birth_date,:employer_id,:department_id,:position_id,:address)');

        //変数紐づけ

        $stmt->bindParam(":account",$employer_account,PDO::PARAM_STR);

        $stmt->bindParam(":password",$employer_password,PDO::PARAM_STR);

        $stmt->bindParam(":id",$new_id,PDO::PARAM_INT);

        $stmt->bindParam(":name",$employer_name,PDO::PARAM_STR);

        $stmt->bindParam(":name_kana",$employer_name_kana,PDO::PARAM_STR);

        $stmt->bindParam(":birth_date",$employer_birth_date,PDO::PARAM_STR);

        $stmt->bindParam(":employer_id",$new_employer_id,PDO::PARAM_INT);

        $stmt->bindParam(":department_id",$department,PDO::PARAM_INT);

        $stmt->bindParam(":position_id",$position,PDO::PARAM_INT);

        $stmt->bindParam(":address",$address,PDO::PARAM_STR);

        $target_employer_id=$new_id;

    }



    //実行

    try{

        $flag = $stmt->execute();

        if (!$flag){

            echo "Error: Update or Insert";

            exit;

        }

    }catch (PDOException $e){

       print('Error:'.$e->getMessage());

       exit;

    }



    //各オブジェクトの初期化

    $result = null;

    $stmt = null;



    //情報削除の場合は、従業員一覧に遷移

    if($submit_option == '3') {

        header('location: ./employerlist.php');

        exit;

    }



    //SQL構文を作成

    $stmt = $dbh->prepare('select employer_master.account account,employer_master.password password, employer_master.employer_id employer_id,employer_master.name name,employer_master.name_kana name_kana,employer_master.birth_date birth_date,department_master.department_id department_id,position_master.position_id position_id,employer_master.address address from employer_master,department_master,position_master where employer_master.department_id=department_master.department_id and employer_master.position_id = position_master.position_id and employer_master.id = :target_employer_id');



    //変数紐づけ

    $stmt->bindParam(":target_employer_id",$target_employer_id,PDO::PARAM_INT);



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

       $line=str_replace("<###EMPLOYERID###>",$target_employer_id,$line);

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
