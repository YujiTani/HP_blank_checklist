<?php

//================================
// ログ
//================================
//ログを取るか
ini_set('log_errors','on');
//ログの出力ファイルを指定
ini_set('error_log','php.log');

//================================
// デバッグ
//================================
//デバッグフラグ
$debug_flg = true;
//デバッグログ関数
function debug($str){
    global $debug_flg;
    if(!empty($debug_flg)){
        error_log('デバッグ：'.$str);
    }
};




//================================
// 画面表示処理開始ログ吐き出し関数
//================================
function debugLogstart(){
    debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 画面表示処理開始');
}

//====================================
// セッション準備・セッション有効期限を延ばす
//====================================
//ガーベージコレクションが削除するセッションの有効期限を設定（30日以上経っているものに対してだけ１００分の１の確率で削除）
ini_set('session.gc_maxlifetime', 60*60);
//ブラウザを閉じても削除されないようにクッキー自体の有効期限を延ばす
ini_set('session.cookie_lifetime ', 60*60);
//セッションを使う
session_start();
//現在のセッションIDを新しく生成したものと置き換える（なりすましのセキュリティ対策）
session_regenerate_id();

//================================
// 定数
//================================
//エラーメッセージを定数に設定
define('MSG01','入力必須項目の入力がありません。');
define('MSG02','500文字以内で入力してください');
define('MSG03','すでに同じ名前の人が登録されています');
define('MSG04','予期せぬエラーが発生しました。しばらく経ってからやり直してください。');
define('MSG05','エラーが確認されました。画面表示を確認し再度入力を行なってください。');

define('SUC01', 'データは正しく送信されました。');
define('SUC02', 'データは正しく送信されました、引き続き入力を行なってください。');



//================================
// グローバル変数の設定
//================================
//エラーメッセージ格納用の配列
$err_msg   = array();


//================================
// バリデーション関数
//================================

//バリデーション関数（未入力チェック）
function validRequired($str, $key){
    if($str === ''){ //金額フォームなどを考えると数値の０はOKにし、空文字はダメにする
        global $err_msg;
        $err_msg[$key] = MSG01;
        $_SESSION['history'] = MSG05;
    }
}

//バリデーション関数（最大文字数チェック）
function validMaxLen($str, $key, $max = 500){
    if(mb_strlen($str) > $max){
        global $err_msg;
        $err_msg[$key] = MSG02;
        $_SESSION['history'] = MSG05;

    }
}

//バリデーション関数（スタッフ名：重複チェック）
function validStuffNameDup($stuffName , $key){
    global $err_msg;
    //例外処理
    try {
        // DBへ接続
        $dbh = dbConnect();
        // SQL文作成
        $sql = 'SELECT count(*) FROM stuffs WHERE user_name = :user_name AND delete_flg = 0';
        $data = array(':user_name' => $stuffName);
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        // クエリ結果の値を取得
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        //array_shift関数は配列の先頭を取り出す関数です。クエリ結果は配列形式で入っているので、array_shiftで1つ目だけ取り出して判定します
        if(!empty(array_shift($result))){
            $err_msg[$key] = MSG03;
            $_SESSION['history'] = MSG05;

        }
    } catch (Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
        $_SESSION['history'] = MSG04;
    }
}


//バリデーション関数（患者名：重複チェック）
function validPatientNameDup($patientName , $key){
    global $err_msg;
    //例外処理
    try {
        // DBへ接続
        $dbh = dbConnect();
        // SQL文作成
        $sql = 'SELECT count(*) FROM patients WHERE user_name = :user_name AND delete_flg = 0';
        $data = array(':user_name' => $patientName);
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        // クエリ結果の値を取得
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        //array_shift関数は配列の先頭を取り出す関数です。クエリ結果は配列形式で入っているので、array_shiftで1つ目だけ取り出して判定します
        if(!empty(array_shift($result))){
            $err_msg[$key] = MSG03;
            $_SESSION['history'] = MSG05;

        }
    } catch (Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
        $_SESSION['history'] = MSG04;
    }
}

