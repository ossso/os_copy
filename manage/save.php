<?php
require '../../../../zb_system/function/c_system_base.php';
require '../../../../zb_system/function/c_system_admin.php';

$zbp->Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('os_copy')) {$zbp->ShowError(48);die();}

$type = GetVars("type", "GET");

if ($type == "base") {
    $params = array(
        "offArticleInsertCopy",
        "offArticleFooterCopy",
        "openPreCodeCopy",
        "footer_btn_text",
        "btn_text",
        "btn_type",
        "btn_footer_color",
        "copyContentCenterType",
    );

    foreach ($params as $v) {
        $zbp->Config('os_copy')->$v = GetVars($v, "POST");
    }
    $zbp->SaveConfig('os_copy');

    $json = array(
        "code"     => "100000",
        "message"  => "保存成功"
    );
    echo json_encode($json);
}
