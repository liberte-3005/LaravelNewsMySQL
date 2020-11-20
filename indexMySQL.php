<?php

$dsn = 'mysql:host=localhost;dbname=LaravelNews;charset=utf8';
$user = 'root';
$pass = 'root';

//DB接続


try {
    $dbh = new PDO ($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    //echo '接続成功';
    //SQLの準備
    $sql = 'SELECT * FROM article';
    //SQLの実行
    $stmt = $dbh->query($sql);
    //SQLの結果を受け取る
    $result = $stmt->fetchall(PDO::FETCH_ASSOC);
    //var_dump($result);
    $dbh = null;
} catch(PODException $e) {
    echo '接続に失敗しました'. $e->getMessage();
    exit();
};



$title = ''; 
$text = '';
$id = uniqid(); //IDの自動生成
$now_date = date("Y-m-d H:i:s");
$DATA = []; //一回分の投稿情報
$BOARD = []; //全ての投稿情報
$error_message = [];

//投稿部
//クリックされたリクエストの判別、POSTメゾットは投稿されたという意
if ($_SERVER ['REQUEST_METHOD'] === 'POST' ){

    //titleとtxtの中身が入っているかを確認(empty(空)の!(否定))
    if (!empty($_POST['title']) && !empty($_POST['txt'])){  

        //テキストの代入
        $title = $_POST['title'];
        $text = $_POST['txt'];
        //新規データ
        $DATA = [ $id, $title, $text, $now_date];
        $BOARD[] = $DATA;

        //ファイルに保存する(FILEにBOARDの内容を上書きする)関数、決まりごと
        file_put_contents($FILE, json_encode($BOARD, JSON_UNESCAPED_UNICODE));
        header('Location:'.$_SERVER['SCRIPT_NAME']); 
        exit;
    }
}

//エラーメッセージを表示する
if(empty($_POST['title'])){
    $error_message[] = 'タイトルは必須です。';}
if(empty($_POST['txt'])){
    $error_message[] = '記事は必須です。';}
if(strlen($_POST['title']) > 30){
    $error_message[] = 'タイトルは30字以内で入力してください。';}


?>

<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>Laravel News</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <nav class="navHead">
        <a class="mainTitle" href="/">Laravel News</a>
    </nav>
<section>
    <h2>さぁ、最新のニュースをシェアしましょう</h2>
    <!--ErrorMessageStart-->
    <?php if( !empty($error_message) ): ?>
	    <ul class="error_message">
		    <?php foreach( $error_message as $value ): ?>
			    <li><?php echo $value; ?></li>
		    <?php endforeach; ?>
	    </ul>
    <?php endif; ?>
<!--ErrorMessageEnd-->
<!--PostStart-->
     <form method="POST" class="form" onsubmit="return confirm('投稿してよろしいですか？')">
            <div class='titleContainer'>
                <lavel class='nameFlex'>タイトル：</lavel>
                <input type='text' name='title' class="inputFlex" placeholder="入力してください※30字以内">
            </div>
            <div class='articleContainer'>
                <lavel class='nameFlex'>記事：</lavel>
                <textarea name="txt" cols="50" rows="10" class="inputFlexArticle" placeholder="入力してください"></textarea>
            </div>
            <div class="submitContainer">
                <input type="submit" value="投稿" class="submitStyle">
            </div>
     </form>
<!--PostEnd-->
<hr>
<!--ContentStart-->
    <div class="postsContainer">
        <?php foreach (array_reverse ($result) as $ARTICLE) : ?>
            <div class="post">
                <p class="articleTitle"><?php echo $ARTICLE['title']; ?></p>
                <p class="articleText"><?php echo $ARTICLE['text']; ?></p>
                <a class="postPage" href="http://localhost/text.php?id=<?php echo $ARTICLE[0] ?>">コメントを見る　</a>
            </div> <hr>
        <?php endforeach; ?>
    </div>
<!--ContentStartEnd-->
</section>
<script src="index.js"></script>

</body>
</html>