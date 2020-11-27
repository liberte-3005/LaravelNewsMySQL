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

    //エスケープ処理(echoするところにh())
    function h($s) {
        return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
        }

    // MySQLからデータを取得記事
    $id = $_GET['id'];
    $ArticleData = [];

    $query = "SELECT * FROM `article`";
    if($success) {
        $result = mysqli_query($link, $query);
        while($row = mysqli_fetch_array($result)){
            $ArticleData[] = [$row['id'],$row['title'],$row['text']];
        }
    }


    // MySQLからデータを取得コメント
    $Text = $_POST['Text'];
    $DATA = []; 
    $CommentData = []; 
    $commentId = uniqid(); 
    $error_message = [];
    $CommentBord = [];

    $query = "SELECT * FROM `comment`";
    if($success) {
        $result = mysqli_query($link, $query);
        while($row = mysqli_fetch_array($result)){
            $CommentData[] = [$row['comment_id'],$row['id'],$row['comment_text']];
        }
    }

    //コメント部
    //クリックされたリクエストの判別
    if ($_SERVER ['REQUEST_METHOD'] === 'POST' ){

        //titleとtxtの中身が入っているかを確認(empty(空)の!(否定))
        if (!empty($_POST['Text'])){
                //テキストの代入
                $text = $_POST['Text'];
                //新規データ
                $DATA = [ $commentId, $id, $text];
                $CommentData[] = $DATA;

                //コメント追加用のQueryを書く
                $insert_query = "INSERT INTO `comment`(`comment_id`,`id`, `comment_text`) VALUES ('{$commentId}','{$id}','{$Text}')";
                mysqli_query($link, $insert_query);
                header('Location: ' . $_SERVER['REQUEST_URI']);
                exit; 
                }
    }
    if(strlen($_POST['commentText']) > 50){
        $error_message[] = 'コメントは50字以内で入力してください。';
    }
    if(isset($_POST['del'])) {
        //削除ボタンを押したときの処理を書く。
        $delete_query = "DELETE FROM `comment` WHERE `comment_id` = '{$_POST['del']}'";
        mysqli_query($link, $delete_query);
        header('Location: ' . $_SERVER['REQUEST_URI']);
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
 <!--ArticleStart-->
    <?php foreach ($ArticleData as $data) {
        if( $id === $data[0]){ ?>
            <div>
            <h3><?php echo h($data[1]); ?></h3>
            <p><?php echo h($data[2]); ?></P>
        </div>
        <?php
        }
    } ?>

 <!--ArticleEnd-->
    
<!--ErrorMessageStart-->
    <?php if( !empty($error_message) ): ?>
	    <ul class="error_message">
		    <?php foreach( $error_message as $ERROR ): ?>
			    <li><?php echo $ERROR; ?></li>
		    <?php endforeach; ?>
	    </ul>
    <?php endif; ?>
<!--ErrorMessageEnd-->
<hr>
<!--CommentPostStart-->
     <form method="POST" class="comment" >
            <div class='commentContainer'>
                <textarea name="Text" class="commentBox" placeholder="入力してください"></textarea>
            </div>
            <div class="commentButton">
                <input type="submit" value="コメントを書く" class="commentSubmitStyle"　onSubmit="return checkArticle()" name="<?php echo $id ?>">
            </div>
    </form>

<!--CommentPostEnd-->

<!--ContentStart-->
    <div class="postsContainer">
        <?php foreach ($CommentData as $array) {
            if( $id === $array[1]){ ?>
            <div class="commentContent">
                <p><?php echo h($array[2]); ?></p>
                <form method="POST" class="delForm" onSubmit="return checkDelete()">
                    <input type= "hidden" name= "del" value= "<?php echo $array[0]; ?>">
                    <input type="submit" value="記事を削除する" class="deleteArticle" >
                </form>
            </div> 
        <?php }
        }
        ?>
    </div>
<!--ContentStartEnd-->
</section>
<script src="index.js"></script>

</body>
</html>