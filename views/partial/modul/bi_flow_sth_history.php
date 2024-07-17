

<style type="text/css">
	.<?php echo $id; ?>_table{ 
		width	 		: 99%;
	}

	.<?php echo $id; ?>_table thead tr th{    
		border 			: 1px solid #89b9e0;
	    text-align 		: center;
	    color 			: #FFFFFF;
	    background 		: rgb(84, 140, 182) none repeat scroll 0% 0%;
	    text-transform 	: uppercase; 
	    padding 		: 5px;
	}

	.<?php echo $id; ?>_table tbody tr td{
		border 			: 1px #dddddd solid;
		padding 		: 3px;
		text-align 		: center;
	}

	.<?php echo $id; ?>_table tbody tr{
		border 			: 1px solid #ddd;
		border-collapse : collapse;
		background 		: #fff
	}

	.<?php echo $id; ?>{
		min-width 		: 99%; 
		overflow-x 		: scroll; 
		overflow-y 		: hidden; 
		white-space 	: nowrap;
		margin 			: 5px;
	}
</style>

<div class="<?php echo $id; ?>">
	<table class="<?php echo $id; ?>_table">
		<thead>
			<tr>
				<th>PIC</th>
				<th>Ext</th>
				<th>Feedback</th>
				<th>Tgl Submit</th>
				<th>Validasi</th>
				<th>Status</th>
			</tr>
		</thead>
		<tbody>
			<?php
				foreach($datasql as $k => $r){
					$grid 		= str_replace('_'.$field, '', $id);
					$pic 	= $r['cPic'];
					$ext	 	= $r['mExt'];
					$fb		 	= $r['mFeedback'];
					$fb_sa	 	= $r['mValidate'];
					$tgl_submit	= $r['dSubmit'];
					$validasi 	= $r['Validasi'];
					$status 	= $r['Status'];
			?>
			<tr>
				<td><?php echo $pic; ?></td>
				<td><?php echo $ext; ?></td>			
				<td> SA : <?php  echo $fb_sa;?> <br/>
					BI : <?php  echo $fb;?>
				</td>
				<td><?php echo $tgl_submit; ?></td>
				<td><?php echo $validasi; ?></td>
				<td><?php echo $status; ?></td>
			</tr>
			<?php
				}
			?>
		</tbody>
	</table>
</div>
