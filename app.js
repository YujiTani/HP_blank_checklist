//-----------------
//javascript
//-----------------
$(function () {


    var $SE01 = $('#SE01').get(0);
    var $SE02 = $('#SE02').get(0);
    var $comp;
    var compId;
    $SE01.volume = .2;
    $SE02.volume = .2;

    //変数設定
    let textCount = 0;
    let selectRoute = "";



    //---------------------
    //プルダウンメニュー
    //---------------------

    //    alert('読み込みました');
    $('.js-down-guide').on('click', function () {
        //        alert('クリックしました');
        if ($('.js-click-close').hasClass('display-none')) {

            $('.js-click-rotation').toggleClass('display-none');
            $('.form-guide-message').slideToggle("slow");


        } else {
            $('.js-click-rotation').toggleClass('display-none');
            $('.js-down-menu:not(:animated)').slideToggle("slow");
        }
    });



    //---------------------
    //Ajaxでの表示切り替え
    //---------------------

    //    error_log('デバッグ3：'.inputSelect);
    //    alert('読み込みました。');

    $('#js-formBtn').click(function (e) { // formのsubmitに処理を登録する、引数としてEventのオブジェクトeを受け取る
        e.preventDefault(); // これを一行目に追加
        //        alert('ボタンが押されました。');

        selectRoute = $('#js-getVal').val();
        console.log('ajax通信を行います');
        $.ajax({
            "type": 'POST',
            "url": 'ajax-html.php',
            "dataType": 'html',

            "data": {
                route: selectRoute
            }

        }).done(function (data, textStatus, xhr) {
            console.log("ajax通信に成功しました");
            console.log(data);
            console.log(xhr);
            if (selectRoute == "01") {

                $('.js-ajax-toggle-1').removeClass(function () {
                    return 'display-none';
                });
                $('.js-ajax-toggle-2').addClass(function () {
                    return 'display-none';
                });
                $('.js-ajax-toggle-3').addClass(function () {
                    return 'display-none';
                });


            } else if (selectRoute == "02") {
                $('.js-ajax-toggle-2').removeClass(function () {
                    return 'display-none';
                });
                $('.js-ajax-toggle-1').addClass(function () {
                    return 'display-none';
                });
                $('.js-ajax-toggle-3').addClass(function () {
                    return 'display-none';
                });


            } else if (selectRoute == "03") {
                $('.js-ajax-toggle-3').removeClass(function () {
                    return 'display-none';
                });
                $('.js-ajax-toggle-1').addClass(function () {
                    return 'display-none';
                });
                $('.js-ajax-toggle-2').addClass(function () {
                    return 'display-none';
                });

            } else {
                alert('入力が正しくありません。');
            }


        }).fail(function () {
            console.log("ajax通信に失敗しました");
        });
    });




    //----------------------
    //文字数カウント
    //----------------------

    $(".ajax-text-lengthCount").on('keyup keydown', function (e) {
        //        alert('ボタンが離れました');
        let text_length = $(this).val().length;
        console.log(text_length);
        if (text_length <= 500) {
            //            alert('500以下です');
            $("#js-count").text(text_length); //現在の文字数を表示

        } else if (text_length > 500) {
            popmsg('警告:文字数オーバー', '500文字の以上入力があるため登録が出来ません！');
            $("#js-count").css({
                "color": "#ff0000",
            });
            $(".ajax-text-lengthCount").css({
                "boder": "2px solid #ff0000",
            });
            $(".js-count").text(text_length); //現在の文字数を表示
        }

    });




    //--------------------------
    //ポップアップメッセージ
    //--------------------------

    function popmsg($str, $str2) {
        var $jsShowMsg = $('.js-show-msg');

        //全体のサイズ測定
        let w = $(window).width();
        let h = $(window).height();
        //対象のサイズ測定
        var popWidth = $jsShowMsg.width();
        var popHeight = $jsShowMsg.height(); //画面全体の表示領域を取得し、収納している

        //画面中央に表示されるように設定
        $($jsShowMsg).css({
            "left": ((w - popWidth) / 2) + "px",
            "top": ((h - popHeight) / 2) + "px"
        });

        $('.js-popMsg').text($str);
        $('.js-popMsg2').text($str2);
        $('.js-show-msg:not(:animated)').slideDown('slow'); //スピードを設定できる　

        $('*').on('click', function () {
            $('.js-show-msg:not(:animated)').slideUp('slow');
        });
        setTimeout(function () {
            $('.js-show-msg:not(:animated)').slideUp('slow');
        }, 3500);
    };



    //-------------------------
    //動作連絡用のメッセージ
    //-------------------------

    var $jsShowMsg = $('#js-show-msg');
    var msg = $jsShowMsg.text();
    if (msg.replace(/^[\s　]+|[\s　]+$/g, "").length) {
        $jsShowMsg.slideToggle('slow');
        setTimeout(function () {
            $jsShowMsg.slideToggle('slow');
        }, 5000);
    };




    //-------------------------------
    //member.php モダールウインドウ
    //-------------------------------

    $('#js-click-modal1').on('click', function () {
        //        alert('押しました。');
        if ($('#js-show-modal2').hasClass('display-none')) {
            $('#js-show-modal1').removeClass('display-none');
            $('#js-blind').removeClass('display-none');
        } else {
            $('#js-show-modal1').addClass('display-none');
            $('#js-blind').addClass('display-none');
        }
    })
    $('#js-click-modal2').on('click', function () {
        //        alert('押しました。');
        if ($('#js-show-modal1').hasClass('display-none')) {
            $('#js-show-modal2').removeClass('display-none');
            $('#js-blind').removeClass('display-none');
        } else {
            $('#js-show-modal2').addClass('display-none');
            $('#js-blind').addClass('display-none');
        }
    })
    $('#js-blind').on('click', function () {
        $('#js-show-modal1').addClass('display-none');
        $('#js-show-modal2').addClass('display-none');
        $('#js-blind').addClass('display-none');
    });



    //----------------------------------------
    //member.php モダールウインドウの中のp要素取得
    //----------------------------------------

    $('.js-modal-item').on('click', function () {
        //        alert('送信されたよ');
        let result = $(this).attr('id');
        console.log(result);
        //submitされた中のvalとidを取得し、$_GETに収納する処理を行う
    });




    //----------------------------------
    //ミッションカード 達成：未達成 ajax処理
    //----------------------------------


    $('.js-html-change:not(:animated)').on('click', function () {
        //        alert('クリックしました。');
        let $this = $(this);
        compId = $this.data('id');
        console.log(compId);
        $.ajax({
            type: "POST",
            url: "ajax-delete.php",
            data: {
                missonID: compId
            }
        }).done(function (data) {
            console.log('Ajax Success');
            if (!$this.hasClass('success')) {
                $SE01.play();
                $this.addClass('jumpRoll').html('達成！').addClass('success').removeClass('failed');
                $this.one('animationend', function () {
                    $this.removeClass('jumpRoll');
                });
            } else {
                $SE02.play();
                $this.addClass('jumpRoll').html('未達成').addClass('failed').removeClass('success');
                $this.one('animationend', function () {
                    $this.removeClass('jumpRoll');
                });
            };
        }).fail(function (msg) {
            console.log('Ajax Error');
        });
    });



    //---------------------
    //TOPへ移動する
    //    ---------------------
    $('.js-click-top').on('click', function () {
        //        alert('押しました');
        //スクロールのスピード
        var speed = 500;
        //スムーススクロール
        $('html,body').animate({
            scrollTop: 0
        }, speed, 'swing');
    });


    ////---------------------
    ////音楽再生
    ////---------------------
    // .get()が必要です
    // もしくは document.getElementById('mp3');
    //var $audio = $('#bgm').get(0);
    //var $voice1 = $('#voice1').get(0);
    //var $voice2 = $('#voice2').get(0);
    //
    //// .volume で音量（0.1から1.0）を変更
    //$audio.volume = 0.2;
    //$voice1.volume = 1;
    //$voice2.volume = 1;
    //
    //// クリックして音楽を再生
    //$(function () {
    //    $('.js-bgm-btn').on('click', function () {
    //        $('.js-birthday-img').removeClass('display-none');
    //        // 再生するときは .play()
    //        setTimeout(function () {
    //            $audio.play();
    //            $voice1.play();
    //        }, 1000);
    //        setTimeout(function () {
    //            $voice2.play();
    //        }, 300000);
    //    })
    //});


});
