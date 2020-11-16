<?php
//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　Ajax delete処理ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//================================
// Ajax処理
//================================


if(isset($_POST['missonID'])){
    debug('POST送信があります。');
    $m_id = $_POST['missonID'];
    debug('ミッションID：'.$m_id);

//     例外処理
    try{
//        DBへ接続
        $dbh = dbConnect();
        //レコードがあるか検索
        $sql = 'SELECT * FROM tasks WHERE id = :m_id AND delete_flg = 0';
        $data = array(':m_id' => $m_id);
//        クエリ実行
        $stmt = queryPost($dbh , $sql , $data);
        $resultCount = $stmt->rowCount();
        debug($resultCount.'件の未達成データが見つかりました。');
//        １つでもレコードがあるならば
        if(!empty($resultCount)){
            debug('このデータに削除フラグを立てます。');
            $sql = 'UPDATE tasks SET delete_flg = 1 WHERE id = :m_id';
            $data = array(':m_id' => $m_id);
//            クエリ実行
            $stmt = queryPost($dbh , $sql , $data);
            debug('データに削除フラグを挿入しました。');
        }else{
            debug('このデータの削除フラグを解除します');
            $sql = 'UPDATE tasks SET delete_flg = 0 WHERE id = :m_id';
            $data = array(':m_id' => $m_id);
            //            クエリ実行
            $stmt = queryPost($dbh , $sql , $data);
            debug('削除フラグを解除しました。');
        }


    } catch (Exception $e){
        error_log('エラー発生' , $e->getMessage());
    }
}
debug('Ajax処理終了===========================');
?>
