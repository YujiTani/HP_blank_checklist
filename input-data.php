<?php
//関数ファイルの読み込み
require('function.php');

debug('---------------------------------------------------------------');
debug('-- データ登録ページ --');
debug('---------------------------------------------------------------');
debugLogStart();





//スタッフ名情報を取得
$save_stuffName = getStuffNames();
//debug('取得した職員名'.print_r($save_stuffName,true)); //必要時コメントオフ解除

//患者名情報を取得
$save_patientName = getPatientNames();
//debug('取得した患者名'.print_r($save_patientName,true));

//カテゴリー情報を取得
$save_jobCategory = getJobCategoryData();
//debug('取得した職種情報'.print_r($save_JobCategory[0],true));


//POST送信されていた場合
if(!empty($_POST))
{
debug('POST送信があります');

//変数にユーザー情報登録
    $taskMessage = $_POST['task-message']; //伝達事項
    $senders     = $_POST['senders'];//送信者選択
    $stuffs      = $_POST['stuffs']; //職員選択
    $patients    = $_POST['patients']; //患者選択
    $inputDays   = (!empty($_POST['input_days']) == "") ? null : $_POST['input_days']; //日付の登録
    $stuffName   = $_POST['stuff-name']; //職員の名前
    $jobs        = $_POST['jobs']; //職業の選択
    $patientName = $_POST['patient-name']; //患者氏名
    $saveAction  = $_POST['save_action']; //保存形式変更
    $inputSelect = $_POST['input-select']; //フォーム登録ルートの保存


    if(!empty($_POST)) {
    debug('POSTに値が入力されています。'.print_r($_POST, true));

        switch($_POST['input-select']){



    case "01":
        //バリデーションチェック
        validRequired($taskMessage , 'task-message');//伝達事項の未入力チェック
        validMaxLen($taskMessage , 'task-message');
        //エラーメッセージの有無確認
        if(empty($err_msg)){
            debug('case1のバリデーションチェックOK');
// //----例外処理----
            try {
                //----DBへ接続----
                $dbh = dbConnect();
                debug('DB接続します');
                $sql = 'INSERT INTO tasks (task_Message,senders,stuffs,patients,input_Days,create_date) VALUES(:task_Message,:senders,:stuffs,:patients,:input_Days,:create_date)';
                $data = array(
                    ':task_Message' => $taskMessage,
                    ':senders' => $senders,
                    ':stuffs' => $stuffs,
                    ':patients' => $patients,
                    ':input_Days' => $inputDays,
                    ':create_date' => date('Y-m-d H:i:s')
                    );
                    $stmt = queryPost($dbh , $sql , $data);
                //クエリ成功の場合
                if($stmt){
                    if($saveAction === '続けて登録をする') {
                        $_POST['input-select'] = '';
                        debug('データをDBに保存し、データの登録を継続します。');
                        $_SESSION['history'] = SUC02;
                        debug('セッション変数の中身：'.print_r($_SESSION,true));
                    }else{
                        $_POST['input-select'] = '';
                        debug('データを保存しました、HOMEへ遷移します');
                        $_SESSION['history'] = SUC01;
                        debug('セッション変数の中身：'.print_r($_SESSION,true));
                        header("location:index.php"); //HOMEへ遷移する
                        return;
                    }
                }


            } catch (Exception $e) {
                error_log('エラー発生:'.$e->getMessage());
                $_SESSION['history'] .= MSG04;
            }

        }else{
            echo debug('エラーがあります：'.print_r($err_msg,true));
        }
        break;





    case "02":
        //バリデーションチェック
        validRequired($stuffName , 'stuff-name');//スタッフ名の未入力チェック
        validRequired($jobs , 'jobs');//職種の未入力チェック
        validMaxLen($stuffName, 'stuff-name');
        validStuffNameDup($stuffName , 'stuff-name');//名前重複チェック
        //エラーメッセージの有無確認
        if(empty($err_msg)){
            debug('case2のバリデーションチェックOK');
            //----例外処理----
            try {
                //----DBへ接続----
                $dbh = dbConnect();
                debug('DB接続します');
                $sql = 'INSERT INTO stuffs (user_name , user_job , create_date) VALUES(:user_name , :user_job , :create_date)';
                $data = array(
                    ':user_name' => $stuffName,
                    ':user_job' => $jobs,
                    ':create_date' => date('Y-m-d H:i:s')
                );
                $stmt = queryPost($dbh , $sql , $data);
                //クエリ成功の場合
                if($stmt){
                    if($saveAction === '続けて登録をする') {
                        $_POST['input-select'] = '';
                        debug('データをDBに保存し、データの登録を継続します。');
                        $_SESSION['history'] = SUC02;
                    }else{
                        $_POST['input-select'] = '';
                        debug('データを保存しました、HOMEへ遷移します');
                        $_SESSION['history'] = SUC01;
                        debug('セッション変数の中身：'.print_r($_SESSION,true));
                        header("location:index.php"); //HOMEへ遷移する
                        return;
                    }
                }


            } catch (Exception $e) {
                error_log('エラー発生:'.$e->getMessage());
                $_SESSION['history'] .= MSG04;
            }

        }else{
            echo debug('エラーがあります：'.print_r($err_msg,true));
        }
        break;


    case "03":
        //バリデーションチェック
        validRequired($patientName , 'patient-name'); //患者名の未入力チェック
        validMaxLen($patientName , 'patient-name');
        validStuffNameDup($patientName , 'patient-name');//名前重複チェック
        //エラーメッセージの有無確認
        if(empty($err_msg)){
            debug('case3のバリデーションチェックOK');
            //----例外処理----
            try {
                //----DBへ接続----
                $dbh = dbConnect();
                debug('DB接続します');
                $sql = 'INSERT INTO patients (user_name , create_date) VALUES(:user_name , :create_date)';
                $data = array(
                    ':user_name' => $patientName,
                    ':create_date' => date('Y-m-d H:i:s')
                );
                $stmt = queryPost($dbh , $sql , $data);
                //クエリ成功の場合
                if($stmt){
                    if($saveAction === '続けて登録をする') {
                        $_POST['input-select'] = '';
                        debug('データをDBに保存し、データの登録を継続します。');
                        $_SESSION['history'] = SUC02;
                    }else{
                        $_POST['input-select'] = '';
                        debug('データを保存しました、HOMEへ遷移します');
                        $_SESSION['history'] = SUC01;
                        debug('セッション変数の中身：'.print_r($_SESSION,true));
                        header("location:index.php"); //HOMEへ遷移する
                        return;
                    }
                }

            } catch (Exception $e) {
                error_log('エラー発生:'.$e->getMessage());
                $_SESSION['history'] .= MSG04;
            }

        }else{
            echo debug('エラーがあります：'.print_r($err_msg,true));
        }
        break;

    default:
                $_SESSION['history'] .= MSG04;
                debug('警告:出力エラー:'.print_r($err_msg , true));
//--switch文--終了--
}

}else{
        debug('POST送信がありません:'.$_POST);
    }
}

