<?php
    error_reporting(E_ALL & ~E_NOTICE & E_WARNING);
?>

<?php
// ログアウトボタンが押された時
    session_start();

    require_once("access.php");
    
    // sessionを削除
    $_SESSION=array();
    session_destroy();

    $alert="<script type='text/javascript'>
        alert('ログアウトしました。');</script>";
        echo $alert;

    header("location: login.html");
?>