//================================
// データベース
//================================
//DB接続関数
//---------------------
function dbConnect() {
    //DB接続準備
    $dbn      = 'mysql:dbname=yuzunosk2998_fastdb;host=mysql7072.xserver.jp;charset=utf8';
    $user     = 'yuzunosk2998_1st';
    $password = 'awwt2998';
    $options   = array(
                        PDO::ATTR_ERRMODE =>PDO::ERRMODE_SILENT,
                        PDO::ATTR_DEFAULT_FETCH_MODE =>PDO::FETCH_ASSOC,
                        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY =>true,
    );
    // PDOクラスを使って、新たなインスタンスを生成
    $dbh = new PDO($dbn , $user , $password , $options);
    return $dbh; //この値をDBと接続する際、情報取得の際に使用する
}

//SQL実行関数
//---------------------
function queryPost($dbh , $sql , $data) {
    $stmt = $dbh->prepare($sql); //prepareは、クラスメソッド？
    if(!$stmt->execute($data)){ //executeメソッドで$dataを$sqlに入れる
        debug('クエリに失敗しました。');
        debug('失敗したSQL:'.print_r($stmt,true)); //print_rで$stmtに代入されたSQLを確認している
        $_SESSION['history'] = MSG04;
        return 0;
    }
    debug('クエリ成功。');
    debug('成功したSQL:'.print_r($stmt,true));
    return $stmt; //結果は$stmtの中に保存されているので、これを返す
}

//=====================
//DBから情報を取得
//=====================
//登録されているスタッフ名を取得
function getStuffNames() {
    debug('登録済みのスタッフ名を取得します');
    //例外処理
    try{
        //DB接続
        $dbh = dbConnect();
        //SQL作成
        $sql = 'SELECT * FROM stuffs WHERE delete_flg = 0';
        $data = array();
//        クエリ実行
        $stmt = queryPost($dbh , $sql , $data);

        if($stmt){
            //クエリ結果の全データを返却
            debug('スタッフ名の取得が成功しました');
            return $stmt->fetchAll();
        }else{
            return false;
        }
    }catch (Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
    }
}

//登録されている患者氏名を取得
function getPatientNames() {
    debug('登録済みの患者名を取得します');
    //例外処理
    try{
        //DB接続
        $dbh = dbConnect();
        //SQL作成
        $sql = 'SELECT * FROM patients WHERE delete_flg = 0';
        $data = array();
        //        クエリ実行
        $stmt = queryPost($dbh , $sql , $data);

        if($stmt){
            //クエリ結果の全データを返却
            debug('患者名の取得が成功しました');
            return $stmt->fetchAll();
        }else{
            return false;
        }
    } catch (Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
    }
}

//登録されているカテゴリー情報を取得
function getJobCategoryData() {
    debug('登録済みの職種情報を取得します');
    //例外処理
    try{
        //DB接続
        $dbh = dbConnect();
        //SQL作成
        $sql = 'SELECT * FROM jobCategory WHERE delete_flg = 0';
        $data = array();
        //        クエリ実行
        $stmt = queryPost($dbh , $sql , $data);

        if($stmt){
            //クエリ結果の全データを返却
            debug('職種情報の取得が成功しました');
            return $stmt->fetchAll();
        }else{
            return false;
        }
    } catch (Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
    }
}

