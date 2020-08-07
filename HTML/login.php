<?php
    error_reporting(E_ALL & ~E_NOTICE & E_WARNING);
?>
        
<?php
    session_start();

    $dsn="mysql:dbname=xxx;host=localhost";
    $user="xxx";
    $password="xxx";
    $pdo=new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING));
    // login.htmlからPORT
    if (isset($_POST["login_submit"])){
        $login_user=$_POST["login_user"];
        $login_password=$_POST["login_password"];
        if ($login_user=="" || $login_password==""){
            echo "未入力項目があります。";
            exit;
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
            echo "ユーザー名またはパスワードが違います。";
            exit;
        }else{
            // 他ファイルに渡すため
            $_SESSION["user_name"]=$user;
            $_SESSION["table_name"]=$table;
            // 画面遷移
            header("location: Form.php");
        }
        
    }
?>

