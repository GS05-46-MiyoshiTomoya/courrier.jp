function file_upload(id)
{
    // フォームデータを取得
    var comment_form = '#comment_form_' + id;
    var fd = new FormData($(comment_form).get(0));
    var cid = id;
    console.log(fd);
    // POSTでアップロード
    $.ajax({
        url  : "comment_insert.php",
        type: "POST",
        data: fd,
        processData: false,
        contentType: false,
        dataType: 'html'
    })
    .done(function(data, textStatus, jqXHR){
//        var using = '#using_' + id;
//        $(using).html('<form id="comment_form_"'+ id +'><input type = "text" placeholder="コメント" id="inp_comment" class="inp_comment" name="inp_comment" value=""><label for="file_photo"><i class="fa fa-camera" aria-hidden="true"></i><input type="file" id="file_photo" name="file_photo" style="display:none;"></label></form>');
//        var inp_img = '#inp_img_' + id;
//        $(inp_img).html("");
        $('img').remove('.imgView');
        var service_used_comment = '#service_used_comment_' + id;
        console.log(service_used_comment);
        $(service_used_comment).html(data);
        $('#inp_comment').val("");
        $('#file_photo').val("");
        
        
    })
}
