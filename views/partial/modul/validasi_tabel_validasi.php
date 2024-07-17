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

<div id="<?php echo $url; ?>_validasi_wrapper">
    <table id="<?php echo $url ?>_validasi_tabel" style="width: 100%;">
        <thead>
            <!-- <tr style="width: 100%; border: 1px solid #dddddd; background: #548cb6; border-collapse: collapse">
                <th colspan="5">
                    <span style="font-weight: bold; color: #ffffff; text-shadow: 0 1px 1px rgba(0, 0, 0, 0.3); text-transform: uppercase;">
                        VALIDASI
                    </span>
                </th>
            </tr> -->
            <tr>
                <th rowspan="2" style="width: 5%;">No</th>
                <th rowspan="2" style="width: 55%;">Parameter</th>
                <th colspan="2" style="width: 10%;">BI</th>
                <th rowspan="2" style="width: 10%;">Keterangan</th>
                <th colspan="2" style="width: 10%;">SA</th>
                <th rowspan="2" style="width: 10%;">Keterangan</th>
            </tr>
            <tr>
                <th style="width: 5%;">Yes</th>
                <th>No</th>
                <th style="width: 5%;">Yes</th>
                <th>No</th>
            </tr>
        </thead>

        <tbody>
<?php  
	$i = 1;
	foreach ($rows as $key => $r) {
		// get value when update/view
		$sqlChkd = "SELECT *
					FROM ".$table." a
					WHERE a.lDeleted = 0
					AND a.".$table_key." = '".$rowData[$table_key]."'
					AND a.cKode_parameter = '".$r['cKode_parameter']."' ";
		$rowChkd = $this->db->query($sqlChkd)->row_array();

		$chkdBiYes = '';
		$chkdBiNo = '';
		if($rowChkd['iStatus_bi'] == '2'){
			$chkdBiYes = 'checked';
		}else if($rowChkd['iStatus_bi'] == '1'){
			$chkdBiNo = 'checked';
		}

		$chkdSaYes = '';
		$chkdSaNo = '';
		if($rowChkd['iStatus_sa'] == '2'){
			$chkdSaYes = 'checked';
		}else if($rowChkd['iStatus_sa'] == '1'){
			$chkdSaNo = 'checked';
		}

		$readonly_sa = 'readonly';
		if($rowChkd['iStatus_sa'] == '1'){
			$readonly_sa = '';
		}

		echo '<tr>';
			echo '<td>'.$i.'</td>';
			echo '<td style="text-align: left;">'.nl2br($r['vName']).'</td>';
			echo '<td>
					<input class="radio_bi" type="radio" name="status_bi['.$r['cKode_parameter'].']" value="2" '.$chkdBiYes.' disabled>
					<input type="hidden" name="status_bi['.$r['cKode_parameter'].']" value="'.$rowChkd['iStatus_bi'].'">
				</td>';
			echo '<td>
					<input class="radio_bi" type="radio" name="status_bi['.$r['cKode_parameter'].']" value="1" '.$chkdBiNo.' disabled>
				</td>';
			echo '<td>
					<textarea class="textarea_bi" name="keterangan_bi['.$r['cKode_parameter'].']" readonly>'.nl2br($rowChkd["mKeterangan_bi"]).'</textarea>
				</td>';
			echo '<td>
					<input class="radio_sa" type="radio" name="status_sa['.$r['cKode_parameter'].']" value="2" '.$chkdSaYes.'>
				</td>';
			echo '<td>
					<input class="radio_sa" type="radio" name="status_sa['.$r['cKode_parameter'].']" value="1" '.$chkdSaNo.'>
				</td>';
			echo '<td>
					<textarea class="textarea_sa" name="keterangan_sa['.$r['cKode_parameter'].']" '.$readonly_sa.'>'.nl2br($rowChkd["mKeterangan_sa"]).'</textarea>
				</td>';
		echo '</tr>';
		$i++;
	}
?>
        </tbody>
    </table>
</div>


<script>
	// Delete Label
	$('label[for="<?php echo $url.'_'.$field ?>"]').css({"border": "1px solid #dddddd", "background": "#548cb6", "border-collapse": "collapse","width":"99%","font-weight":"bold","color":"#ffffff","text-shadow": "0 1px 1px rgba(0, 0, 0, 0.3)","text-transform": "uppercase","text-align": "center","padding":"5px","margin-top":"10px"});
	$('#<?php echo $url ?>_validasi_wrapper').parent().removeClass('rows_input');

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

	$(".radio_sa").on("change", function(){
		// 1 = No, 2 = Yes
		var ix = $(this).closest('tr').index();

		if($(this).val() == '1'){
			$('.textarea_sa').eq(ix).prop('readonly', false);
		}else{
			$('.textarea_sa').eq(ix).prop('readonly', true);
		}
	})

</script>