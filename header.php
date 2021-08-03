<?php
/*
 * タイトルを指定してヘッダーを作成する
 * @param $title
 * @return string
 */
function getHeader($title){
  return <<<EOF
    <head>
        <meta charset="utf-8" />
        <title>SimpleMemo | {$title}</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="./main.css" />
        <script defer src="./js/all.js"></script>
    </head>
EOF;
}
