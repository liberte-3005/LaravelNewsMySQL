<?php
    $user = 'root';
    $password = 'root';
    $db = 'LaravelNews'; //各々で作ったDBの名前をここに入れる
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
   
    $CommentData = [];
    $comment_id = '';
    $id = '';
    $comment_text = '';
    
    // MySQLからデータを取得
    $query = "SELECT * FROM `comment`";
    if($success) {
        $result = mysqli_query($link, $query);
        while($row = mysqli_fetch_array($result)){
            $ArticleData[] = [$row['comment_id'],$row['id'],$row['comment_text']];
        }
    }

    $title = $_POST['title'];
    $text = $_POST['text'];
    $id = uniqid(); //IDの自動生成
    $DATA = []; //一回分の投稿情報
    $BOARD = []; //全ての投稿情報
    $error_message = [];


$id = $_GET['id'];
$text = '';
$DATA = []; 
$COMMENT_BOX = []; 
$commentId = uniqid(); 
$error_message = [];

//コメントを取得
foreach ($comment_data as $index => list($key, $comment_id)){
$comment_box[] = $comment_data[$index];
if ($comment_id == $id) {
  $COMMENT_BOX[] = $comment_data[$index];
}
}



//コメント部
//クリックされたリクエストの判別
if ($_SERVER ['REQUEST_METHOD'] === 'POST' ){

    //titleとtxtの中身が入っているかを確認(empty(空)の!(否定))
    if (!empty($_POST['txt'])){  
        if(strlen($_POST['txt']) > 50){
            $error_message[] = 'コメントは50字以内で入力してください。';
        }else{
        //テキストの代入
        $text = $_POST['txt'];
        //新規データ
        $DATA = [ $commentId, $id, $text];
        $comment_box[] = $DATA;

                //コメント追加用のQueryを書く
                $insert_query = "INSERT INTO `comment`(`id`,`title`, `text`) VALUES ('{$id}','{$title}','{$text}')";
                mysqli_query($link, $insert_query);
                header('Location: ' . $_SERVER['SCRIPT_NAME']);
    }
  }
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
        <a class="title" href="/">Laravel News</a>
    </nav>

 <section>
 <!--ArticleStart-->
    <div>
        <h3><?php echo $page_data[1]; ?></h3>
        <p><?php echo $page_data[2]; ?></P>
    </div>
 <!--ArticleEnd-->
    
<!--ErrorMessageStart-->
    <?php if( !empty($error_message) ): ?>
	    <ul class="error_message">
		    <?php foreach( $error_message as $value ): ?>
			    <li><?php echo $value; ?></li>
		    <?php endforeach; ?>
	    </ul>
    <?php endif; ?>

<!--ErrorMessageEnd-->
<!--CommentPostStart-->
     <form method="POST" class="comment" >
            <div class='commentContainer'>
                <textarea name="txt" class="commentBox" placeholder="入力してください"></textarea>
            </div>
            <div class="commentButton">
                <input type="submit" value="コメントを書く" class="commentSubmitStyle" name="<?php echo $id ?>">
            </div>
    </form>

<!--CommentPostEnd-->
<hr>
<!--ContentStart-->
    <div class="postsContainer">
        <?php foreach ((array) $COMMENT_BOX as $DATA) : ?>
            <div class="commentContent">
                <p><?php echo $DATA[2]; ?></p>
                <p><input type="submit" value="削除" class="deleteComment"></p>
            </div> 
        <?php endforeach; ?>
    </div>
<!--ContentStartEnd-->
</section>
<script src="index.js"></script>

</body>
</html>