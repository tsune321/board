<?php
// データベース接続設定
$dsn = 'mysql:dbname=データベース名;host=localhost';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

// フォームの初期化
$edit_name = "";
$edit_comment = "";
$id = "";

// 編集処理
if (!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["ID"]) && !empty($_POST["password"])) {
    $id = $_POST["ID"];
    $password = $_POST["password"];
    $name = $_POST["name"];
    $comment = $_POST["comment"];

    // データを更新するSQLを準備
    $sql = 'UPDATE board_2 SET name=:name, comment=:comment WHERE id=:id AND password=:password';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':password', $password, PDO::PARAM_STR);

    // 実行
    $stmt->execute();
    $id = ""; // 編集後はIDをリセット
}

// 新規投稿
if (!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["password"]) && empty($_POST["ID"])) {
    $name = $_POST["name"];
    $comment = $_POST["comment"];
    $password = $_POST["password"];
    $dateTime = date('Y-m-d H:i:s');

    $sql = "INSERT INTO board_2 (name, comment, password, dateTime) VALUES (:name, :comment, :password, :dateTime)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
    $stmt->bindParam(':password', $password, PDO::PARAM_STR);
    $stmt->bindParam(':dateTime', $dateTime, PDO::PARAM_STR);
    $stmt->execute();
}

// 削除
if (!empty($_POST["delete_number"]) && !empty($_POST["password"])) {
    $id = $_POST["delete_number"];
    $password = $_POST["password"];

    // パスワードの確認
    $sql = "SELECT password FROM board_2 WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch();

    if ($result['password'] === $password) {
        // 削除
        $sql = "DELETE FROM board_2 WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        echo "パスワードが一致しません";
    }
}

// 編集
if (!empty($_POST["edit_number"]) && !empty($_POST["password"])) {
    $id = $_POST["edit_number"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM board_2 WHERE id = :id AND password = :password";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':password', $password, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch();

    if ($result) {
        $edit_name = $result['name'];
        $edit_comment = $result['comment'];
        $id = $result['id'];
    } else {
        echo "パスワードが一致しません";
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-4</title>
</head>
<body>
    <div style="text-align:center;">
        <h1>掲示板</h1>
    </div>
    
    【新規投稿フォーム】
    <form action="" method="post">
        <input type="text" name="name" placeholder="名前" value="<?php echo $edit_name ?>">
        <input type="text" name="comment" placeholder="コメント" value="<?php echo $edit_comment ?>">
        <input type="hidden" name="ID" value="<?php echo $id?>">
        <input type="text" name="password" placeholder="パスワード">
        <input type="submit" name="submit" value="送信"><br>
    </form>
    
    【削除】
    <form action="" method="post">
        <input type="text" name="delete_number" placeholder="削除番号">
        <input type="text" name="password" placeholder="パスワード">
        <input type="submit" name="delete" value="削除">
    </form>
    
    【編集】
    <form action="" method="post">
        <input type="text" name="edit_number" placeholder="編集番号">
        <input type="text" name="password" placeholder="パスワード">
        <input type="submit" name="edit" value="編集">
    </form>

    <?php
    // データを取得して表示
    $sql = 'SELECT * FROM board_2';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll(); 

  // ループして、取得したデータを表示
    foreach ($results as $row) {
        echo'<div style="padding: 10px; margin-bottom: 10px; border: 1px dotted #333333;">';
        echo "ID: " . $row['id']. '<br>';
        echo "名前: " .$row['name']. '<br>';
        echo "コメント:".$row['comment'].'<br>';
        echo "日時:".$row['dateTime'].'<br>';
        echo "</div><hr>";
    }
    ?>
</body>
</html>
