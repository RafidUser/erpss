<style type="text/css">
	#<?php echo $url; ?>_validasi_wrapper{ 
        border: 2px #A1CCEE solid;
        padding: 5px;
        background: #fff;
        border-radius: 5px;
        margin: auto;
        width: 96%;
        display: block !important;
        overflow: auto !important;
        min-height: 300px;
    }

	#<?php echo $url; ?>_validasi_tabel {
	    border: 1px solid #dddddd; 
	    padding: 5px;
	    background: #fff;
	    border-radius: 5px;
	    width: 98%;
	    text-align: center;
	    margin-left: 8px;
	    border-collapse: collapse;
	    margin: auto;
	    /*overflow: hidden !important;*/
	}

	#<?php echo $url; ?>_validasi_tabel tr td {
	    vertical-align: top;
	    border: 1px solid #dddddd;
	    padding: 5px;
	}

	#<?php echo $url; ?>_validasi_tabel tr th {
	    border: 1px solid #dddddd;
	}

	#<?php echo $url; ?>_validasi_tabel thead tr {
	    width: 100%;
	    border: 1px solid #dddddd;
	    background: #b3d2ea;
	    border-collapse: collapse;
	}
</style>


<?php  

$label = $this->url .'_form_detail_' . $field;

// get List Parameter
$sql = "SELECT *
		FROM hrd.biflow_ms_parameter a
		WHERE a.lDeleted = 0";
$rows = $this->db->query($sql)->result_array();

// get Checked Parameter
// if(isset($rowData) && !empty($rowData)){
// 	$sqlChkd = "SELECT *
// 				FROM ".$table." a
// 				WHERE a.lDeleted = 0
// 				AND a.".$table_key." = '".$rowData[$table_key]."' ";
// 	$rowChkd = $this->db->query($sqlChkd)->result_array();
// }

?>

<div id="<?php echo $this->url; ?>_validasi_wrapper">
    <table id="<?php echo $url ?>_validasi_tabel" style="width: 98%;">
        <thead>
            <tr>
                <th rowspan="2" style="width: 5%;">No</th>
                <th rowspan="2" style="width: 75%;">Parameter</th>
                <!-- <th colspan="2" style="width: 20%;">BI</th>
                <th rowspan="2" style="width: 20%;">Keterangan</th> -->
            </tr>
            <!-- <tr>
            	<th style="width: 10%;">Yes</th>
            	<th>No</th>
            </tr> -->
        </thead>

        <tbody>
<?php  
	$table      = "hrd.biflow_iat_param";
    $table_key  = "cKode_iat";

	$i = 1;
	foreach ($rows as $key => $r) {
		// get value when update/view
		$sqlChkd = "SELECT *
					FROM ".$table." a
					WHERE a.lDeleted = 0
					AND a.".$table_key." = '".$rowData[$table_key]."'
					AND a.cKode_parameter = '".$r['cKode_parameter']."' ";
		$rowChkd = $this->db->query($sqlChkd)->row_array();

		$chkdYes = '';
		$chkdNo = '';
		if($rowChkd['iStatus_bi'] == '2'){
			$chkdYes = 'checked';
		}else if($rowChkd['iStatus_bi'] == '1'){
			$chkdNo = 'checked';
		}

		$readonly_bi = 'readonly';
		if($rowChkd['iStatus_bi'] == '1'){
			$readonly_bi = '';
		}

		echo '<tr>';
			echo '<td>'.$i.'</td>';
			echo '<td style="text-align: left;">'.nl2br($r['vName']).'</td>';
			// echo '<td>
			// 		<input class="radio_bi" type="radio" name="status_bi['.$r['cKode_parameter'].']" value="2" '.$chkdYes.'>
			// 	</td>';
			// echo '<td>
			// 		<input class="radio_bi" type="radio" name="status_bi['.$r['cKode_parameter'].']" value="1" '.$chkdNo.'>
			// 	</td>';
			// echo '<td>
			// 		<textarea class="textarea_bi" name="keterangan_bi['.$r['cKode_parameter'].']" '.$readonly_bi.'>'.nl2br($rowChkd["mKeterangan_bi"]).'</textarea>
			// 	</td>';
		echo '</tr>';
		$i++;
	}
?>
        </tbody>
    </table>
</div>


<script>
	// Delete Label
	$('label[for="<?php echo $label ?>"]').css({"border": "1px solid #dddddd", "background": "#548cb6", "border-collapse": "collapse","width":"99%","font-weight":"bold","color":"#ffffff","text-shadow": "0 1px 1px rgba(0, 0, 0, 0.3)","text-transform": "uppercase","text-align": "center","padding":"5px","margin-top":"10px"});
	$('#<?php echo $this->url ?>_validasi_wrapper').parent().removeClass('rows_input');

	// Jika No maka Aktifkan Textarea
	$(".radio_bi").on("change", function(){
		// 1 = No, 2 = Yes
		var ix = $(this).closest('tr').index();

		if($(this).val() == '1'){
			$('.textarea_bi').eq(ix).prop('readonly', false);
		}else{
			$('.textarea_bi').eq(ix).prop('readonly', true);
		}
	})

</script>