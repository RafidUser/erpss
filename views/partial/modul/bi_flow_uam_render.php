<?php 

    // $data['main_table'] = $this->main_table;
    // $data['main_table_pk'] = $this->main_table_pk;
    // $data['main_table_key'] = $this->main_table_key;

    // $data['urlH'] = $this->url;
    // $pield = 'ws_real_mikro_lap';
    // $data['sub_ctrl'] = $pield;
    // $data['urlpath'] = $this->urlpath;

    // $data['field_id'] = $field;
    // $data['id'] = $id;
    // $data['rowData'] = $rowData;
    // $data['modul_key'] = $this->modul_key;
    // $o = $this->load->view('partial/modul/water_system/subcontroller/render_subctrl_ws', $data, true);

?>

<?php
    $rowData = $rowDataH; 
    
    $label = str_replace($field, 'form_detail_'.$field, $id);

    $id_head = $rowData['id'];
    $cKode = $rowData['cKode'];
    
    $sub_ctrl = 'uam';
    
    $this->sub_ctrl = $this->url.'_'.$sub_ctrl;

    $data['rowData'] = $rowData;
    $data['urlpath'] = $this->urlpath;
    $data['sub_ctrl'] = $this->sub_ctrl;
    $data['urlH'] = $this->url;


    //$url2       = base_url() . 'processor/' . $this->urlpath . '_' . $this->sub_ctrl;
    $url2       = base_url().'processor/erpss/'.$this->sub_ctrl;
    $urlParam   = $url2.'?urlH='.$urlH.'&modul_key='.$modul_key.'&id_head='.$id_head.'&cKode='.$cKode.'&company_id='.$this->input->get('company_id').'&modul_id='.$this->input->get('modul_id').'&group_id='.$this->input->get('group_id');

  if($act == 'create'){
    // echo '<p><b>Save First...!!!</b></p>';
    echo '-';
  }else{
    $return      = '<table id="'.$id.'" width="99%" style="margin:5px;">';
    $return     .= '<tr>';
    $return     .= '    <td>';
    $return     .= '        <script type="text/javascript">';
    $return     .= '            $("#'.$this->url.'setup_'.$this->url.'_'.$this->sub_ctrl.'").tabs();
                                    browse_tab(\''.$urlParam.'\',\''.strtoupper($sub_ctrl).'\',\''.$this->url.'_sub_'.$this->url.'_'.$this->sub_ctrl.'\');';
    $return     .= '        </script>';
    $return     .= '        <div id="'.$this->url.'setup_'.$this->url.'_'.$this->sub_ctrl.'" width="100%">';
    $return     .= '            <ul style="display: none;">
                                        <li><a href="#'.$this->url.'_sub_'.$this->url.'_'.$this->sub_ctrl . '">'.strtoupper($sub_ctrl).'</a></li>
                                    </ul>
                                    <div id="'.$this->url.'_sub_'.$this->url.'_'.$this->sub_ctrl.'"></div>
                                </div> ';
    $return     .= '    </td>';
    $return     .= '<tr>';
    $return     .= '</table>';
    echo $return;
?>

<script type="text/javascript">
  // $("label[for='<?php echo $label; ?>']").css({"border": "1px solid #dddddd", "background": "#548cb6", "border-collapse": "collapse","width":"99%","font-weight":"bold","color":"#ffffff","text-shadow": "0 1px 1px rgba(0, 0, 0, 0.3)","text-transform": "uppercase","text-align": "center","padding":"5px","margin-top":"10px"});
    $("#<?php echo $id; ?>").parent().removeClass('rows_input');
    $("label[for='<?php echo $label; ?>']").hide();

    // Minimize sub controller
    /*setTimeout(function(){
        $("#<?php echo $this->url.'_'.$field ?>").find('.HeaderButton').click();
    }, 1000);*/
  
</script>

<?php  
  }

?>