<?php

require('function.php');

require('misson-card.php');

debug('---------------------------------------------------------------');
debug('-- 各スタッフページ --');
debug('---------------------------------------------------------------');
debugLogStart();


//このスタッフページのGETパラメータを取得
$u_id = (!empty($_GET['u_id'])) ? $_GET['u_id'] : '';
//debug('データ取得：u_idは、'.$u_id.'です');

//検索メニュー
$which = (!empty($_GET['task-select'])) ? $_GET['task-select'] : '0';

//ソート順
$sort = (!empty($_GET['sort'])) ? $_GET['sort'] : '';

$currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1;

//不当なものが入ってないかチェック
if(!is_int($currentPageNum)){
    error_log('エラー発生:指定ページに不正な値が入りました');
    header("Location:all-taskpage.php"); //トップページへ
}

//表示件数
$listSpan = 10;
// 現在の表示レコード先頭を算出
$currentMinNum = ($currentPageNum -1) * $listSpan;

//スタッフネーム取得
$userName = getUserName($u_id);
//debug(print_r($userName,true));


$viewData = getStuffTaskData($u_id , $which , $sort , $currentMinNum);
debug('取得したデータ：'.print_r($viewData,true));


//総ページ数
$totalPageNum = $viewData['total_page'];


//このページのurl
$page_url = 'stuff-page.php';


debug('画面表示処理終了---------------------------');
?>


<?php
$site_title = $userName['user_name'].'のページ';
require('head.php');
?>

<body>
    <!--ヘッダー-->
    <?php require('header.php'); ?>

    <div class="main">

        <!--メッセージ-->
        <p id="js-show-msg" style="display:none;" class="msg-slide">
            <?php echo getSessionFlash('history'); ?>
        </p>



        <section class="page-container">
            <div class="stuff-menu">
                <h2 class="title">
                    <?php echo (!empty($userName)) ? $userName['user_name'].'のページ' : '氏名不明'; ?>
                </h2>
                <!--取得件数表示-->
                <section class="seach-form">
                    <div class="seach-left">
                        <span class="total-num">
                            <?php echo sanitize($viewData['total']); ?></span>件の伝達事項が見つかりました。
                    </div>

                    <div class="seach-right">
                        <span class="num"><?php echo (!empty($viewData['data'])) ? $currentMinNum+1 : 0; ?></span> 〜 <span class="num"><?php echo $currentMinNum + count($viewData['data']); ?></span>件 / <span class="num"><?php echo sanitize($viewData['total']); ?></span>件中
                    </div>

                    <!--ページネーション-->
                    <?php pagination($currentPageNum , $totalPageNum , $page_url); ?>

                </section>


                <!--サイドバー-->
                <section id="sidebar">
                    <form action="" method="get">
                        <div class="sort-container">
                            <input class="display-none" type="text" name="u_id" value="<?php if(!empty($_GET['u_id'])) echo $_GET['u_id']; ?>">
                            <h1 class="sort-title">検索メニュー</h1>
                            <div class="sort-which">
                                <label for="sort-All"><input id="sort-All" type="radio" name="task-select" value="1" style="font-size:20px;">全て表示する<br></label>
                                <label for="sort-Unachieved"><input id="sort-Unachieved" type="radio" name="task-select" value="2" style="font-size:20px;">未達成のみ</label>
                            </div>


                            <h3 class="sort-title">表示順</h3>
                            <div class="sort-box">
                                <select name="sort" id="">
                                    <option value="">選択してください</option>
                                    <option value="1">新しい順</option>
                                    <option value="2">古い順</option>
                                </select>
                            </div>
                            <input type="submit" value="検索" style="margin-top:15px;width:60px;font-size:20px;padding:5px;cursor:pointer;">
                        </div>
                    </form>
                </section>


                <div class="page-up js-click-top">
                    <i class="fas fa-angle-double-up"></i>
                </div>



                <!-- デッキを制作する -->
                <?php echo createMissonCard($viewData); ?>

                <!--ミッションカードを検索し、自動で生成する-->
                <?php if(!empty($missons)) :  ?>


                <!--もし、ミッションが作られていれば-->
                <?php  for($i = 0; $i < count($missons , COUNT_RECURSIVE); $i++) :  ?>

                <div class="incomplete_task">
                    <div class="task-card">

                        <div class="task-card-head">
                            <h3 class="sub-title js-html-change <?php if(!empty($missons[$i]->getDelete()) == 1) echo 'success'; ?>" data-ID="<?php echo $missons[$i]->getID(); ?>"><?php echo(!empty($missons[$i]->getDelete()) == 1) ? '達成!' : '未達成' ?></h3>

                            <div class="card-maindata">
                                <div class="task-data">
                                    <h4><?php echo $missons[$i]->getTarget(); ?></h4>
                                </div>
                                <div class="task-days">
                                    <p><?php echo $missons[$i]->getDoDays(); ?></p>
                                </div>
                            </div>

                        </div>
                        <div class="task-item">
                            <p>＜内容＞</p>
                            <div class="task-list">
                                <p><?php echo $missons[$i]->getContents(); ?></p>
                            </div>


                        </div>
                    </div>

                    <div class="create-date">
                        <h6 class="sender-user">投稿者 <span><?php echo $missons[$i]->getSender(); ?></span></h6>
                        <h6 class="create-day">投稿日 <?php echo $missons[$i]->getSaveDays(); ?></h6>
                    </div>

                </div>
                <?php endfor; ?>

                <?php else : ?>
                <!-- 空だった場合 -->

                <h2>あなたへの伝達事項は、見つかりませんでした。</h2>
                <?php echo debug('伝達事項はありません。'); ?>

                <?php endif; ?>


                <!--ページネーション-->
                <?php pagination($currentPageNum , $totalPageNum, $page_url , $u_id); ?>


                <!--画面遷移-->
                <div class="form-area">
                    <button class="btn form-submit btn-white"><a class="btn-link" href="member.php">前の画面に戻る</a></button>
                </div>


                <div class="form-area">
                    <button class="btn form-submit btn-white"><a class="btn-link" href="index.php">HOMEに戻る</a></button>
                </div>

            </div>
        </section>


    </div>



    <?php
    require('footer.php');
    ?>
