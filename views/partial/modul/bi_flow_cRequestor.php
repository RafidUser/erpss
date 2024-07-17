<?php  

$sql = "SELECT CONCAT_WS(' - ', a.cNip, a.vName) AS showshow
        FROM hrd.employee a
        WHERE a.lDeleted = 0
        AND a.cNip = '" . $rowDataH[$field] . "' ";
$row = $this->db->query($sql)->row_array();

$value_dis = $row['showshow'];

$isOpenEditing = $this->lib_sub_core->getOpenEditing($this->modul_id, $rowDataH[$this->main_table_pk]);
$readonly = '';
if($rowDataH['iSubmit'] == 1){
    if(!$isOpenEditing){
        $readonly = 'readonly';
    }
}

$o = '<input type="hidden" id="' . $id . '" name="' . $field . '" value="' . $rowDataH[$field] . '">';
$o .= '<input type="text" id="' . $id . '_id" name="' . $field . '_id" class="required" value="' . $value_dis . '" size="45" '.$readonly.'>';

$o .= "<script>";

$o .= "$(document).ready(function(){
            var config1 = {
                source: base_url+'processor/" . $this->urlpath . "?action=get_pic&company_id=" . $this->company_id . "',                  
                select: function(event, ui){

                    var i = $('#" . $id . "_id').index(this);
                    $('#" . $id . "_id').eq(i).val(ui.item.value);
                    $('#" . $id . "').eq(i).val(ui.item.id);
                },
                minLength: 2,
                autoFocus: true,
            };
            $('#" . $id . "_id').livequery(function(){
                $(this).autocomplete(config1);
                var i = $('#" . $id . "_id').index(this);
                $(this).keypress(function(e, ui){
                    if(e.which != 13) {
                        $('#" . $id . "').eq(i).val('');
                    }     

                    if(e.which == 13){
                        reload_grid('grid_" . $this->url . "');
                    }     

                });
                $(this).blur(function(){
                    if($('#" . $id . "').eq(i).val() == '') {
                        $(this).val(''); 
                    }           
                }); 
            });

            // Untuk searchbox
            // Synchronize two input fields
            // $('#" . $id . "_id').bind('keyup paste', function() {
            //     $('#" . $id . "').val($(this).val());
            // });

        });";

$o .= "</script>";

echo $o;

?>