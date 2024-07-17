<?php 

    //print_r($post);
    $company_id = $_GET['company_id'];
    $url 	= base_url()."processor/erpss/bi_flow";

    $searchCol      = array('tgl' => 'Tanggal'
                            , 'pic' => 'PIC'
                            , 'setatus' => 'Status'
                            , 'file' => 'File Feedback'
                        );
    $searchWidth    = array('tgl' => 130
                            , 'pic' => 380
                            , 'setatus' => 120
                            , 'file' => 350
                        );
    $searchAlign    = array(
                            'tgl' => 'center'
                            ,'pic' => 'left'
                            , 'setatus' => 'center'
                            , 'file' => 'center'
                        );
?>

<table id="<?php echo 'tabel_rpt_summary_'.$grid ?>"></table>
<div id="<?php echo 'pager_rpt_summary_'.$grid ?>"></div>

<script>
    <?php
        
        foreach ($searchCol as $kv => $vv) {
            $col_name[] = "'" . $vv . "'";
            $col_width  = isset($searchWidth[$kv]) ? ",width: " . $searchWidth[$kv] : "";
            $col_align  = isset($searchAlign[$kv]) ? ",align: '" . $searchAlign[$kv] . "'" : "";
            $col_model[] = "{name:'" . $kv . "'" . $col_width . $col_align . "}";
        }

        /* tambahkan param untuk jqgrid */
        $postData = array();
        foreach ($post as $kv => $vv) {
            $postData[] = $kv .": '" . $vv . "' ";
        }
        
    ?>

    var alamat 		= '<?php echo $url;?>';
    var company_id 		= '<?php echo $company_id;?>';
    var cKode 		= '<?php echo $rowDataH['cKode'];?>';
    jQuery("#<?php echo 'tabel_rpt_summary_'.$grid ?>").jqGrid({
        url: alamat+'?action=getDataValidasi&company_id='+company_id+'&cKode='+cKode,
        postData: {
                    grid: "<?php echo $grid ?>",
                    <?php echo implode(",", $postData) ?> 
                    
                    
                },
        datatype: "json",
        mtype: 'GET',
        colNames: [<?php echo implode(",", $col_name) ?>],
        colModel: [
                    <?php echo implode(",", $col_model); ?>
                ],
        //loadonce: false,
        height: 400,
        rowNum:10,
        rowList:[10,20,30],
        pager: '<?php echo 'pager_rpt_summary_'.$grid ?>',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        subGrid: true,
        subGridOptions: {
            "plusicon"  : "ui-icon-triangle-1-e",
            "minusicon" : "ui-icon-triangle-1-s",
            "openicon"  : "ui-icon-arrowreturn-1-e",
            // load the subgrid data only once
            // and the just show/hide
            "reloadOnExpand" : false,
            // select the row when the expand column is clicked
            "selectOnExpand" : true
        },
        subGridRowExpanded: function(subgrid_id, row_id) {
            var subgrid_table_id, pager_id;
            subgrid_table_id = "<?php echo 'tabel_rpt_summary_'.$grid ?>_"+subgrid_id+"_t";
            pager_id = "<?php echo 'tabel_rpt_summary_'.$grid ?>_"+"p_"+subgrid_table_id;
            $("#"+subgrid_id).html("<table id='"+subgrid_table_id+"' class='scroll'></table><div id='"+pager_id+"' class='scroll'></div>");
            jQuery("#"+subgrid_table_id).jqGrid({
                url: alamat+'?action=getDataValidasiSub&company_id='+company_id+'&idRow='+row_id+'&index=',
                datatype: "json",
                colNames: ['Kategori','Status'],
                colModel: [
                    {name:"vName",index:"vName",width:350,"align":"left"},
                    {name:"validasi",index:"validasi",width:120,"align":"center"},
                ],
                rowNum:15,
                rownumbers:true,
                pager: pager_id,
                sortname: 'num',
                sortorder: "asc",
                height: '100%'
            });
            jQuery("#"+subgrid_table_id).jqGrid('navGrid',"#"+pager_id,{edit:false,add:false,del:false,search:false})
        },
        caption: "<?php echo $caption ?>"
    }).navGrid('#<?php echo 'pager_rpt_summary_'.$grid; ?>', {
                edit: false,
                add: false,
                del: false,
                search: false,
                refresh: true
            });
    
</script>