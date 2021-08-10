<?php

    // 設定ファイルの読み込み

    require_once('./database_config.php');

        //一覧から遷移した場合

        if(isset($_GET['targetID'])) {

            $target_employer_service_id=$_GET['targetID'];
      
          } else {
      
          //新規登録の場合
      
            $target_employer_service_id="";
      
          }


    //  セッション処理

    session_save_path('/home/m_kona/session/');

    session_start();

    $login_name=$_SESSION['employer_name'];

    $login_id=$_SESSION['employer_id'];

    // POST PARAMETER

    $employer_id=$login_id;

    $service_id=$_POST['service_id'];

    $employer_service_date=$_POST['service_date'];

    $employer_service_scheduled_time=$_POST['scheduled_time'];

    $employer_service_offer_time=$_POST['offer_time'];

    $employer_service_client_name=$_POST['client_name'];

    $employer_service_id=$_POST['employer_service_id'];

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

       if($target_employer_service_id != "") {

        //一覧からの時
  
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
  
          $stmt = $dbh->prepare('delete from employer_service_master where employer_service_id=:employer_service_id');
  
          //変数紐づけ
  
          $stmt->bindParam(":employer_service_id",$employer_service_id,PDO::PARAM_INT);
  
          //削除なので従業員IDなし
  
          $target_service_id="";
  
      //情報変更
  
      } else if($submit_option=='1') {
  
          $stmt = $dbh->prepare('update employer_service_master set employer_id=:employer_id,service_id=:service_id,date=:service_date,scheduled=:scheduled_time,offer_time=:offer_time,client_name=:client_name where employer_service_id=:employer_service_id');


  
          //変数紐づけ
  
          $stmt->bindParam(":employer_id",$employer_id,PDO::PARAM_INT);
  
          $stmt->bindParam(":service_id",$service_id,PDO::PARAM_INT);
          
          $stmt->bindParam(":service_date",$employer_service_date,PDO::PARAM_STR);
          
          $stmt->bindParam(":scheduled_time",$employer_service_scheduled_time,PDO::PARAM_STR);

          $stmt->bindParam(":offer_time",$employer_service_offer_time,PDO::PARAM_INT);

          $stmt->bindParam(":client_name",$employer_service_client_name,PDO::PARAM_STR);

          $stmt->bindParam(":employer_service_id",$employer_service_id,PDO::PARAM_INT);


  
          //従業員IDをセット
  
          $target_employer_service_id=$employer_service_id;
  
  
  
      //  新規追加
  
      } else {
        //  id と user_idは自動的に付与（+1）

        $stmt=$dbh->prepare('select max(id)+1 id,max(employer_service_id)+1 employer_service_id from employer_service_master');

        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $new_id=$result['id'];

        if (is_null($new_id)){
            $new_id = 1;
        }

        $new_employer_service_id=$result['employer_service_id'];

        if (is_null($new_employer_service_id)){
            $new_employer_service_id = 1;
        }

        $stmt = null;


        $stmt=$dbh->prepare('insert into employer_service_master(employer_id,service_id,date,scheduled,offer_time,client_name,employer_service_id)
        values(:employer_id,:service_id,:service_date,:scheduled_time,:offer_time,:client_name,:employer_service_id)');
  
          //変数紐づけ

          $stmt->bindParam(":employer_id",$employer_id,PDO::PARAM_INT);
  
          $stmt->bindParam(":service_id",$service_id,PDO::PARAM_INT);
          
          $stmt->bindParam(":service_date",$employer_service_date,PDO::PARAM_STR);

          $stmt->bindParam(":scheduled_time",$employer_service_scheduled_time,PDO::PARAM_STR);
  
          $stmt->bindParam(":offer_time",$employer_service_offer_time,PDO::PARAM_INT);

          $stmt->bindParam(":client_name",$employer_service_client_name,PDO::PARAM_STR);

          $stmt->bindParam(":employer_service_id",$new_employer_service_id,PDO::PARAM_INT);

          //従業員IDをセット
  
          $target_employer_service_id=$new_employer_service_id;

  
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


        //従業員マスタからデータ取得

        $stmt = $dbh->prepare('select * from employer_master order by employer_id');



        //実行
    
        $stmt->execute();
    
        //  従業員マスタにレコードが存在しない場合はエラー
    
        if($stmt->rowCount() < 1) {
    
           echo "Error: 従業員マスタにレコードが存在しません";
    
           exit;
    
        }
    
    
    
        //プルダウン対応
    
        $line_employer="";
    
    
    
        //実行結果を変数にセット
    
        $result = $stmt->fetchAll(PDO::FETCH_NUM);
    
        foreach($result as $row) {
    
           $employer_id = $row[0];
    
           $employer_name = $row[1];
    
           if($employer_id==$service_employer_id) {
    
               $line_employer .= "<option value='".$employer_id."' selected>".$employer_name."</option>\n";
    
           } else {
    
               $line_employer .= "<option value='".$employer_id."'>".$employer_name."</option>\n";
    
           }
    
        }
    
        /* 結果セット、statementオブジェクトを初期化 */
    
        $result = null;
    
        $stmt = null;
    
    
    
        //サービスマスタからデータ取得
    
        $stmt = $dbh->prepare('select * from service_master order by service_id');
    
    
    
        //実行
    
        $stmt->execute();
    
    
    
        //  部署マスタにレコードが存在しない場合はエラー
    
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
    
           if($service_id==$employer_service_id) {
    
              $line_service .= "<option value='".$service_id."' selected>".$service_name."</option>\n";
    
           } else {
    
              $line_service .= "<option value='".$service_id."'>".$service_name."</option>\n";
    
           }
    
        }
    
    
    
        /* 結果セット、statementオブジェクトを初期化 */
    
        $result = null;
    
        $stmt = null;
    



    //サービス実績一覧に遷移

        header('location: ./helper_employer_service_list.php');

        exit;

?>
