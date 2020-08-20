<!DOCTYPE html>
    <html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>5-1</title>
</head>
<body> 
<?php

	$dsn='mysql:dbname="データベース名";host=localhost';
	$user = 'ユーザー名';
    $password = 'パスワード';
    
    /*テーブル作成（別ファイル内）
    <?php
        $dsn='mysql:dbname="データベース名";host=localhost';
        $user = 'ユーザー名';
        $password = 'パスワード';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

        $sql = "CREATE TABLE IF NOT EXISTS mission5（テーブル名）"
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "name char(32),"
        . "comment TEXT,"
        . "time TEXT,"
        . "password TEXT"
        .");";
        $stmt = $pdo->query($sql);
    ?>
    */

	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    $sql = $pdo -> prepare("INSERT INTO mission5 (name, comment, time, password) VALUES (:name, :comment, :time, :password)");



                
    //１投稿動作            
            //氏名が入力されたら次に
            if(!empty($_POST["name"])){
                //コメントが入力されたら次に
                if(!empty($_POST["comment"])){
                    //パスワードが入力されたら次に
                    if(!empty($_POST["pass"])){
                          
                        if(empty($_POST["e"])){

                            $name = $_POST["name"];
                            $com = $_POST["comment"];
                            $time = date ( "Y/m/d  H:i:s" );
                            $pass = $_POST["pass"];

                            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                            $sql -> bindParam(':comment', $com, PDO::PARAM_STR);
                            $sql -> bindParam(':time', $time, PDO::PARAM_STR);
                            $sql -> bindParam(':password', $pass, PDO::PARAM_STR);                         
                            $sql -> execute();

                        }
                        else{

                            $Editnum = $_POST["e"];
                            $name = $_POST["name"];
                            $com = $_POST["comment"];
                            $time = date ( "Y/m/d  H:i:s" );
                            $pass = $_POST["pass"];
                            
                            

                            $sql = 'UPDATE mission5 SET name=:name,comment=:comment,time=:time,password=:password where id=:id';
                            $stmt = $pdo->prepare($sql);

                            $stmt -> bindParam(':id', $Editnum, PDO::PARAM_INT);
                            $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
                            $stmt -> bindParam(':comment', $com, PDO::PARAM_STR);
                            $stmt -> bindParam(':time', $time, PDO::PARAM_STR);
                            $stmt -> bindParam(':password', $pass, PDO::PARAM_STR);                         
                            $stmt -> execute();

                        }
                          
                        
                    }//パスあり終
                    else{
                        echo "パスワードが入力されていません<br>";
                        }
                }//コメントあり終
                else{
                    echo "コメントが入力されていません<br>";
                }
               
            }//送信終


     //2削除動作
        //削除番号が入力されたら次に
        if(!empty($_POST["dnum"])){
            //削除パスあり
            if(!empty($_POST["dpass"])){
                
                $dnum = $_POST["dnum"];
                $dpass = $_POST["dpass"]; 
               
                $id = $dnum;
                $sql = 'SELECT * FROM mission5 WHERE id=:id ';
                $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
                $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
                $stmt->execute();

                    //格納
                    foreach($stmt as $row){
                        $deletepass = $row["password"];
                    }    
                    //パスワード一致
                    if($deletepass == $dpass){
                    $sql = 'delete from mission5 where id=:id'; 
                    $stmt=$pdo->prepare($sql);                   
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);                                          
                    $stmt->execute();

                    }
                    //パス違う
                    else{
                        echo "パスワードが違います";
                    }
                
            
              
            
            }//パスあり
            else{
                echo "パスワードを入力してください";
                }
        }//削除終

        //3編集機能
        if(!empty($_POST["enum"]))
               {
                   if(!empty($_POST["epass"])){ 
                        $enum = $_POST["enum"];
                        $epass = $_POST["epass"];

                        $id = $enum; //変更する投稿番号

                        $sql = 'SELECT * FROM mission5 WHERE id=:id ';
                        $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
                        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
                        $stmt->execute();

                        $results = $stmt->fetchAll();
                            //格納 
                            foreach ($results as $row){
                                //パスワード一致
                                if($row["password"] == $epass){
                                $editid = $row["id"];
                                $editname = $row["name"];
                                $editcomment = $row["comment"];
                                }
                                else{
                                    echo "パスワードが違います<br>";
                                }
                            }
                    }
                    else{
                        echo "パスワードを入力してください";
                    }
               }
        
        //3.5編集書き込み
        


?>

<form action="" method="post">
        <input type="text" name="name" placeholder="氏名" 
            value = <?php if(isset($editname)){echo $editname;}?>></p>
        <input type="text" name="comment" placeholder="コメント"
            value = <?php if(isset($editcomment)){echo $editcomment;} ?>></p>
        <input type="text" name="pass" placeholder="パスワード">
        <input type="hidden" name="date" >
        <input type="hidden" name="e" 
            value = <?php if(isset($editid)){echo $editid;}?>>
        <input type="submit" name="send"></p></form>
    <form action="" method="post">
        <input type="number" name="dnum" placeholder="削除する番号">
        <input type="text" name="dpass" placeholder="パスワードを入力">
        <input type="submit" name="delete" value="削除"></p>
    </form>
    <form action="" method="post">
        <input type="number" name="enum" placeholder="編集する番号">
        <input type="text" name="epass" placeholder="パスワードを入力">
        <input type="submit" name="edit" value="編集">
    </form>

<?php

$sql = 'SELECT * FROM mission5';
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll();
foreach ($results as $row){
    //$rowの中にはテーブルのカラム名が入る
    echo $row['id'].',';
    echo $row['name'].',';
    echo $row['comment'].',';
    echo $row['time'].'<br>';

echo "<hr>";
}
?>
</body>
</html>