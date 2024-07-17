<?php
    $CI = &get_instance();
    $dbset = $CI->load->database('hrd', true);
	
	///print_r($dtcomp);
?>
<div class="box_cbox">
    <table border="0" width="100%">
		<tr>
			<td valign='top' width='20%'>
				Is assigned to handle
			</td>
			<td width='80%'>
				<?php
					$sql = "SELECT iCompanyId, vCompName from hrd.company where CAST(ldeleted as unsigned) = 0";
					$query = $dbset->query($sql);
					if ($query->num_rows() > 0) {
						foreach($query->result() as $r) {
							if (in_array($r->iCompanyId, $dtcomp)) $checked = 'checked';
							else $checked = ' ';
				?>
				<div>
				<input <?php echo $checked;?> type='checkbox' name='<?php $class_name;?>_company[]' 
					id = '<?php $class_name;?>_company_<?php echo $r->iCompanyId;?>' 
					class ='<?php $class_name;?>_company' value = '<?php echo $r->iCompanyId;?>'/>
					<?php echo $r->vCompName;?>
				</div>
				<?php
						}
					}
				?>
			</td>
		</tr>
	</table>
</div>