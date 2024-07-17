<style type="text/css">
	table.hover_table tr:hover {
		
	}
</style>
<script type="text/javascript">	

function add_row_group_pic(table_id){		
		//alert(table_id);
		var row = $('table#'+table_id+' tbody tr:last').clone();
		$("span."+table_id+"_num:first").text('1');
		var n = $("span."+table_id+"_num:last").text();
		if (n.length == 0) {
			var c = 1;
			var row_content = '';
			row_content	  = '<tr style="border: 1px solid #dddddd; border-collapse: collapse; background: #ffffff; ">';
			row_content	 += '<td style="border: 1px solid #dddddd; width: 3%; text-align: center;">';
			row_content	 += '<span class="'+table_id+'_num">1</span></td>';			
			row_content	 += '<td style="border: 1px solid #dddddd; width: 25%">';
			row_content	 += '<input type="text"  name="vgroup[]"  style="width: 100%" /></td>';
			row_content	 += '<td style="border: 1px solid #dddddd; width: 25%">';
			row_content  += '<input name="master_default_pic_location[]" id="master_default_pic_location'+c+'" type="hidden"/>';	
			row_content  += '<input name="master_default_pic_location_text[]" id="master_default_pic_location_text'+c+'" type="text" />';
			row_content	 += '<div id="l_location"></div></td>';
			row_content	 += '<td style="border: 1px solid #dddddd; width: 25%">';
			row_content  += '<input name="master_default_pic_pic[]" id="master_default_pic_pic'+c+'" type="hidden"/>';	
			row_content  += '<input name="master_default_pic_pic_text[]" id="master_default_pic_pic_text'+c+'" type="text" />';
			row_content	 += '<div id="l_pic3"></div></td>';
			row_content	 += '<td style="border: 1px solid #dddddd; width: 10%">';
			row_content	 += '<span class="delete_btn"><a href="javascript:;" class="group_pic_del" onclick="del_row(this, \'group_pic_del\')">[Hapus]</a></span></td>';		
			row_content  += '</tr>';
			
			jQuery("#"+table_id+" tbody").append(row_content);
		} else {
			var no = parseInt(n);
			var c = no + 1;
			var row_content = '';
			row_content	  = '<tr id="tr'+c+'"style="border: 1px solid #dddddd; border-collapse: collapse; background: #ffffff; ">';
			row_content	 += '<td style="border: 1px solid #dddddd; width: 3%; text-align: center;">';
			row_content	 += '<span class="'+table_id+'_num">1</span></td>';			
			row_content	 += '<td style="border: 1px solid #dddddd; width: 25%">';
			row_content	 += '<input type="text"  name="vgroup[]"  style="width: 100%" /></td>';
			row_content	 += '<td style="border: 1px solid #dddddd; width: 25%">';
			row_content  += '<input name="master_default_pic_location[]" id="master_default_pic_location'+c+'" type="hidden"/>';	
			row_content  += '<input name="master_default_pic_location_text[]" id="master_default_pic_location_text'+c+'" type="text" />';
			row_content	 += '<div id="l_location'+c+'"></div></td>';
			row_content	 += '<td style="border: 1px solid #dddddd; width: 25%">';
			row_content  += '<input name="master_default_pic_pic[]" id="master_default_pic_pic'+c+'" type="hidden"/>';	
			row_content  += '<input name="master_default_pic_pic_text[]" id="master_default_pic_pic_text'+c+'" type="text" />';
			row_content	 += '<div id="l_pic3'+c+'"></div></td>';
			row_content	 += '<td style="border: 1px solid #dddddd; width: 10%">';
			row_content	 += '<span class="delete_btn"><a href="javascript:;" class="group_pic_del" onclick="del_row(this, \'group_pic_del\')">[Hapus]</a></span></td>';		
			row_content  += '</tr>';
			
			$('table#'+table_id+' tbody tr:last').after(row_content);
           	$('table#'+table_id+' tbody tr:last input').val("");
			$('table#'+table_id+' tbody tr:last div').text("");
			$("span."+table_id+"_num:last").text(c);		
		}
		$(document).ready(function() {
				var config = {
					source: function( request, response) {
						$.ajax({
							url: base_url+"processor/ss/master/problem/category?action=pic",
							dataType: "json",
							data: {
									term: request.term,
									l_pic3: getLPIC3(c)
							},
							success: function( data ) {
								response( data );
							}
						});
					},
					select:function(event, ui){
					//	alert("change"+ui.item.id);
						$("#l_pic3"+c).append("<div id=\'div_"+ui.item.id+"\'><input type=\'hidden\' class=\'nip\' id=\'nip_"+ui.item.id+"\' name=\'nip"+c+"[]\' value=\'"+ui.item.id+"\'/>"+ui.item.value+" [<span onclick=\'remove_element(\""+ui.item.id+"\");\' style=\'cursor:pointer;color:red;\'> x </span>]</div>");
						$(this).val(""); return false;
					},
					//minLength: 2,
					autoFocus: true,
				};
				function getLPIC3(id) {
					var l_pic3 = [];
					$(".nip").each(function() {								
						l_pic3.push($(this).val());
					});
					return l_pic3;
				}
				function remove_element(id) {
					$("#div_"+id).remove();
				}
				
				$( "#master_default_pic_pic_text"+c ).livequery(function() {
					$( this ).autocomplete(config);
					
				});
				
		var config1 = {
			source: function( request, response) {
				$.ajax({
					url: base_url+"processor/ss/master/problem/category?action=npl",
					dataType: "json",
					data: {
						term: request.term,
						l_location: getLocation(c)
					},
					success: function( data ) {
						response( data );
					}
				});
			},
			select:function(event, ui){								
				$("#l_location"+c).append("<div id=\'div_"+ui.item.id+"\'><input type=\'hidden\' class=\'location\' id=\'location_"+ui.item.id+"\' name=\'location"+c+"[]\' value=\'"+ui.item.id+"\'/>"+ui.item.value+" [<span onclick=\'remove_element(\""+ui.item.id+"\");\' style=\'cursor:pointer;color:red;\'> x </span>]</div>");
				
				$(this).val(""); return false;
			},
			//minLength: 2,
			autoFocus: true,
		};

		$( "#master_default_pic_location_text"+c ).livequery(function() {
			$( this ).autocomplete(config1);
			
		});

	});
	
	function getLocation(c) {
		var l_location = [];
		$(".location").each(function() {								
			l_location.push($(this).val());
		});
		
		return l_location;
	}
	
				
}
	
