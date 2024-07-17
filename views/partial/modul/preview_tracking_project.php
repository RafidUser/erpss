<?php
$this->load->view('js/template/jquery_datatables_js');
// $this->load->view('js/template/fixed_column');
$this->load->view('template/css/jquery_datatables_css');
// $this->load->view('template/css/fixed_column');
$this->load->helper('tanggal');
//https://stackoverflow.com/questions/27422640/alternate-to-array-column

?>

<div class="boxContent" style="overflow:auto;">
    <div class="box_dropdown">
        <div class="content">
            <div class="box_content_form">
                <div class="full_colums">
                    <div class="table_kondisi">
                    <?php 
                             //print_r($ruangs);
                             //exit;
                                $data['urlpath'] = $urlpath;
                                $data['rows'] = $rows;
                                $data['param'] = $param;
                                $data['grid'] = $grid;
                                $data['company_id'] = $company_id;
                                $data['preview'] = true;
                                echo $return = $this->load->view('partial/modul/_rpt_tbl',$data);
                                
                          
                        ?>
                    </div>
                    <br>
                </div>
                <div class="control-group-btn">
                    <div class="left1-control-group-btn">
                    </div>
                    <div class="left-control-group-btn">
                        <button onclick="javascript:print_<?php echo $grid; ?>()" class="ui-button-text icon-save" id="btn_print_pdf">Print Report</button>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function print_<?php echo $grid; ?>() {
        var form_value = $("form#<?php echo $grid; ?>_form").serialize();
        var url = base_url + 'processor/erpss/<?php echo $grid; ?>?action=print&' + form_value;
        var format = $("#search_grid_<?php echo $grid ?>_iformat").val();

        if (format == "") {
            _custom_alert("Format Print Belum Dipilih", 'Error!', 'info', "<?php echo $grid; ?>", 1, 5000);
            return false;
        } else {
            window.open(url, "_blank",'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no');
        }
    }

    
    // $("#tbl_<?php echo $grid; ?>").dataTable({

    // });

    $("#tbl_<?php echo $grid; ?>").dataTable({
        "filter": false,
    });
    
</script>

