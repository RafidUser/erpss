<style type="text/css">
	table.hover_table tr:hover {
		
	}
</style>
<script type="text/javascript">
	var config = {
	source: base_url+'processor/ss/master/problem/category?action=pic',					
		select: function(event, ui){
			var i = $('.vDeafult_pic_div').index(this);
			$('.vDeafult_pic_div_cNip').eq(i).val(ui.item.id);
		},
		minLength: 2,
		autoFocus: true,
	};
	$(".vDeafult_pic_div").livequery(function(){
		$(this).autocomplete(config);
		var i = $('.vDeafult_pic_div').index(this);
		$(this).keypress(function(e){
			if(e.which != 13) {
				$('.vDeafult_pic_div_cNip').eq(i).val('');
			}			
		});
		$(this).blur(function(){
			if($('.vDeafult_pic_div_cNip').eq(i).val() == '') {
				$(this).val('');
			}			
		});
	})
	
</script>
<table class="hover_table" id="default_pic" cellspacing="0" cellpadding="1" style="width: 98%; border: 1px solid #dddddd; text-align: center; margin-left: 5px; border-collapse: collapse">
	<thead>
	<tr style="width: 98%; border: 1px solid #dddddd; background: #b3d2ea; border-collapse: collapse">
		<th style="border: 1px solid #dddddd;">No</th>
		<th style="border: 1px solid #dddddd;">PIC</th>
		<th style="border: 1px solid #dddddd;">Activity</th>
		<th style="border: 1px solid #dddddd;">Action</th>		
		
	</tr>
	</thead>
	<tbody id="list_default_pic">
		<?php
			$i = 0;
			if(!empty($pic)) {
				foreach($pic as $pic) {
				$i++;				
		?>
				<tr style="border: 1px solid #dddddd; border-collapse: collapse; background: #ffffff; ">
					<td style="border: 1px solid #dddddd; width: 3%; text-align: center;">
						<span class="default_pic_num"><?php echo $i ?></span>
					</td>		
					<td style="border: 1px solid #dddddd; width: 40%">
						<input class="input_rows-table vDeafult_pic_div_cNip"  name="cnip[]" type="hidden" value="<?php echo $pic['cPic'] ?>"  >
						<input type="text" class="input_rows-table vDeafult_pic_div"  name="vnip[]"  style="width: 100%" value="<?php echo $pic['vName']." - ".$pic['cPic'] ?>" />
					
					</td>	
					<td style="border: 1px solid #dddddd; width: 40%">
						<select name="actif[]">
							<option value="(Null)"></option>
							<?php foreach($activity as $row) {
									 if ($pic['id_activity'] == $row['activity_id']) $selected = " selected";
									else $selected = '';
							   ?>
								<option <?php echo $selected; ?> value="<?php echo $row['activity_id'] ?>"><?php echo $row['activity'] ?></option>
							<?php } ?>
				</select>
					</td>
					
					<td style="border: 1px solid #dddddd; width: 10%">
							<span class="delete_btn"><a href="javascript:;" class="default_pic_del" onclick="del_row(this, 'default_pic_del')">[Hapus]</a></span>
					
					</td>		
				</tr>
		<?php
				}
			}
			else {
			$i++;
		?>
		<tr style="border: 1px solid #dddddd; border-collapse: collapse; background: #ffffff; ">
			
			<?php	if ($this->input->get('action') == 'create') { ?><td style="border: 1px solid #dddddd; width: 3%; text-align: center;">
				<span class="default_pic_num">1</span>
			</td>		
			<td style="border: 1px solid #dddddd; width: 75%">
				<input class="input_rows-table vDeafult_pic_div_cNip"  name="cnip[]" type="hidden" ><input type="text" class="input_rows-table vDeafult_pic_div"  name="vnip[]"  style="width: 100%" />
			</td>	
			<td style="border: 1px solid #dddddd; width: 15%">
				<select name="actif[]">
					<option value="(Null)"></option>
					<?php foreach($activity as $a) {?>
						<option value="<?php echo $a['activity_id'] ?>"><?php echo $a['activity'] ?></option>
					<?php } ?>
				</select>
			</td>
			
			<td style="border: 1px solid #dddddd; width: 10%">
				<span class="delete_btn"><a href="javascript:;" class="default_pic_del" onclick="del_row(this, 'default_pic_del')">[Hapus]</a></span>
			</td>
				<?php } ?>
		</tr>
		<?php } ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="3"></td><td style="text-align: center"><a href="javascript:;" onclick="javascript:add_row('default_pic')">Tambah</a></td>
		
		</tr>
	</tfoot>
</table>