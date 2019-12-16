<!doctype html>
<html lang="ja">
<head>
<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<!-- Bootstrap CSS -->
<link rel="stylesheet" href="../css/style.css">

<title>PHP</title>
</head>
<body>
<header>
<h1 class="font-weight-normal">PHP</h1>    
</header>

<main>
<h2>Practice</h2>
<pre>
<?php
    try{
        // IPアドレスはMySQLコンテナIDを取得後、docker exec -it xxxx bashでコンテナ内部に入り、のhost-iで確認
        $db = new PDO('mysql:dbname=mydb;host=172.18.0.2;charset=utf8',
        'root', 'pass');
    } catch (PDOException $e) {
        echo 'DB接続エラー：'. $e->getMessage();
    }
    $records = $db -> query("SELECT * FROM my_items");
    while ($record = $records -> fetch()) {
        print($record['item_name']. "\n");
    }
?>
</pre>
</main>
</body>    
</html>