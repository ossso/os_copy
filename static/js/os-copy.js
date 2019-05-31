!function() {
$('#os-copy-insert-script').remove();
$('.os-copy-btn').on('click', function() {
    var $cont = $(this).parents('.os-copy-mode').find('.os-copy-content');
    if (!$cont.length) return false;
    var text = $cont.get(0).innerText;
    if (text) {
        clipboard.writeText(text);
        try {
            layer.msg('已复制到剪贴板');
        } catch (e) {
            alert('已复制到剪贴板');
        }
    }
});
$('.os-slide-btn').on('click', function() {
    var $mode = $(this).parents('.os-copy-mode');
    if (!$mode.length) return false;
    if ($mode.hasClass('os-copy-mode-hide')) {
        $(this).html('折叠');
    } else {
        $(this).html('展开');
    }
    $mode.toggleClass('os-copy-mode-hide');
});
if (window.osCopyEnablePreCode) {
    $(function() {
        var $elems = $('.prism-highlight');
        $elems.each(function(index) {
            $(this).attr('data-code-index', index);
            var $showElem = $(this).prev('.prism-show-language');
            $('<span class="os-copy-copy-code-btn">复制</span>').appendTo($showElem)
            .attr('data-code-key', index)
            .css('right', $showElem.find('.prism-show-language-label').width() + 20)
            .on('click', function() {
                var key = $(this).attr('data-code-key');
                var $codeElem = $('.prism-highlight[data-code-index="' + key + '"]');
                clipboard.writeText($codeElem.text());
                layer.msg('已复制到剪贴板');
            });
        });
    });
}
}();
