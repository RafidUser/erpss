<style type="text/css" media="screen">
	.f {
    background-color: #eaf1f7;
    border: 1px solid #89b9e0;
    margin-bottom: 5px;
    width: 100%;
}
</style>
<div class="f">
	<div class="top_form_head">									
		<span class="form_head top-head-content">
		<?php echo $caption ?></span>
	</div> 
	<div class="clear"></div>
	<div class="content-table" style="overflow:hidden;">
		<?php foreach ($rfilter as $kf => $vf) {?>
		<!-- <div class="left_colums2"> -->
			<form id="<?php echo $grid; ?>_form">
			<div class="form_horizontal_plc">
				<div class="rows_group">
					<label class="rows_label" for="search_grid_transaksi_<?php echo $grid; ?>"><?php echo $vf ?></label>
					<div class="rows_input select_rows">
						<?php echo $rinput[$kf]; ?>
					</div>
				</div>
			</div>
			</form>
		<!-- </div> -->
		<?php	
		} ?>
		<div class="clear"></div>
		<div class="control-group-btn">
			<div class="left-control-group-btn">
				<?php echo $button; ?>								
			</div>
		</div>		

	</div>
</div>
<div class="preview_<?php echo $grid?>">
</div>
<script>
	$(".<?php echo $grid?>_choosen").chosen({
        allow_single_deselect: true
    });
    $(function() {
        var els = jQuery(".<?php echo $grid?>_choosen");
        els.chosen({no_results_text: "No results matched"});
        els.on("liszt:showing_dropdown", function () {
                $(this).parents("div").css("overflow", "visible");
            });
        els.on("liszt:hiding_dropdown", function () {
                $(this).parents("div").css("overflow", "");
            });
    });

	function reset_<?php echo $grid ?>(){
		$.each($(".search_box_<?php echo $grid; ?>"), function(index, el) {
			$(".search_box_<?php echo $grid; ?>").eq(index).val("");
		});
		
		$(".<?php echo $grid?>_choosen").val('').trigger("liszt:updated");
		$(".preview_<?php echo $grid?>").html("");
	}

	function priview_<?php echo $grid ?>(grid,url){
		//var mom_id  	= $("#search_grid_<?php echo $grid?>_iplc2_mom").val();

		// if ( mom_id == "" ){
		// 	_custom_alert("Nomor Registrasi Belum Dipilih", 'Error!', 'info', "<?php echo $grid; ?>", 1, 5000);
		// 	return false;
		// } else {
			var form_value = $("form#<?php echo $grid; ?>_form").serialize();
			$.ajax({
				url 	: url,
				type 	: 'post',
				data 	: form_value,
				beforeSend: function() {
					$(".preview_<?php echo $grid?>").html("<img src=\"<?php echo base_url('assets/images/376.GIF'); ?>\">");
				},
				success: function(data) {
					$(".preview_<?php echo $grid?>").html(data);
				}		
			});
		// }

	}
</script>