</script>
<table class="hover_table" id="group_pic" cellspacing="0" cellpadding="1" style="width: 98%; border: 1px solid #dddddd; text-align: center; margin-left: 5px; border-collapse: collapse">
	<thead>
	<tr style="width: 98%; border: 1px solid #dddddd; background: #b3d2ea; border-collapse: collapse">
		<th style="border: 1px solid #dddddd;">No</th>
		<th style="border: 1px solid #dddddd;">Group Name</th>
		<th style="border: 1px solid #dddddd;">Covarge Area</th>
		<th style="border: 1px solid #dddddd;">Pic</th>
		<?php if ($this->input->get('action') == 'create') { ?><th style="border: 1px solid #dddddd;">Action</th>		
		<?php } ?>
	</tr>
	</thead>
	<tbody id="list_group_pic">
		<?php
			$i = 0;
			if(!empty($group_pic)) {
				foreach($group_pic as $pic) {
				$i++;				
		?>
				<tr style="border: 1px solid #dddddd; border-collapse: collapse; background: #ffffff; ">
					<td style="border: 1px solid #dddddd; width: 3%; text-align: center;">
						<span class="group_pic_num"><?php echo $i ?></span>
					</td>		
					<td style="border: 1px solid #dddddd; width: 40%">
						<input type="text"  name="vgroup[]"  style="width: 100%" value="<?php echo $pic['cGroupName']?>" />
					</td>	
					<td style="border: 1px solid #dddddd; width: 25%">
						
						<input name="master_default_pic_location" id="master_default_pic_location" type="hidden"/>
						<input name="master_default_pic_location_text" id="master_default_pic_location_text" type="text" />
						<?php 
						$sql = "SELECT DISTINCT(s.`ilocationId`), w.`V_LOCATION_NAME` FROM ss.`pic_group_detail` s INNER JOIN  hrd.`worklocation` w ON w.`I_LOCATION_ID`= s.`ilocationId` WHERE s.`id_pic_gd` = '".$pic['id_pic_gd']."'";
						$query = mysql_query($sql);
						while($row = mysql_fetch_array($query)) {	
							echo "<br>".$row['V_LOCATION_NAME']."<br>";
						}
						?>
						<div id="l_location"></div>
					</td>
					<td style="border: 1px solid #dddddd; width: 25%">
						<input name="master_default_pic_pic" id="master_default_pic_pic" type="hidden"/>
						<input name="master_default_pic_pic_text" id="master_default_pic_pic_text" type="text" />
						<div id="l_pic3"></div>
						<?php 
						$sql = "SELECT DISTINCT(s.`pic`), w.`vName` FROM ss.`pic_group_detail` s INNER JOIN  hrd.`employee` w ON w.`cNip`= s.`pic` WHERE s.`id_pic_gd` = '".$pic['id_pic_gd']."'";
						$query = mysql_query($sql);
						while($row = mysql_fetch_array($query)) {	
							echo "<br>".$row['vName']."-".$row['pic']."<br>";
						}
						?>
					</td>
					
					<td style="border: 1px solid #dddddd; width: 10%">
						<?php if ($this->input->get('action') == 'create') { ?>	<span class="delete_btn"><a href="javascript:;" class="group_pic_del" onclick="del_row(this, 'group_pic_del')">[Hapus]</a></span>
						<?php } ?>
					</td>		
				</tr>
		<?php
				}
			}
			else {
			
		?>
		<tr style="border: 1px solid #dddddd; border-collapse: collapse; background: #ffffff;" id="tr">
			<?php	if ($this->input->get('action') == 'create') { ?>
			<td style="border: 1px solid #dddddd; width: 3%; text-align: center;">
				<span class="group_pic_num">1</span>
			</td>		
			<td style="border: 1px solid #dddddd; width: 25%">
				<input type="text"  name="vgroup[]"  style="width: 100%" />
			</td>
			<td style="border: 1px solid #dddddd; width: 25%">
				<input name="master_default_pic_location" id="master_default_pic_location" type="hidden"/>
				<input name="master_default_pic_location_text" id="master_default_pic_location_text" type="text" />
				<div id="l_location"></div>
			</td>
			<td style="border: 1px solid #dddddd; width: 25%">
				<input name="master_default_pic_pic" id="master_default_pic_pic" type="hidden"/>
				<input name="master_default_pic_pic_text" id="master_default_pic_pic_text" type="text" />
				<div id="l_pic3"></div>
			</td>
			
			<td style="border: 1px solid #dddddd; width: 10%">
				<span class="delete_btn"><a href="javascript:;" class="group_pic_del" onclick="del_row(this, 'group_pic_del')">[Hapus]</a></span>
			</td>
				<?php } ?>
		</tr>
		<?php } ?>
	</tbody>
	<tfoot>
		<tr>
			<?php	if ($this->input->get('action') == 'create') { ?><td colspan="4"></td><td style="text-align: center"><a href="javascript:;" onclick="javascript:add_row_group_pic('group_pic')">Tambah</a></td>
			<?php } ?>
		</tr>
	</tfoot>
