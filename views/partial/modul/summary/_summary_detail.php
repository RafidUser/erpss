<?php
	$urlProses = base_url();
    $rowD = $this->db->get_where('hrd.biflow_kategori', array('cKode_kategori' => $post['cKode_kategori']))->row();
    unset($postData['action']);
    // print_r($rowD);
    // echo '<br>';
    // print_r($post);

    // stdClass Object ( [id] => 4 [cKode_kategori] => BFK-22-10-004 [vName] => Informasi Input Awal Transaksi [mDescription] => [lNeed_valid] => 2 [lHas_param] => 2 [iUrut] => 4 [cTable_main] => hrd.biflow_iat [cTable_main_pk] => cKode_iat [cTable_history] => hrd.biflow_iat_history [cTable_history_pk] => cKode_history [cCreate] => N14615 [dCreate] => 2022-10-06 09:05:25 [cUpdate] => [dUpdate] => 2022-10-06 11:02:50 [lDeleted] => 0 )
    // Array ( [action] => detail_sub [act] => getDataValidasiSub [cKode_kategori] => BFK-22-10-004 [id_hist] => NPL-BFL-22-10-003-HIST-2022-10-05 16:52:54 [company_id] => 3 [modul_id] => 0 [group_id] => 0 ) 

    $company_id = $_GET['company_id'];
    $url 	= base_url()."processor/erpss/bi/flow";

    $searchCol      = array(
                             'mKeterangan' => 'Keterangan BI'
                            ,'iValidate' => 'Status'
                            , 'mValidate' => 'Keterangan SA'
                        );
    $searchWidth    = array(
                             'mKeterangan' => 450
                             ,'iValidate' => 120
                            , 'mValidate' => 450
                        );
    $searchAlign    = array(
                            
                            'mKeterangan' => 'left'
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
            url: alamat+'?action=getData&company_id='+company_id+'&id_hist='+id_hist+'&cKode_kategori='+cKode_kategori,
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
            rowList:[10,20,30],
            pager: '<?php echo 'pager_sum_detail_'.$grid ?>',
            sortname: 'id',
            viewrecords: true,
            sortorder: "desc",
            height: '100%',
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