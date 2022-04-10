<?php
//$_FILES['file']['error']==0
if(!empty($_FILES['csv']['tmp_name'])){
    $filename=md5(time());
    /* $subname=$_FILES['file']['name'];
    $subname=explode(".",$subname); */

    $subname=explode(".",$_FILES['csv']['name'])[1];

    $newFileName=$filename.".".$subname;

    echo "new=>".$newFileName."<br>";
    echo "tmp_name=>".$_FILES['csv']['tmp_name']."<br>";
    echo "fileOrignName=>".$_FILES['csv']['name']."<br>";
    
    move_uploaded_file($_FILES['csv']['tmp_name'],"./file/".$newFileName);

    //echo "<a href='file/{$newFileName}'>{$_FILES['csv']['name']}</a>";

    // 如果上傳的檔案副檔名是txt或csv時才落入saveToDB的function處理
    // 存入資料庫儲存及控管相關資訊
    if($subname=='txt' || $subname=="csv"){
        saveToDB("./file/".$newFileName);
    }
}

// ↑↑↑上半部改為編碼的名字後存到file資料夾
// ↓↓↓如果副檔名是txt或csv，就進行以下寫如資料庫的作業


function saveToDB($file){
    echo "得到檔案".$file."<br>";
    echo "準備進行資料處理作業.....<br>";

    $dsn="mysql:host=localhost;charset=utf8;dbname=file_uploade";
    $pdo=new PDO($dsn,'root','');

    // $file是一個resource，$resource也要是一個resource才能為以下feof及fget等函式所用
    // r+是讀寫方式打開，將文件指針指向文件頭。
    $resource=fopen($file,'r+');
    //fwrite($resource,"0,candy,女,1\r\n");
    $count=0;
    $success=0;
    // feof是判斷檔案結尾，當還沒到結尾就執行以下
    while(!feof($resource)){
        // fgets是讀一行
        $str=explode(",",fgets($resource));
        // 一行用，分隔後，存成陣列到$str陣列
        echo "<pre>";
        print_r($str);
        echo "</pre>";
        // ↓↓↓只有一行資料有4個資料時，才能存到資料庫
        if($count>0 && count($str)==4){
            $sql="INSERT INTO `users` (`num`, `name`, `gender`, `status`) 
                       VALUES ('{$str[0]}', '{$str[1]}', '{$str[2]}', '{$str[3]}')";
                //INSERT INTO `users` (`num`, `name`, `gender`, `status`) VALUES (NULL, '', '', '')
            $pdo->exec($sql);
            echo "<br>己經寫入了".implode(",",$str)."到資料表<br>";
            // ↑↑↑implode讓它們變成一行又秀出來
            $success++;
        }
        $count++;
    }
// 檔案如果開了(fopen函式)，最後一定要關喔，不然不能再用惹
    fclose($resource);

    echo "<br>一共處理了".($count)."筆資料<br>";
    echo "<br>總共成功寫入了了".($success)."筆資料<br>";
}

?>