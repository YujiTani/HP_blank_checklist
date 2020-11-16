<?php
require('function.php');

debug('---------------------------------------------------------------');
debug('-- 伝達内容検索ページ --');
debug('---------------------------------------------------------------');
debugLogStart();


//スタッフ名情報を取得
$save_stuffName = getStuffNames();
debug('取得した職員名'.print_r($save_stuffName,true));

//患者名情報を取得
$save_patientName = getPatientNames();
?>



<?php
$site_title = '検索ページ';
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


        <section id="member-list">
            <div class="member-list-container page-container">
                <h2 class="title">検索ページ</h2>
                <div class="new-tpics"><a href="all-taskpage.php"><span class="btn btn-news js-bgm-btn">伝達事項を確認する</span></a></div>

                <form action="" method="get">
                    <div id="seach-container">
                        <div class="stuff-seach" id="js-click-modal1">
                            <img src="img/%E3%83%A1%E3%83%B3%E3%83%90%E3%83%BC.png">
                            <h5 class="card-item"><br>職員で探す</h5>
                            <p>検索したい職員の名前を<br>選択してください。</p>
                        </div>
                        <!--                    モーダルウィンドウ-->
                        <div id="js-show-modal1" class="modal-finder-left display-none">
                            <h4 class="suvtitle">職員一覧</h4>


                            <?php foreach($save_stuffName as $key => $val) { ?>

                            <?php if(!empty($save_stuffName) && $val['user_job'] == "1"): ?>
                            <div class="stuff-nurse">
                                <a href="stuff-page.php?u_id=<?php if(!empty($save_stuffName)) echo $val['id']; ?>" class="js-modal-item modal-finder-item">
                                    <?php if(!empty($save_stuffName) && $val['user_job'] == "1") echo $val['user_name']; ?>
                                </a>
                            </div>


                            <?php elseif(!empty($save_stuffName) && $val['user_job'] == "2"): ?>
                            <div class="stuff-helper">
                                <a href="stuff-page.php?u_id=<?php if(!empty($save_stuffName)) echo $val['id']; ?>" class="js-modal-item modal-finder-item">
                                    <?php if(!empty($save_stuffName) && $val['user_job'] == "2") echo $val['user_name']; ?></a>
                            </div>

                            <?php endif; ?>

                            <?php } ?>

                        </div>


                        <!--
                        <div class="js-birthday-img display-none">
                            <img class="haru-img" src="img/o1e6706d7388bdb6f33bc82fc27141a61_33755450_190806_0002.jpg" alt="">
                            <p class="js-get-html haru-p">はるちゃん、２９歳のお誕生日おめでとう！！！</p>
                        </div>
-->



                        <div class="patient-seach" id="js-click-modal2">
                            <img src="img/%E6%82%A3%E8%80%85%E6%A7%98s.png">
                            <h5 class="card-item"><br>患者さんで探す</h5>
                            <p>検索したい患者さんの名前を<br>選択してください。</p>
                        </div>

                        <!--                    モーダルウィンドウ-->
                        <div id="js-show-modal2" class="modal-finder-right display-none">
                            <h4 class="suvtitle">患者さん一覧</h4>
                            <?php foreach($save_patientName as $key => $val) { ?>
                            <a href="patients-page.php?p_id=<?php echo $val['id']; ?>" class="modal-finder-item"><?php echo $val['user_name']; ?></a>
                            <?php } ?>
                        </div>
                    </div>
                </form>

                <div id="js-blind" class="map-blind display-none"></div>


                <!--いつか実装予定
                <div class="name-search">
                    <input type="search" placeholder="キーワード検索：例:山本" value=""><button class="btn btn-search"><i class="fas fa-search search-fa"></i></button>
                </div>
-->

                <div class="form-area">
                    <button class="btn form-submit btn-white"><a class="btn-link" href="index.php">HOMEに戻る</a></button>
                </div>

            </div>
        </section>
    </div>

    <!--
    <audio id="bgm" src="bgm/Memories_of_the_music_box_ORG_Free_ver.mp3"></audio>
    <audio id="voice1" src="bgm/%E6%98%A5%E3%81%A1%E3%82%83%E3%82%93%E3%81%B8%E3%81%AE%E3%83%A1%E3%83%83%E3%82%BB%E3%83%BC%E3%82%B8.m4a"></audio>
    <audio id="voice2" src="bgm/%E3%81%AF%E3%82%8B%E3%81%A1%E3%82%83%E3%82%93%E3%83%A1%E3%83%83%E3%82%BB%EF%BC%92.m4a"></audio>
-->

    <?php
    require('footer.php');
    ?>