//全ての伝達情報の取得を行う
function getAllTaskData($currentMinNum = 1 , $sort , $span = 10){
    debug('全ての伝達情報を新しい順に取得します');
    //例外処理
    try{
        //DB接続
        $dbh = dbConnect();
        //件数用のSQL作成
        $sql = 'SELECT t.id , t.task_Message , t.senders , t.stuffs , t.patients , t.input_Days , t.delete_flg , t.create_date , t.delete_flg ,  t.update_date , s.user_name AS stuffs , p.user_name AS patients FROM tasks AS t INNER JOIN stuffs AS s ON t.stuffs = s.id LEFT JOIN patients AS p ON t.patients = p.id WHERE s.id = 1 AND t.delete_flg = 0';

        if(!empty($sort)){
            debug('ソート情報があります。');
            switch($sort){
                case 1:
                    $sql .= ' ORDER BY t.update_date DESC';
                    $_SESSION['history'] = '新しい順に並び替えました。';
                    break;
                case 2:
                    $sql .= ' ORDER BY t.update_date ASC';
                    $_SESSION['history'] = '古い順に並び替えました。';
                    break;
            }
        }

        $data = array();
        //        クエリ実行
        $stmt = queryPost($dbh , $sql , $data);

        $result['total'] = $stmt->rowCount(); //総レコード数
        debug('総レコード数'.$result['total']);
        $result['total_page'] = ceil($result['total']/$span); //総ページ数
        debug('総ページ数'.$result['total_page']);
        if(!$stmt){
            return false;
        }

        //ページング用のSQL作成
        $sql = 'SELECT t.id , t.task_Message , t.senders , t.stuffs , t.patients , t.input_Days , t.delete_flg , t.create_date , t.delete_flg ,  t.update_date , s.user_name AS stuffs , p.user_name AS patients FROM tasks AS t INNER JOIN stuffs AS s ON t.stuffs = s.id LEFT JOIN patients AS p ON t.patients = p.id WHERE s.id = 1 AND t.delete_flg = 0';

        if(!empty($sort)){
            switch($sort){
                case 1:
                    $sql .= ' ORDER BY t.update_date DESC';
                    break;
                case 2:
                    $sql .= ' ORDER BY t.update_date ASC';
                    break;
            }
        }

        $sql .= ' LIMIT '.$span.' OFFSET '.$currentMinNum;
        $data = array();
        debug('SQL:'.$sql);
        //クエリ実行
        $stmt = queryPost($dbh , $sql , $data);

        if($stmt){
            //クエリ結果の全データを返却
            debug('全体への伝達情報の取得が成功しました');

            $result['data'] = $stmt->fetchAll();
            return $result;
        }else{
            return false;
        }

    } catch (Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
    }
}





