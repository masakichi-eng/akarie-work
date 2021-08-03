<?php

  require_once('./errorlist.php');
  include_once "./header.php";
  echo getHeader("ログイン");


  if(isset($_GET['em'])) {
      $error_no=$_GET['em'];
  } else {
      $error_no=0;
  }

  // ファイル内容を変数に取り込む
  $fp=fopen('./login.html','r');

  // ファイルの最後まで処理を行う
  while(!feof($fp)) {
     // 1行ずつファイルを読み込み変数にセット
     $line=fgets($fp);
     // 置き換え
     $lines=str_replace("<###ERROR###>",$error_msg[$error_no],$line);
     echo $lines;
  }

  fclose($fp);

?>
