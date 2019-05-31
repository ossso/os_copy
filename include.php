<?php
#注册插件
RegisterPlugin('os_copy', 'ActivePlugin_os_copy');

function ActivePlugin_os_copy() {
    Add_Filter_Plugin('Filter_Plugin_ViewPost_Template','os_copy_ViewPost_Template');
    Add_Filter_Plugin('Filter_Plugin_Edit_Response','os_copy_Edit_Response');
    Add_Filter_Plugin('Filter_Plugin_Edit_Response3','os_copy_Edit_Response3');
}
function InstallPlugin_os_copy() {}
function UninstallPlugin_os_copy() {}

/**
 * 插入内容
 */
function os_copy_ViewPost_Template() {
    global $zbp;
    $article = $zbp->template->templateTags['article'];

    os_copy_RelpaceCopyMulti($article);
    os_copy_RelpaceCopySingle($article);

    $htmlCode = '';
    /**
     * 底部插入复制块
     */
    if ($zbp->Config('os_copy')->offArticleFooterCopy != '1' && $article->Metas->os_copy_content) {
        $htmlCode .= os_copy_GetCopyHTML($article->Metas->os_copy_content, 'footer');
    }
    /**
     * 插入样式与脚本
     */
    $insertList = array($zbp->host . 'zb_users/plugin/os_copy/static/js/os-copy.min.js');
    if (!$zbp->Config('os_copy')->offClipboardJS == '1') {
        array_push($insertList, $zbp->host . 'zb_users/plugin/os_copy/static/libs/clipboard/clipboard-polyfill.js');
    }
    if (!$zbp->Config('os_copy')->offLayerJS == '1') {
        array_push($insertList, $zbp->host . 'zb_users/plugin/os_copy/static/libs/layer/layer.js');
    }
    $insertJS = '';
    foreach ($insertList as $item) {
        $insertJS .= 'insertScript("' . $item . '");';
    }
    $openPreCodeCopyBtn = '';
    if ($zbp->Config('os_copy')->openPreCodeCopy == '1') {
        $openPreCodeCopyBtn = 'window.osCopyEnablePreCode = true;';
    }
    $htmlCode .= '
    <script id="os-copy-insert-script">
    !function() {
        var head = document.getElementsByTagName("head")[0];
        var link = document.createElement("link");
        head.appendChild(link);
        link.setAttribute("rel", "stylesheet");
        link.setAttribute("type", "text/css");
        link.setAttribute("href", "' . $zbp->host . 'zb_users/plugin/os_copy/static/css/style.min.css");
        function insertScript(jsPath) {
            var script = document.createElement("script");
            script.src = jsPath;
            document.body.appendChild(script);
        }
        ' . $insertJS . '
        ' . $openPreCodeCopyBtn . '
    }();
    </script>';
    $article->Content .= $htmlCode;
}

/**
 * 替换内容中的复制块
 */
function os_copy_RelpaceCopyMulti(&$article) {
    global $zbp;

    $pattern = "/<p>\[os\-copy\]\<\/p>(.*)<p>\[\/os\-copy\]<\/p>/Ui";
    preg_match_all($pattern, $article->Content, $matchContent);
    $list = $matchContent[0];
    if (!$list || count($list) == 0) {
        return false;
    }

    $btnText = $zbp->Config('os_copy')->btn_text;
    $btnText = empty($btnText) ? '复制' : $btnText;
    foreach ($list as $item) {
        preg_match($pattern, $item, $matches);
        if (count($matches) > 1) {
            $copyItem = os_copy_GetCopyHTML($matches[1], 'multi');
            $article->Content = str_replace($matches[0], $copyItem, $article->Content);
        }
    }
}

/**
 * 替换内容中的复制块
 */
function os_copy_RelpaceCopySingle(&$article) {
    global $zbp;

    $pattern = "/<p>\[os\-copy\-single\]\<\/p>(.*)<p>\[\/os\-copy\-single\]<\/p>/Ui";
    preg_match_all($pattern, $article->Content, $matchContent);
    $list = $matchContent[0];
    if (!$list || count($list) == 0) {
        return false;
    }

    $btnText = $zbp->Config('os_copy')->btn_text;
    $btnText = empty($btnText) ? '复制' : $btnText;
    foreach ($list as $item) {
        preg_match($pattern, $item, $matches);
        if (count($matches) > 1) {
            $copyItem = os_copy_GetCopyHTML($matches[1], 'single');
            $article->Content = str_replace($matches[0], $copyItem, $article->Content);
        }
    }
}

/**
 * 生成被复制模块
 */
