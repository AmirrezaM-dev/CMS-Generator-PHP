<?php
	require_once("../class/jdf.php");
?>
<div class="modal-header justify-content-center">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
        <i class="tim-icons icon-simple-remove"></i>
    </button>
    <h6 class="title title-up text-white"><?php print_r($primary); ?></h6>
</div>
<div class="modal-body">
    <table id="rows_informations" class="table table-striped disable_custom_table">
        <thead>
            <tr>
                <th>
                    <label class="data-text" data-text-en="Name" data-text-fa="نام" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? "Name":"نام"); ?></label>
                </th>
                <th>
                    <label class="data-text" data-text-en="Value" data-text-fa="داده" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? "Value":"داده"); ?></label>
                </th>
                <th>
                    <label class="data-text" data-text-en="information" data-text-fa="اطلاعات" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? "information":"اطلاعات"); ?></label>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <label class="hide">0</label>
                    <label class="data-text" data-text-en="ID" data-text-fa="آیدی" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? "ID":"آیدی"); ?></label>
                </td>
                <td>
                    <label style="margin-bottom: 0px !important;"><?php print_r($dataGet["id"]); ?></label>
                </td>
                <td>
                    <label class="data-text" style="margin-bottom: 0px !important;"></label>
                </td>
            </tr>
            <?php
                $res_table_column=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE table_id='".$table_id."'");
                while($table_column=$res_table_column->fetch()){
                    if(isset($op_admin) && $op_admin || $table_column['visible']==1){
                        if(checkPermission(2,$table_column['id'],"read",$table_column['act'],$table_id)){
                            if($table_column['current_name']!="ordering" && $table_column['current_name']!="act"){
                                switch ($table_column['mode']) {//tables_mode_code
                                    //edit_data_tables_mode_input
                                    case '1':case 1:case '2':case 2://info search case 1 for see all things about this part//info search case 2 for see all things about this part
                                        ?>
                                            <tr>
                                                <td>
                                                    <label class="hide"><?php print_r($table_column['column_number']); ?></label>
                                                    <label class="data-text" data-text-en="<?php print_r($table_column['description_name_en']); ?><?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>" data-text-fa="<?php print_r($table_column['description_name_fa']); ?><?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>" style="margin-bottom: 0px !important;">
                                                        <?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_name_en']:$table_column['description_name_fa']); ?>
                                                        <?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label style="margin-bottom: 0px !important;"><?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $dataGet[$table_column['current_name']], 1)); ?></label>
                                                </td>
                                                <td>
                                                    <label class="data-text" data-text-en="<?php print_r($table_column['description_info_en']); ?>" data-text-fa="<?php print_r($table_column['description_info_fa']); ?>" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_info_en']:$table_column['description_info_fa']); ?></label>
                                                </td>
                                            </tr>
                                        <?php
                                    break;
                                    case '3':case 3: //info search case 3 for see all things about this part
                                        $res_options=$connection->query("SELECT * FROM ".$sub_name."yes_no_question_options WHERE table_id='".$table_column['table_id']."' AND column_id='".$table_column['id']."' AND act=1");
                                        if($res_options->rowCount()){
                                            $options=$res_options->fetch();
                                        }else{
                                            $options=["yes_value"=>"1","no_value"=>"0","yes_option"=>"Yes","no_option"=>"No"];
                                        }
                                        ?>
                                            <tr>
                                                <td>
                                                    <label class="hide"><?php print_r($table_column['column_number']); ?></label>
                                                    <label class="data-text" data-text-en="<?php print_r($table_column['description_name_en']); ?><?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>" data-text-fa="<?php print_r($table_column['description_name_fa']); ?><?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>" style="margin-bottom: 0px !important;">
                                                        <?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_name_en']:$table_column['description_name_fa']); ?>
                                                        <?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>
                                                    </label>
                                                </td>
                                                <td>
                                        <?php
                                        if($GLOBALS["options"]['yes_value']==preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $dataGet[$table_column['current_name']], 1)){
                                            ?>
                                                <label style="margin-bottom: 0px !important;"><?php print_r($options['yes_option']); ?></label>
                                            <?php
                                        }else if($GLOBALS["options"]['no_value']==preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $dataGet[$table_column['current_name']], 1)){
                                            ?>
                                                <label style="margin-bottom: 0px !important;"><?php print_r($options['no_option']); ?></label>
                                            <?php
                                        }else{
                                            ?>
                                                <label style="margin-bottom: 0px !important;"><?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $dataGet[$table_column['current_name']], 1)); ?></label>
                                            <?php
                                        }
                                        ?>
                                                </td>
                                                <td>
                                                    <label class="data-text" data-text-en="<?php print_r($table_column['description_info_en']); ?>" data-text-fa="<?php print_r($table_column['description_info_fa']); ?>" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_info_en']:$table_column['description_info_fa']); ?></label>
                                                </td>
                                            </tr>
                                        <?php
                                    break;
                                    case '4':case 4://info search case 4 for see all things about this part
                                        $newxdata="";
                                        unset($newxdata);
                                        $res_options_setting=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."select_options_setting WHERE table_id='".$table_column['table_id']."' AND column_id='".$table_column['id']."' AND act=1");
                                        $options_setting=($res_options_setting->rowCount() ? $res_options_setting->fetch():["is_multiple"=>0,"is_forced"=>0,"min_allowed"=>0,"max_allowed"=>0]);
                                        if($options_setting['is_multiple']){
                                            if(strpos($dataGet[$table_column['current_name']],'_-.,.-_')){
                                                $dataGet[$table_column['current_name']]=explode("_-.,.-_",$dataGet[$table_column['current_name']]);
                                            }else{
                                                $dataGet[$table_column['current_name']]=[$dataGet[$table_column['current_name']]];
                                            }
                                            foreach ($dataGet[$table_column['current_name']] as &$xvalue) {
                                                if(strpos($xvalue,'_-...-_')){
                                                    $xdata=explode("_-...-_",$xvalue)[1];
                                                }else{
                                                    $xdata=$xvalue;
                                                }
                                                if(strpos($xdata,'_-..-_')){
                                                    $xdata_optVAL=explode("_-..-_",$xdata)[1];
                                                    $xdata_optID=explode("_-..-_",$xdata)[0];
                                                    $res_options=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."select_options WHERE table_id='".$table_column['table_id']."' AND column_id='".$table_column['id']."' AND id='".$xdata_optID."' AND act=1");
                                                    if($res_options->rowCount()){
                                                        $options=$res_options->fetch();
                                                        if($options['connected_table']>0){
                                                            $res_connected_table=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."table_config WHERE id='".$options['connected_table']."'");
                                                            if($res_connected_table->rowCount()){
                                                                $connected_table=$res_connected_table->fetch();
                                                                $res_connected_table_column=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."table_column_config WHERE table_id='".$options['connected_table']."' AND id='".$options['option_value']."'");
                                                                $res_connected_table_column_show=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."table_column_config WHERE table_id='".$options['connected_table']."' AND id='".$options['option_text']."'");
                                                                if(($res_connected_table_column->rowCount() || $options['option_value']==0) && ($res_connected_table_column_show->rowCount() || $options['option_text']==0)){
                                                                    $connected_table_column=$res_connected_table_column->fetch();
                                                                    $connected_table_column_show=$res_connected_table_column_show->fetch();
                                                                    $res_data_connected_table=$GLOBALS['connection']->query("SELECT * FROM ".$connected_table['current_name']." WHERE ".($options['option_value'] ? $connected_table_column['current_name']:"id")."='".$xdata_optVAL."'");
                                                                    if($res_data_connected_table->rowCount()){
                                                                        $data_connected_table=$res_data_connected_table->fetch();
                                                                        $xsdata=$data_connected_table[($options['option_text'] ? $connected_table_column_show['current_name']:"id")];
                                                                    }else{
                                                                        $xsdata=$xdata;
                                                                    }
                                                                }else{
                                                                    $xsdata=$xdata;
                                                                }
                                                            }else{
                                                                $xsdata=$xdata;
                                                            }
                                                        }else{
                                                            $xsdata=$xdata;
                                                        }
                                                    }else{
                                                        $xsdata=$xdata;
                                                    }
                                                }else{
                                                    if(strpos($xdata,'_-.-_')){
                                                        $xdata=explode("_-.-_",$xvalue)[1];
                                                        $res_options=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."select_options WHERE table_id='".$table_column['table_id']."' AND column_id='".$table_column['id']."' AND option_value='".$xdata."' AND act=1");
                                                        if($res_options->rowCount()){
                                                            $options=$res_options->fetch();
                                                            $xsdata=$options['option_text'];
                                                        }else{
                                                            $xsdata=$xdata;
                                                        }
                                                    }else{
                                                        $res_options=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."select_options WHERE table_id='".$table_column['table_id']."' AND column_id='".$table_column['id']."' AND option_value='".$xdata."' AND act=1");
                                                        if($res_options->rowCount()){
                                                            $options=$res_options->fetch();
                                                            $xsdata=$options['option_text'];
                                                        }else{
                                                            $xsdata=$xdata;
                                                        }
                                                    }
                                                }
                                                if(isset($newxdata) && $newxdata!="" && !empty($newxdata)){
                                                    $newxdata.=",".$xsdata;
                                                }else{
                                                    $newxdata=$xsdata;
                                                }
                                            }
                                            $dataGet[$table_column['current_name']]=$newxdata;
                                        }else{
                                            if(strpos($dataGet[$table_column['current_name']],'_-.,.-_')){
                                                $dataGet[$table_column['current_name']]=explode("_-.,.-_",$dataGet[$table_column['current_name']]);
                                            }else{
                                                $dataGet[$table_column['current_name']]=[$dataGet[$table_column['current_name']]];
                                            }
                                            foreach ($dataGet[$table_column['current_name']] as &$xvalue) {
                                                if(strpos($xvalue,'_-...-_')){
                                                    $xdata=explode("_-...-_",$xvalue)[1];
                                                }else{
                                                    $xdata=$xvalue;
                                                }
                                                if(strpos($xdata,'_-..-_')){
                                                    $xdata_optVAL=explode("_-..-_",$xdata)[1];
                                                    $xdata_optID=explode("_-..-_",$xdata)[0];
                                                    $res_options=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."select_options WHERE table_id='".$table_column['table_id']."' AND column_id='".$table_column['id']."' AND id='".$xdata_optID."' AND act=1");
                                                    if($res_options->rowCount()){
                                                        $options=$res_options->fetch();
                                                        if($options['connected_table']>0){
                                                            $res_connected_table=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."table_config WHERE id='".$options['connected_table']."'");
                                                            if($res_connected_table->rowCount()){
                                                                $connected_table=$res_connected_table->fetch();
                                                                $res_connected_table_column=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."table_column_config WHERE table_id='".$options['connected_table']."' AND id='".$options['option_value']."'");
                                                                $res_connected_table_column_show=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."table_column_config WHERE table_id='".$options['connected_table']."' AND id='".$options['option_text']."'");
                                                                if(($res_connected_table_column->rowCount() || $options['option_value']==0) && ($res_connected_table_column_show->rowCount() || $options['option_text']==0)){
                                                                    $connected_table_column=$res_connected_table_column->fetch();
                                                                    $connected_table_column_show=$res_connected_table_column_show->fetch();
                                                                    $res_data_connected_table=$GLOBALS['connection']->query("SELECT * FROM ".$connected_table['current_name']." WHERE ".($options['option_value'] ? $connected_table_column['current_name']:"id")."='".$xdata_optVAL."'");
                                                                    if($res_data_connected_table->rowCount()){
                                                                        $data_connected_table=$res_data_connected_table->fetch();
                                                                        $xsdata=$data_connected_table[($options['option_text'] ? $connected_table_column_show['current_name']:"id")];
                                                                    }else{
                                                                        $xsdata=$xdata;
                                                                    }
                                                                }else{
                                                                    $xsdata=$xdata;
                                                                }
                                                            }else{
                                                                $xsdata=$xdata;
                                                            }
                                                        }else{
                                                            $xsdata=$xdata;
                                                        }
                                                    }else{
                                                        $xsdata=$xdata;
                                                    }
                                                }else{
                                                    if(strpos($xdata,'_-.-_')){
                                                        $xdata=explode("_-.-_",$xvalue)[1];
                                                        $res_options=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."select_options WHERE table_id='".$table_column['table_id']."' AND column_id='".$table_column['id']."' AND option_value='".$xdata."' AND act=1");
                                                        if($res_options->rowCount()){
                                                            $options=$res_options->fetch();
                                                            $xsdata=$options['option_text'];
                                                        }else{
                                                            $xsdata=$xdata;
                                                        }
                                                    }else{
                                                        $res_options=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."select_options WHERE table_id='".$table_column['table_id']."' AND column_id='".$table_column['id']."' AND option_value='".$xdata."' AND act=1");
                                                        if($res_options->rowCount()){
                                                            $options=$res_options->fetch();
                                                            $xsdata=$options['option_text'];
                                                        }else{
                                                            $xsdata=$xdata;
                                                        }
                                                    }
                                                }
                                                if(isset($newxdata) && $newxdata!="" && !empty($newxdata)){
                                                    $newxdata.=",".$xsdata;
                                                }else{
                                                    $newxdata=$xsdata;
                                                }
                                            }
                                            $dataGet[$table_column['current_name']]=$newxdata;
                                        }
                                        ?>
                                            <tr>
                                                <td>
                                                    <label class="hide"><?php print_r($table_column['column_number']); ?></label>
                                                    <label class="data-text" data-text-en="<?php print_r($table_column['description_name_en']); ?><?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>" data-text-fa="<?php print_r($table_column['description_name_fa']); ?><?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>" style="margin-bottom: 0px !important;">
                                                        <?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_name_en']:$table_column['description_name_fa']); ?>
                                                        <?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label style="margin-bottom: 0px !important;"><?php print_r($dataGet[$table_column['current_name']]); ?></label>
                                                </td>
                                                <td>
                                                    <label class="data-text" data-text-en="<?php print_r($table_column['description_info_en']); ?>" data-text-fa="<?php print_r($table_column['description_info_fa']); ?>" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_info_en']:$table_column['description_info_fa']); ?></label>
                                                </td>
                                            </tr>
                                        <?php
                                    break;
                                    case '5':case 5://info search case 5 for see all things about this part
                                        $dataGet[$table_column['current_name']]='<span class="badge badge-danger color-shows"'."style='background: ".$dataGet[$table_column['current_name']].";'".'>'.($GLOBALS['user_language']=="en" ? "Color":"رنگ").'</span>';
                                        ?>
                                            <tr>
                                                <td>
                                                    <label class="hide"><?php print_r($table_column['column_number']); ?></label>
                                                    <label class="data-text" data-text-en="<?php print_r($table_column['description_name_en']); ?><?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>" data-text-fa="<?php print_r($table_column['description_name_fa']); ?><?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>" style="margin-bottom: 0px !important;">
                                                        <?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_name_en']:$table_column['description_name_fa']); ?>
                                                        <?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label style="margin-bottom: 0px !important;"><?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $dataGet[$table_column['current_name']], 1)); ?></label>
                                                </td>
                                                <td>
                                                    <label class="data-text" data-text-en="<?php print_r($table_column['description_info_en']); ?>" data-text-fa="<?php print_r($table_column['description_info_fa']); ?>" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_info_en']:$table_column['description_info_fa']); ?></label>
                                                </td>
                                            </tr>
                                        <?php
                                    break;
                                    case '6':case 6://info search case 6 for see all things about this part
                                        ?>
                                            <tr>
                                                <td>
                                                    <label class="hide"><?php print_r($table_column['column_number']); ?></label>
                                                    <label class="data-text" data-text-en="<?php print_r($table_column['description_name_en']); ?><?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>" data-text-fa="<?php print_r($table_column['description_name_fa']); ?><?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>" style="margin-bottom: 0px !important;">
                                                        <?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_name_en']:$table_column['description_name_fa']); ?>
                                                        <?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label style="margin-bottom: 0px !important;"><i class="far fa-shield text-success fa-2x"></i></label>
                                                </td>
                                                <td>
                                                    <label class="data-text" data-text-en="<?php print_r($table_column['description_info_en']); ?>" data-text-fa="<?php print_r($table_column['description_info_fa']); ?>" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_info_en']:$table_column['description_info_fa']); ?></label>
                                                </td>
                                            </tr>
                                        <?php
                                    break;
                                    case '7':case 7://info search case 7 for see all things about this part
                                        ?>
                                            <tr>
                                                <td>
                                                    <label class="hide"><?php print_r($table_column['column_number']); ?></label>
                                                    <label class="data-text" data-text-en="<?php print_r($table_column['description_name_en']); ?><?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>" data-text-fa="<?php print_r($table_column['description_name_fa']); ?><?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>" style="margin-bottom: 0px !important;">
                                                        <?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_name_en']:$table_column['description_name_fa']); ?>
                                                        <?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <a href="<?php print_r(strlen($dataGet[$table_column['current_name']]) ? $dataGet[$table_column['current_name']]:"javascript:void(0)"); ?>" target="<?php print_r(strlen($dataGet[$table_column['current_name']]) ? "_blank":""); ?>" style="margin-bottom: 0px !important;">
                                                        <i class="far fa-link text-info fa-2x"></i>
                                                    </a>
                                                </td>
                                                <td>
                                                    <label class="data-text" data-text-en="<?php print_r($table_column['description_info_en']); ?>" data-text-fa="<?php print_r($table_column['description_info_fa']); ?>" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_info_en']:$table_column['description_info_fa']); ?></label>
                                                </td>
                                            </tr>
                                        <?php
                                    break;
                                    case '8':case 8:case '19':case 19://info search case 8 for see all things about this part//info search case 19 for see all things about this part
                                        ?>
                                            <tr>
                                                <td>
                                                    <label class="hide"><?php print_r($table_column['column_number']); ?></label>
                                                    <label class="data-text" data-text-en="<?php print_r($table_column['description_name_en']); ?><?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>" data-text-fa="<?php print_r($table_column['description_name_fa']); ?><?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>" style="margin-bottom: 0px !important;">
                                                        <?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_name_en']:$table_column['description_name_fa']); ?>
                                                        <?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <ckeditor>
                                                        <?php
                                                            print_r($dataGet[$table_column['current_name']]);
                                                        ?>
                                                    </ckeditor>
                                                </td>
                                                <td>
                                                    <label class="data-text" data-text-en="<?php print_r($table_column['description_info_en']); ?>" data-text-fa="<?php print_r($table_column['description_info_fa']); ?>" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_info_en']:$table_column['description_info_fa']); ?></label>
                                                </td>
                                            </tr>
                                        <?php
                                    break;
                                    case '9':case 9://info search case 9 for see all things about this part
                                        $dataGet[$table_column['current_name']]=str_replace("_-...-_"," , ",$dataGet[$table_column['current_name']]);
                                        ?>
                                            <tr>
                                                <td>
                                                    <label class="hide"><?php print_r($table_column['column_number']); ?></label>
                                                    <label class="data-text" data-text-en="<?php print_r($table_column['description_name_en']); ?><?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>" data-text-fa="<?php print_r($table_column['description_name_fa']); ?><?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>" style="margin-bottom: 0px !important;">
                                                        <?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_name_en']:$table_column['description_name_fa']); ?>
                                                        <?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <?php
                                                        $values=0;
                                                        if(strlen($dataGet[$table_column['current_name']]) && $dataGet[$table_column['current_name']]!=0){
                                                            foreach (explode("_-...-_",$dataGet[$table_column['current_name']]) as &$value) {
                                                                if($values==0){
                                                                    $values.=",".$value;
                                                                }else{
                                                                    $values=$value;
                                                                }
                                                            }
                                                        }
                                                        $dataGet[$table_column['current_name']]="";
                                                        $res_options=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."checkbox_options WHERE table_id='".$table_get['id']."' AND column_id='".$table_column['id']."' AND option_value IN (".$values.") AND act=1");
                                                        while ($options=$res_options->fetch()) {
                                                            if(strlen($dataGet[$table_column['current_name']])){
                                                                $dataGet[$table_column['current_name']].=",".$options["option_name"];
                                                            }else{
                                                                $dataGet[$table_column['current_name']]=$options["option_name"];
                                                            }
                                                        }
                                                    ?>
                                                    <label style="margin-bottom: 0px !important;"><?php print_r($dataGet[$table_column['current_name']]); ?></label>
                                                </td>
                                                <td>
                                                    <label class="data-text" data-text-en="<?php print_r($table_column['description_info_en']); ?>" data-text-fa="<?php print_r($table_column['description_info_fa']); ?>" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_info_en']:$table_column['description_info_fa']); ?></label>
                                                </td>
                                            </tr>
                                        <?php
                                    break;
                                    case '10':case 10://info search case 10 for see all things about this part
                                        ?>
                                            <tr>
                                                <td>
                                                    <label class="hide"><?php print_r($table_column['column_number']); ?></label>
                                                    <label class="data-text" data-text-en="<?php print_r($table_column['description_name_en']); ?><?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>" data-text-fa="<?php print_r($table_column['description_name_fa']); ?><?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>" style="margin-bottom: 0px !important;">
                                                        <?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_name_en']:$table_column['description_name_fa']); ?>
                                                        <?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <i class="<?php print_r($dataGet[$table_column['current_name']]); ?> text-primary fa-2x"></i>
                                                </td>
                                                <td>
                                                    <label class="data-text" data-text-en="<?php print_r($table_column['description_info_en']); ?>" data-text-fa="<?php print_r($table_column['description_info_fa']); ?>" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_info_en']:$table_column['description_info_fa']); ?></label>
                                                </td>
                                            </tr>
                                        <?php
                                    break;
                                    case '12':case 12://info search case 12 for see all things about this part
                                        ?>
                                            <tr>
                                                <td>
                                                    <label class="hide"><?php print_r($table_column['column_number']); ?></label>
                                                    <label class="data-text" data-text-en="<?php print_r($table_column['description_name_en']); ?><?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>" data-text-fa="<?php print_r($table_column['description_name_fa']); ?><?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>" style="margin-bottom: 0px !important;">
                                                        <?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_name_en']:$table_column['description_name_fa']); ?>
                                                        <?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label style="margin-bottom: 0px !important;"><?php print_r((strlen($dataGet[$table_column['current_name']])>3 ? date("d/m/Y",mb_substr($dataGet[$table_column['current_name']], 0, -3)):"")); ?></label>
                                                </td>
                                                <td>
                                                    <label class="data-text" data-text-en="<?php print_r($table_column['description_info_en']); ?>" data-text-fa="<?php print_r($table_column['description_info_fa']); ?>" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_info_en']:$table_column['description_info_fa']); ?></label>
                                                </td>
                                            </tr>
                                        <?php
                                    break;
                                    case '13':case 13://info search case 13 for see all things about this part
                                        ?>
                                            <tr>
                                                <td>
                                                    <label class="hide"><?php print_r($table_column['column_number']); ?></label>
                                                    <label class="data-text" data-text-en="<?php print_r($table_column['description_name_en']); ?><?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>" data-text-fa="<?php print_r($table_column['description_name_fa']); ?><?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>" style="margin-bottom: 0px !important;">
                                                        <?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_name_en']:$table_column['description_name_fa']); ?>
                                                        <?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label style="margin-bottom: 0px !important;"><?php print_r((strlen($dataGet[$table_column['current_name']])>3 ? date("d/m/Y H:i:s",mb_substr($dataGet[$table_column['current_name']], 0, -3)):"")); ?></label>
                                                </td>
                                                <td>
                                                    <label class="data-text" data-text-en="<?php print_r($table_column['description_info_en']); ?>" data-text-fa="<?php print_r($table_column['description_info_fa']); ?>" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_info_en']:$table_column['description_info_fa']); ?></label>
                                                </td>
                                            </tr>
                                        <?php
                                    break;
                                    case '14':case 14://info search case 14 for see all things about this part
                                        ?>
                                            <tr>
                                                <td>
                                                    <label class="hide"><?php print_r($table_column['column_number']); ?></label>
                                                    <label class="data-text" data-text-en="<?php print_r($table_column['description_name_en']); ?><?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>" data-text-fa="<?php print_r($table_column['description_name_fa']); ?><?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>" style="margin-bottom: 0px !important;">
                                                        <?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_name_en']:$table_column['description_name_fa']); ?>
                                                        <?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <?php
                                                        if(strlen($dataGet[$table_column['current_name']]) && $dataGet[$table_column['current_name']]!=0){
                                                    ?>
                                                        <label style="margin-bottom: 0px !important;"><?php print_r((strlen($dataGet[$table_column['current_name']])>3 ? jdate("Y/m/d",mb_substr($dataGet[$table_column['current_name']], 0, -3),'','','en'):"")); ?></label>
                                                    <?php
                                                        }
                                                    ?>
                                                </td>
                                                <td>
                                                    <label class="data-text" data-text-en="<?php print_r($table_column['description_info_en']); ?>" data-text-fa="<?php print_r($table_column['description_info_fa']); ?>" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_info_en']:$table_column['description_info_fa']); ?></label>
                                                </td>
                                            </tr>
                                        <?php
                                    break;
                                    case '15':case 15://info search case 15 for see all things about this part
                                        ?>
                                            <tr>
                                                <td>
                                                    <label class="hide"><?php print_r($table_column['column_number']); ?></label>
                                                    <label class="data-text" data-text-en="<?php print_r($table_column['description_name_en']); ?><?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>" data-text-fa="<?php print_r($table_column['description_name_fa']); ?><?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>" style="margin-bottom: 0px !important;">
                                                        <?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_name_en']:$table_column['description_name_fa']); ?>
                                                        <?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <?php
                                                        if(strlen($dataGet[$table_column['current_name']]) && $dataGet[$table_column['current_name']]!=0){
                                                    ?>
                                                        <label style="margin-bottom: 0px !important;"><?php print_r((strlen($dataGet[$table_column['current_name']])>3 ? jdate("Y/m/d H:i:s",mb_substr($dataGet[$table_column['current_name']], 0, -3),'','','en'):"")); ?></label>
                                                    <?php
                                                        }
                                                    ?>
                                                </td>
                                                <td>
                                                    <label class="data-text" data-text-en="<?php print_r($table_column['description_info_en']); ?>" data-text-fa="<?php print_r($table_column['description_info_fa']); ?>" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_info_en']:$table_column['description_info_fa']); ?></label>
                                                </td>
                                            </tr>
                                        <?php
                                    break;
                                    case '16':case 16://info search case 16 for see all things about this part
                                        ?>
                                            <tr>
                                                <td>
                                                    <label class="hide"><?php print_r($table_column['column_number']); ?></label>
                                                    <label class="data-text" data-text-en="<?php print_r($table_column['description_name_en']); ?><?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>" data-text-fa="<?php print_r($table_column['description_name_fa']); ?><?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>" style="margin-bottom: 0px !important;">
                                                        <?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_name_en']:$table_column['description_name_fa']); ?>
                                                        <?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label style="margin-bottom: 0px !important;"><?php print_r((strlen($dataGet[$table_column['current_name']])>3 ? date("H:i:s",mb_substr($dataGet[$table_column['current_name']], 0, -3)):"")); ?></label>
                                                </td>
                                                <td>
                                                    <label class="data-text" data-text-en="<?php print_r($table_column['description_info_en']); ?>" data-text-fa="<?php print_r($table_column['description_info_fa']); ?>" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_info_en']:$table_column['description_info_fa']); ?></label>
                                                </td>
                                            </tr>
                                        <?php
                                    break;
                                    default:
                                        ?>
                                            <tr>
                                                <td>
                                                    <label class="hide"><?php print_r($table_column['column_number']); ?></label>
                                                    <label class="data-text" data-text-en="<?php print_r($table_column['description_name_en']); ?><?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>" data-text-fa="<?php print_r($table_column['description_name_fa']); ?><?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>" style="margin-bottom: 0px !important;">
                                                        <?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_name_en']:$table_column['description_name_fa']); ?>
                                                        <?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label style="margin-bottom: 0px !important;"><?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $dataGet[$table_column['current_name']], 1)); ?></label>
                                                </td>
                                                <td>
                                                    <label class="data-text" data-text-en="<?php print_r($table_column['description_info_en']); ?>" data-text-fa="<?php print_r($table_column['description_info_fa']); ?>" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_info_en']:$table_column['description_info_fa']); ?></label>
                                                </td>
                                            </tr>
                                        <?php
                                    break;
                                }
                            }elseif($table_column['current_name']=="ordering"){
                                ?>
                                    <tr>
                                        <td>
                                            <label class="hide"><?php print_r($table_column['current_name'] == "ordering" ? 999998:999999); ?></label>
                                            <label class="data-text" data-text-en="<?php print_r($table_column['description_name_en']); ?><?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>" data-text-fa="<?php print_r($table_column['description_name_fa']); ?><?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>" style="margin-bottom: 0px !important;">
                                                <?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_name_en']:$table_column['description_name_fa']); ?>
                                                <?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>
                                            </label>
                                        </td>
                                        <td>
                                            <label style="margin-bottom: 0px !important;"><?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $dataGet[$table_column['current_name']], 1)); ?></label>
                                        </td>
                                        <td>
                                            <label class="data-text" data-text-en="<?php print_r($table_column['description_info_en']); ?>" data-text-fa="<?php print_r($table_column['description_info_fa']); ?>" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_info_en']:$table_column['description_info_fa']); ?></label>
                                        </td>
                                    </tr>
                                <?php
                            }elseif($table_column['current_name']=="act"){
                                ?>
                                    <tr>
                                        <td>
                                            <label class="hide"><?php print_r($table_column['current_name'] == "ordering" ? 999998:999999); ?></label>
                                            <label class="data-text" data-text-en="<?php print_r($table_column['description_name_en']); ?><?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>" data-text-fa="<?php print_r($table_column['description_name_fa']); ?><?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>" style="margin-bottom: 0px !important;">
                                                <?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_name_en']:$table_column['description_name_fa']); ?>
                                                <?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?>
                                            </label>
                                        </td>
                                        <td>
                                            <label style="margin-bottom: 0px !important;"><?php if($_SESSION["username"]!=getSetting("op_admin")){print_r($dataGet[$table_column['current_name']] ? ($GLOBALS['user_language']=="en" ? "Yes":"بله"):($GLOBALS['user_language']=="en" ? "No":"خیر"));}else{print_r($dataGet[$table_column['current_name']]);} ?></label>
                                        </td>
                                        <td>
                                            <label class="data-text" data-text-en="<?php print_r($table_column['description_info_en']); ?>" data-text-fa="<?php print_r($table_column['description_info_fa']); ?>" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_info_en']:$table_column['description_info_fa']); ?></label>
                                        </td>
                                    </tr>
                                <?php
                            }
                        }
                    }
                }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <th>
                    <label class="data-text" data-text-en="Name" data-text-fa="نام" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? "Name":"نام"); ?></label>
                </th>
                <th>
                    <label class="data-text" data-text-en="Value" data-text-fa="داده" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? "Value":"داده"); ?></label>
                </th>
                <th>
                    <label class="data-text" data-text-en="information" data-text-fa="اطلاعات" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? "information":"اطلاعات"); ?></label>
                </th>
            </tr>
        </tfoot>
    </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger ml-auto mr-auto" data-dismiss="modal"><?php print_r($GLOBALS['user_language']=="en" ? "Close":"خروج"); ?></button>
</div>
<script>
    $('#rows_informations').DataTable({
        "drawCallback": function( settings ) {pscrollbarUpdate();},
        "responsive": true,
        "order": [[ 0, "asc" ]],
        "language": langObjs()
    });
    $.fn.dataTable.ext.errMode = 'none';
    $('#rows_informations').on( 'error.dt', function ( e, settings, techNote, message ) {
        // console.log( 'An error has been reported by DataTables: ', message );
        // window.location.reload();
    }).DataTable();
</script>