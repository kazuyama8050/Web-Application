<?php
    error_reporting(E_ALL & ~E_NOTICE & E_WARNING);
?>

<!DOCTYPE html>
<html lang="ja">
    
    <head>
        <meta charset="utf-8">
        <title>投稿</title>
        <meta name="description" content="投稿">
        <link rel="stylesheet" href="style.css">
        <meta http-equiv="refresh" content="0;URL='Form.php'"/>
    </head>

<?php
    session_start();
    if (isset($_SESSION["table_name"])){
        $table=$_SESSION["table_name"];
    }else{
        $alert="<script type='text/javascript'>
            alert('不正な画面遷移です。');
            location.href='login.html';
        </script>";
        echo $alert;
    }

    
    require_once("access.php");

    // Form.phpで記録ボタンが押されたとき
    if (isset($_POST["submit"])){
        $date=date("Y/m/d");
        $category=$_POST["category"];
        $capital=$_POST["capital"];
        $memo=$_POST["memo"];
        $important=$_POST["important"];
        // 締切日と重要印は任意
        if ($category=="" || $capital=="" || $memo==""){
            $alert="<script type='text/javascript'>
            alert('未入力フォームがあります。');</script>";
            echo $alert;
            exit;
        }
        // データベースに書き込み
        $last_date=$_POST["last_date"];
        $sql=$pdo->prepare("INSERT INTO $table (category,capital,memo,dates,last_date,important) VALUES (:category, :capital, :memo, :dates, :last_date, :important)");
        $sql->bindParam(":category", $category, PDO::PARAM_STR);
        $sql->bindParam(":capital", $capital, PDO::PARAM_STR);
        $sql->bindParam(":memo", $memo, PDO::PARAM_STR);
        $sql->bindParam(":dates", $date, PDO::PARAM_STR);
        $sql->bindParam(":important", $important, PDO::PARAM_INT);
        if (!empty($last_date)){
            $sql->bindParam(":last_date", $last_date, PDO::PARAM_STR);
        }else{
            $sql->bindParam(":last_date", $last_date, PDO::PARAM_NULL);
        }
        $sql->execute();
    }
        
?>