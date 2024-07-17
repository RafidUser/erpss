<?php
	$urlProses = base_url();
    $rowD = $this->db->get_where('hrd.biflow_kategori', array('cKode_kategori' => $post['cKode_kategori']))->row();
    unset($postData['action']);
    $company_id = $_GET['company_id'];
    $url 	= base_url()."processor/erpss/bi/flow";

    $searchCol      = array(
                            'vModul' => 'Nama Modul'
                            , 'mKeterangan' => 'Keterangan BI'
                            ,'iValidate' => 'Status'
                            , 'mValidate' => 'Keterangan SA'
                        );
    $searchWidth    = array(
                             'vModul' => 250
                            , 'mKeterangan' => 360
                             ,'iValidate' => 120
                            , 'mValidate' => 360
                        );
    $searchAlign    = array(
                            
                            'vModul' => 'left'
                            ,'mKeterangan' => 'left'
                            ,'iValidate' => 'center'
                            , 'mValidate' => 'left'
                        );
?>
<div id="<?php echo $id; ?>">
    <table id="<?php echo 'tabel_sum_detail_'.$grid ?>"></table>
    <div id="<?php echo 'pager_sum_detail_'.$grid ?>"></div>

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
        var id_hist 		= '<?php echo $post['id_hist'];?>';
        var cKode_kategori 		= '<?php echo $post['cKode_kategori'];?>';
        jQuery("#<?php echo 'tabel_sum_detail_'.$grid ?>").jqGrid({
            url: alamat+'?action=getData2&company_id='+company_id+'&id_hist='+id_hist+'&cKode_kategori='+cKode_kategori,
            /* postData: {
                        grid: "<?php echo $grid ?>",
                        <?php echo implode(",", $postData) ?> 
                        
                        
                    }, */
            datatype: "json",
            mtype: 'GET',
            colNames: [<?php echo implode(",", $col_name) ?>],
            colModel: [
                        <?php echo implode(",", $col_model); ?>
                    ],
            //loadonce: false,
            rowNum:10,
            height: 450,
            rowList:[10,20,30],
            pager: '<?php echo 'pager_sum_detail_'.$grid ?>',
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
                subgrid_table_id = "<?php echo 'tabel_sum_detail_'.$grid ?>_"+subgrid_id+"_t";
                pager_id = "<?php echo 'tabel_sum_detail_'.$grid ?>_"+"p_"+subgrid_table_id;
                $("#"+subgrid_id).html("<table id='"+subgrid_table_id+"' class='scroll'></table><div id='"+pager_id+"' class='scroll'></div>");
                jQuery("#"+subgrid_table_id).jqGrid({
                    url: alamat+'?action=getDataParam&company_id='+company_id+'&idRow='+row_id+'&index=',
                    datatype: "json",
                    colNames: ['Parameter','BI','Keterangan BI','SA','Keterangan SA'],
                    colModel: [
                        {name:"vName",index:"vName",width:500,"align":"left"},
                        {name:"iStatus_bi",index:"iStatus_bi",width:60,"align":"center"},
                        {name:"mKeterangan_bi",index:"mKeterangan_bi",width:250,"align":"center"},
                        {name:"iStatus_sa",index:"iStatus_sa",width:60,"align":"center"},
                        {name:"mKeterangan_sa",index:"mKeterangan_sa",width:250,"align":"center"},
                    ],
                    rowNum:20,
                    rownumbers:true,
                    pager: pager_id,
                    sortname: 'id',
                    sortorder: "asc",
                    height: '100%'
                });
                jQuery("#"+subgrid_table_id).jqGrid('navGrid',"#"+pager_id,{edit:false,add:false,del:false,search:false})
            },
            caption: "<?php echo $caption ?>"
        }).navGrid('#<?php echo 'pager_sum_detail_'.$grid; ?>', {
                    edit: false,
                    add: false,
                    del: false,
                    search: false,
                    refresh: true
                });

    </script>
</div>