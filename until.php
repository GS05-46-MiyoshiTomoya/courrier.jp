<?php

/*****************************************************
 * 共通関数
 *****************************************************/

/**
 * 値存在チェック
 * @Param 値
 * @Return True 値が存在する
 *         False 値が存在しない
 */
function notEmpty($val)
{
    if ($val == '') {
        return false;
    }
    return true;
}

/**
 * データベース接続設定
 * @param なし
 * @return $dbh データベース接続情報
 */
function dbSetting(){
    //データーベース接続情報を入力します。
    $user = "root";
    $pass = "root";
//    $user = "labo-scip";
//    $pass = "bbwp1407";

    $dbh = '';
    try {
        $dbh = new PDO('mysql:host=localhost;dbname=gs_db;charset=utf8', $user, $pass);
//        $dbh = new PDO('mysql:host=mysql534.db.sakura.ne.jp;dbname=scip_test;charset=utf8', $user, $pass);
        $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (Exception $e) {
        echo "エラー発生: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "";
        die();
    }
    return $dbh;
}

/**
 * SQL処理エラー
 * @Param $stmt
 * @Return なし
 */
function queryError($stmt)
{
    //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
    $error = $stmt->errorInfo();
    exit("QueryError:" . $error[2]);
}

/**
 * XSS
 * @Param:  $str(string) 表示する文字列
 * @Return: (string)     サニタイジングした文字列
 */
function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES, "UTF-8");
}

/**
 * 現在ページング生成
 * @Param:  $cuurentPageIndex(string) 現在のページ数
 * @Return: (string) ページング用文字列
 */
function getCurrentPagingTag($cuurentPageIndex)
{
    $paging = '<span class="current">';
    $paging .= '<p>' . $cuurentPageIndex . '</p>';
    $paging .= '<img src="img/polygon4.png" class="">';
    $paging .= '</span>';

    return $paging;
}

/**
 * 次ページング生成
 * @Param:  $cuurentPageIndex(string) 現在のページ数
 * @Param:  $year 年
 * @Param:  $month 月
 * @Return: (string) ページング用文字列
 */
function getNextPagingTag($cuurentPageIndex, $year, $month)
{
    $paging = '<span class="next">';
    $paging .= '<a href="news_list.php?page=' . ($cuurentPageIndex + 1) . '&year=' . $year . '&month=' . $month . '">>></a>';
    $paging .= '</span>';

    return $paging;
}

/**
 * 前ページング生成
 * @Param:  $cuurentPageIndex(string) 現在のページ数
 * @Param:  $year 年
 * @Param:  $month 月
 * @Return: (string) ページング用文字列
 */
function getPrevPagingTag($cuurentPageIndex, $year, $month)
{
    $paging = '<span class="prev">';
    $paging .= '<a href="news_list.php?page=' . ($cuurentPageIndex - 1) . '&year=' . $year . '&month=' . $month . '"><<</a>';
    $paging .= '</span>';
    return $paging;

    return $paging;
}

/**
 * ページング生成
 * @Param:  $pageIndex(string) ページ数
 * @Param:  $year 年
 * @Param:  $month 月
 * @Return: (string) ページング用文字列
 */
function getPagingTag($pageIndex, $year, $month)
{
    $paging = '<span>';
    $paging .= '<a href="news_list.php?page=' . ($pageIndex) . '&year=' . $year . '&month=' . $month . '">';
    $paging .= h($pageIndex);
    $paging .= '</a></span>';

    return $paging;
}

/**
 *  アーカイブ生成
 * @param $dbh データベース接続情報
 * @Return アーカイブ文字列
 *
 */
function getArchive($dbh)
{
    // アーカイブ作成
    $archiveSql = "select distinct date_format(date, '%Y') as year,date_format(date, '%m') as month from news order by date desc";
    // SQL実行
    $stmt = $dbh->prepare($archiveSql);
    $status = $stmt->execute();
    $archives = "";

    // アーカイブ取得失敗
    if ($status == false) {
        echo 'お知らせ取得失敗';
        queryError($stmt);
    }

    // アーカイブの設定
    while ($archive = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // 日付フォーマット変換
        $archives .= '<li class="month-category-item">';
        $archives .= '<a class="archives-item-link" href="news_list.php?year=' . ($archive['year']) . '&month=' . ($archive['month']) . '">';
        $archives .= '- ' . $archive['year'] . '年' . $archive['month'] . '月';
        $archives .= '</a>';
        $archives .= '</li>';
    }

    return $archives;
}

/*****************************************************
 * 管理者用関数
 *****************************************************/

/**
 * 次ページング生成
 * @Param:  $cuurentPageIndex(string) 現在のページ数
 * @Param:  $year 年
 * @Param:  $month 月
 * @Return: (string) ページング用文字列
 */
function getNextPagingTagMaster($cuurentPageIndex, $year, $month)
{
    $paging = '<span class="next">';
    $paging .= '<a href="master_news_list.php?page=' . ($cuurentPageIndex + 1) . '&year=' . $year . '&month=' . $month . '">>></a>';
    $paging .= '</span>';

    return $paging;
}

/**
 * 前ページング生成
 * @Param:  $cuurentPageIndex(string) 現在のページ数
 * @Param:  $year 年
 * @Param:  $month 月
 * @Return: (string) ページング用文字列
 */
function getPrevPagingTagMaster($cuurentPageIndex, $year, $month)
{
    $paging = '<span class="prev">';
    $paging .= '<a href="master_news_list.php?page=' . ($cuurentPageIndex - 1) . '&year=' . $year . '&month=' . $month . '"><<</a>';
    $paging .= '</span>';
    return $paging;

    return $paging;
}

/**
 * ページング生成
 * @Param:  $pageIndex(string) ページ数
 * @Param:  $year 年
 * @Param:  $month 月
 * @Return: (string) ページング用文字列
 */
function getPagingTagMaster($pageIndex, $year, $month)
{
    $paging = '<span>';
    $paging .= '<a href="master_news_list.php?page=' . ($pageIndex) . '&year=' . $year . '&month=' . $month . '">';
    $paging .= h($pageIndex);
    $paging .= '</a></span>';

    return $paging;
}

/**
 *  アーカイブ生成
 * @param $dbh データベース接続情報
 * @Return アーカイブ文字列
 *
 */
function getArchiveMaster($dbh)
{
    // アーカイブ作成
    $archiveSql = "select distinct date_format(date, '%Y') as year,date_format(date, '%m') as month from news order by date desc";
    // SQL実行
    $stmt = $dbh->prepare($archiveSql);
    $status = $stmt->execute();
    $archives = "";

    // アーカイブ取得失敗
    if ($status == false) {
        echo 'お知らせ取得失敗';
        queryError($stmt);
    }

    // アーカイブの設定
    while ($archive = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // 日付フォーマット変換
        $archives .= '<li class="month-category-item">';
        $archives .= '<a class="archives-item-link" href="master_news_list.php?year=' . ($archive['year']) . '&month=' . ($archive['month']) . '">';
        $archives .= '- ' . $archive['year'] . '年' . $archive['month'] . '月';
        $archives .= '</a>';
        $archives .= '</li>';
    }

    return $archives;
}


?>