<style type="text/css">
	table.hover_table tr:hover {
		
	}
</style>
<script type="text/javascript">
	var config = {
	source: base_url+'processor/ss/transaksi/support/request?action=pic',
		select: function(event, ui){
			var i = $('.vName_pic_div').index(this);
			$('.vName_pic_div_cNip').eq(i).val(ui.item.id);
		},
		minLength: 2,
		autoFocus: true,
	};
	$(".vName_pic_div").livequery(function(){
		$(this).autocomplete(config);
		var i = $(".vName_pic_div").index(this);
		$(this).keypress(function(e){
			if(e.which != 13) {
				$(".vName_pic_div_cNip").eq(i).val('');
			}			
		});
		$(this).blur(function(){
			if($(".vName_pic_div_cNip").eq(i).val() == "") {
				$(this).val('');
			}			
		});
	})
/*function add_row(table_id){		
		//alert(table_id);
		var row = $('table#'+table_id+' tbody tr:last').clone();
		$("span."+table_id+"_num:first").text('1');
		var n = $("span."+table_id+"_num:last").text();
		if (n.length == 0) {
			var row_content = '';
			row_content	  = '<tr style="border: 1px solid #dddddd; border-collapse: collapse; background: #ffffff; ">';
			row_content	 += '<td style="border: 1px solid #dddddd; width: 3%; text-align: center;">';
			row_content	 += '<span class="'+table_id+'_num">1</span></td>';			
			row_content	 += '<td  style="border: 1px solid #dddddd; width: 30%">';
			row_content	 += '<input class="input_rows-table vName_pic_div"  name="nip[]" type="hidden" >';
			row_content	 +='<input type="text" class="input_rows-table vName_pic_div"  name="vnip[]"  style="width: 100%" /></td>';
			row_content  += '<td style="border: 1px solid #dddddd; width: 15%">';
			row_content	 += '<select name="actif[]">';
			row_content  += '	<option value=""></option>';
			row_content	 += '	<?php foreach($activity as $a) {?>';
			row_content	 +=	'<option value="<?php echo $a['typeId'] ?>"><?php echo $a['typeName'] ?></option>';
			row_content	 +=	'<?php } ?>';
			row_content  +='</select>';
			row_content  +='</td>';
			row_content	 += '<td style="border: 1px solid #dddddd; width: 10%">';
			row_content	 += '<span class="delete_btn"><a href="javascript:;" class="brosur_bb_del" onclick="del_row(this, \'brosur_bb_del\')">[Hapus]</a></span></td>';		
			row_content  += '</tr>';
			
			jQuery("#"+table_id+" tbody").append(row_content);
		} else {
			var no = parseInt(n);
			var c = no + 1;
			var row_content = '';
			row_content	  = '<tr style="border: 1px solid #dddddd; border-collapse: collapse; background: #ffffff; ">';
			row_content	 += '<td style="border: 1px solid #dddddd; width: 3%; text-align: center;">';
			row_content	 += '<span class="'+table_id+'_num">1</span></td>';			
			row_content	 += '<td  style="border: 1px solid #dddddd; width: 30%">';
			row_content	 += '<input class="input_rows-table vName_pic_div"  name="nip[]" type="hidden" >';
			row_content	 +='<input type="text" class="input_rows-table vName_pic_div"  name="vnip[]"  style="width: 100%" /></td>';
			row_content  += '<td style="border: 1px solid #dddddd; width: 15%">';
			row_content	 += '<select name="actif[]">';
			row_content  += '	<option value=""></option>';
			row_content	 += '	<?php foreach($activity as $a) {?>';
			row_content	 +=	'<option value="<?php echo $a['typeId'] ?>"><?php echo $a['typeName'] ?></option>';
			row_content	 +=	'<?php } ?>';
			row_content  +='</select>';
			row_content  +='</td>';
			row_content	 += '<td style="border: 1px solid #dddddd; width: 10%">';
			row_content	 += '<span class="delete_btn"><a href="javascript:;" class="brosur_bb_del" onclick="del_row(this, \'brosur_bb_del\')">[Hapus]</a></span></td>';		
			row_content  += '</tr>';
			$('table#'+table_id+' tbody tr:last').after(row_content);
           	$('table#'+table_id+' tbody tr:last input').val("");
			$('table#'+table_id+' tbody tr:last div').text("");
			$("span."+table_id+"_num:last").text(c);		
		}
}
*/
	
