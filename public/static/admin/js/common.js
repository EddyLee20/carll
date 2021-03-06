/**
 * 用于将$('form').serializeArray()获取的数组转换为对象
 */
window.formArray2Data=function(array=[]){
    var data={};
    for (var i = 0; i < array.length; i++) {
        data[array[i].name]=array[i].value;
    }
    return data;
};

/**
 * 类Vue路由设计
 */
var router;
var url = window.location.href;
function initRouter() {
    if (url.indexOf("#/") > -1) {
        router = url.substring(url.indexOf('#/') + 2);
        if (router === '') {
            router = 'admin/index';
        }
        $('#iframeParent').attr('src', '/admin/index./' + router);
    } else {
        $('#iframeParent').attr('src', '/admin/index./info');
        // history.replaceState(null, null, './');
    }
    //地址栏修改不刷新的解决方案
    $('a').click(function () {
        if ($(this).attr('href')) {
            window.location.href = $(this).attr('href');
            window.location.reload();
        }
    });
}

var layuiModules=[];
var useKindEditor=false;
window.globalConfig = {router:url.substring(url.indexOf('#/') + 2)};

$(function () {

    //加载layui模块
    layui.use(layuiModules, function () {
        for (let layuiModule of layuiModules) {
            eval(`window.${layuiModule}=layui.${layuiModule}`);
        }
        if(useKindEditor){
            KindEditor.ready(function (K) {
                window.KindEditor=K;
                mounted();
            });
        }else{
            mounted();
        }

    });
});

