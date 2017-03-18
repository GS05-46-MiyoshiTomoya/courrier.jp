$(function (){
    $('grid__item').on('click',function () {
        {
            // いいね機能
            // いいね有無の取得
            var data = {
                id: id,
                up_user_id: $('#user_id').val(),
            };

            /**
             * Ajax通信メソッド
             */
            $.ajax({
                type: "POST",
                url: "update_favorite.php",
                data: data,
                success: function (data, dataType) {
                    // いいねの返却値取得
                    $(form_id).html(data);
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    alert('Error : ' + errorThrown);
                }
            });
        }
        return false;
    });

});