//スタッフへの伝達情報の取得を行う
function getStuffTaskData($u_id , $which , $sort ,$currentMinNum = 1 , $span = 10){
    debug('各スタッフの伝達情報を取得します');
    debug('スタッフID:'.$u_id.'を参照します');
    //例外処理
    try{
        //DB接続
        $dbh = dbConnect();
        //SQL作成
        $sql = 'SELECT t.id , t.task_Message , t.senders , t.stuffs , t.patients , t.input_Days , t.delete_flg , t.create_date , t.update_date , s.user_name AS stuffs , p.user_name AS patients FROM tasks AS t INNER JOIN stuffs AS s ON t.stuffs = s.id LEFT JOIN patients AS p ON t.patients = p.id WHERE s.id = :u_id';


        if(!empty($which) && $which == "2") {
            $sql .= ' AND t.delete_flg = 0';
            $_SESSION['history'] = '未達成の伝達情報を取得しました。';
        }elseif(!empty($which) && $which == "1"){
            $sql .= '';
            $_SESSION['history'] = '全ての伝達情報を取得しました。';
        }else{
            $sql .= ' AND t.delete_flg = 0';
            $_SESSION['history'] = '伝達情報を取得しました。';
        }

        if(!empty($sort)){
            debug('ソート情報があります。');
            switch($sort){
                case 1:
                    $sql .= ' ORDER BY t.update_date DESC';
                    $_SESSION['history'] .= '新しい順に並び替えました。';
                    break;
                case 2:
                    $sql .= ' ORDER BY t.update_date ASC';
                    $_SESSION['history'] .= '古い順に並び替えました。';
                    break;
            }
        }

        $data = array(':u_id' => $u_id);
        //        クエリ実行
        $stmt = queryPost($dbh , $sql , $data);

        $result['total'] = $stmt->rowCount(); //総レコード数
        debug('総レコード数'.$result['total']);
        $result['total_page'] = ceil($result['total']/$span); //総ページ数
        debug('総ページ数'.$result['total_page']);
        if(!$stmt){
            return false;
        }

        //ページング用のSQL作成
        $sql = 'SELECT t.id , t.task_Message , t.senders , t.stuffs , t.patients , t.input_Days , t.delete_flg , t.create_date , t.update_date , s.user_name AS stuffs , p.user_name AS patients FROM tasks AS t INNER JOIN stuffs AS s ON t.stuffs = s.id LEFT JOIN patients AS p ON t.patients = p.id WHERE s.id = :u_id';

        if(!empty($which) && $which == "2") {
            $sql .= ' AND t.delete_flg = 0';
            $_SESSION['history'] = '未達成の伝達情報を取得しました。';
        }elseif(!empty($which) && $which == "1"){
            $sql .= '';
            $_SESSION['history'] = '全ての伝達情報を取得しました。';
        }else{
            $sql .= ' AND t.delete_flg = 0';
            $_SESSION['history'] = '伝達情報を取得しました。';
        }

        if(!empty($sort)){
            debug('ソート情報があります。');
            switch($sort){
                case 1:
                    $sql .= ' ORDER BY t.update_date DESC';
                    $_SESSION['history'] .= '新しい順に並び替えました。';
                    break;
                case 2:
                    $sql .= ' ORDER BY t.update_date ASC';
                    $_SESSION['history'] .= '古い順に並び替えました。';
                    break;
            }
        }

        $sql .= ' LIMIT '.$span.' OFFSET '.$currentMinNum;
        $data = array(':u_id' => $u_id);
        debug('SQL:'.$sql);
        //クエリ実行
        $stmt = queryPost($dbh , $sql , $data);

        if($stmt){
            debug($u_id.'さんのへの伝達情報の取得が成功しました');
            //クエリ結果の全データを返却
            $result['data'] = $stmt->fetchAll();
            return $result;
        }else{
            return false;
        }

    }catch (Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
    }
}



