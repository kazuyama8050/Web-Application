<?php
    error_reporting(E_ALL & ~E_NOTICE & E_WARNING);
?>

<!DOCTYPE html>
<html lang="ja">
    
    <head>
        <meta charset="utf-8">
        <title>新規登録画面</title>
        <meta name="description" content="新規登録画面">
        <meta name=”viewport” content=”width=device-width”, initial-scale=1”>
        <link rel="stylesheet" href="register.css">
    </head>
    
    <body>
    <div class="format">
        <p class="form-title">新 規 登 録</p>
        <form method="POST" action="">
            <ul class="ul-content">
                <ol>ユ ー ザ ー 名：<input class="info_format" type="text" name="user_name" maxlength="10" pattern="^[0-9A-Za-z]+$"><br><small class="small_format">半角英数字10文字以内</small></ol>
                <ol style="margin-left: 21px">ア ド レ ス：<input class="info_format" type="text" name="address" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$"></ol>
                <ol>パ ス ワ ー ド：<input class="info_format" type="text" name="register_password" maxlength="8" pattern="^[0-9A-Za-z]+$"><br><small class="small_format">半角英数字8文字以内</small></ol>
                <ol><input class="submit_form" type="submit" name="register_submit" value="登録"></ol>
            </ul>
        </form>
    </div>
    <div class="register_form">
        <p>ログインは<a href="login.html">こちら</a></p>
    </div>
        
<?php
        require_once("access.php");
    
    // 上からID、ユーザー名、メールアドレス、パスワード、Form.phpで使用するテーブル名
    $sql="CREATE TABLE IF NOT EXISTS tb_manage"
    ."("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "user_name TEXT,"
    . "address TEXT,"
    . "login_password TEXT,"
    . "table_name TEXT"
    .");";
    $stmt=$pdo->query($sql);
    
    if (isset($_POST["register_submit"])){
        $user_name=$_POST["user_name"];
        $address=$_POST["address"];
        $register_password=$_POST["register_password"];
        if ($user_name=="" || $address=="" || $register_password==""){
            $alert="<script type='text/javascript'>
            alert('未入力項目があります');</script>";
            echo $alert;
            exit;
        }
        if ($user_name==$register_password){
            $alert="<script type='text/javascript'>
            alert('ユーザー名とパスワードは違うものを登録してください');</script>";
            echo $alert;
            exit;
        }
        // ユーザー名が既に存在する場合
        $stmt=$pdo->prepare("SELECT user_name FROM tb_manage WHERE user_name=:user_name");
        $stmt->bindParam(":user_name", $user_name, PDO::PARAM_STR);
        $stmt->execute();
        $results=$stmt->fetch();
        $result=$results[0];
        if ($result!=""){
            $alert="<script type='text/javascript'>
            alert('登録済みのユーザー名です。');</script>";
            echo $alert;
            exit;
        }
        // データベースにユーザー情報を登録
        // テーブル名はID＋ユーザー名
        $table_name="tb".$user_name;
        $sql=$pdo->prepare("INSERT INTO tb_manage (user_name,address,login_password,table_name) VALUES (:user_name, :address, :register_password, :table_name)");
        $sql->bindParam(":user_name", $user_name, PDO::PARAM_STR);
        $sql->bindParam(":address", $address, PDO::PARAM_STR);
        $sql->bindParam(":register_password", $register_password, PDO::PARAM_STR);
        $sql->bindParam(":table_name", $table_name, PDO::PARAM_STR);
        $sql->execute();
        $alert="<script type='text/javascript'>
                alert('登録しました。');
                location.href='login.html';
            </script>";
            echo $alert;
    }
    
?>
</body>
</html>