debug('-------------------画面表示処理終了---------------------');



?>
<?php
$site_title = '登録ページ';
require('head.php');
?>


<!--メニュー-->

<body>
    <!--ヘッダー-->
    <?php require('header.php'); ?>


    <div class="main">
        <form id="form" class="form-container page-container" action="" method="post">
            <h2 class="title">新しい情報を登録する</h2>


            <!--メッセージ-->
            <p id="js-show-msg" style="display:none;" class="msg-slide">
                <?php echo getSessionFlash('history'); ?>
            </p>




            <div class="form-guide-wrap">
                <div class="form-guide-header  js-down-guide">
                    <i class="fas fa-plus-circle form-note-icon js-click-rotation js-click-close" style="transform:scale(1.5);color:#f1390b;"></i>
                    <i class="fas fa-minus-circle form-note-icon js-click-rotation display-none" style="transform:scale(1.5);color:#f1390b;"></i>
                    <h3 class="form-guide" style="color:#f1390b;">情報登録のガイドライン</h3>
                </div>

                <div class="guide-menu">
                    <!--                      クリックで現れる説明欄-->
                    <ul class="form-guide-message js-down-menu" style="display:none">
                        <!--項目ごとにエリアを分ける-->
                        <div class="form-guide-eria">
                            <h5 class="form-guide-head">【登録したい情報選択】</h5>
                            <p class="form-guide-note">
                                ・伝達事項の登録をする前に、先にスタッフ氏名、患者氏名の登録をして下さい。入力の簡素化をする事ができます。<br>
                                ・その後、伝達事項の登録を選択タブから選択し、’入力を開始する’のボタンを押してください。下記に入力フォームが出てきます。
                            </p>
                        </div>


                        <div class="form-guide-eria">
                            <h5 class="form-guide-head">【１.伝達事項の登録】</h5>
                            <h5 class="form-guide-head">【伝達内容の入力】</h5>

                            <p class="form-guide-note">
                                ・５００文字以内で、知らせたい内容の記述をする事出来ます。自分の名前を入力する事で、誰からの投稿なのかわかりやすくなります。<br>
                            </p>
                        </div>

                        <div class="form-guide-eria">
                            <h5 class="form-guide-head">【投稿者の選択】</h5>
                            <p class="form-guide-note">
                                ・伝達事項を投稿しているあなたの名前を入力してください<br>

                            </p>
                        </div>


                        <div class="form-guide-eria">
                            <h5 class="form-guide-head">【職員の選択】</h5>
                            <p class="form-guide-note">
                                ・選択タブを押すと、登録している職員の一覧が表示されます。お知らせしたい職員を選択して下さい。<br>・全員への伝達事項の場合、全職員を選んでください。<br>
                                ・一覧に出てこない職員は、未登録の可能性があります。先に職員氏名の登録を行なって下さい。<br>

                            </p>
                        </div>

                        <div class="form-guide-eria">
                            <h5 class="form-guide-head">【患者さんの選択】</h5>
                            <p class="form-guide-note">
                                ・選択タブを押すと、登録している患者さんの一覧が表示されるので、関連する患者さんを選択して下さい。<br>
                                ・一覧に出てこない患者さんは、未登録の可能性があります。先に患者さん氏名の登録を行なって下さい。
                            </p>
                        </div>

                        <div class="form-guide-eria">
                            <h5 class="form-guide-head">【伝達内容を実行する日】</h5>
                            <p class="form-guide-note">
                                ・伝達内容を実行して欲しい日付の入力をしてください。投稿日は自動保存されますので、入力不要です。<br>
                            </p>
                        </div>


                        <div class="form-guide-eria">
                            <h5 class="form-guide-head">【２.職員情報の登録】</h5>
                            <h5 class="form-guide-head">【職員名の入力】</h5>
                            <p class="form-guide-note">
                                ・登録したい職員の氏名を入力して下さい。<br>
                                ・この名前は投稿者でも使用する事ができます。<br>
                            </p>
                        </div>

                        <div class="form-guide-eria">
                            <h5 class="form-guide-head">【職種の選択】</h5>
                            <p class="form-guide-note">
                                ・看護師、介護士から職業を選択して下さい。<br>
                            </p>
                        </div>

                        <div class="form-guide-eria">
                            <h5 class="form-guide-head">【３.患者さんの氏名を登録】</h5>
                            <p class="form-guide-note">
                                ・登録したい患者さんの氏名を入力して下さい。<br>
                            </p>
                        </div>


                    </ul>
                </div>

            </div>




            <!--登録内容選択-->
            <label for="input-select" class="form-label-head form-area">
                登録内容の選択
                <span class="form-tag form-tag-required">必須<br></span>
                <span class="form-description">登録したい内容を"伝達事項の登録"、"職員氏名の登録"、"患者氏名の登録"から選択してください。<br></span>
                <select name="input-select" id="js-getVal" class="form-input input-select">
                    <option value="00" <?php if(empty($_POST['input-select'])) echo 'selected'; ?>>-- 選択してください --</option>
                    <option value="01" <?php if(!empty($_POST['input-select']) && $_POST['input-select'] == "01") { echo 'selected';} ?>>-- 伝達事項の登録 --</option>
                    <option value="02" <?php if(!empty($_POST['input-select']) && $_POST['input-select'] == "02") { echo 'selected';} ?>>-- 職員氏名の登録 --</option>
                    <option value="03" <?php if(!empty($_POST['input-select']) && $_POST['input-select'] == "03") { echo 'selected';} ?>>-- 患者氏名の登録 --</option>
                </select>
                <input id="js-formBtn" class="btn input-selectBtn" type="submit" value="▼入力を開始する▼">
            </label>


            <!--動的可変フォーム 伝達事項の登録-->
            <div id="input-stuff-form" class="js-ajax-toggle-1 <?php echo(!empty($_POST['input-select']) && $_POST['input-select'] == "01") ? '' : 'display-none'; ?> ">
                <!--動的表示タイトル-->
                <h3 class="ajax-title">伝達事項の登録</h3>
                <label for="form-transmission-matter" class="form-label-head form-area">
                    伝達内容を入力して下さい
                    <span class="form-tag form-tag-required">必須<br></span>
                    <span class="area-msg">
                        <!--エラーメッセージ表示-->
                        <?php if(!empty($err_msg['task-message'])) echo $err_msg['task-message']; ?>
                    </span>
                    <span class="form-description">500文字以内で入力して下さい</span>
                    <textarea class="form-textarea ajax-text-lengthCount" name="task-message" id="form-transmission-matter" rows="5" value="<?php if(!empty($_POST['task-message'])) echo $_POST['task-message']; ?>">
