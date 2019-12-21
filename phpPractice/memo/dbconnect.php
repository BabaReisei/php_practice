<?php
    try{
        $db = new PDO('mysql:dbname=mydb;host=172.18.0.2;charset=utf8',
        'root', 'pass');
    } catch (PDOException $e) {
        echo 'DB接続エラー：'. $e -> getMessage();
    }
?>