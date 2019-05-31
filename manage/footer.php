<div class="manage-footer">
    <div class="container">
        <span>本插件由橙色阳光开发</span>
        <span class="fr">Powered By Z-BlogPHP</span>
    </div>
</div>
<script src="<?php echo $static ?>libs/jquery/jquery-3.4.0.min.js"></script>
<script src="<?php echo $static ?>libs/layui/layui.js"></script>
<script src="<?php echo $static ?>libs/clipboard/clipboard-polyfill.js"></script>
<script>
!function() {
if (!/(Mobile|mobile)/.test(navigator.appVersion)) {
    if (document.body.offsetHeight == document.body.scrollHeight) {
        $('body').addClass('no-scroll');
    } else {
        $('body').removeClass('no-scroll');
    }
}

var $li = $('.menu-list li');
$li.each(function(index, el) {
    var $item = $(el);
    if ($item.find('a').get(0).href == window.location.href) {
        $item.addClass('active');
    }
});
}();
</script>
