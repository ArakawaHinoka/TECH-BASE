<?php
    //DB接続
    $dsn='mysql:dbname=＊＊＊＊＊;host=＊＊＊＊＊';
    $user='＊＊＊＊＊';
    $password='＊＊＊＊＊';
    $pdo=new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING));
    //TB作成
    $sql="CREATE TABLE IF NOT EXISTS tbkiroku"
    ."("
    ."id INT AUTO_INCREMENT PRIMARY KEY,"
    ."name char(32),"
    ."comment TEXT,"
    ."date datetime,"
    ."password char(32)"
    .");";
    $stmt=$pdo->query($sql);
?>
<?php
    $name=$_POST["name"];
    $comment=$_POST["comment"];
    $hantei=$_POST["hantei"];
    $number_sakujo=$_POST["number_sakujo"];
    $number_hensyuu=$_POST["number_hensyuu"];
    $pass=$_POST["pass"];
    //編集フォーム
    if (!empty($number_hensyuu)&&!empty($pass)==true){
        //編集したいレコードの取り出し
        $id=$number_hensyuu;
        $sql='SELECT*FROM tbkiroku WHERE id=:id';
        $stmt=$pdo->prepare($sql);
        $stmt->bindParam(':id',$id,PDO::PARAM_INT);
        $stmt->execute();
        //取り出したレコードを連想配列にいれる
        $result=$stmt->fetchAll();
        foreach($result as $row){
            //値は連想配列$row=['カラム名']に格納される
        }
        //パスワードcheck
        if ($row['password']==$pass){
            echo "パスワード一致<br>";
            //投稿フォームへの表示処理
            $hensyuu_pass=$row['password']; //name="pass"のvalueに指定
            if (!empty($row['id']==true)){
                $hensyuu_form_num=$row['id'];//name="hantei"のvalueに指定
            }
            if (!empty($row['name'])==true){
                $hensyuu_form_name=$row['name'];//name="name"のvalueに指定
            }
            if (!empty($row['comment'])==true){                    
                $hensyuu_form_comment=$row['comment'];//name="comment"のvalueに指定
            }
        }else{
            echo "パスワード不一致<br>";
        }
    //書き換え処理
    }elseif(!empty($hantei)==true){
        if($pass!=$hensyuu_pass){
            echo "パスワードが変更されます";
        }
        $id=$hantei;
        $sql='UPDATE tbkiroku SET name=:name,comment=:comment,password=:password WHERE id=:id';
        $stmt=$pdo->prepare($sql);
        $stmt->bindParam(':id',$id,PDO::PARAM_INT);
        $stmt->bindParam(':name',$name,PDO::PARAM_STR);
        $stmt->bindParam(':comment',$comment,PDO::PARAM_STR);
        $stmt->bindParam(':password',$pass,PDO::PARAM_STR);
        $stmt->execute();
    //投稿フォーム↓
    }elseif(!empty($name)&&!empty($comment)&&!empty($pass)==true){
        $date=date("Y/m/d h:i:s");
        //TBにデータを追加
        $sql=$pdo->prepare("INSERT INTO tbkiroku(name,comment,date,password)
        VALUES(:name,:comment,:date,:password)");
        $sql->bindParam(':name',$name,PDO::PARAM_STR);
        $sql->bindParam(':comment',$comment,PDO::PARAM_STR);
        $sql->bindParam(':date',$date,PDO::PARAM_STR);
        $sql->bindParam(':password',$pass,PDO::PARAM_STR);
        $sql->execute();
        //MySQL連番の振り直し(先頭から)
        $sql='SET @i:=0';
        $pdo->query($sql);
        $sql='UPDATE tbkiroku SET id=(@i:=@i+1)';
        $pdo->query($sql);
    //削除フォーム
    }elseif(!empty($number_sakujo)&&!empty($pass)&&empty($name)&&empty($comment)==true){
        //削除したいレコードの取り出し
        $id=$number_sakujo;
        $sql='SELECT*FROM tbkiroku WHERE id=:id';
        $stmt=$pdo->prepare($sql);
        $stmt->bindParam(':id',$id,PDO::PARAM_INT);
        $stmt->execute();
        //取り出したレコードを連想配列にいれる
        $result=$stmt->fetchAll();
        foreach($result as $row){
            //値は連想配列$row=['カラム名']に格納される
        }
        //パスワードcheck
        if ($row['password']==$pass){
            echo "パスワード一致<br>";
            $sql='DELETE FROM tbkiroku WHERE id=:id';
            $stmt=$pdo->prepare($sql);
            $stmt->bindParam(':id',$id,PDO::PARAM_INT);
            $stmt->execute();
            //MySQL連番の振り直し(先頭から)
            $sql='SET @i:=0';
            $pdo->query($sql);
            $sql='UPDATE tbkiroku SET id=(@i:=@i+1)';
            $pdo->query($sql);
        }else{
            echo "パスワード不一致<br>";
        }    
    //送信の欠陥処理
    }elseif(!empty($name)==true){
        echo "コメントを記入してください。<br>";
    }elseif(!empty($comment)==true){
        echo "名前を記入してください。<br>";
    }elseif(empty($comment)&&empty($name)==true){
        echo "名前とコメントを記入してください。<br>";
    }elseif(empty($number_sakujo)==true){
        echo "削除NOを記入してください<br>";
    }elseif(empty($$number_hensyuu)==true){
        echo "編集NOを記入してください<br>";
    }elseif(empty($pass)==true){
        echo "パスワードを入力してください<br>";
    }  
?> 
<!DOCTIPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>mission_5-1</title>
    </head>
    <body>
        【投稿フォーム】
        <form action="" method="post" >
            <!--↓編集判定-->
            <input type="hidden" name="hantei" value="<?php echo $hensyuu_form_num;?>">
            <p>名前　　　　　　　：
            <input type="text" name="name" placeholder="名前" value="<?php echo $hensyuu_form_name;?>"><br>
            コメント　　　　　：
            <input type="text" name="comment" placeholder="コメント"value="<?php echo $hensyuu_form_comment ; ?>"><br>
            パスワード　　　　：
            <input type="password" name="pass" placeholder="パスワード"
            value="<?php echo $hensyuu_pass;?>">
            </p>
            <input type="submit" name="submit" value="送信">
        </form>
        ------------------------------------------------------------------<br>
        【削除フォーム】
        <form action="" method="post" >
            <p>削除したい投稿番号：
            <input type="text" name="number_sakujo" placeholder="削除したい投稿番号"><br>
            パスワード　　　　：
            <input type="password" name="pass" placeholder="パスワード">
            </p>
            <input type="submit" name="submit_sakujo" value="削除">
        </form>
        -----------------------------------------------------------------<br>
        【編集フォーム】
        <form action="" method="post" >
            <p>編集したい投稿番号：
            <input type="text" name="number_hensyuu" placeholder="編集したい投稿番号"><br>
            パスワード　　　　：
            <input type="password" name="pass" placeholder="パスワード">
            </p>
            <input type="submit" name="hensyuu_submit" value="編集">
        </form>
        -----------------------------------------------------------------
        <br>
    </body>
</html>                    
<?php  //投稿内容のブラウザへの表示          
    echo "<hr>";
    echo "【投稿一覧】<br>";
    //DB入力したデータレコードを抽出し、表示する
    $sql='SELECT*FROM tbkiroku';
    $stmt=$pdo->query($sql);
    $result=$stmt->fetchAll();
    foreach($result as $row){
        echo $row['id'].',';
        echo $row['name'].',';
        echo $row['comment'].',';
        echo $row['date'].'<br>';
        //パスワードは非表示
    }
?>