//患者さんへの伝達情報の取得を行う
function getPatiTaskData($p_id , $which , $sort , $currentMinNum = 1 , $span = 10){
    debug('各患者様への伝達情報を取得します');
    debug('患者さんID:'.$p_id.'を参照します');
    $sqlPlus = ' AND t.delete_flg = 0';

    //例外処理
    try{
        //DB接続
        $dbh = dbConnect();
        //SQL作成
        $sql = 'SELECT t.id , t.task_Message , senders , t.stuffs , t.patients , t.input_Days , t.delete_flg , t.create_date , t.update_date , s.user_name AS stuffs , p.user_name AS patients FROM tasks AS t INNER JOIN stuffs AS s ON t.stuffs = s.id LEFT JOIN patients AS p ON t.patients = p.id WHERE p.id = :u_id';

        if(!empty($which) && $which == "2") {
            $sql .= ' AND t.delete_flg = 0';
            $_SESSION['history'] = '未達成の伝達情報を取得しました。';
        }elseif(!empty($which) && $which == "1"){
            $sql .= '';
            $_SESSION['history'] = '全ての伝達情報を取得しました。';
        }else{
            $sql .= ' AND t.delete_flg = 0';
            $_SESSION['history'] = '伝達情報を取得しました。';
        }

        if(!empty($sort)){
            debug('ソート情報があります。');
            switch($sort){
                case 1:
                    $sql .= ' ORDER BY t.update_date DESC';
                    $_SESSION['history'] .= '新しい順に並び替えました。';
                    break;
                case 2:
                    $sql .= ' ORDER BY t.update_date ASC';
                    $_SESSION['history'] .= '古い順に並び替えました。';
                    break;
            }
        }

        $data = array(':u_id' => $p_id);
        //        クエリ実行
        $stmt = queryPost($dbh , $sql , $data);

        $result['total'] = $stmt->rowCount(); //総レコード数
        debug('総レコード数'.$result['total']);
        $result['total_page'] = ceil($result['total']/$span); //総ページ数
        debug('総ページ数'.$result['total_page']);
        if(!$stmt){
            return false;
        }


        //ページング用のSQL作成
        $sql = 'SELECT t.id , t.task_Message , senders , t.stuffs , t.patients , t.input_Days , t.delete_flg , t.create_date , t.update_date , s.user_name AS stuffs , p.user_name AS patients FROM tasks AS t INNER JOIN stuffs AS s ON t.stuffs = s.id LEFT JOIN patients AS p ON t.patients = p.id WHERE p.id = :u_id';

        if(!empty($which) && $which == "2") {
            $sql .= ' AND t.delete_flg = 0';
            $_SESSION['history'] = '未達成の伝達情報を取得しました。';
        }elseif(!empty($which) && $which == "1"){
            $sql .= '';
            $_SESSION['history'] = '全ての伝達情報を取得しました。';
        }else{
            $sql .= ' AND t.delete_flg = 0';
            $_SESSION['history'] = '伝達情報を取得しました。';
        }

        if(!empty($sort)){
            debug('ソート情報があります。');
            switch($sort){
                case 1:
                    $sql .= ' ORDER BY t.update_date DESC';
                    $_SESSION['history'] .= '新しい順に並び替えました。';
                    break;
                case 2:
                    $sql .= ' ORDER BY t.update_date ASC';
                    $_SESSION['history'] .= '古い順に並び替えました。';
                    break;
            }
        }

        $sql .= ' LIMIT '.$span.' OFFSET '.$currentMinNum;
        $data = array(':u_id' => $p_id);
        debug('SQL:'.$sql);
        //クエリ実行
        $stmt = queryPost($dbh , $sql , $data);

        if($stmt){
            //クエリ結果の全データを返却
            debug($p_id.'さんのへの伝達情報の取得が成功しました');
            $result['data'] = $stmt->fetchAll();
            return $result;
        }else{
            return false;
        }

    }catch (Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
    }
}





