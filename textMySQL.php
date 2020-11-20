<?php


//var_dump($_); 




$id = $_GET['id'];
$FILE = './article.txt';
$file = json_decode(file_get_contents($FILE));
$page_data = [];


$COMMENT_DATA = './comments.txt';
$comment_data = json_decode(file_get_contents($COMMENT_DATA));
$comment_box = []; 
$text = '';
$DATA = []; 
$COMMENT_BOX = []; 
$commentId = uniqid(); 
$error_message = [];

//indexを取得
foreach ($file as $index => list($ID)){
    if ($ID == $id){
        $page_data = $file[$index];
    }
}

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

        //ファイルに保存する(FILEにBOARDの内容を上書きする)関数、決まりごと
        file_put_contents($COMMENT_DATA, json_encode($comment_box, JSON_UNESCAPED_UNICODE));
        header('Location:'.$_SERVER['REQUEST_URI']); 
        exit;
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
            </div> 
        <?php endforeach; ?>
    </div>
<!--ContentStartEnd-->
</section>
<script src="index.js"></script>

</body>
</html>




//データの取得
function getAllArticleData() {
    $dbh = dbConnect();
    //SQLの準備
    $sql = 'SELECT * FROM article';
    //SQLの実行
    $stmt = $dbh->query($sql);
    //SQLの結果を受け取る
    $result = $stmt->fetchall(PDO::FETCH_ASSOC);
    return $result;
    $dbh = null;
}
//取得したデータを表示
$ArticleData = getAllArticleData();
