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


    //  セッション処理

    session_save_path('/home/m_kona/session/');

    session_start();

    $login_name=$_SESSION['employer_name'];

    $login_id=$_SESSION['employer_id'];

    // POST PARAMETER

    $service_name=$_POST['service_name'];

    $service_time=$_POST['service_time'];
    
    $service_price=$_POST['service_price'];

    $service_id=$_POST['service_id'];

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

      //情報削除
  
      if($submit_option=='3') {
  
          $stmt = $dbh->prepare('delete from service_master where service_id=:service_id');
  
          //変数紐づけ
  
          $stmt->bindParam(":service_id",$service_id,PDO::PARAM_INT);
  
          //削除なので従業員IDなし
  
          $target_service_id="";
  
      //情報変更
  
      } else if($submit_option=='1') {
  
          $stmt = $dbh->prepare('update service_master set name=:name,time=:time,price=:price where service_id=:service_id');
  
          //変数紐づけ
  
          $stmt->bindParam(":name",$service_name,PDO::PARAM_STR);
  
          $stmt->bindParam(":time",$service_time,PDO::PARAM_INT);
          
          $stmt->bindParam(":price",$service_price,PDO::PARAM_INT);
  
          $stmt->bindParam(":service_id",$service_id,PDO::PARAM_INT);
  
          //従業員IDをセット
  
          $target_service_id=$service_id;
  
  
  
      //  新規追加
  
      } else {
        //  id と user_idは自動的に付与（+1）

        $stmt=$dbh->prepare('select max(id)+1 id,max(service_id)+1 service_id from service_master');

        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $new_id=$result['id'];

        if (is_null($new_id)){
            $new_id = 1;
        }

        $new_service_id=$result['service_id'];

        if (is_null($new_id)){
            $new_service_id = 1;
        }

        $stmt = null;


        $stmt=$dbh->prepare('insert into service_master(id,name,time,price,service_id)
        values(:id,:name,:time,:price,:service_id)');
  
          //変数紐づけ

          $stmt->bindParam(":id",$new_id,PDO::PARAM_INT);
  
          $stmt->bindParam(":name",$service_name,PDO::PARAM_STR);
  
          $stmt->bindParam(":time",$service_time,PDO::PARAM_INT);
          
          $stmt->bindParam(":price",$service_price,PDO::PARAM_INT);
  
          $stmt->bindParam(":service_id",$new_service_id,PDO::PARAM_INT);
  
          //従業員IDをセット
  
          $target_service_id=$new_service_id;

  
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



    //サービス一覧に遷移

        header('location: ./service_list.php');

        exit;

?>
