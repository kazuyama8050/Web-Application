<?php
    error_reporting(E_ALL & ~E_NOTICE & E_WARNING);
?>

<?php
    session_start();

    $dsn="mysql:dbname=xxx;host=localhost";
    $user="xxx";
    $password="xxx";
    $pdo=new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING));

    ##ログイン時の情報からテーブル名を取り出す。
    if (isset($_SESSION["table_name"])){
        $table=$_SESSION["table_name"];
    }else{
        $alert="<script type='text/javascript'>
            alert('不正な画面遷移です。');</script>";
            echo $alert;
        header("location: login.php");
    }
   
// 上からID、カテゴリー、題名、メモ欄、記録した日付、締切日、重要な印
    $sql="CREATE TABLE IF NOT EXISTS $table"
    ."("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "category TEXT,"
    . "capital TEXT,"
    . "memo TEXT,"
    . "dates DATE,"
    . "last_date DATE,"
    . "important TEXT"
    .");";
    $stmt=$pdo->query($sql);

?>


        
<!DOCTYPE html>
<html lang="ja">
    
    <head>
        <meta charset="utf-8">
        <title>ホーム画面</title>
        <meta name="description" content="ホーム画面">
        <meta name=”viewport” content=”width=device-width”, initial-scale=1”>
        <link rel="stylesheet" href="style.css">
    </head>
    
    <body>

    <header class="page-header wrapper">
      <h1><a href="">メ  モ  帳</a></h1>
      <nav>
        <ul class="main-nav">
            <li><a href="logout.php">ログアウト</a></li>
            <li><a href="">お問い合わせ</a></li>
        </ul>
      </nav>
    </header>

    <div class="all_pages">
    <main>
    <div class="container">
        <div class="form_format">
        
        <form method="POST" action="Write.php">
            <p style="margin-tpo: 20px">カテゴリー</p>
            <input class="text_form" type="text" name="category">
            <p>題名</p>
            <input class="text_form" type="text" name="capital">
            <p>締切日</p><input class="text_form" type="date" name="last_date" placeholder="締切日">
            <div class="text_submit">
                <ol>
                    <textarea name="memo" placeholder="TEXT" rows="8" cols="40"></textarea>
                </ol>
                <ol><input type="hidden" name="important" value="0"></ol>
                <ol><input class="user_button" type="submit" name="submit" value="記録"></ol>
            </div>        
            <ol><input class="check_box" type="checkbox" name="important" value="1">重要</ol>
        </form>
        </div>

        <div class="form_format3">
        <form method="POST" action="">
        <p style="margin-top: 20px">カテゴリー検索</p>
        <!-- データベースからカテゴリーの選択肢を抽出 -->
            <select name="search_category">
                <?php $sql="SELECT DISTINCT category FROM $table"; ?>
                <?php $stmt=$pdo->query($sql); ?>
                <?php $results=$stmt->fetchAll(); ?>
                
                <?php foreach ($results as $row): ?>
                    <?php $overlap=$row["category"]; ?>
                    <option value="<?php echo $overlap; ?>"><?php echo $overlap; ?></option>
                <?php endforeach; ?>  
            </select>
            <input class="user_button" style="margin-bottom: 20px" type="submit" name="search_category_s" value="カテゴリー検索">
        </form>
            
        <form method="POST" action="">
            <p>題名検索</p>
            <input class="text_form" style="margin-bottom: 5px" type="text" name="search_capital" placeholder="題名">
            <input class="user_button" type="submit" name="search_capital_s" value="題名検索">
        </form>
        </div>
    </div>

        
        
        <?php
            // カテゴリーで選択
            if (isset($_POST["search_category"])&&$_POST["search_category"]!=""&&($_POST["search_category_s"])){
                $search_category=$_POST["search_category"];
                if ($search_category!=""){
                    $stmt=$pdo->prepare("SELECT * FROM $table WHERE category LIKE :search_category");
                    $stmt->bindParam(":search_category", $search_category, PDO::PARAM_STR);
                    $stmt->execute();
                    $results=$stmt->fetchAll();
                    $count=count($results);
                }
            // 題名で部分一致
            }elseif (isset($_POST["search_capital_s"])){
                $search_capital=$_POST["search_capital"];
                if ($search_capital!=""){
                    $search_capital="%".$search_capital."%";
                    $stmt=$pdo->prepare("SELECT * FROM $table WHERE capital LIKE :search_capital");
                    $stmt->bindParam(":search_capital", $search_capital, PDO::PARAM_STR);
                    $stmt->execute();
                    $results=$stmt->fetchAll();
                    $count=count($results);
                }else{
                    $alert="<script type='text/javascript'>
                    alert('検索したい題名を入力してください。');</script>";
                    echo $alert;
                    exit;
                }
            }
        ?>
        
            <!-- 件数を表示 -->
            <p style="text-align: center;"> <?php if (isset($_POST["search_category_s"])||isset($_POST["search_capital_s"])): echo $count."件ヒットしました。"; ?></p> 
            <?php endif; ?>
            <!-- 出力結果 -->
        <?php if (isset($_POST["search_category_s"])||isset($_POST["search_capital_s"])): ?>
            <?php foreach ($results as $key): ?>
                <?php $key["dates"]=date("Y/m/d",strtotime($key["dates"]))?>
                <?php $key["last_date"]=date("Y/m/d",strtotime($key["last_date"]))?>
                <div class="box">
                    <small><span style='color:#666666 text-align: center;'> <?php echo $key["date"] ;?></span></small><br>
                    <details  style=" width:auto; padding: 2px; margin-bottom: 5px; margin-left: 10 border: 1px solid #cccccc;" 
                    open="close"><summary style="background-color:#EEEEEE width: auto; text-align: center;">
                    <strong> <?php echo $key["capital"]; ?></strong></summary>
                    <br>
                    <div class="container">
                        <div class="form_format2">
                            <ul class="result_list">
                            <ol style="color: #f37053;"><?php if ($key["important"]=="1") echo "重要!!"; ?></ol>
                            <ol>番号： <?php echo $key["id"]; ?></ol>
                            <ol>カテゴリー： <?php echo $key["category"]; ?></ol>
                            <ol>期限： <?php if ($key["last_date"]=="1970/01/01") echo "期限なし"; else echo $key["last_date"];?></ol>
                            <ol>メモ： <?php echo nl2br($key["memo"]); ?></ol>
                            </ul>
                        </div>

                        <!-- 削除と編集 -->
                        <div class="form_format2">
                            <form method="POST" action="Form2.php">
                            <ul style="display: flex; margin-right: 80px; margin-bottom: 10px;">
                            <ol><input type="hidden" name="id" value="<?php echo $key["id"]?>"></ol>
                            <ol><input class="user_button" type="submit" name="delete" value="削除"></ol>
                            <ol><input class="user_button" type="submit" name="edit" value="編集"></ol>
                            </ul>
                            <textarea style="margin-right: 30px;" name="edit_text" placeholder="編集フォーム" rows="7" cols="50"></textarea>

                            </form>
                        </div>
                    </div>
                    </details>
                </div>
            <?php endforeach; ?>   
            <?php else: ?>
        <?php endif; ?>
        
        </main>

        <aside>
        <div class="attension_s">
            <p>
                このサイトは様々なフィルターを掛けることができるメモ帳です。
            </p>
            <p>
                締切日をい指定することで、直近の予定を簡単に把握することができます。
            </p>
            <p>
                改善点がある場合は、お問い合わせへどうぞ
            </p>
        </div>
            
        <div class="aside_submit">
            <form class="form_format" method="POST" action="">
                <input class="user_button" type="submit" name="order_search" value="期限順">
            </form>

            <form class="form_format" method="POST" action="">
                <input class="user_button" type="submit" name="important_search" value="重要">
        </div>

        <?php
        // 締切日順
            if (isset($_POST["order_search"])){
                $stmt=$pdo->prepare("SELECT * FROM $table WHERE last_date!='' ORDER BY last_date");
                $stmt->execute();
                $results=$stmt->fetchAll();
        // 重要マーク
            }else{
                $stmt=$pdo->prepare("SELECT * FROM $table WHERE important='1'");
                $stmt->execute();
                $results=$stmt->fetchAll();
            }

            
        ?>
            <!-- 出力結果　最大10個 -->
            <?php $i=0; ?>
            <?php foreach ($results as $key): ?>
                <?php $key["dates"]=date("Y/m/d",strtotime($key["dates"]))?>
                <?php $key["last_date"]=date("Y/m/d",strtotime($key["last_date"]))?>
                <div class="box">
                    <small><span style='color:#666666 text-align: center;'> <?php echo $key["date"] ;?></span></small><br>
                    <details  style=" width:auto; padding: 2px; margin-bottom: 5px; margin-left: 10 border: 1px solid #cccccc;" 
                    open="close"><summary style="background-color:#EEEEEE width: auto; text-align: center;">
                    <strong> <?php echo $key["capital"]; ?></strong></summary>
                    <br>
                    <div class="container">
                        <div class="form_format2">
                            <ul class="result_list">
                            <ol style="color: #f37053;"><?php if ($key["important"]=="1") echo "重要!!"; ?></ol>
                            <ol>番号： <?php echo $key["id"]; ?></ol>
                            <ol>カテゴリー： <?php echo $key["category"]; ?></ol>
                            <ol>期限： <?php if ($key["last_date"]=="1970/01/01") echo "期限なし"; else echo $key["last_date"];?></ol>
                            <ol>メモ： <?php echo nl2br($key["memo"]); ?></ol>
                            </ul>
                        </div>

                        <div class="form_format2">
                            <form method="POST" action="Form2.php">
                            <ul style="display: flex; margin-right: 80px; margin-bottom: 10px;">
                            <ol><input type="hidden" name="id" value="<?php echo $key["id"]?>"></ol>
                            <ol><input class="user_button" type="submit" name="delete" value="削除"></ol>
                            <ol><input class="user_button" type="submit" name="edit" value="編集"></ol>
                            </ul>
                            <textarea style="margin-right: 30px;" name="edit_text" placeholder="編集フォーム" rows="7" cols="50"></textarea>

                            </form>
                        </div>
                    </div>
                    </details>
                </div>
            <?php $i++; ?>
            <?php if ($i>=10): break; ?>
            <?php endif; ?>
            <?php endforeach; ?>   
            



        </aside>
        </div>
        
    
    
    <footer>
    <div class="whole-footer">
        <h4 id="scroll-top"><a href="">トップへ戻る</a></h4>
      <div>
      <nav>
        <ul class="footer-nav">
        <li><a href="logout.php"class="arrow sample1">ログアウト</a></li>
        <li><a href=""class="arrow sample1">お問い合わせ</a></li>
        </ul>    
      </nav>
      </div>
        <p><small><a>&copy; 2020 KAZUROG<a></small></p> 
    </div><!--whole-footer終了!-->
    </footer>
    </body>
</html>