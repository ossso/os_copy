!function() {
var transformHtml = function(str) {
    return str.replace(/\u00a0/g, ' ');
}
$('#os-copy-insert-script').remove();
$('.os-copy-btn').on('click', function() {
    var $cont = $(this).parents('.os-copy-mode').find('.os-copy-content');
    if (!$cont.length) return false;
    var text = $cont.get(0).innerText;
    if (text) {
        console.log(text.charCodeAt(0))
        text = transformHtml(text);
        var dt = new clipboard.DT();
        dt.setData('text/plain', text);
        clipboard.write(dt);
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
            var $btn = $('<span class="os-copy-copy-code-btn">复制</span>').appendTo($showElem)
            .attr('data-code-key', index)
            .on('click', function() {
                var key = $(this).attr('data-code-key');
                var $codeElem = $('.prism-highlight[data-code-index="' + key + '"]');
                var cont = $codeElem.text();
                cont = transformHtml(cont);
                var dt = new clipboard.DT();
                dt.setData('text/plain', cont);
                clipboard.write(dt);
                layer.msg('已复制到剪贴板');
            });
            if (window.osCopyhidePreCodeName) {
                $btn.css('right', 0)
            } else {
                $btn.css('right', $showElem.find('.prism-show-language-label').width() + 20)
            }
        });
    });
}
}();
