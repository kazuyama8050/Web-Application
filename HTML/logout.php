<?php
    error_reporting(E_ALL & ~E_NOTICE & E_WARNING);
?>

<?php
// ログアウトボタンが押された時
    session_start();

    $dsn="mysql:dbname=xxx;host=localhost";
    $user="xxx";
    $password="xxx";
    $pdo=new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING));
    
    // sessionを削除
    $_SESSION=array();
    session_destroy();

    $alert="<script type='text/javascript'>
        alert('ログアウトしました。');</script>";
        echo $alert;

    header("location: login.html");
?>