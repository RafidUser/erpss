

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
				<th>Tgl Upload</th>
				<th>Dok Upload</th>
				<th>Feedback</th>
				<th>Keterangan</th>
				<th>Validasi</th>
				<th>Status</th>
			</tr>
		</thead>
		<tbody>
			<?php
				foreach($datasql as $k => $r){
					$grid 		= str_replace('_'.$field, '', $id);
					$dSubmit 	= $r['dSubmit_uat'];
					// $dok	 	= $r['vFilename'];
					$fb		 	= $r['mFeedback'];
					$fb_sa	 	= $r['mValidate'];
					$desc	 	= $r['mKeterangan'];
					$validasi 	= $r['Validasi'];
					$status 	= $r['Status'];

					$linknya = '';
					if (!empty($r)) {
						if(file_exists($r['vFilename_generate'])) {
							$link = base_url().'processor/erpss/uat_ifm?action=download&path='.$r['vFilename_generate'].'&name='.$r['vFilename'];                
							$linknya = '<a class="" href="javascript:;" onclick="window.open(\''.$link.'\', \'_blank\')">'.$r['vFilename'].'</a> <br><br>';
						}else{
							$linknya = $r['vFilename'].' [No File] <br><br>';
						}
						$dok = $linknya;
					}
			?>
			<tr>
				<td><?php echo $dSubmit; ?></td>
				<td><?php echo $dok; ?></td>				
				<td> SA : <?php  echo $fb_sa;?> <br/>
					BI : <?php  echo $fb;?>
				</td>
				<td><?php echo $desc; ?></td>
				<td><?php echo $validasi; ?></td>
				<td><?php echo $status; ?></td>
			</tr>
			<?php
				}
			?>
		</tbody>
	</table>
</div>
