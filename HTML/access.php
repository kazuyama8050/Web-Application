
<?php
    $dsn="mysql:dbname=xxx;host=localhost";
    $user="xxx";
    $password="xxx";
    try{
        $pdo=new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING));
    }catch (PDOException $e){
        $error=$e->getMessage();
    }
?>