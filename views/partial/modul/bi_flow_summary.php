<?php  
    //print_r($rowDataH);
    $label = $this->url.'_form_detail_'.$field;

?>
<div id="<?php echo $id ?>">
    <div class="boxContent" style="overflow:auto;">
        <div class="box_dropdown">
            <div class="content">
                <div class="box_content_form">
                    <div class="full_colums">
                        <div class="table_kondisi">
                            <?php 
                                $data['rowDataH'] = $rowDataH;
                                $this->load->view('partial/modul/summary/_summary',$data);
                            ?>
                        </div>
                        <br>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style type="text/css">
.table_header_mom {
    padding: 10px;
}

.table_detail_inner {
    border-collapse: collapse;
}

.table_detail_inner thead tr {
    border-collapse: collapse;
    background-color: #548cb6;
    color: #fff;
}

.table_detail_inner thead tr th {
    border: 1px solid #ddd;
}

.table_detail_inner tbody tr {
    border-collapse: collapse;
    background-color: #fff;
    color: #000;
}

.table_detail_inner tbody tr td {
    border: 1px solid #ddd;
    text-align: center;
    padding: 5px;
    vertical-align: top;
}

.table_kondisi {
    margin: 10px;
}

.table_kondisi.dataTables_wrapper {
    width: 800px;
}

.table_detail_kondisi thead tr {
    border-collapse: collapse;
    background-color: #548cb6;
    color: #fff;
}

.table_detail_kondisi thead tr th {
    border: 1px solid #ddd;
}

.table_detail_kondisi tbody tr {
    border-collapse: collapse;
    background-color: #fff;
    color: #000;
}

.table_detail_kondisi tbody tr td {
    border: 1px solid #ddd;
    text-align: center;
    padding: 5px;
    vertical-align: top;
}

.table_kondisi_2 {
    margin: 10px;
    width: auto;
}

.table_detail_kondisi_2 {
    border-collapse: collapse;
}

.table_detail_kondisi_2 thead tr {
    border-collapse: collapse;
    background-color: #548cb6;
    color: #fff;
}

.table_detail_kondisi_2 thead tr th {
    border: 1px solid #ddd;
}

.table_detail_kondisi_2 tbody tr {
    border-collapse: collapse;
    background-color: #fff;
    color: #000;
}

.table_detail_kondisi_2 tbody tr td {
    border: 1px solid #ddd;
    text-align: center;
    padding: 5px;
    vertical-align: top;
}

.detail_inner {
    margin: 10px;
}

.table-kesimpulan {
    width: 50%;
    margin: 0 auto;
    border-collapse: collapse;
    border: none !important;
}

.table-kesimpulan tr td {
    border-collapse: collapse;
    border: 1px solid #fff !important;
}
</style>

<script type="text/javascript">

    // $("label[for='<?php echo $label; ?>']").remove();
    $('label[for="<?php echo $label ?>"]').css({"border": "1px solid #dddddd", "background": "#548cb6", "border-collapse": "collapse","width":"99%","font-weight":"bold","color":"#ffffff","text-shadow": "0 1px 1px rgba(0, 0, 0, 0.3)","text-transform": "uppercase","text-align": "center","padding":"5px","margin-top":"10px"});
    $("#<?php echo $id; ?>").parent().removeClass('rows_input');
</script>