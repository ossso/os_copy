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
    if (!array_key_exists('article', $zbp->template->templateTags)) {
        return null;
    }

    $article = $zbp->template->templateTags['article'];
    $article->Content = os_copy_HTMLZip($article->Content);

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
    $insertList = array($zbp->host . 'zb_users/plugin/os_copy/static/js/os-copy.min.js?t=20190618');
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


/**
 * 压缩 HTML 代码
 *
 * @author  情留メ蚊子 <qlwz@qq.com>
 * @version 1.0.0.0 By 2016-11-23
 * @link    http://blog.cbylpt.cn
 * @param string $html_source HTML 源码
 * @return string 压缩后的代码
 */
function os_copy_HTMLZip($html_source) {
    $chunks = preg_split('/(<!--<nocompress>-->.*?<!--<\/nocompress>-->|<nocompress>.*?<\/nocompress>|<pre.*?\/pre>|<textarea.*?\/textarea>|<script.*?\/script>)/msi', $html_source, -1, PREG_SPLIT_DELIM_CAPTURE);
    $compress = '';
    foreach ($chunks as $c) {
        if (strtolower(substr($c, 0, 19)) == '<!--<nocompress>-->') {
            $c = substr($c, 19, strlen($c) - 19 - 20);
            $compress .= $c;
            continue;
        } elseif (strtolower(substr($c, 0, 12)) == '<nocompress>') {
            $c = substr($c, 12, strlen($c) - 12 - 13);
            $compress .= $c;
            continue;
        } elseif (strtolower(substr($c, 0, 4)) == '<pre' || strtolower(substr($c, 0, 9)) == '<textarea') {
            $compress .= $c;
            continue;
        } elseif (strtolower(substr($c, 0, 7)) == '<script' && strpos($c, '//') != false && (strpos($c, "\r") !== false || strpos($c, "\n") !== false)) { // JS代码，包含“//”注释的，单行代码不处理
            $tmps = preg_split('/(\r|\n)/ms', $c, -1, PREG_SPLIT_NO_EMPTY);
            $c = '';
            foreach ($tmps as $tmp) {
                if (strpos($tmp, '//') !== false) { // 对含有“//”的行做处理
                    if (substr(trim($tmp), 0, 2) == '//') { // 开头是“//”的就是注释
                        continue;
                    }
                    $chars = preg_split('//', $tmp, -1, PREG_SPLIT_NO_EMPTY);
                    $is_quot = $is_apos = false;
                    foreach ($chars as $key => $char) {
                        if ($char == '"' && !$is_apos && $key > 0 && $chars[$key - 1] != '\\') {
                            $is_quot = !$is_quot;
                        } elseif ($char == '\'' && !$is_quot && $key > 0 && $chars[$key - 1] != '\\') {
                            $is_apos = !$is_apos;
                        } elseif ($char == '/' && $chars[$key + 1] == '/' && !$is_quot && !$is_apos) {
                            $tmp = substr($tmp, 0, $key); // 不是字符串内的就是注释
                            break;
                        }
                    }
                }
                $c .= $tmp;
            }
        }

        $c = preg_replace('/[\\n\\r\\t]+/', ' ', $c); // 清除换行符，清除制表符
        $c = preg_replace('/\\s{2,}/', ' ', $c); // 清除额外的空格
        $c = preg_replace('/>\\s</', '> <', $c); // 清除标签间的空格
        $c = preg_replace('/\\/\\*.*?\\*\\//i', '', $c); // 清除 CSS & JS 的注释
        $c = preg_replace('/<!--[^!]*-->/', '', $c); // 清除 HTML 的注释
        $compress .= $c;
    }
    return $compress;
}
