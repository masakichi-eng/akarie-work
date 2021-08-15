<?php
   // 設定ファイルの読み込み

   require_once('./database_config.php');

   //Post Parameter

   $account=$_POST['account'];

   $password=$_POST['password'];

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

   //SQL構文を作成

   $stmt = $dbh->prepare('select id,name from employer_master where account= :account and password = :password');

   //変数紐づけ

   $stmt->bindParam(":account",$account,PDO::PARAM_STR);

   $stmt->bindParam(":password",$password,PDO::PARAM_STR);

   //実行

   $stmt->execute();

   //取得したレコードの数が１かどうか

   if($stmt->rowCount() != 1) {

     move_login();

   }

   //実行結果を変数にセット(1件)

   $result = $stmt->fetch(PDO::FETCH_ASSOC);

   //テーブルより取得した値をセット

   $employer_id = $result['id'];

   $employer_name = $result['name'];

   // keep login information between screens

   session_save_path('/home/m_kona/session/');

   session_start();

   // inititalize session

   $_SESSION = array();

   $_SESSION['employer_id'] = $employer_id;

   $_SESSION['employer_name'] = $employer_name;

   //statementオブジェクトを初期化

   $stmt = null;

   //接続情報を初期化

   $dbh = null;

   if($employer_id != 1){
      header('location: ./helper_employer_service_list.php');
   } else{
      header('location: ./employerlist.php');
   }

   

   exit;

   function move_login() {

       header("location: ./login.php?em=1");

       exit;

   }

?>
