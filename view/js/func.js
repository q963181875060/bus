/**
 *
 * @param url
 * @returns {boolean}
 */
function gourl(url) {
    lock();
    window.location.href = url;
    return false;
}

function alerts() {
    var content = arguments[0];
    alert(content);
}

/**
 * 锁定屏幕，显示加载中
 */
function lock() {
    //$('#lock').show();
   /* var popu = document.createElement("div");
    popu.setAttribute('id','lock');
    popu.innerHTML = '<div id="shadow_loading"></div><div id="shadow_bg"></div>';
    document.body.appendChild(popu);*/
}

/**
 * 解锁屏幕
 */
function unlock(){
    //$('#lock').hide();
   // $('#lock').remove();
}


function test(t) {
    alerts(t);
}




/*------------------------------------------------------*/
jQuery.fn.center = function () {
    this.css("position","absolute");
    this.css("top", ( $(window).height() - this.height() ) / 2+$(window).scrollTop() + "px");
    this.css("left", ( $(window).width() - this.width() ) / 2+$(window).scrollLeft() + "px");
    return this;
};


$(function(){

    /**
     * 模拟复选框
     * --------------------------------------------------------------
     * @data-status true/false 是否默认勾选 必须
     * @data-true-value 勾选后input的值 不必须 默认为 1
     * @data-false-value 取消勾选后input的值 不必须 默认为 0
     * @data-func 点击后执行的其他操作
     * <i class="checkbox" data-status="false" data-true-value="{$vo.class_id}" data-false-value="0">
     <input type="checkbox" name="class_id" value="0" />
     </i>
     */
    $('i.checkbox').click(function(){
        //选中所赋的值
        var true_val = $(this).attr('data-true-value') == undefined ? 1 : $(this).attr('data-true-value');

        //不选中所赋的值
        var false_val = $(this).attr('data-false-value') == undefined ? 0 : $(this).attr('data-false-value');

        if( $(this).attr('data-status') == 'true' ) {
            $(this).attr('data-status','false');
            $(this).find('input').val(false_val);
            var is_check = false;
        } else {
            $(this).attr('data-status','true');
            $(this).find('input').val(true_val);
            var is_check = true;
        }

        //处理其他事件
        if( $(this).attr('data-func') != '' && $(this).attr('data-func') != undefined ) {
            var funcName = $(this).attr('data-func');

            //注册是勾选同意协议
            if( funcName == 'gree' ) {
                if( is_check ) {
                    $('#reg_submit').addClass('btn-red').attr("disabled", false);
                } else {
                    $('#reg_submit').removeClass('btn-red').attr("disabled", true);
                }
            }

        }
    })



    /**
     * 模拟单选框
     * --------------------------------------------------------------
     * @data-status true/false 是否默认勾选 必须
     * @data-value 勾选后input的值 不必须 默认为 1
     * @data-func 点击后执行的其他操作
     * <div class="radio-box">
     * <i class="radio" data-status="true" data-key="class_id" data-value="0"></i>
     * <i class="radio" data-status="false" data-key="class_id" data-value="{$vo.class_id}"></i>
     * <input type="radio" name="class_id" id="class_id" value="0" />
     * </div>
     */
    $('.radio-box i').on('click',function(){
        //选中所赋的值
        var true_val = $(this).attr('data-value') == undefined ? 1 : $(this).attr('data-value');

        //要赋值的对象
        var key = $(this).attr('data-key');

        $('.radio-box i').attr('data-status','false');
        $(this).attr('data-status','true');
        $('#' + key).val(true_val);

        //处理其他事件
        if( $(this).attr('data-func') != '' && $(this).attr('data-func') != undefined ) {
            var funcName = $(this).attr('data-func');

            //test
            if( funcName == 'test' ) {
                alert('test');
            }

        }
    })



    //点击返回 exp: <div id="back" data-url="" data-go=""></div>
    $('#back').click(function(){
        if( $(this).attr('data-confirm') != '' && $(this).attr('data-confirm') != undefined ) {
            var zh = $(this).attr('data-confirm');
            if(!confirm(zh)){
                return false;
            }
        }
        if( $(this).attr('data-url') != '' && $(this).attr('data-url') != undefined ) {
            window.location.href = $(this).attr('data-url');
        } else {
            var history_go = -1;
            if( $(this).attr('data-go') != '' && $(this).attr('data-go') != undefined ){
                history_go = $(this).attr('data-go');
            }
            window.history.go(history_go);
        }
    })


    //弹窗
    $('.popup_select').on('click',function(){
        var popu = document.createElement("div");
        popu.setAttribute('id','shadow_bg');
        document.body.appendChild(popu);

        var objID = $(this).attr('data-pop');
        $('#' + objID).show().center();
    })
    //弹窗选择后关闭
    $('.select_div ul li').on('click',function(){
        $('#shadow_bg').remove();
        var id = $(this).closest('.select_div').attr('id');
        $(":input[data-pop="+id+"]").val($(this).attr('data-val'));
        $('#' + id).hide();
    })


    /**
     * 分享 引导
     */
    $('.toShare').click(function(){
        var html = '<div id="mcover" onclick="close_share_tip()"><img src="/Public/home/images/guide.png" /></div>';
        $('body').append(html);
    })

});

//关闭分享引导
function close_share_tip() {
    $('#mcover').remove()
}







/**
 * 图片上传函数
 * @param files         文件表单object
 * @param upload_url    服务器路径
 * @param formID       表单ID
 * @param ProcessID         进程ID，当一个页面多次调用此函数时，以此ID区别回调函数要处理的对象,结果集原样返回
 * @param callback      上传成功后回调函数
 * arguments[6]         返回当前上传进度百分比
 * arguments[7]         返回上传前的内存图片地址
 */
function upload_base64image(files, upload_url, formID, inputID, ProcessID, callback ) {
    //回调返回当前上传进度
    var callPercent = arguments[6] ? arguments[6] : false;
    //回调返回上传前的内存图片地址
    var callBefore = arguments[7] ? arguments[7] : false;
    // 获取目前上传的文件
    var URL = window.URL || window.webkitURL;
    // 通过 file 生成目标 url
    var imgURL = URL.createObjectURL(files);
    //创建图片对象;
    var img = new Image();
    img.src = imgURL;
    img.onload = function(){
        var width = img.width;
        if( width > 640 ) width = 640;
        //使用lrz插件压缩图片成base64编码
        lrz(files, {width:width}, function (lrz_results) {

            $("#"+inputID).val(lrz_results.base64); //将生成的base64图片编码加入表单
            /* AJAX File upload Progress */
            var options = {
                //向服务器发送请求前执行一些动作
                beforeSend: function() {
                    if(callBefore) callBefore(imgURL);
                },
                //监听上传进度
                uploadProgress: function(event, position, total, percentComplete) {
                    if(callPercent) callPercent(percentComplete);
                },
                url: upload_url,
                dataType:'json',
                success: function (result) {
                    result.ProcessID = ProcessID;
                    //执行回调函数
                    callback(result);

                    //释放内存中 url 的伺服, 使之无效
                    URL.revokeObjectURL(imgURL);
                },
                error: function (result) {
                    result.ProcessID = ProcessID;
                    result.info = '网络错误';
                    //释放内存中 url 的伺服, 使之无效
                    URL.revokeObjectURL(imgURL);
                    callback(result);
                }
            };
            $('#' + formID).ajaxSubmit(options);

        })

    }

}
