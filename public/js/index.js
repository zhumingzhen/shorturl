/**
 * Created by Administrator on 2017/11/15.
 */
$(document).ready(function(){
    $("#lBtn").click(function(){
        var lUrl = $("#lUrl").val();
        //var reg=/^([hH][tT]{2}[pP]:\/\/|[hH][tT]{2}[pP][sS]:\/\/)(([A-Za-z0-9-~]+)\.)+([A-Za-z0-9-~\/])+$/;
        var reg=/(http|ftp|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&:/~\+#]*[\w\-\@?^=%&/~\+#])?/;
        if(!reg.test(lUrl)){
            alert("网址不是以 http:// 或 https:// 开头，或者不是网址！");
        }
        else{
            // alert("输入成功");
            //Ajax调用处理
            $.ajax({
                type: "post",
                url: "http://2dw.win/longtoshort",
                // data: "para="+para,  此处data可以为 a=1&b=2类型的字符串 或 json数据。
                data: {"lUrl":lUrl},
                cache: false,
                async : false,
                dataType: "json",
                success: function (data ,textStatus, jqXHR)
                {
                    if("200"==data.code){
                        alert(data.msg);
                        sUrl = data.data;
                        $("#sUrl").text(data.data);
                        $("#copySUrl").attr("data-clipboard-text",data.data);
                        return true;
                    }else{
                        alert("不合法！错误信息如下："+data.msg);
                        return false;
                    }
                },
                error:function (XMLHttpRequest, textStatus, errorThrown) {
                    alert("页面太火爆，请稍后重试。。。");
                }
            });
        }
    });

    var clipboard = new Clipboard('#copySUrl');
    clipboard.on('success', function(e) {
        console.log(e);
        alert("短链复制成功")
    });
    clipboard.on('error', function(e) {
        console.log(e);
        alert("短链复制失败！请手动复制")
    });
});