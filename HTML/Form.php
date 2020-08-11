<?php
    error_reporting(E_ALL & ~E_NOTICE & E_WARNING);
?>

<?php
    session_start();

    require_once("access.php");
    
    ##ログイン時の情報からテーブル名を取り出す。
    if (isset($_SESSION["table_name"])){
        $table=$_SESSION["table_name"];
    }else{
        $alert="<script type='text/javascript'>
            alert('不正な画面遷移です。');
            location.href='login.html';
        </script>";
        echo $alert;
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
        <meta name=”viewport” content=”width=device-width, initial-scale=1, minimum-scale=1, user-scalable=yes”>
        <link rel="stylesheet" href="style_2.css">
    </head>
    
    <body>

    <header class="page-header wrapper">
      <h1><a href=""><img class="logo" src="images/memo1.png"></a></h1>
      <nav>
        <ul class="main-nav">
            <li><a href="logout.php">ログアウト</a></li>
            <li><a href="">お問い合わせ</a></li>
        </ul>
      </nav>
    </header>

    <div class="all_pages">
    <main>
        <ul id="selection">
            <ol><a href="#" class="active" data-id="form1">記録</a></ol>
            <ol><a href="#" data-id="form2">閲覧</a></ol>
        </ul>

        <div class="form_format active" id="form1">
        
            <form method="POST" action="Write.php">

            <ul class="text_submit">
                <ol>
                    <p>カテゴリー</p>
                    <input class="text_form" type="text" name="category">
                    <p>題名</p>
                    <input class="text_form" type="text" name="capital">
                    <p>締切日</p><input class="text_form" type="date" name="last_date" placeholder="締切日">
                    
                </ol>
                <ol class="left_write">
                    <ol><textarea name="memo" placeholder="TEXT" rows="10" cols="40"></textarea></ol>
                    <ol><input type="hidden" name="important" value="0"></ol>
                    <div class="sub_left_write">       
                        <ol><input class="check_box" type="checkbox" name="important" value="1">✿</ol>
                        <ol><input class="user_button" type="submit" name="submit" value="記録"></ol>
                    </div>
                </ol>
            </ul>

            </form>

        </div>

        <div class="form_format form_sub" id="form2">
            <ul class="form_sub_ul">
                <ol>
                    <form method="POST" action="">
                        <p>カテゴリー検索</p>
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
                        <input class="user_button" type="submit" name="search_category_s" value="カテゴリー検索">
                    </form>
                </ol>
                <ol>
                <form method="POST" action="">
                    <p>題名検索</p>
                    <input class="text_form" type="text" name="search_capital" placeholder="題名">
                    <input class="user_button" type="submit" name="search_capital_s" value="題名検索">
                </form>
                </ol>
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
                <small>
                    <span style='color:#666666 text-align: center;'> <?php echo $key["dates"] ;?></span>
                </small><br>
                <details open="close">
                    <summary>
                        <strong> <?php echo $key["capital"]; ?></strong>
                    </summary><br>
                    <ul class="result_list">
                        <ol>
                            <ul class="first_list">
                                <ol style="color: #f37053;"><?php if ($key["important"]=="1") echo "重要!!"; ?></ol>
                                <ol>カテゴリー： <?php echo $key["category"]; ?></ol>
                                <ol>期限： <?php if ($key["last_date"]=="1970/01/01") echo "期限なし"; else echo $key["last_date"];?></ol>
                                <ol>メモ： <?php echo nl2br($key["memo"]); ?></ol>
                            </ul>
                        </ol>
                        
                        <ol><!-- 削除と編集 -->
                            <form method="POST" action="Form2.php">
                            <ul class="second_list">
                                <ol><input type="hidden" name="id" value="<?php echo $key["id"]?>"></ol>
                                <ol><input class="user_button" type="submit" name="delete" value="削除"></ol>
                                <ol><input class="user_button" type="submit" name="edit" value="編集"></ol>
                            </ul>
                            <textarea name="edit_text" placeholder="編集フォーム" rows="7" cols="30"></textarea>

                            </form>
                        </ol>
                    </ul>
                </details>
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
                締切日を指定することで、直近の予定を簡単に把握することができます。<br>                
            </p>
            <p>
                また、✿マークにチェックすることで後で簡単にチェックできます。
            </p>
            <p>
                改善点がある場合は、お問い合わせへどうぞ
            </p>
        </div>
            
        <ul id="selection_sub">
            <ol><a href="#" class="active" data-id="form3">期限順</a></ol>
            <ol><a href="#" data-id="form4">重要</a></ol>
        </ul>

        <?php
        // 締切日順
            $stmt=$pdo->prepare("SELECT * FROM $table WHERE last_date!='' ORDER BY last_date");
            $stmt->execute();
            $results_dates=$stmt->fetchAll();
        // 重要マーク
            $stmt=$pdo->prepare("SELECT * FROM $table WHERE important='1'");
            $stmt->execute();
            $results_imp=$stmt->fetchAll();
            

            
        ?>
            <!-- 出力結果　最大10個 -->
        <div class="form_format_sub active box_color" id="form3">
            <?php $i=0; ?>
            <?php foreach ($results_dates as $key): ?>
                <?php $key["dates"]=date("Y/m/d",strtotime($key["dates"]))?>
                <?php $key["last_date"]=date("Y/m/d",strtotime($key["last_date"]))?>
                <small>
                    <span style='color:#666666 text-align: center;'> <?php echo $key["dates"] ;?></span>
                </small><br>
                <details open="close">
                    <summary>
                        <strong> <?php echo $key["capital"]; ?></strong>
                    </summary><br>
                    <ul class="result_list">
                        <ol>
                            <ul class="first_list">
                                <ol style="color: #f37053;"><?php if ($key["important"]=="1") echo "重要!!"; ?></ol>
                                <ol>カテゴリー： <?php echo $key["category"]; ?></ol>
                                <ol>期限： <?php if ($key["last_date"]=="1970/01/01") echo "期限なし"; else echo $key["last_date"];?></ol>
                                <ol>メモ： <?php echo nl2br($key["memo"]); ?></ol>
                            </ul>
                        </ol>
                        
                        <ol><!-- 削除と編集 -->
                            <form method="POST" action="Form2.php">
                            <ul class="second_list">
                                <ol><input type="hidden" name="id" value="<?php echo $key["id"]?>"></ol>
                                <ol><input class="user_button" type="submit" name="delete" value="削除"></ol>
                                <ol><input class="user_button" type="submit" name="edit" value="編集"></ol>
                            </ul>
                            <textarea name="edit_text" placeholder="編集フォーム" rows="7" cols="30"></textarea>

                            </form>
                        </ol>
                        </ul>
                </details>
            <?php $i++; ?>
            <?php if ($i>=10): break; ?>
            <?php endif; ?>
            <?php endforeach; ?>   
        </div>

        <!-- 出力結果　最大10個 -->
        <div class="form_format_sub box_color" id="form4">
            <?php $i=0; ?>
            <?php foreach ($results_imp as $key): ?>
                <?php $key["dates"]=date("Y/m/d",strtotime($key["dates"]))?>
                <?php $key["last_date"]=date("Y/m/d",strtotime($key["last_date"]))?>
                <small>
                    <span style='color:#666666 text-align: center;'> <?php echo $key["dates"] ;?></span>
                </small><br>
                <details open="close">
                    <summary>
                        <strong> <?php echo $key["capital"]; ?></strong>
                    </summary><br>
                    <ul class="result_list">
                        <ol>
                            <ul class="first_list">
                                <ol style="color: #f37053;"><?php if ($key["important"]=="1") echo "重要!!"; ?></ol>
                                <ol>カテゴリー： <?php echo $key["category"]; ?></ol>
                                <ol>期限： <?php if ($key["last_date"]=="1970/01/01") echo "期限なし"; else echo $key["last_date"];?></ol>
                                <ol>メモ： <?php echo nl2br($key["memo"]); ?></ol>
                            </ul>
                        </ol>
                        
                        <ol><!-- 削除と編集 -->
                            <form method="POST" action="Form2.php">
                            <ul class="second_list">
                                <ol><input type="hidden" name="id" value="<?php echo $key["id"]?>"></ol>
                                <ol><input class="user_button" type="submit" name="delete" value="削除"></ol>
                                <ol><input class="user_button" type="submit" name="edit" value="編集"></ol>
                            </ul>
                            <textarea name="edit_text" placeholder="編集フォーム" rows="7" cols="30"></textarea>

                            </form>
                        </ol>
                        </ul>
                </details>
            <?php $i++; ?>
            <?php if ($i>=10): break; ?>
            <?php endif; ?>
            <?php endforeach; ?>   
        </div>
            



        </aside>
        <script src="main.js"></script>
        </div>
        
    
    
    <footer>
    <div class="whole-footer">
        <h4><a href="">トップへ戻る</a></h4>
      
      <nav>
        <ul class="footer-nav">
            <li><a href="logout.php"class="arrow sample1">ログアウト</a></li>
            <li><a href=""class="arrow sample1">お問い合わせ</a></li>
        </ul>    
      </nav>
      
        <p><small>&copy; 2020 KAZUROG</small></p> 
    </div><!--whole-footer終了!-->
    </footer>
    </body>
</html>