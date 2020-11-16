<?php
require('function.php');


debug('---------------------------------------------------------------');
debug('-- HOMEページ --');
debug('---------------------------------------------------------------');
debugLogStart();

debug('セッション変数の中身：'.print_r($_SESSION,true));
?>

<?php
$site_title = 'HOME';
require('head.php');

?>

<body>
    <!--ヘッダー-->
    <?php require('header.php'); ?>



    <div class="main">




        <section id="main-container page-container">

            <!--            伝達メッセージ用-->
            <p id="js-show-msg" style="display:none;" class="msg-slide">
                <?php echo getSessionFlash('history'); ?>
            </p>

            <h1 class="title">HOME</h1>
            <div id="top-menu">
                <div class="left-menu menu-list">
                    <a href="input-data.php"><img src="img/%E6%B6%88%E3%81%97%E3%82%B4%E3%83%A0%E3%81%A4%E3%81%8D%E9%89%9B%E7%AD%86.png"></a>
                    <a class="top-nav-item" href="input-data.php">登録を行う</a>
                    <p>伝達したい事、スタッフの氏名登録、患者さんの氏名登録はここから！</p>
                </div>

                <div class="right-menu menu-list">
                    <a href="member.php"><img src="img/%E3%83%8E%E3%83%BC%E3%83%88.png" alt=""></a>
                    <a class="top-nav-item" href="member.php">伝達ノート</a>
                    <p>伝達事項の確認、検索をしたい時は、ここから！</p>
                </div>


            </div>
        </section>
    </div>

    <?php
    require('footer.php');
    ?>