//スタッフ名取得
function getUserName($u_id){
    debug('ユーザーの名前を取得します');
    //例外処理
    try{
        $dbh  = dbConnect();
        $sql  = 'SELECT * FROM stuffs WHERE stuffs.id = :u_id AND delete_flg = 0';
        $data = array(':u_id' => $u_id);
        //クエリ実行
        $stmt = queryPost($dbh , $sql , $data);
        if($stmt){
            //クエリ結果の全データを返却
            debug('ユーザー名の取得が成功しました');
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
    }catch (Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
    }
};


//スタッフ名取得
function getPatiName($p_id){
    debug('患者様の名前を取得します');
    //例外処理
    try{
        $dbh  = dbConnect();
        $sql  = 'SELECT * FROM patients WHERE patients.id = :p_id AND delete_flg = 0';
        $data = array(':p_id' => $p_id);
        //クエリ実行
        $stmt = queryPost($dbh , $sql , $data);
        if($stmt){
            //クエリ結果の全データを返却
            debug('患者名の取得が成功しました');
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
    }catch (Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
    }
};





//sessionを１回だけ取得できる
function getSessionFlash($key){
    if(!empty($_SESSION[$key])){
        $data = $_SESSION[$key];
        $_SESSION[$key] = '';
        return $data;
    }
};



//GETパラメータ付与 ちょっとよくわからないので不採用
// $del_key : 付与から取り除きたいGETパラメータのキー
function appendGetParam($arr_del_key = array()){
    if(!empty($_GET)){
        $str = '?';
        foreach($_GET as $key => $val){
            if(!in_array($key,$arr_del_key,true)){ //取り除きたいパラメータじゃない場合にurlにくっつけるパラメータを生成
                $str .= $key.'='.$val.'&';
            }
        }
        $str = mb_substr($str, 0, -1, "UTF-8");
        return $str;
    }
}



//ページング
// $currentPageNum : 現在のページ数
// $totalPageNum : 総ページ数
// $link : 検索用GETパラメータリンク
// $pageColNum : ページネーション表示数
function pagination( $currentPageNum , $totalPageNum , $link = '', $pageColNum = 5){
    debug('総ページ数：'.$totalPageNum);
    // 現在のページが、総ページ数と同じ　かつ　総ページ数が表示項目数以上なら、左にリンク４個出す
    if( $currentPageNum == $totalPageNum && $totalPageNum > $pageColNum){
        $minPageNum = $currentPageNum - 4;
        $maxPageNum = $currentPageNum;
        // 現在のページが、総ページ数の１ページ前なら、左にリンク３個、右に１個出す
    }elseif( $currentPageNum == ($totalPageNum-1) && $totalPageNum > $pageColNum){
        $minPageNum = $currentPageNum - 3;
        $maxPageNum = $currentPageNum + 1;
        // 現ページが2の場合は左にリンク１個、右にリンク３個だす。
    }elseif( $currentPageNum == 2 && $totalPageNum > $pageColNum){
        $minPageNum = $currentPageNum - 1;
        $maxPageNum = $currentPageNum + 3;
        // 現ページが1の場合は左に何も出さない。右に５個出す。
    }elseif( $currentPageNum == 1 && $totalPageNum > $pageColNum){
        $minPageNum = $currentPageNum;
        $maxPageNum = 5;
        // 総ページ数が表示項目数より少ない場合は、総ページ数をループのMax、ループのMinを１に設定
    }elseif($totalPageNum < $pageColNum){
        $minPageNum = 1;
        $maxPageNum = $totalPageNum;
        // それ以外は左に２個出す。
    }else{
        $minPageNum = $currentPageNum - 2;
        $maxPageNum = $currentPageNum + 2;
    }

    echo '<div class="pagination">';
    echo '<ul class="pagination-list">';
    if($currentPageNum != 1){
        echo '<li class="list-item"><a href="?p=1'.$link.'">&lt;</a></li>';
    }
    for($i = $minPageNum; $i <= $maxPageNum; $i++){
        echo '<li class="list-item ';
        if($currentPageNum == $i ){ echo 'active'; }
        echo '"><a href="'.$page_url.'?p='.$i.$link.'">'.$i.'</a></li>';
    }
    if($currentPageNum != $maxPageNum && $maxPageNum > 1){
        echo '<li class="list-item"><a href="?p='.$maxPageNum.$link.'">&gt;</a></li>';
    }
    echo '</ul>';
    echo '</div>';
}


//サニタイズ
function sanitize($str){
    return htmlspecialchars($str , ENT_QUOTES);
}

//フォーム入力保持  作ったけどいらない可能性あり
//--------------------
function getFormData($str){
    global $dbFormData;
    //ユーザーデータがある場合
    if(!empty($dbFormData)){
        //フォームのエラーがある場合
        if(!empty($err_msg[$str])){
            //POSTにデータがある場合
            if(isset($method[$str])){
                return sanitize($method[$str]);
            }else{
                //ない場合(基本ありえない)は、DBの情報を表示
                return sanitize($dbFormData[$str]);
            }

        }else{
            //POSTにデータがあり、DBの情報と違う場合
            if(isset($method[$str]) && $method[$str] !== $dbFormData[$str]){
                debug('POSTデータを返します');
                return sanitize($method[$str]);
            }else{
                return sanitize($dbFormData[$str]);
            }
        }

    }else{
        if(isset($method[$str])){
            return sanitize($method[$str]);
        }
    }
}
