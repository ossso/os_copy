<?php foreach ($form_list as $v) { ?>
<div class="layui-form-item">
    <label class="layui-form-label" for="<?php echo $v[1] ?>"><?php echo $v[2] ?></label>
    <div class="layui-input-block">
<?php switch ($v[0]) {
        case 'input-text':
            echo '<input type="text" name="'.$v[1].'" placeholder="'.$v[3].'" class="layui-input" value="'.$v[4].'" />';
        break;
        case 'input-number':
            echo '<input type="number" name="'.$v[1].'" placeholder="'.$v[3].'" class="layui-input" value="'.$v[4].'" />';
        break;
        case 'input-switch':
            echo '<input type="checkbox" name="'.$v[1].'" lay-skin="switch" lay-text="'.$v[3].'" '.($v[4] == 1?'checked':'').'/>';
        break;
        case 'input-radio':
            foreach ($v[3] as $v2) {
                if ($v[4] == $v2[0]) {
                    echo '<input type="radio" name="'.$v[1].'" value="'.$v2[0].'" title="'.$v2[1].'" checked />';
                } else {
                    echo '<input type="radio" name="'.$v[1].'" value="'.$v2[0].'" title="'.$v2[1].'" />';
                }
            }
        break;
        case 'input-checkbox':
            foreach ($v[3] as $v2Key => $v2Val) {
                if (is_array($v[4]) && in_array($v2Key, $v[4])) {
                    echo '<input type="checkbox" name="'.$v[1].'['.$v2Key.']" lay-skin="primary" title="'.$v2Val.'" checked />';
                } else {
                    echo '<input type="checkbox" name="'.$v[1].'['.$v2Key.']" lay-skin="primary" title="'.$v2Val.'" />';
                }
            }
        break;
        case 'textarea':
            echo '<textarea name="'.$v[1].'" placeholder="'.$v[3].'" class="layui-textarea">'.$v[4].'</textarea>';
        break;
        case 'select':
            echo '<select name="'.$v[1].'">';
            foreach ($v[3] as $k => $v2) {
                if ($v[4] == $k) {
                    echo '<option selected value="'.$k.'">'.$v2.'</option>';
                } else {
                    echo '<option value="'.$k.'">'.$v2.'</option>';
                }
            }
            echo '</select>';
        break;
    }?>
    </div>
</div>
<?php } ?>
