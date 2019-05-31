<?php
require '../../../../zb_system/function/c_system_base.php';
require '../../../../zb_system/function/c_system_admin.php';

$zbp->Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('os_copy')) {$zbp->ShowError(48);die();}

$static = $zbp->host . 'zb_users/plugin/os_copy/static/';

$form_list = array(
    array('input-switch', 'offArticleInsertCopy', '关闭插入复制块按钮', ''),
    array('input-switch', 'offArticleFooterCopy', '关闭底部复制块功能', ''),
    array('input-switch', 'openPreCodeCopy', '开启代码块复制功能', ''),
    array('input-text', 'footer_btn_text', '底部复制块按钮名称', '默认“复制”'),
    array('input-text', 'btn_text', '所有的复制按钮名称', '默认“复制”'),
    array('select', 'btn_type', '底部复制块按钮类型', array(
        'default'   => '底部复制块下方',
        'right'     => '底部复制块右上角',
        'left'      => '底部复制块左上角',
    )),
    array('select', 'btn_footer_color', '底部复制块按钮颜色', array(
        'normal'    => '装逼蓝 #3a6ea5',
        'black'     => '黑又粗 #000000',
        'pink'      => '小粉红 #ffc0cb',
        'wennai'    => '文乃红 #ff6666',
        'green'     => '草头绿 #66cc99',
        'wechat'    => '微信绿 #44b549',
    )),
    array('select', 'copyContentCenterType', '复制块内容是否居中', array(
        'normal'    => '不居中',
        'all'       => '全部类型都居中',
        'footer'    => '底部复制块居中',
        'replace'   => '中间插入的居中',
    )),
    array('input-switch', 'offClipboardJS', '关闭ClipboardJS插入', ''),
    array('input-switch', 'offLayerJS', '关闭LayerJS插入', ''),
);
foreach ($form_list as $k => $v) {
    $key = $v[1]; // PHP7兼容特性，不允许对象键值后为数组键引用
    $val = $zbp->Config('os_copy')->$key;
    $form_list[$k][4] = empty($val)?'':$val;
}

?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge, Chrome=1" />
    <meta name="renderer" content="webkit" />
    <meta http-equiv="Cache-Control" content="no-transform" />
    <meta name="format-detection" content="telephone=no, email=no">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>基础配置 - 一键复制插件 - <?php echo $zbp->name ?></title>
    <link rel="stylesheet" href="<?php echo $static ?>libs/layui/css/layui.css" >
    <link rel="stylesheet" href="<?php echo $static ?>iconfont/iconfont.css" >
    <link rel="stylesheet" href="<?php echo $static ?>css/admin.css" >
    <style>
        .layui-form-label {
            width: 160px;
        }
        .layui-input-block {
            margin-left: 180px;
        }
    </style>
</head>
<body class="page-bg">
<?php require './header.php' ?>

<div class="main-container container">
    <form class="layui-form" action="./save.php?type=base" method="post">
        <?php require './layui-form-item.php' ?>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <p class="tips" style="margin-bottom: 10px;">ClipboardJS为https://github.com/lgarron/clipboard-polyfill</p>
                <p class="tips" style="margin-bottom: 10px;">LayerJS为http://layer.layui.com</p>
                <p>如果你使用的主题已经调用这两个JS，请关闭它们，减少再次引入浪费的流量</p>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit lay-filter="save">保存配置</button>
            </div>
        </div>
    </form>
</div>

<?php require './footer.php' ?>
<script>
window.__page_option = {}
layui.use(['form', 'layer'], function() {
    var form = layui.form
    form.on('submit(save)', function(e) {
        var data = e.field
        data.offArticleInsertCopy = typeof data.offArticleInsertCopy === 'undefined' ? '0' : '1'
        data.offArticleFooterCopy = typeof data.offArticleFooterCopy === 'undefined' ? '0' : '1'
        data.openPreCodeCopy = typeof data.openPreCodeCopy === 'undefined' ? '0' : '1'
        layer.load(2, {
            shade: [.5, '#000']
        })
        $.ajax({
            type: "post",
            url: e.form.action,
            data: data,
            dataType: "json",
            success: function(res) {
                layer.msg(res.message)
            },
            complete: function() {
                layer.closeAll('loading')
            }
        });
        return false;
    })
});
</script>
</body>
</html>