<?php   
    error_reporting(E_ALL & ~E_NOTICE & E_WARNING);
?>

<!DOCTYPE html>
<html lang="ja">
    
    <head>
        <meta charset="utf-8">
        <title>削除・編集</title>
        <meta name="description" content="削除・編集">
        <link rel="stylesheet" href="style.css">
        <meta http-equiv="refresh" content="0;URL='Form.php'"/>
    </head>

<?php
// 直接アクセス防止
    session_start();
    if (isset($_SESSION["table_name"])){
        $table=$_SESSION["table_name"];
    }else{
        $alert="<script type='text/javascript'>
            alert('不正な画面遷移です。');</script>";
            echo $alert;
        header("location: login.html");
    }

    $dsn="mysql:dbname=xxx;host=localhost";
    $user="xxx";
    $password="xxx";
    $pdo=new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING));
    
    // 削除ボタンが押された時
    if (isset($_POST["delete"])&&$_POST["delete"]!=""){
        $delete=$_POST["id"];
        $sql="delete from $table where id=:delete";
            $stmt=$pdo->prepare($sql);
            $stmt->bindParam(":delete",$delete,PDO::PARAM_INT);
            $stmt->execute();
            
            $alert="<script type='text/javascript'>
            alert('削除しました。');</script>";
            echo $alert;

    // 編集ボタンが押された時
    }elseif (isset($_POST["edit"])&&$_POST["edit"]!=""&&
    isset($_POST["edit_text"])&&$_POST["edit_text"]!=""){
        $change=$_POST["id"];
        $edit_text=$_POST["edit_text"];
        if ($edit_text!=""){
            $sql = "UPDATE $table SET memo=:edit_text WHERE id=:change";
	           $stmt = $pdo->prepare($sql);
	           $stmt->bindParam(':edit_text', $edit_text, PDO::PARAM_STR);
	           $stmt->bindParam(':change', $change, PDO::PARAM_INT);
	           $stmt->execute();
	           
                $alert="<script type='text/javascript'>
                alert('編集しました。');</script>";
                echo $alert;
        }    
    }
?>