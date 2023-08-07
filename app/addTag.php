<?php

if(isset($_POST['tag'])){
    require '../db_conn.php';

    $tag = $_POST['tag'];

    if(empty($tag)){
        header("Location: ../index.php?tag_mess=error");
    }else {
        $stmt = $conn->prepare("INSERT INTO etiketler(etiket_adi) VALUE(?)");
        $res = $stmt->execute([$tag]);

        if($res){
            header("Location: ../index.php?tag_mess=success"); 
        }else {
            header("Location: ../index.php");
        }
        $conn = null;
        exit();
    }
}else {
    header("Location: ../index.php?tag_mess=error");
}