</table>

<Script>
$(document).ready(function() {
	var config = {
		source: function( request, response) {
			$.ajax({
				url: base_url+"processor/ss/master/problem/category?action=pic",
				dataType: "json",
				data: {
					term: request.term,
					l_pic3: getLPIC3()
				},
				success: function( data ) {
					response( data );
				}
			});
		},
		select:function(event, ui){								
			$("#l_pic3").append("<div id=\'div_"+ui.item.id+"\'><input type=\'hidden\' class=\'nip\' id=\'nip_"+ui.item.id+"\' name=\'nip1[]\' value=\'"+ui.item.id+"\'/>"+ui.item.value+" [<span onclick=\'remove_element(\""+ui.item.id+"\");\' style=\'cursor:pointer;color:red;\'> x </span>]</div>");
			
			$(this).val(""); return false;
		},
		minLength: 2,
		autoFocus: true,
	};
	function getLPIC3() {
		var l_pic3 = [];
		$(".nip").each(function() {								
			l_pic3.push($(this).val());
		});
		
		return l_pic3;
	}
	function remove_element(id) {
		$("#div_"+id).remove();
	}
	$( "#master_default_pic_pic_text" ).livequery(function() {
		$( this ).autocomplete(config);
		//var i = $('#master_default_pic_pic_text').index(this);
		
	});
	
		var config1 = {
			source: function( request, response) {
				$.ajax({
					url: base_url+"processor/ss/master/problem/category?action=npl",
					dataType: "json",
					data: {
						term: request.term,
						l_location: getLocation()
					},
					success: function( data ) {
						response( data );
					}
				});
			},
			select:function(event, ui){								
				$("#l_location").append("<div id=\'div_"+ui.item.id+"\'><input type=\'hidden\' class=\'location\' id=\'location_"+ui.item.id+"\' name=\'location1[]\' value=\'"+ui.item.id+"\'/>"+ui.item.value+" [<span onclick=\'remove_element(\""+ui.item.id+"\");\' style=\'cursor:pointer;color:red;\'> x </span>]</div>");
				
				$(this).val(""); return false;
			},
			minLength: 2,
			autoFocus: true,
		};

		$( "#master_default_pic_location_text").livequery(function() {
			$( this ).autocomplete(config1);
			
		});

		});

		function getLocation() {
		var l_location = [];
		$(".location").each(function() {								
			l_location.push($(this).val());
		});

		return l_location;
		}
	
</script>
	