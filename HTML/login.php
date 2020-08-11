<?php
    error_reporting(E_ALL & ~E_NOTICE & E_WARNING);
?>
        
<?php
    session_start();


    require_once("access.php");


    // login.htmlからPORT
    if (isset($_POST["login_submit"])){
        $login_user=$_POST["login_user"];
        $login_password=$_POST["login_password"];
        if ($login_user=="" || $login_password==""){
            $alert="<script type='text/javascript'>
                alert('未入力項目があります。');
                location.href='login.html';
            </script>";
            echo $alert;
        }
        // ユーザー名とパスワードが一致するもの
        $stmt=$pdo->prepare("SELECT user_name,table_name FROM tb_manage WHERE user_name=:login_user AND login_password=:login_password");
        $stmt->bindParam(":login_user", $login_user, PDO::PARAM_STR);
        $stmt->bindParam(":login_password", $login_password, PDO::PARAM_STR);
        $stmt->execute();
        $results=$stmt->fetch();
        $user=$results[0];
        $table=$results[1];
        if ($table==""){
            $alert="<script type='text/javascript'>
                alert('ユーザー名またはパスワードが違います。');
                location.href='login.html';
            </script>";
            echo $alert;
        }else{
            // 他ファイルに渡すため
            $_SESSION["user_name"]=$user;
            $_SESSION["table_name"]=$table;
            // 画面遷移
            header("location: Form.php");
        }
        
    }
?>

