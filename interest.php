<?php
include("until.php");
//ssidCheck();
header("Content-type: text/plain; charset=UTF-8");


if(!isset($_POST["con_id"]) || $_POST["con_id"]==""){
    exit('ParamError');
}


$con_id = $_POST['con_id'];
$user_id = $_POST['user_id'];
    
$pdo = db_setting();
$stmt = $pdo->prepare("SELECT * FROM favorites WHERE con_list_id = :con_list_id AND user_id = :user_id");   
$stmt->bindValue(':con_list_id', $con_id, PDO::PARAM_INT);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);  //Integer（数値の場合 PDO::PARAM_INT)
$status = $stmt->execute();

//４．データ登録処理後
if($status==false){
    //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
    queryError($stmt);
}else{
    $row = $stmt->fetch();
    if($row["count"]==NULL || $row["count"]==""){
        $count = 1;
        $pdo = db_setting();
        $stmt = $pdo->prepare("INSERT INTO favorites(id, user_id, article_id, count, update_date)VALUES(NULL, :user_id, :con_list_id, :count, sysdate())");
        $stmt->bindValue(':con_list_id', $con_id, PDO::PARAM_INT);  //Integer（数値の場合 PDO::PARAM_INT)
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);  //Integer（数値の場合 PDO::PARAM_INT)
        $stmt->bindValue(':count', $count, PDO::PARAM_INT);  //Integer（数値の場合 PDO::PARAM_INT)
        $status = $stmt->execute();
    }else{
        if($status==false){
            //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
            queryError($stmt);
        }else{
            if($row["count"]==0){
                $count = 1;
            }elseif($row["count"]==1){
                $count = 0;
            }else{
                echo "count errer";
            }
            $stmt = $pdo->prepare("UPDATE favorites SET count=:count WHERE id=:id");
            $stmt->bindValue(':id', $row["id"], PDO::PARAM_INT);  //Integer（数値の場合 PDO::PARAM_INT)
            $stmt->bindValue(':count', $count, PDO::PARAM_INT);  //Integer（数値の場合 PDO::PARAM_INT)
            $status = $stmt->execute();
            
            if($status==false){
                //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
                queryError($stmt);
            }
        }    
    }
    //いいねカウント
    $stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM favorites WHERE con_list_id = :con_list_id AND count = 1 "); 
    $stmt->bindValue(':con_list_id', $con_id, PDO::PARAM_INT);  //Integer（数値の場合 PDO::PARAM_INT)
    $status = $stmt->execute();
    $row = $stmt->fetch();
    $iine = $row["cnt"];
    
    $stmt = $pdo->prepare("UPDATE articles SET iine_count=:iine_count WHERE id=:id");
    $stmt->bindValue(':id', $con_id, PDO::PARAM_INT);  //Integer（数値の場合 PDO::PARAM_INT)
    $stmt->bindValue(':iine_count', $iine, PDO::PARAM_INT);  //Integer（数値の場合 PDO::PARAM_INT)
    $status = $stmt->execute();
    
}
    
//いいねカウント
echo $iine;
exit();

?>