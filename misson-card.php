<!--オブジェクト指向を使って、伝達情報をカード化する。-->
<?php
error_log('読み込みました。');

//ミッションカードを格納する変数
$missons = array();
$misson_no = 0;
//クラス ミッションカードの生成

        class MissonCard {

            private $id;//数値を入れたいし、nullを入れたくないので空文字を入れる。
            private $sender_name;//送信者
            private $user_name;
            private $target_name;//対象者
            private $save_days; //データを登録した日
            private $do_days; //内容を実行する日
            private $contents;
            private $delete_flg;//削除フラグの有無

            public function __construct($id , $sender_name , $user_name, $target_name , $save_days, $do_days , $contents , $delete_flg) {
                $this->id        = $id;
                $this->sender    = $sender_name;
                $this->name      = $user_name;
                $this->target    = $target_name;
                $this->save_days = $save_days;
                $this->do_days   = $do_days;
                $this->contents  = $contents;
                $this->delete    = $delete_flg;
            }
//----------------------
//セッター
//----------------------
            public function setID($num){
                $this->id = (int)filter_var($num , FILTER_VALIDATE_FLOAT);
            }

            public function setName($str){
                $this->name = $str;
            }

            public function setTarget($str){
                $this->target = $str;
            }


 //----------------------
//ゲッター
//----------------------
            public function getID(){
                 return $this->id;
            }

            public function getSender(){
                return $this->sender;
            }


            public function getName(){
                //伝達する人物の指定がなかった場合、全体への伝達事項と判断する
                if(empty($this->name)){
                    return '全体伝達事項';
                }
                return $this->name;
            }


            public function getTarget(){
                return $this->target;
            }

            public function getSaveDays(){
                return $this->save_days;
            }
            public function getDoDays(){
                return $this->do_days;
            }

            public function getContents(){
                return $this->contents;
            }

            public function getDelete(){
                return $this->delete;
            }

        }

//クラス：ミッションカード　終了

        //新しいインスタンスを生成する

    //練習用　インスタンス
//        $missons[] = new MissonCard(1, '谷　太一', '7月15日' , '7月10日' , '実施入力の未入力があります。');
//        $missons[] = new MissonCard(2, 'マイケル　木村', '7/15' , '7/11', '実施入力の未入力があります。');
//        $missons[] = new MissonCard(6, '', '' , '' , '');
//        $missons[] = new MissonCard();

//インスタンスを作る関数 DBとのやり取りを行う。


    function createMissonCard($viewData){
        debug('ミッションカードの生成を始めます。');
        global $missons;
        global $misson_no;
        if(!empty($viewData)){
            debug('$viewDataがあります。');
            $count = 0;
            //        debug('$mcの中身は：'.print_r($mc , true)); //中身確認
            foreach($viewData['data'] as $key => $val){
                $missons[] = new MissonCard($val['id'] , $val['senders'] , $val['stuffs'] , $val['patients'], $val['update_date'] , $val['input_Days'] , $val['task_Message'] , $val['delete_flg']);
                $count++;

        }
        debug('成功しました。');
        debug('合計'.$count.'回のミッションカード生成を行いました');
        }
    };

//
// function makeDeck(){
// global $missons;
// };
