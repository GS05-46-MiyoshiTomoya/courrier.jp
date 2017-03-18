<?php

// until読み込む
include("until.php");

// ページング処理
$currentPage = $_REQUEST['page'];
// 指定がない場合
if ($currentPage == "" || $currentPage < 1) {
    $currentPage = 1;
}

// ページング処
$newsCount = 0;
// アーカイブ情報の取得
$year = $_REQUEST['year'];
$month = $_REQUEST['month'];

try {
    //データーベース接続情報を入力します。
    $dbh = dbSetting();
    if (notEmpty($year) && notEmpty($month)) {
        // 月日の条件が設定されている場合
        // SQL生成
        $newsCountSql = "select count(*) from news where year(date) = :year and month(date) = :month";
        // SQL実行
        $stmt = $dbh->prepare($newsCountSql);
        $stmt->bindValue(":month", $month, PDO::PARAM_STR);
    } else {
        // SQL生成
        $newsCountSql = "select count(*) from news";
        // SQL実行
        $stmt = $dbh->prepare($newsCountSql);
    }
    $status = $stmt->execute();
    $news = "";
    if ($status == false) {
        echo 'レコードカウント取得失敗';
        queryError($stmt);
    }
    $stmt->bindValue(":year", $year, PDO::PARAM_STR);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // ページング処理
    $newsCount = $result['count(*)'];
    $maxPage = ceil($newsCount / 5);
    $startIndex = ($currentPage - 1) * 5;

    $paging = "";
    // ページング生成
    if ($maxPage == 0 || $maxPage == 1) {
        $paging .= getCurrentPagingTag(1);
    } elseif ($maxPage == 2) {
        $paging .= getPagingTag(1,$year,$month);
        $paging .= getCurrentPagingTag(2);
    } else {
        // 3ページ以上存在する
        if ($currentPage == 1) {
            $paging .= getCurrentPagingTag($currentPage);
            $paging .= getPagingTag($currentPage + 1,$year,$month);
            $paging .= getPagingTag($currentPage + 2,$year,$month);
            // 次ページボタン生成
            $paging .= getNextPagingTag($currentPage,$year,$month);
        } else if (($maxPage - $currentPage) == 0) {
            // 前ページボタン生成
            $paging .= getPrevPagingTag($currentPage,$year,$month);
            $paging .= getPagingTag($currentPage - 2,$year,$month);
            $paging .= getPagingTag($currentPage - 1,$year,$month);
            $paging .= getCurrentPagingTag($currentPage);
        } else {
            // 前ページボタン生成
            $paging .= getPrevPagingTag($currentPage,$year,$month);
            $paging .= getPagingTag($currentPage - 1,$year,$month);
            $paging .= getCurrentPagingTag($currentPage);
            $paging .= getPagingTag($currentPage + 1,$year,$month);
            // 次ページボタン生成
            $paging .= getNextPagingTag($currentPage,$year,$month);
        }
    }
    // アーカイブの取得
    $archives = getArchive($dbh);

    // お知らせ取得
    if (notEmpty($year) && notEmpty($month)) {
        // 月日の条件が設定されている場合
        // SQL生成
        $newsSql = "select * from news where year(date) = :year and month(date) = :month order by date desc limit {$startIndex},5";
        // SQL実行
        $stmt = $dbh->prepare($newsSql);
        $stmt->bindValue(":year", $year, PDO::PARAM_STR);
        $stmt->bindValue(":month", $month, PDO::PARAM_STR);
    } else {
        // 月日の条件が設定されていない場合
        // SQL生成
        $newsSql = "select * from news order by date desc limit {$startIndex},5";
        // SQL実行
        $stmt = $dbh->prepare($newsSql);
    }
    $status = $stmt->execute();
    $news = "";
    if ($status == false) {
        echo 'お知らせ取得失敗';
        queryError($stmt);
    }
    // お知らせ一覧の設定
    while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // 日付フォーマット変換
        $date = new DateTime($result['date']);
        $news .= '<li class="news-list-item">';
        $news .= '<div class="news-list-item-inner">';
        $news .= '<a class="news-item-link" href="news_detail.php?id=' . ($result['id']) . '&page=' . ($currentPage) . '">';
        $news .= '<span class="news-date">';
        $news .= $date->format('Y.m.d');
        $news .= '</span><span class="news-content">';
        $news .= h($result["name"]);
        $news .= '</span></a></div></li>';
    }
} catch (Exception $e) {
    echo "エラー発生: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "";
    die();
} finally {
    $dbh = null;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>対話探究ラボSCiP</title>
    <meta name="description" content="対話探究ラボSCiPは、哲学を"/>
    <link rel="stylesheet" type="text/css" href="css/reset.css">
    <link rel="stylesheet" type="text/css" href="css/news_list.css">
    <link rel="stylesheet" type="text/css" href="css/common.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="js/index.js"></script>
</head>

<body>
<div class="wrapper">
    <?php include "header_common.php"?>
    <div class="contents">
        <div class="news-inner">
            <nav class="month-category">
                <p class="month-category-title"><span class="month-category-title-text">アーカイブ</span></p>
                <ul class="month-category-menu">
                    <?= $archives; ?>
                </ul>
            </nav>
            <div class="news">
                <p class="news-list-title"><span class="news-list-title-text">お知らせ</span></p>
                <ul class="news-list">
                    <?= $news; ?>
                </ul>
                <div class="paging">
                    <?= $paging; ?>
                </div>
            </div>
        </div>
    </div>
    <?php include "footer_common.php"; ?>
</div>
</body>
</html>