</textarea>
                    <p class="count-area"><span id="js-count">0</span>/500</p>

                </label>


                <!--            ポップアップメッセージ--NG---->
                <div class="show-NG-Msg js-show-msg">
                    <h2 class="js-popMsg"></h2>
                    <p class="js-popMsg2"></p>
                </div>






                <label for="input-patient" class="form-label-head form-area">
                    投稿者の名前を入力してください。
                    <span class="form-tag form-tag-required">必須<br></span>
                    <span class="form-description">あなたの名前を入力してください。<br></span>
                    <span class="area-msg">
                        <!--エラーメッセージ表示-->
                        <?php if(!empty($err_msg['senders'])) echo $err_msg['senders']; ?>
                    </span>
                    <input class="input-patient form-input" id="input-senders" type="text" name="senders" value="<?php if(!empty($_POST['senders'])) echo $_POST['senders']; ?>">
                </label>




                <label for="stuff-select" class="form-label-head form-area">
                    職員を選択して下さい
                    <span class="form-tag form-tag-required">必須<br></span>
                    <span class="form-description">伝達したい職員の選択をして下さい、全体への伝達事項の場合、全職員を選択してください<br></span>
                    <span class="area-msg">
                        <!--エラーメッセージ表示-->
                        <?php if(!empty($err_msg['stuffs'])) echo $err_msg['stuffs']; ?>
                    </span>

                    <select name="stuffs" id="stuff-select" class="form-input" form="form">
                        <option value="0" <?php if(!empty($_POST['stuffs']) && $_POST['stuffs'] == 0){echo 'selected'; } ?>>
                            -- 選択してください --
                        </option>
                        <?php foreach($save_stuffName as $key => $val) { ?>
                        <option value="<?php echo $val['id']; ?>" <?php if(!empty($_POST['stuffs']) && $_POST['stuffs'] == $val['id']){echo 'selected'; } ?>>
                            <?php echo $val['user_name']; ?>
                        </option>

                        <?php } ?>
                        <!--foreach終了-->
                    </select>
                </label>



                <label for="patient-select" class="form-label-head form-area">
                    患者様を選択して下さい
                    <span class="form-tag form-tag-required">未入力でもOK<br></span>
                    <span class="form-description">対象の患者様がいる場合、どの患者様への伝達情報か入力してください。</span>
                    <span class="area-msg">
                        <!--エラーメッセージ表示-->
                        <?php if(!empty($err_msg['patients'])) echo $err_msg['patients']; ?>
                    </span>

                    <select name="patients" id="patient-select" class="form-input" form="form">
                        <option value="0" <?php if(!empty($_POST['patients']) && $_POST['patients'] == "0"){echo 'selected'; } ?>>
                            -- 選択してください --
                        </option>
                        <?php foreach($save_patientName as $key => $val) { ?>
                        <option value="<?php echo $val['id']; ?>" <?php if(!empty($_POST['patients']) && $_POST['patients'] == $val['id']){echo 'selected'; } ?>>
                            <?php echo $val['user_name']; ?>
                        </option>

                        <?php } ?>
                        <!--foreach終了-->
                    </select>
                </label>



                <label for="form-transmission-matter" class="form-label-head form-area">
                    内容の実行日
                    <span class="form-tag form-tag-required">未入力でもOK<br></span>
                    <span class="form-description">伝達内容を実行してほしい日付を入力できます、投稿日は別に自動保存されます。</span>
                    <span class="area-msg">
                        <!--エラーメッセージ表示-->
                        <?php if(!empty($err_msg['input_days'])) echo $err_msg['input-days']; ?>
                    </span>
                    <input class="input-days" name="input_days" type="date" value="<?php if(!empty($_POST['input_days'])) echo $_POST['input_days']; ?>">
                </label>


            </div>



            <!--動的可変フォーム 職員登録-->
            <div id="input-stuff-form" class="js-ajax-toggle-2 <?php echo (!empty($_POST['input-select']) && $_POST['input-select'] == "02") ? '' : 'display-none'; ?> ">
                <!--動的表示タイトル-->
                <h3 class="ajax-title">職員情報の登録</h3>

                <label for="input-stuff" class="form-label-head form-area">
                    職員名の名前を入力して下さい
                    <span class="form-tag form-tag-required">必須<br></span>
                    <span class="area-msg">
                        <!--エラーメッセージ表示-->
                        <?php if(!empty($err_msg['stuff-name'])) echo $err_msg['stuff-name']; ?>
                    </span>
                    <input class="input-stuff form-input" id="input-stuff" type="text" name="stuff-name" value="<?php if(!empty($_POST['stuff-name'])) echo $_POST['stuff-name']; ?>">
                </label>



                <label for="job-select" class="form-label-head form-area">
                    その方の職種を選択して下さい
                    <span class="form-tag form-tag-required">必須<br></span>
                    <span class="form-description">職種を"看護師"、"介護士"から選択してください<br></span>
                    <span class="area-msg">
                        <!--エラーメッセージ表示-->
                        <?php if(!empty($err_msg['jobs'])) echo $err_msg['jobs']; ?>
                    </span>

                    <select name="jobs" id="job-select" class="form-input" form="form">
                        <option value="" <?php if(!empty($_POST['jobs']) == ""){echo 'selected'; } ?>>
                            -- 選択してください --
                        </option>
                        <?php foreach($save_jobCategory as $key => $val) { ?>
                        <option value="<?php if(!empty($save_jobCategory)) echo $val['id']; ?>" <?php if(!empty($_POST['jobs']) == $val['id']){echo 'selected'; } ?>>
                            <?php if(!empty($save_jobCategory)) echo $val['job_name']; ?>
                        </option>

                        <?php } ?>
                        <!--foreach終了-->
                    </select>
                </label>
            </div>

            <!--動的可変フォーム 患者氏名登録-->
            <div id="input-patient-form" class="js-ajax-toggle-3 <?php echo(!empty($_POST['input-select']) && $_POST['input-select'] == "03") ? '' : 'display-none'; ?> ">
                <!--動的表示タイトル-->
                <h3 class="ajax-title">患者様の名前を登録</h3>



                <label for="input-patient" class="form-label-head form-area">
                    患者様の名前を入力して下さい
                    <span class="form-tag form-tag-required">必須<br></span>
                    <span class="area-msg">
                        <!--エラーメッセージ表示-->
                        <?php if(!empty($err_msg['patient-name'])) echo $err_msg['patient-name']; ?>
                    </span>
                    <input class="input-patient form-input" id="input-patient" type="text" name="patient-name" value="<?php if(!empty($_POST['patient-name'])) echo $_POST['patient-name']; ?>">
                </label>
            </div>



            <div class="form-area">
                <input name="save_action" type="submit" class="btn form-submit" value="登録を完了する">
            </div>

            <div class="form-area">
                <input name="save_action" type="submit" class="btn form-submit" value="続けて登録をする">
            </div>

            <div class="form-area">
                <button class="btn form-submit btn-white"><a class="btn-link" href="index.php">HOMEに戻る</a></button>
            </div>


        </form>
    </div>



    <?php
    require('footer.php');
    ?>
