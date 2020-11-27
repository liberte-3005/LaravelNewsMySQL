<?php
    $user = 'root';
    $password = 'root';
    $db = 'LaravelNews';
    $host = 'localhost';
    $port = 3306;
    $link = mysqli_init();
    $success = mysqli_real_connect(
      $link,
      $host,
      $user,
      $password,
      $db,
      $port
    );
   
    $ArticleData = [];
    $id = '';
    $title = '';
    $text = '';

    //エスケープ処理(echoするところにh())
    function h($s) {
        return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
        }
    
    // MySQLからデータを取得
    $query = "SELECT * FROM `article`";
    if($success) {
        $result = mysqli_query($link, $query);
        while($row = mysqli_fetch_array($result)){
            $ArticleData[] = [$row['id'],$row['title'],$row['text']];
        }
    }

    $title = $_POST['title'];
    $text = $_POST['text'];
    $id = uniqid(); //IDの自動生成
    $DATA = []; //一回分の投稿情報
    $BOARD = []; //全ての投稿情報
    $error_message = [];

    //投稿部
    //クリックされたリクエストの判別
    if ($_SERVER ['REQUEST_METHOD'] === 'POST' ){
        //titleとtxtの中身が入っているかを確認(empty(空)の!(否定))
        if (!empty($_POST['title']) && !empty($_POST['text'])){

            //新規データ
            $DATA = [$id,$title,$text];
            $BOARD[] = $DATA;

            //記事追加用のQueryを書く
            $insert_query = "INSERT INTO `article`(`id`,`title`, `text`) VALUES ('{$id}','{$title}','{$text}')";
            mysqli_query($link, $insert_query);
            header('Location: ' . $_SERVER['SCRIPT_NAME']);
            exit; 

            //エラーメッセージを表示する
            }else{
                if(empty($_POST['title']))$error_message[] = 'タイトルは必須です。';
                if(empty($_POST['text']))$error_message[] = '記事は必須です。';
            }
        }
        if(strlen($_POST['title']) > 30){
        $error_message[] = 'タイトルは30字以内で入力してください。';
    }
    if(isset($_POST['del'])) {
        //削除ボタンを押したときの処理を書く。
        $delete_query = "DELETE FROM `article` WHERE `id` = '{$_POST['del']}'";
        mysqli_query($link, $delete_query);
        header('Location: ' . $_SERVER['SCRIPT_NAME']);
        exit;
    }

?>

<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>Laravel News</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <nav class="navHead">
        <a class="mainTitle" href="http://localhost/indexMySQL.php">Laravel News</a>
    </nav>
<section>
    <h2>さぁ、最新のニュースをシェアしましょう</h2>
    <!--ErrorMessageStart-->
    <?php if( !empty($error_message) ): ?>
	    <ul class="error_message">
		    <?php foreach( $error_message as $value ): ?>
			    <li><?php echo h($value); ?></li>
		    <?php endforeach; ?>
	    </ul>
    <?php endif; ?>
<!--ErrorMessageEnd-->
<!--PostStart-->
     <form method="POST" class="newForm" onSubmit="return checkArticle()">
            <div class='titleContainer'>
                <lavel class='nameFlex'>タイトル：</lavel>
                <input type='text' name='title' class="inputFlex" placeholder="入力してください※30字以内">
            </div>
            <div class='articleContainer'>
                <lavel class='nameFlex'>記事：</lavel>
                <textarea name="text" cols="50" rows="10" class="inputFlexArticle" placeholder="入力してください"></textarea>
            </div>
            <div class="submitContainer">
                <input type="submit" value="投稿" class="submitStyle">
            </div>
     </form>
<!--PostEnd-->
<hr>
<!--ContentStart-->
    <div class="postsContainer">
        <?php foreach (array_reverse ($ArticleData) as $ARTICLE) : ?>
            <div class="post">
                <p class="articleTitle"><?php echo h($ARTICLE[1]); ?></p>
                <p class="articleText"><?php echo h($ARTICLE[2]); ?></p>
                <p class="postPage"><a href="http://localhost/textMySQL.php?id=<?php echo $ARTICLE[0] ?>">コメントを見る</a></p>
                <form method="POST" class="delForm" onSubmit="return checkDelete()">
                    <input type= "hidden" name= "del" value= "<?php echo $ARTICLE[0]; ?>">
                    <input type="submit" value="記事を削除する" class="deleteArticle" >
                </form>
            </div> <hr>
        <?php endforeach; ?>
    </div>
<!--ContentStartEnd-->
</section>
<script src="index.js"></script>

</body>
</html>