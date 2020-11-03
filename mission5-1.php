<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>mission5-1</title>
</head>
<body>

<?php
// DB接続設定
	$dsn = 'データベース名';
	$user = 'ユーザー名';
	$password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
 
//テーブル作成   
    $sql = "CREATE TABLE IF NOT EXISTS KEIZIBAN"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
    . "comment TEXT,"
    . "datetime timestamp"
	.");";
	$stmt = $pdo->query($sql);
    
//「投稿フォーム」
    if(!empty($_POST["str"])&& !empty($_POST["comment"])&& !empty($_POST["pass1"])){//送信されたものがあり、中身が空でないときに以下の処理を行う。
        $pass= $_POST["pass1"];//送信されたパス1を変数に代入する
        if($pass="pass"){//変数パスがpassのとき
            $name = $_POST["str"];//送信された名前を変数に代入する
            $comment = $_POST["comment"];//送信されたコメントを変数に代入する
            $datetime = date("Y/m/d H:i:s");//現在の日時を変数に代入する
            if(!empty($_POST["editflag"])){ // 「編集投稿」　送信された編集番号があり、中身が空でないとき
                $id = $_POST["editflag"]; //変更する投稿番号を指定する       	
                $sql = 'UPDATE KEIZIBAN SET name=:name,comment=:comment,datetime=:datetime WHERE id=:id';//名前、コメント、日時を更新する
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt-> bindParam(':datetime', $datetime, PDO::PARAM_STR);                 
                $stmt->execute();                        
            }else{//「普通投稿」　そうでなければ
                $sql = $pdo -> prepare("INSERT INTO KEIZIBAN (name, comment, datetime) VALUES (:name, :comment, :datetime)");//データベースに、名前、コメント、日時を記録する
                $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql -> bindParam(':datetime', $datetime, PDO::PARAM_STR);                                  
                $sql -> execute();
            }
        }
    }   

//「削除フォーム」
    if (!empty($_POST["delete"])&& !empty($_POST["pass2"])) {//送信されたものがあり、中身が空でないときに以下の処理を行う。
        $pass= $_POST["pass2"];//送信されたパス2を変数に代入する
        if($pass="pass"){//変数パスがpassのとき 	
            $id = $_POST["delete"];
            $sql = 'delete from KEIZIBAN where id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();                   
        }
    } 

//「編集フォーム」（投稿フォームへ飛ばす）   
    if (!empty($_POST["edit"])&& !empty($_POST["pass3"])) {//送信されたものがあり、中身が空でないときに以下の処理を行う。
        $pass= $_POST["pass3"];//送信されたパス3を変数に代入する
        if($pass="pass"){//変数パスがpassのとき
            $edit=$_POST["edit"];//送信された編集番号を変数に代入する                                          
        }        
    }
?>  

<form method= "post" action="mission5-1.php">
【  投稿フォーム  】<br>
名前：       <input type="text" name="str"  
                    value="<?php 
                    if(isset($edit)){$id = $edit ; //
                    $sql = 'SELECT * FROM KEIZIBAN WHERE id=:id ';
                    $stmt = $pdo->prepare($sql);                  // 差し替えるパラメータを含めて記述したSQLを準備する
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT); // その差し替えるパラメータの値を指定する
                    $stmt->execute();                             // SQLを実行する
                    $results = $stmt->fetchAll(); 
                        foreach ($results as $row){
                            echo $row['name'];
                        }
                    }?>" ><br>

コメント：   <input type="text" name="comment"  
                    value="<?php 
                    if(isset($edit)){$id = $edit ; 
                    $sql = 'SELECT * FROM KEIZIBAN WHERE id=:id ';
                    $stmt = $pdo->prepare($sql);                  // 差し替えるパラメータを含めて記述したSQLを準備する
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT); // その差し替えるパラメータの値を指定する
                    $stmt->execute();                             // SQLを実行する
                    $results = $stmt->fetchAll(); 
                        foreach ($results as $row){
                            echo $row['comment'];
                        }
                    }?>" >
<!--編集用の見えないテキストボックス-->
<input type="hidden" name="editflag" value="<?php if(isset($edit)){echo $edit;}?>" ><br>

パスワード： <input type="text" name="pass1"  value="" ><br>
 <input type="submit" value="送信">
 </form>
 
 <form method= "post" action="mission5-1.php">
<br>【  削除フォーム  】<br>
 投稿番号：  <input type = "text" name = "delete" ><br>
 パスワード： <input type="text" name="pass2"  value="" ><br>
  <input type = "submit" value="削除" ><br>
</form>

 <form method= "post" action="mission5-1.php">
<br>【  編集フォーム  】<br>
 投稿番号：  <input type = "text" name = "edit" ><br>
 パスワード： <input type="text" name="pass3"  value="" ><br>
  <input type = "submit" value="編集" ><br><br>
</form>



<?php
echo"---------------------------------------<br>";
echo" 【　投稿一覧　】<br><br>";

    //データベースに書き込まれた全ての投稿をブラウザに表示
 $sql = 'SELECT * FROM KEIZIBAN';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		echo $row['id'].',';
		echo $row['name'].',';
        echo $row['comment'].',';
        echo $row['datetime'];
	    echo "<hr>";
	}
?>  


</body>
</html>