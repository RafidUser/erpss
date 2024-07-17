<style type="text/css">
	margin: 0 7px 0 0;
    padding: 0px;
</style>
<div class="box_cbox">
	<table width="100%" border="0">
		<tbody>
			<tr>
			<?php
				$i=0;
				$icomp = '';
				foreach($isi as $value) {
					foreach($value as $k=>$v) {
						$icomp= $icomp ."".$v.",";
					}
				}
				$val = explode(",",$icomp);
				foreach($company as $d) {
						echo '</tr><tr>';
					$check = in_array($d['iCompanyId'], $val) ? 'checked' : '';
					
			?>
				<td><input type="checkbox" <?php echo $check ?>  name="iCom[]" id="iCom<?php echo $i ?>" value="<?php echo $d['iCompanyId'] ?>" class="dn_radio dokbb">
				<label for="iCom<?php echo $i ?>"><?php echo $d['vCompName'] ?></label></td>			
			<?php
					$i++;
				}
			?>
			</tr>
			
		</tbody>
	</table>
</div>