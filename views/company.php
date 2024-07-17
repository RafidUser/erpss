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
				foreach($company as $d) {
				
				echo '</tr><tr>';
					
			?>
				<td><input type="checkbox" name="iCom[]" id="iCom<?php echo $i ?>" value="<?php echo $d['iCompanyId'] ?>" class="dn_radio dokbb">
				<label for="iCom<?php echo $i ?>"><?php echo $d['vCompName'] ?></label></td>				
			<?php
					$i++;
				}
			?>
			</tr>
			<!-- <input type="hidden" name="iComp" id="iComp" value=<?php //echo $i;?>> -->
		</tbody>
	</table>
</div>