<?php

//エラーログ表示用記述
ini_set('display_errors', 1);

// until読み込む
include("until.php");

try {
    //データーベース接続情報を入力します。
    $dbh = dbSetting();
    // 記事取得
    $articlesQuery = "SELECT a.title,a.content content,a.price price,a.category category,a_img.path1 a_path1,a_img.path2 a_path2,a_img.path3 a_path3,u.name u_name,u_img.path u_path,c.content c_content from articles a inner join article_images a_img on a.id = a_img.article_id inner join users u on u.id = a.user_id inner join user_images u_img on u_img.id = u.id left join (select article_id ,max(content) content,max(update_date) update_date from coments group by article_id) c on a.id = c.article_id";
    $stmt = $dbh->prepare($articlesQuery);

    $status = $stmt->execute();
    $articles = "";
    if ($status == false) {
        echo 'お知らせ取得失敗';
        queryError($stmt);
    }
    // 記事一覧の設定
    while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // 日付フォーマット変換
        $articles .= '<div class="grid__item ' . $result['category'] . '">';
        $articles .= '<div class="slider">';
        $articles .= '<div class="slider__item">';
        $articles .= '<img src="' . $result['a_path1'] . '"alt="Dummy"/>';
        $articles .= '</div>';
        $articles .= '<div class="slider__item">';
        $articles .= '<img src="' . $result['a_path2'] . '"alt="Dummy"/>';
        $articles .= '</div>';
        $articles .= '<div class="slider__item">';
        $articles .= '<img src="' . $result['a_path3'] . '"alt="Dummy"/>';
        $articles .= '</div>';
        $articles .= '</div>';
        $articles .= '<div class="meta">';
        $articles .= '<h3 class="meta__title">' . $result['title'] . '</h3>';
        $articles .= '<span class="meta__price">' . $result['price'] . '</span>';
        $articles .= '<div class="item__header clearfix">';
        $articles .= '<span class="item__user-icon">';
        $articles .= '<img src="./img/jibanyan.jpg" height="48" width="48" alt="ジバニャン" class="item__user-icon-img" />';
        $articles .= '</span>';
        $articles .= '<span class="item__user-name">';
        $articles .= '<strong>' . $result['u_name'] . '</strong>';
        $articles .= '</span>';
        $articles .= '<span class="user-comment">' . $result['c_content'] . '</span>';
        $articles .= '</div>';
        $articles .= '</div>';
        $articles .= '<button class="action action--button action--buy">';
        $articles .= '<i class="fa fa-heart"></i>';
        $articles .= '</button>';
//        $articles .= '<input type="hidden" name="article" value="'.$result['id'].'">';
//        $articles .= '<input type="hidden" name="article_user_id" value="'.$result['user_id'].'">';
        $articles .= '<input type="hidden" name="favorite">';
        $articles .= '<input type="hidden" name="coment">';
        $articles .= '</div>';

    }
} catch (Exception $e) {
    echo "エラー発生: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "";
    die();
} finally {
    $dbh = null;
}

?>

<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>クーリエ・ジャポン | 海外メディアから記事を厳選！</title>
    <meta name="description"
          content="Blueprint: A responsive product grid layout with touch-friendly Flickity galleries and Isotope-powered filter functionality."/>
    <meta name="keywords"
          content="blueprint, template, layout, grid, responsive, products, store, filter, isotope, flickity"/>
    <meta name="author" content="Codrops"/>
    <link rel="shortcut icon" href="favicon.ico">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="css/font-awesome.css">
    <!-- Pixel Fabric clothes icons -->
    <link rel="stylesheet" type="text/css" href="fonts/pixelfabric-clothes/style.css"/>
    <!-- General demo styles & header -->
    <link rel="stylesheet" type="text/css" href="css/demo.css"/>
    <!-- Flickity gallery styles -->
    <link rel="stylesheet" type="text/css" href="css/flickity.css"/>
    <!-- Component styles -->
    <link rel="stylesheet" type="text/css" href="css/component.css"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="js/modernizr.custom.js"></script>
    <script src="js/until.js"></script>
</head>
<body>
<div class="head">
    <p class="align_c"><img src="img/headlogo.png" width="180" height="42" alt="クーリエ・ジャポン |"></p>
</div>
<!-- Bottom bar with filter and cart info -->
<div class="bar">
    <div class="filter">
        <span class="filter__label">CATEGORY: </span>
        <button class="action filter__item filter__item--selected" data-filter="*">All</button>
        <button class="action filter__item" data-filter=".jackets"><i class="icon icon--jacket"></i><span
                class="action__text">WORLD</span></button>
        <button class="action filter__item" data-filter=".shirts"><i class="icon icon--shirt"></i><span
                class="action__text">NIPPON</span></button>
        <button class="action filter__item" data-filter=".dresses"><i class="icon icon--dress"></i><span
                class="action__text">BUSINESS</span></button>
        <button class="action filter__item" data-filter=".trousers"><i class="icon icon--trousers"></i><span
                class="action__text">CULTURE</span></button>
        <button class="action filter__item" data-filter=".shoes"><i class="icon icon--shoe"></i><span
                class="action__text">TRABEL</span></button>
    </div>
    <button class="cart">
        <i class="cart__icon fa fa-heart"></i>
        <span class="text-hidden">Shopping cart</span>
        <span class="cart__count">0</span>
    </button>
</div>
<div class="view">
    <!-- Grid -->1
    <section class="grid grid--loading">
        <!-- Loader -->
        <img class="grid__loader" src="img/grid.svg" width="60" alt="Loader image"/>
        <!-- Grid sizer for a fluid Isotope (Masonry) layout -->
        <div class="grid__sizer"></div>
        <!-- Grid items -->
        <?= $articles; ?>
    </section>
    <!-- /grid-->
</div>
<!-- /view -->
<script src="js/isotope.pkgd.min.js"></script>
<script src="js/flickity.pkgd.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>