</script>
<table class="hover_table" id="assigment" cellspacing="0" cellpadding="1" style="width: 98%; border: 1px solid #dddddd; text-align: center; margin-left: 5px; border-collapse: collapse">
	<thead>
	<tr style="width: 98%; border: 1px solid #dddddd; background: #548cb6; border-collapse: collapse">
		<th colspan="4" style="border: 1px solid #dddddd;"><span style="font-weight: bold; color: #ffffff; text-shadow: 0 1px 1px rgba(0, 0, 0, 0.3); text-transform: uppercase;">Assign To</span></th>
	</tr>
	<tr style="width: 98%; border: 1px solid #dddddd; background: #b3d2ea; border-collapse: collapse">
		<th style="border: 1px solid #dddddd;">No</th>
		<th style="border: 1px solid #dddddd;">Activity</th>
		<th style="border: 1px solid #dddddd;">PIC</th>
		<?php if ($this->input->get('action') == 'create') { ?><th style="border: 1px solid #dddddd;">Action</th>		
		<?php } ?>
	</tr>
	</thead>
	<tbody id="list_assigment">
		<?php
			$i = 0;
			if(!empty($pic)) {
				foreach($pic as $pic) {
				$i++;				
		?>
			<tr style="border: 1px solid #dddddd; border-collapse: collapse; background: #ffffff; ">
					<td style="border: 1px solid #dddddd; width: 3%; text-align: center;">
						<span class="assigment_num"><?php echo $i ?></span>
					</td>		
				
					<td style="border: 1px solid #dddddd; width: 40%">
						<input type="hidden"  value="<?php echo $pic['cPIC'] ?>" />
						<input type="text" value="<?php echo $pic['activity'] ?>" disabled  style="width: 100%" />
					</td>
					<td style="border: 1px solid #dddddd; width: 40%">
						<input type="hidden" name="nip1[]" value="<?php echo $pic['cPIC'] ?>" />
						<input type="text" name="vnip1[]" value="<?php echo $pic['vName']." - ".$pic['cPIC'] ?>" disabled  style="width: 100%" />
					</td>	
					
					<td style="border: 1px solid #dddddd; width: 10%">
						<?php if ($this->input->get('action') == 'create') { ?>	<span class="delete_btn"><a href="javascript:;" class="assigment_del" onclick="del_row(this, 'assigment_del')">[Hapus]</a></span>
						<?php } ?>
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
				<span class="assigment_num">1</span>
			</td>		
				
			<td style="border: 1px solid #dddddd; width: 15%">
			<select name="actif[]">
					<option value=""></option>
					<?php foreach($activity as $a) {?>
						<option value="<?php echo $a['activity_id'] ?>"><?php echo $a['activity'] ?></option>
					<?php } ?>
				</select>
			</td>
			<td style="border: 1px solid #dddddd; width: 75%">
				<input class="input_rows-table vName_pic_div_cNip"  name="nip1[]" type="hidden" >
				<input type="text" class="input_rows-table vName_pic_div"  name="vnip1[]"  style="width: 100%" />
			</td>
			
			<td style="border: 1px solid #dddddd; width: 10%">
				<span class="delete_btn"><a href="javascript:;" class="assigment_del" onclick="del_row(this, 'assigment_del')">[Hapus]</a></span>
			</td>
				<?php } ?>
		</tr>
		<?php } ?>
	</tbody>
	<tfoot>
		<tr>
			<?php	if ($this->input->get('action') == 'create') { ?><td colspan="3"></td><td style="text-align: center"><a href="javascript:;" onclick="javascript:add_row('assigment')">Tambah</a></td>
			<?php } ?>
		</tr>
	</tfoot>
</table>