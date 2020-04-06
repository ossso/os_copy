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
    array('input-switch', 'hidePreCodeName', '隐藏代码块代码名称', ''),
    array('input-text', 'copyContentBackground', '复制内容块的背景色', '留空为默认“#f5f5f5”'),
    array('input-text', 'footerBtnText', '文章底部复制块按钮', '留空为默认“复制”'),
    array('select', 'footerBtnType', '底部复制块按钮类型', array(
        'bottom'        => '复制块下方',
        'top-right'     => '复制块右上角',
        'top-left'      => '复制块左上角',
        'bottom-right'  => '复制块右下角',
        'bottom-left'   => '复制块左下角',
    )),
    array('select', 'footerBtnColor', '底部复制块按钮颜色', array(
        'normal'    => '装逼蓝 #3a6ea5',
        'black'     => '黑又粗 #000000',
        'pink'      => '小粉红 #ffc0cb',
        'wennai'    => '文乃红 #ff6666',
        'green'     => '草头绿 #66cc99',
        'wechat'    => '微信绿 #44b549',
        'tianqing'  => '晴天赞助色 #49afcd',
    )),
    array('input-switch', 'showFooterSlideBtn', '底部复制块收展按钮', ''),
    array('input-text', 'insertMultiBtnText', '插入的多行复制按钮', '留空为默认“复制”'),
    array('select', 'insertBtnType', '插入复制块按钮类型', array(
        'top-right'     => '复制块右上角',
        'bottom'        => '复制块下方',
        'top-left'      => '复制块左上角',
        'bottom-right'  => '复制块右下角',
        'bottom-left'   => '复制块左下角',
    )),
    array('select', 'insertBtnColor', '插入复制块按钮颜色', array(
        'normal'    => '装逼蓝 #3a6ea5',
        'black'     => '黑又粗 #000000',
        'pink'      => '小粉红 #ffc0cb',
        'wennai'    => '文乃红 #ff6666',
        'green'     => '草头绿 #66cc99',
        'wechat'    => '微信绿 #44b549',
        'tianqing'  => '晴天赞助色 #49afcd',
    )),
    array('input-switch', 'showInsertSlideBtn', '插入复制块收展按钮', ''),
    array('input-text', 'insertSingleBtnText', '插入的单行复制按钮', '留空为默认“复制”'),
    array('input-checkbox', 'copyContentCenter', '复制块内容要居中的', array(
        'footer'    => '文章底部复制块',
        'multi'     => '文章插入的多行',
        'single'    => '文章插入的单行',
    )),
    array('input-checkbox', 'copyBtnContentCenter', '复制块下会居中按钮', array(
        'footer'    => '文章底部的按钮',
        'multi'     => '文章插入多行的按钮',
    )),
    array('input-switch', 'offClipboardJS', '取消ClipboardJS', ''),
    array('input-switch', 'offLayerJS', '取消LayerJS', ''),
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
                <p>复制文章块中，除了背景色以外，其余内容多为的继承主题样式。</p>
                <p>如果你使用的主题已经调用这两个JS，请关闭它们，减少再次引入浪费的流量。</p>
                <p>按钮居中：在按钮处于复制块下方时居中。</p>
                <p>收展按钮：折叠|展开，仅在按钮处于复制块下方时显示。</p>
                <p>插入的单行复制块，按钮永远固定在右侧上下居中位置；内容只会显示一行。</p>
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
layui.use(['form', 'layer'], function() {
    var form = layui.form
    form.on('submit(save)', function(e) {
        var data = e.field;
        data.offArticleInsertCopy = typeof data.offArticleInsertCopy === 'undefined' ? '0' : '1';
        data.offArticleFooterCopy = typeof data.offArticleFooterCopy === 'undefined' ? '0' : '1';
        data.openPreCodeCopy = typeof data.openPreCodeCopy === 'undefined' ? '0' : '1';
        data.hidePreCodeName = typeof data.hidePreCodeName === 'undefined' ? '0' : '1';
        data.showFooterSlideBtn = typeof data.showFooterSlideBtn === 'undefined' ? '0' : '1';
        data.showInsertSlideBtn = typeof data.showInsertSlideBtn === 'undefined' ? '0' : '1';
        layer.load(2, {
            shade: [.5, '#000'],
        });
        $.ajax({
            type: "post",
            url: e.form.action,
            data: data,
            dataType: "json",
            success: function(res) {
                layer.msg(res.message);
            },
            complete: function() {
                layer.closeAll('loading');
            },
        });
        return false;
    })
});
</script>
</body>
</html>