function os_copy_GetCopyHTML($content, $source = 'footer') {
    global $zbp;
    $contentCenter = false;
    $btnCenter = false;
    $btnText = '复制';
    $btnType = '';
    $btnCode = '';

    $copyContentCenter = $zbp->Config('os_copy')->copyContentCenter;
    $copyBtnContentCenter = $zbp->Config('os_copy')->copyBtnContentCenter;
    
    switch($source) {
        case 'footer':
            if ($zbp->Config('os_copy')->footerBtnText) {
                $btnText = $zbp->Config('os_copy')->footerBtnText;
            }
            if ($zbp->Config('os_copy')->footerBtnType) {
                $btnType = $zbp->Config('os_copy')->footerBtnType;
            } else {
                $btnType = 'bottom';
            }
            if (is_array($copyContentCenter) && in_array('footer', $copyContentCenter)) {
                $contentCenter = true;
            }
            if ($btnType == 'bottom' && is_array($copyBtnContentCenter) && in_array('footer', $copyBtnContentCenter)) {
                $btnCenter = true;
            }
            $btnColor = $zbp->Config('os_copy')->footerBtnColor;
            if ($btnCenter) {
                $slideBtn = '';
                if ($zbp->Config('os_copy')->showFooterSlideBtn == '1') {
                    $slideBtn = '<span class="os-slide-btn">折叠</span>';
                }
                $btnCode = '
                <div class="os-copy-btn-group">
                    <span class="os-copy-btn os-copy-btn-' . $btnType . ' os-copy-color-' . $btnColor . '">' . $btnText . '</span>
                    ' . $slideBtn . '
                </div>';
            } else {
                $btnCode = '<span class="os-copy-btn os-copy-btn-' . $btnType . ' os-copy-color-' . $btnColor . '">' . $btnText . '</span>';
                if ($btnType == 'bottom' && $zbp->Config('os_copy')->showFooterSlideBtn == '1') {
                    $btnCode .= '<span class="os-slide-btn">折叠</span>';
                }
            }
        break;
        case 'multi':
            if ($zbp->Config('os_copy')->insertMultiBtnText) {
                $btnText = $zbp->Config('os_copy')->insertMultiBtnText;
            }
            if ($zbp->Config('os_copy')->insertBtnType) {
                $btnType = $zbp->Config('os_copy')->insertBtnType;
            } else {
                $btnType = 'top-right';
            }
            if (is_array($copyContentCenter) && in_array('multi', $copyContentCenter)) {
                $contentCenter = true;
            }
            if ($btnType == 'bottom' && is_array($copyBtnContentCenter) && in_array('multi', $copyBtnContentCenter)) {
                $btnCenter = true;
            }
            $btnColor = $zbp->Config('os_copy')->insertBtnColor;
            if ($btnCenter) {
                $slideBtn = '';
                if ($zbp->Config('os_copy')->showInsertSlideBtn == '1') {
                    $slideBtn = '<span class="os-slide-btn">折叠</span>';
                }
                $btnCode = '
                <div class="os-copy-btn-group">
                    <span class="os-copy-btn os-copy-btn-' . $btnType . ' os-copy-color-' . $btnColor . '">' . $btnText . '</span>
                    ' . $slideBtn . '
                </div>';
            } else {
                $btnCode = '<span class="os-copy-btn os-copy-btn-' . $btnType . ' os-copy-color-' . $btnColor . '">' . $btnText . '</span>';
                if ($btnType == 'bottom' && $zbp->Config('os_copy')->showInsertSlideBtn == '1') {
                    $btnCode .= '<span class="os-slide-btn">折叠</span>';
                }
            }
        break;
        case 'single':
            if ($zbp->Config('os_copy')->insertSingleBtnText) {
                $btnText = $zbp->Config('os_copy')->insertSingleBtnText;
            }
            if (is_array($copyContentCenter) && in_array('single', $copyContentCenter)) {
                $contentCenter = true;
            }
            $btnColor = $zbp->Config('os_copy')->insertBtnColor;
            $btnCode = '<span class="os-copy-btn os-copy-color-' . $btnColor . '">' . $btnText . '</span>';
        break;
    }
    $contentClassName = $contentCenter ? 'os-copy-center' : '';
    $copyContentBackground = '';
    if ($zbp->Config('os_copy')->copyContentBackground) {
        $copyContentBackground = 'style="background-color: ' . $zbp->Config('os_copy')->copyContentBackground . ';"';
    }
    $htmlCode ='
    <div class="os-copy-mode os-copy-mode-' . $source . '">
        <div class="os-copy-content ' . $contentClassName . '" ' . $copyContentBackground . '>' . $content . '</div>
        ' . $btnCode . '
    </div>';
    return $htmlCode;
}

/**
 * 在1号接口插入复制方案
 */
function os_copy_Edit_Response() {
    global $zbp, $article;
    if ($zbp->Config('os_copy')->offArticleFooterCopy != '1') {
        echo '
        <div>
            <label for="meta_os_copy_content" class="editinputname">插入文章底部的复制块</label>
            <div>
                <textarea style="display: block; min-width: 100%; max-width: 100%; padding: 10px; line-height: 30px; font-size: 14px; box-sizing: border-box;" id="meta_os_copy_content" name="meta_os_copy_content" value="" placeholder="留空不显示">'.htmlspecialchars($article->Metas->os_copy_content).'</textarea>
            </div>
        </div>';
    }
}

/**
 * 在3号接口插入复制按钮
 */
function os_copy_Edit_Response3() {
    global $zbp, $article;
    if ($zbp->Config('os_copy')->offArticleInsertCopy != '1') {
        echo '<input class="button" style="width: 180px; height: 38px;" type="button" value="插入多行复制块" id="os-copy-insert-multi">';
        echo '<input class="button" style="width: 180px; height: 38px;" type="button" value="插入单行复制块" id="os-copy-insert-single">';
        echo '
        <script>
        $(function() {
            $("#os-copy-insert-multi").on("click", function() {
                if (UE) {
                    var ue = editor_api.editor.content.obj;
                    ue.execCommand("inserthtml", "<p>[os-copy]</p><p>要被复制的多行内容</p><p>[/os-copy]</p>");
                } else {
                    var content = editor_api.editor.content.get();
                    content += "<p>[os-copy]</p><p>要被复制的多行内容</p><p>[/os-copy]</p>";
                    editor_api.editor.content.put(content);
                }
            });
            $("#os-copy-insert-single").on("click", function() {
                if (UE) {
                    var ue = editor_api.editor.content.obj;
                    ue.execCommand("inserthtml", "<p>[os-copy-single]</p><p>要被复制的单行内容</p><p>[/os-copy-single]</p>");
                } else {
                    var content = editor_api.editor.content.get();
                    content += "<p>[os-copy-single]</p><p>要被复制的单行内容</p><p>[/os-copy-single]</p>";
                    editor_api.editor.content.put(content);
                }
            });
        });
        </script>';
    }
}
