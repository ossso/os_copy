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
        "copyContentBackground",
        "footerBtnText",
        "footerBtnType",
        "footerBtnColor",
        "showFooterSlideBtn",
        "insertMultiBtnText",
        "insertBtnType",
        "insertBtnColor",
        "showInsertSlideBtn",
        "insertSingleBtnText",
    );
    /**
     * 多选按钮
     */
    $params2 = array(
        "copyContentCenter",
        "copyBtnContentCenter",
    );

    foreach ($params as $v) {
        $zbp->Config('os_copy')->$v = GetVars($v, "POST");
    }
    foreach ($params2 as $v) {
        $valList = GetVars($v, "POST");
        if (is_array($valList)) {
            $array = array();
            foreach ($valList as $key => $val) {
                if ($val == 'on') {
                    array_push($array, $key);
                }
            }
            $zbp->Config('os_copy')->$v = $array;
        }
    }
    $zbp->SaveConfig('os_copy');

    $json = array(
        "code"     => "100000",
        "message"  => "保存成功"
    );
    echo json_encode($json);
}
