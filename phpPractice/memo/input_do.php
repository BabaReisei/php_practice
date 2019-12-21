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
    try {
        $db = new PDO('mysql:dbname=mydb;host=172.18.0.2;charset=utf8','root','pass');
        $db -> exec('INSERT INTO memos SET memo="'. $_POST['memo']. '", created_at=NOW()');
        print ($_POST['memo']);
    } catch (PDOException $e) {
        echo 'DB接続エラー：'. $e -> getMessage();
    }
    ?>
</pre>
</main>
</body>    
</html>