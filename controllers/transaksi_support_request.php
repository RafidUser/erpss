<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    class transaksi_support_request extends MX_Controller {
    private $sess_auth;
    private $dbset;
    function __construct() {
        parent::__construct();
        $this->sess_auth = new Zend_Session_Namespace('auth'); 
		$this->load->library('auth');
		$this->load->library('lib_utilitas');
		$this->user = $this->auth->user();
        $this->dbset = $this->load->database('hrd', true);
        $this->url = 'transaksi_support_request'; 
		$this->load->helper(array('tanggal','to_mysql'));
    }
    
    function index($action = '') {
    	$action = $this->input->get('action');
	
        $grid = new Grid;		
        $grid->setTitle('Support Request');		
        $grid->setTable('ss.raw_problems');		
        $grid->setUrl('transaksi_support_request');
        $grid->addList('id','requestor','support_type','problem_subject','problem_description','proposed_solution','pic','crJustify','crImpact');//'lPersen', 'yPersen',
		$mydept=$this->auth->tipe();
		if($this->auth->is_manager()){
			$x=$this->auth->dept();
			$manager=$x['manager'];
			if(in_array('MIS', $manager)){
				$type='MIS';
				$grid->addFields('requestor','id','CompanyId_support','V_LOCATION_NAME','support_type', 'typeId','pic','Assignment','filename','problem_subject','problem_description','Priority','deadline','crJustify','crImpact','Approval');
			}else{
				$type='';
				$grid->addFields('requestor','id','CompanyId_support','V_LOCATION_NAME','support_type','pic','filename','problem_subject','problem_description','Priority','deadline','crJustify','crImpact','Approval');
			}
		}
		else{
			$x=$this->auth->dept();
			$team=$x['team'];
			if(in_array('MIS', $team)){
				$type='MIS';
				$grid->addFields('requestor','id','CompanyId_support','V_LOCATION_NAME','support_type', 'typeId','pic','Assignment','filename','problem_subject','problem_description','Priority','deadline','crJustify','crImpact','Approval');
			}else{
				$type='';
				$grid->addFields('requestor','id','CompanyId_support','V_LOCATION_NAME','support_type','pic','filename','problem_subject','problem_description','Priority','deadline','crJustify','crImpact','Approval');
			}
		}
		$grid->setLabel('requestor', 'Requestor');
		$grid->setWidth('requestor', '220');
		$grid->setAlign('requestor', 'left');
		$grid->setWidth('pic', '150');
		$grid->setLabel('pic', 'PIC');
		$grid->setLabel('CompanyId_support', 'Company');
		$grid->setLabel('support_type', 'Problem Category');
		$grid->setLabel('proposed_solution', 'Solution');
		$grid->setLabel('typeId', 'Problem Type');
		$grid->setLabel('activity_id', 'Activity Type');
		$grid->setLabel('filename', 'Attachment');	
		$grid->setLabel('problem_subject', 'Subject');
		$grid->setLabel('crJustify', 'Justification');
		$grid->setLabel('crImpact', 'Impact of not implementing proposed of change');
		$grid->setLabel('worklocation.V_LOCATION_NAME', 'Work Area');
		$grid->setLabel('V_LOCATION_NAME', 'Work Area');
		$grid->setLabel('id','SSiD');
		$grid->setWidth('id', '50');
		
		$grid->setFormUpload(TRUE);
		//relation
		//$grid->setJoinTable('hrd.ss_activity_type','ss.raw_problems.activity_id = hrd.ss_activity_type.activity_id','inner');
		$grid->setJoinTable('hrd.employee','raw_problems.requestor = hrd.employee.cNip','inner');
		$grid->setJoinTable('hrd.worklocation', 'hrd.employee.iWorkArea =hrd.worklocation.I_LOCATION_ID', 'inner');
		$grid->setQuery('raw_problems.ldeleted', 0);
		$grid->setSortBy('id');
		$grid->setSortOrder('desc'); //sort ordernya
		//set search
        $grid->setSearch('requestor','id');
		
        //set required
        $grid->setRequired('CompanyId_support','support_type','deadline','problem_subject','problem_description');	//Field yg mandatori

        $grid->setGridView('grid');

        switch ($action) {
                case 'json':
                        $grid->getJsonData();
                        break;
                case 'view':
                        $grid->render_form($this->input->get('id'), true);
                        break;
				case 'searchTypeId' :
							$this->searchTypeId();
							break;
				case 'searchProblem' :
							$this->searchProblem();
							break;
				case 'download':
						$this->download($this->input->get('file'));
						break;
                case 'create':
                        $grid->render_form();
                        break;
				case 'pic':
					$this->pic();
                case 'createproses':
					$x = 0;
			
				
				$isUpload = $this->input->get('isUpload');
				$sql = array();
   				if($isUpload) {

					$path = realpath("files/ss/");
					 if (!mkdir($path."/".$this->input->get('lastId'), 0777, true)) {
					     die('Failed upload, try again!');
					 }

					
					$i = 0;
					foreach ($_FILES['fileupload']["error"] as $key => $error) {
						if ($error == UPLOAD_ERR_OK) {			

							$tmp_name = $_FILES['fileupload']["tmp_name"][$key];
							$name = $_FILES['fileupload']["name"][$key];
							$data['filename'] = $name;
							$data['id']=$this->input->get('lastId');
							$data['dInsertDate'] = date('Y-m-d H:i:s');
							if(move_uploaded_file($tmp_name, $path."/".$this->input->get('lastId')."/".$name)) {

								$sql[] = "INSERT INTO ss.attachment_file (id_ssid, vFilename, dCreated,cCreatedBy) 
										VALUES ('".$data['id']."', '".$data['filename']."','".$data['dInsertDate']."','".$this->user->gNIP."')";
								
								$i++;	
							}
							else{
							echo "Upload ke folder gagal";	
							}
						}
					}
						//upload 
						foreach($sql as $q) {
							try {
								$this->dbset->query($q);
							}catch(Exception $e) {
								die($e);
							}
						}
					
					$r['status'] = TRUE;
					$r['last_id'] = $this->input->get('lastId');					
					echo json_encode($r);
					exit();
				}  else {
					echo $grid->saved_form();
				}
				
				break;
                case 'update':
                        $grid->render_form($this->input->get('id'));
                        break;
                case 'updateproses':
                       $isUpload = $this->input->get('isUpload');
				$sql = array();
   				$file_name= "";
				$fileId = array();
				$path = realpath("files/ss/");
				
				if (!file_exists( $path."/".$this->input->post('master_brosur_id') )) {
					mkdir($path."/".$this->input->post('master_brosur_id'), 0777, true);						 
				}
									
				$file_keterangan = array();
				
				foreach($_POST as $key=>$value) {
											
				
					
					//
					if ($key == 'namafile') {
						foreach($value as $k=>$v) {
							$file_name[$k] = $v;
						}
					}
		
					//
					if ($key == 'fileid') {
						foreach($value as $k=>$v) {
							$fileId[$k] = $v;
						}
					}
				}

				$last_index = 0;	
						
   				if($isUpload) {
					$j = $last_index;		
							
								
					if (isset($_FILES['fileupload'])) {
						$this->hapusfile($path, $file_name, 'attachment_file', $this->input->post('master_brosur_id'));
						foreach ($_FILES['fileupload']["error"] as $key => $error) {	
							if ($error == UPLOAD_ERR_OK) {
								$tmp_name = $_FILES['fileupload']["tmp_name"][$key];
								$name = $_FILES['fileupload']["name"][$key];
								$data['filename'] = $name;
								$data['id']=$this->input->post('master_brosur_id');
								$data['nip']=$this->user->gNIP;
								//$data['iupb_id'] = $insertId;
								$data['dInsertDate'] = date('Y-m-d H:i:s');
				 				//$file_tanggal[$i] = date('l, F jS, Y', strtotime($file_tanggal[$i]));		
				 				if(move_uploaded_file($tmp_name, $path."/".$this->input->post('master_brosur_id')."/".$name)) 
				 				{
				 					
									$sql[] = "INSERT INTO attachment_file (id_ssid, vFilename, dCreated, vKeterangan, cCreatedBy) 
										VALUES ('".$data['id']."', '".$data['filename']."','".$file_keterangan[$j]."','".$data['nip']."')";
									
								$j++;																			
								}
								else{
								echo "Upload ke folder gagal";	
								}
							}
							
						}						
					}		
												
					foreach($sql as $q) {
						try {
							$this->dbset->query($q);
						}catch(Exception $e) {
							die($e);
						}
					}
					
				
					$r['status'] = TRUE;
					$r['last_id'] = $this->input->post('master_brosur_id');					
					echo json_encode($r);
					exit();
				}  else {
					
					if (is_array($file_name)) {									
						$this->hapusfile($path, $file_name, 'attachment_file', $this->input->post('master_brosur_id'));
					}
													
					echo $grid->updated_form();
				}
				break;
				
				case 'getemployee':
						echo $this->getEmployee();
						break;
				case 'getpic':
						echo $this->getPic();
						break;
				case 'getpicsupport':
						echo $this->getPic2();
						break;
				case 'getpicx':
						echo $this->getPicx();
						break;	
				case 'getActivity':
						echo $this->getActivity();
						break;
                case 'delete':
                        echo $grid->delete_row();
                        break;
                default:
                        $grid->render_grid();
						
						
                        break;
        }
    }   
	function download($filename) {
		$this->load->helper('download');		
		$name = $filename;
		$id = $_GET['id'];
		
		$path = file_get_contents('./files/ss/'.$id.'/'.$name);	
		force_download($name, $path);
	}
	
	
	
	
	
	
	//---------------------------------------------------------------//	
	
	function insertbox_transaksi_support_request_problem_subject($field, $id) {
	
		$o = "<input type='text' class='required' name='".$id."' id='".$id."'  size='50' data-validation='required' data-validation-error-msg='Nama sudah melebihi 250 karakter atau kosong sama sekali'  />";
		$o .= "<script>
                   $('#".$id."').restrictLength($('#maxlength'));
               </script>";
        $o .= '<br/>tersisa <span id="maxlength">250</span> karakter<br/>';
        $this->load->validator(1);
        
	                                            
		return $o;
	}
	function updatebox_transaksi_support_request_problem_subject($field, $id, $value, $rowData) {
	 
			$o = "<label title='Problem Subject'>".nl2br($value)."</label>";
		
		
		return $o;
	}
	function updatebox_transaksi_support_request_problem_description($field, $id, $value, $rowData) {
			$o = "<label title='Problem Description'>".nl2br($value)."</label>";

		return $o;
	}
	function insertbox_transaksi_support_request_problem_description($field, $id) {
	
		$o 	= "<textarea name='".$id."' id='".$id."' class='required' data-validation='required' data-validation-error-msg='Nama sudah melebihi 250 karakter atau kosong sama sekali'   style='width: 240px; height: 50px;'size='250'></textarea>";		
		$o .= "<script>
	               $('#".$id."').restrictLength($('#maxlengthnote'));
	           </script>";
	    $o .= '<br/>tersisa <span id="maxlengthnote">250</span> karakter<br/>';
	    $this->load->validator(1);
	                    
		return $o;
	}
	function insertbox_transaksi_support_request_crJustify($field, $id) {
	
		$o 	= "<textarea name='".$id."' id='".$id."' class='baru' ></textarea>";		
	/*	$o .= "<script>
	               $('#".$id."').restrictLength($('#maxlengthnote'));
	           </script>";
	    $o .= '<br/>tersisa <span id="maxlengthnote">250</span> karakter<br/>';
	    $this->load->validator(1);
	    
	  */                                          
		return $o;
	}
	function updatebox_transaksi_support_request_problem_crJustify($field, $id, $value, $rowData) {
	 
			$o = "<label title='Problem Subject'>".nl2br($value)."</label>";
		
		
		return $o;
	}
	
	function insertbox_transaksi_support_request_crImpact($field, $id) {
	
		$o 	= "<textarea name='".$id."' id='".$id."' ></textarea>";		
		/*$o .= "<script>
	               $('#".$id."').restrictLength($('#maxlengthnote'));
	           </script>";
	    $o .= '<br/>tersisa <span id="maxlengthnote">250</span> karakter<br/>';
	    $this->load->validator(1);
	    */
	                                            
		return $o;
	}
	
	function insertBox_transaksi_support_request_Approval($field, $id) {
		$cNip = $this->user->gNIP;
		$sql = "SELECT a.cUpper from hrd.employee a where a.cNip ='{$cNip}'";
		$query = $this->dbset->query($sql);
		$nama_group1 = '-';
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$nama_group1 = $row->cUpper;
		}
		$sql = "SELECT a.vName from hrd.employee a where a.cNip ='{$nama_group1}'";
		$query = $this->dbset->query($sql);
		$nama_group = '-';
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$nama_group = $row->vName;
		}
		$return ='';
		$return .= '<input type="hidden" name="'.$id.'" id="'.$id.'" class="input_rows1 required" value="'.$nama_group1.'" />';
		$return .= '<input type="text" name="'.$id.'" disabled="TRUE" id="'.$id.'_dis" class="input_rows1 required" value="'.$nama_group.'" />' ;
		
		return $return;
	}
	
	function updatebox_transaksi_support_request_Approval($field, $id, $value,$rowData) {
		$request = $rowData['requestor'];
	
		$sql = "SELECT a.cUpper from hrd.employee a where a.cNip ='{$request}'";
		$query = $this->dbset->query($sql);
		$nama_group1 = '-';
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$nama_group1 = $row->cUpper;
		}
		$sql = "SELECT a.vName from hrd.employee a where a.cNip ='{$nama_group1}'";
		$query = $this->dbset->query($sql);
		$nama_group = '-';
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$nama_group = $row->vName;
		}
		
		return $nama_group;
	}
	
	
	function insertBox_transaksi_support_request_deadline($field, $id) {
		$this->load->helper('to_mysql');
		$date = date('Y-m-d', strtotime('7 Day'));
		$return = '<input type="text" class="input_tgl datepicker input_rows1 required" name="'.$field.'"  id="'.$id.'" value="'.$date.'" >';
		return $return;
	}
	function updatebox_transaksi_support_request_deadline($field, $id, $value, $row) {
		$this->load->helper('to_mysql');
		 $o  =  empty($value) ? '-' :  date('d M Y',strtotime($value));
		return $o;
	}
	function insertBox_transaksi_support_request_id($field, $id) {
	
		$return = 'Auto Generated';
		return $return;
	}
	
	
	public function insertBox_transaksi_support_request_Priority($field, $id) {
		$o ="<input type='radio'  name='".$field."' id='".$id."' value='1' checked/>Emergency";
		$o .="<input type='radio' name='".$field."' id='".$id."' value='2'/> High";
		$o .="<input type='radio' name='".$field."' id='".$id."' value='3'/> Mid";
		$o .="<input type='radio' name='".$field."' id='".$id."' value='4'/> Low";
		return $o;
	}
	public function updateBox_transaksi_support_request_Priority($field, $id, $value, $row) {				
		if ($value ==1){
			$o ="<input type='radio'  name='".$field."' id='".$id."' value='1' checked/>Emergency";
			$o .="<input type='radio' name='".$field."' id='".$id."' value='2'/> High";
			$o .="<input type='radio' name='".$field."' id='".$id."' value='3'/> Mid";
			$o .="<input type='radio' name='".$field."' id='".$id."' value='4'/> Low";
		}elseif ($value ==2){
			$o ="<input type='radio'  name='".$field."' id='".$id."' value='1' />Emergency";
			$o .="<input type='radio'  name='".$field."' id='".$id."' value='2' checked/>High";
			$o .="<input type='radio' name='".$field."' id='".$id."' value='3'/> Mid";
			$o .="<input type='radio' name='".$field."' id='".$id."' value='4'/> Low";
			
		}elseif ($value ==3){
			$o ="<input type='radio'  name='".$field."' id='".$id."' value='1' />Emergency";
			$o .="<input type='radio'  name='".$field."' id='".$id."' value='2' />High";
			$o .="<input type='radio' name='".$field."' id='".$id."' value='3' checked/> Mid";
			$o .="<input type='radio' name='".$field."' id='".$id."' value='4'/> Low";
		}elseif($value ==4){
			$o ="<input type='radio'  name='".$field."' id='".$id."' value='1' />Emergency";
			$o .="<input type='radio'  name='".$field."' id='".$id."' value='2' />High";
			$o .="<input type='radio' name='".$field."' id='".$id."' value='3' /> Mid";
			$o .="<input type='radio' name='".$field."' id='".$id."' value='4' checked/> Low";
		}
		return $o;
	}
	
	function getActivity() {
		
		$data = array();
		$typeId = $_POST['term'];
		$sql ="SELECT a.activity_id as id, b.activity as nama FROM ss.problem_act a  INNER JOIN ss.`activity_type` b ON a.activity_id = b.activity_id  WHERE a.typeId ='27'";
		$query = $this->dbset->query($sql);
		if ($query->num_rows() > 0) {
			$default_id = 0;
			foreach($query->result_array() as $r) {
				$row_array['value'] = trim($r['nama']);
				$row_array['id']    = $r['id'];
				$row_array['default_id'] = $default_id;
				array_push($data, $row_array);
			}
		}
		
		return json_encode($data);
	}
	
	public function insertBox_transaksi_support_request_typeId($field, $id) {
	$url = base_url().'processor/ss/transaksi/support/request?action=getpic';
	$url2 = base_url().'processor/ss/transaksi/support/request?action=getActivity';
	$o =	'<script>
			$(document).ready(function(){
				$("#transaksi_support_request_typeId").change(function(){
					var problem_category=$("#transaksi_support_request_support_type").val();
					
					$.ajax({
						type : "POST",
						dataType : "json",
						url :"'.$url.'",
						data: {
							term: $("#transaksi_support_request_typeId").val(),
							term1: $("#transaksi_support_request_support_type").val(),
							
						},
						success: function( data ) {
							
							$("#l_pic2").html("");
								$("#list_assigment").html("");
							$("span.numberlist:first").text("1");
								var n = $("span.numberlist:last").text();
								var no = parseInt(n);
								var c = 0;	
							var tampung="";
							$.each(data, function(index, element) {
								if(index==0){
									if(element.value=="N"){
										$("label[for=transaksi_support_request_Assignment]").hide();
											$("label[for=transaksi_support_request_crImpact]").hide();
											$("label[for=transaksi_support_request_crJustify]").hide();
											
											$("#transaksi_support_request_crImpact").hide();	
											$("#transaksi_support_request_crJustify").hide();
											$("#assigment").hide();
									}else{
											$("label[for=transaksi_support_request_Assignment]").show();
											$("label[for=transaksi_support_request_crImpact]").addClass("required").show();
											$("label[for=transaksi_support_request_crJustify]").addClass("required").show();
											$("#transaksi_support_request_crImpact").addClass("required").show();
											$("#transaksi_support_request_crJustify").addClass("required").show();
											$("#assigment").show();
									
									}
								}else{
									//alert(element.id);
									html = "<tr><td class=\'numberlist\'>"+c+"</td>";
									html += "<td><select name=\'actif[]\' id=\'assignmen_activity_"+element.id+"\'>";
									html += "</select></td>";
									html +="<td><input type=\'hidden\' class=\'input_rows-table vName_pic_div_cNip\' name=\'nip1[]\' /><input type=\'text\' name=\'vnip1[]\'class=\'input_rows-table vName_pic_div\' style=\'width:90%\'/></td>";
									
									html += "<td>[ <a href=\'javascript:;\' class=\'vName_pic_div_del\'>Hapus</a> ]</td></tr>";
									$("#list_assigment").append(html);
																			
									getActivity($("#transaksi_support_request_typeId").val(), element.id);
										//getActivity($("#transaksi_support_request_support_type").val(), element.id, element.id);
										
									$("#l_pic2").append("<div id=\'div_"+element.id+"\'><input type=\'hidden\' class=\'nip\' id=\'nip_"+element.id+"\' name=\'nip[]\' value=\'"+element.id+"\'/>"+element.value+" [<span onclick=\'remove_element(\""+element.id+"\");\' style=\'cursor:pointer;color:red;\'> x </span>]</div>");
									tampung = tampung+""+element.id+",";
								}
								$("span.numberlist:last").text(c);
									c++;
							});									
							
							$("#l_pic2").append("<input type=\'hidden\' name=\'transaksi_support_request_pic\' value=\'"+tampung+"\' />");
										
						},
									
					});

				});
							 
			});
			$(".vName_pic_div_del").live("click", function() {
					var dis = $(this);
					custom_confirm("Delete Selected Record?", function(){
					if($("table#vName_pic_div_table tbody tr").length == 1) {
						custom_alert("Isi Minimal 1");
					}
					else {
						dis.parent().parent().remove();
					}
				})
			})
			function getActivity(typeId, idx) {				
				$.ajax({
					type : "POST",
					dataType : "json",
					url :"'.$url2.'",
					data: {
						term: typeId,
					},
					success: function( data ) {
						$("#assignmen_activity_"+idx).empty();
						$.each(data, function(index, element) {
						//	alert(element.default_id);
							if (element.default_id == element.id) {
								$selected = "selected";
							} else {$selected = "";}
							$("#assignmen_activity_"+idx).append($("<option "+$selected+"></option>").val(element.id).text(element.value));          
							
						});			

							
					},
								
				});
			}
				</script>';
				
	if ($this->input->get('action') == 'view') {
            $sql = "Select typeId as typeId, typeName as typeName 
                    from ss.support_type where typeId = '{$id}'";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $row = $query->row();
                $o .= $row->typeName;
            }
        } else {

            $o  .= "<select class='required' name='".$field."' id='".$id."'>";
            $o .= "<option value='(Null)'>Pilih</option>";
            $sql = "Select typeId as typeId, typeName as typeName 
                    from ss.support_type order by typeName";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $result = $query->result_array();
                foreach($result as $row) {
                      if ($id == $row['typeId']) $selected = " selected";
                       else $selected = '';
                       $o .= "<option value='".$row['typeId']."'>".$row['typeName']."</option>";
                }
            }
		}	

            $o .= "</select>";
			
			return $o;
			
	}
	public function updateBox_transaksi_support_request_typeId($field, $id, $value) {
		$o='';
		if ($this->input->get('action') == 'view') {
            $sql = "Select typeId as typeId, typeName as typeName 
                    from ss.support_type where typeId = '{$value}'";
            $query = $this->dbset->query($sql);
            
			if ($query->num_rows() > 0) {
                $row = $query->row();
                $o = $row->typeName;
            }
        } else {
			//print_r($value);
            $o  = "<select name='".$field."' id='".$id."'>";
            $o .= "<option value='0'>Pilih</option>";
            $sql = "Select typeId as typeId, typeName as typeName 
                    from ss.support_type order by typeName";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $result = $query->result_array();
                foreach($result as $row) {
					   if ($value == $row['typeId']) $selected = " selected"; 
                       else $selected = '';
                       $o .= "<option {$selected} value='".$row['typeId']."'>".$row['typeName']."</option>";
					  
                }
            }
			   $o .= "</select>";
		
		}	
         	
			return $o;
	}
	
	public function updateBox_transaksi_support_request_support_type($field, $id, $value) {
		$o='';
	   if ($this->input->get('action') == 'view') {
            $sql = "Select item as item 
                    from ss.support where id = '{$value}'";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $row = $query->row();
                $o .= $row->item;
            }
			$o .='<input type="hidden" name="'.$field.'" id="'.$id.'" value = "'.$value.'"/>';
        } else {

            $o  = "<select class='required' name='".$field."' id='".$id."'>";
            $o .= "<option value='0'>Pilih</option>";
            $sql = "Select id as id, item as item 
                    from ss.support order by id";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $result = $query->result_array();
                foreach($result as $row) {
                       if ($value == $row['id']) $selected = " selected";
                       else $selected = '';
                       $o .= "<option {$selected} value='".$row['id']."'>".$row['item']."</option>";
                }
            }
		}
		  $o .= "</select>";
			
			return $o;
	}
	
	/*function insertBox_transaksi_support_request_requestor($field, $id) {
	$cNip = $this->user->gNIP;
	
		$sql = "SELECT a.vName from hrd.employee a where a.cNip ='{$cNip}'";
		$query = $this->dbset->query($sql);
		$nama_group1 = '-';
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$nama_group1 = $row->vName;
		}

	
		$return ='';
		$return .= '<input type="hidden" name="'.$field.'" id="'.$id.'" class="input_rows1 required" value="'.$cNip.'" />';
		$return .= '<input type="text" name="'.$field.'_dis" disabled="TRUE"  id="'.$id.'_dis" class="input_rows1" size="40"  value="'.$nama_group1." - ".$cNip.'" />';
		
		return $return;
		
	}
	*/
	
	function insertBox_transaksi_support_request_requestor($field, $id) {
		$user = $this->user->gNIP;
		$return = '<script>
						$( "button.icon_pop" ).button({
							icons: {
								primary: "ui-icon-newwin"
							},
							text: false
						})
					</script>';
		$return .= '<input type="hidden" name="'.$id.'" id="'.$id.'" class="input_rows1 required" value="'.$user.'" />';
		$return .= '<input type="text" name="'.$id.'_dis" disabled="TRUE" id="'.$id.'_dis" class="input_rows1" size="30" value="'.$this->getEmployeeName($user).'" />';
		$return .= '&nbsp;<button class="icon_pop"  onclick="browse(\''.base_url().'processor/ss/master/employee/popup?field=transaksi_support_request\',\'List Employee\')" type="button">&nbsp;</button>';
		
		return $return;
	}
	function updateBox_transaksi_support_request_requestor($field, $id, $value, $row) {
	
		$sql = "SELECT a.vName from hrd.employee a where a.cNip ='{$value}'";
		$query = $this->dbset->query($sql);
		$nama_group1 = '-';
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$nama_group1 = $row->vName;
		}

	
		$return ='';
		$return .= '<input type="hidden" name="'.$field.'" id="'.$id.'" class="input_rows1 required" value="'.$value.'" />';
		$return .= $nama_group1;
		
		return $return;
		
	}
	
	
	function nilai_icompany($a){
		$iCompany='';
		$sql = "SELECT a.I_LOCATION_ID
				FROM hrd.worklocation a, hrd.employee b WHERE a.I_LOCATION_ID = b.iWorkArea and b.cNip = '".$a."' ";
		$query = $this->dbset->query($sql);
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$iCompany=$row->I_LOCATION_ID;
				
		}
		return $iCompany;
	
	}
	public function insertBox_transaksi_support_request_V_LOCATION_NAME($field, $id) {
		$vCompName='';
		$iCompanyId='';
		$cNip = $this->user->gNIP;
		$sql = "SELECT a.V_LOCATION_NAME, b.iWorkArea
				FROM hrd.worklocation a, hrd.employee b WHERE a.I_LOCATION_ID = b.iWorkArea and b.cNip = '".$cNip."' ";
		$query = $this->dbset->query($sql);
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$vCompName=$row->V_LOCATION_NAME;
			$iCompanyId=$row->iWorkArea;
				
		}
			$o  = "<select name='".$field."' id='".$id."'>";
            $o .= "<option value='0'>Pilih</option>";
            $sql = "SELECT I_LOCATION_ID AS I_LOCATION_ID, V_LOCATION_NAME AS vDescription 
                    FROM hrd.worklocation";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $result = $query->result_array();
                foreach($result as $row) {
                       if ($iCompanyId == $row['I_LOCATION_ID']) $selected = " selected";
                       else $selected = '';
                       $o .= "<option {$selected} value='".$row['I_LOCATION_ID']."'>".$row['vDescription']."</option>";
                }
            }
			$o .="</select>";
		
		return $o;
	}
	
	public function updateBox_transaksi_support_request_V_LOCATION_NAME($field, $id, $value, $row) {
		//print_r($value);
	 if ($this->input->get('action') == 'view') {
           	$sql = "select ma.iWorkArea, w.V_LOCATION_NAME 
				from hrd.employee ma
					inner join hrd.worklocation w on w.I_LOCATION_ID=ma.iWorkArea
				where ma.cNip ='".$row['requestor']."'";
				$query = $this->db->query( $sql );
				$lokasi = '';
				$tes='';
				//print ($sql);
				if( $query->num_rows() > 0 ) {
					$rows = $query->row_array();
					$lokasi=$rows['V_LOCATION_NAME'];
					$tes =$rows['iWorkArea'];
				}
				$o ='';
				//$o.='<input type="text" name="'.$field.'" id="'.$id.'" value="'.$lokasi.'"/>';
				$o .=$lokasi;
		}else{
			 	$sql = "select ma.iWorkArea, w.V_LOCATION_NAME 
				from hrd.employee ma
					inner join hrd.worklocation w on w.I_LOCATION_ID=ma.iWorkArea
				where ma.cNip ='".$row['requestor']."'";
				$query = $this->db->query( $sql );
				$lokasi = '';
				$tes='';
				//print ($sql);
				if( $query->num_rows() > 0 ) {
					$rows = $query->row_array();
					$lokasi=$rows['V_LOCATION_NAME'];
					$tes =$rows['iWorkArea'];
				}
			$o  = "<select name='".$field."' id='".$id."'>";
            $o .= "<option value='0'>Pilih</option>";
            $sql = "SELECT I_LOCATION_ID AS I_LOCATION_ID, V_LOCATION_NAME AS vDescription 
                    FROM hrd.worklocation";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $result = $query->result_array();
			
                foreach($result as $row) {
                       if ($tes == $row['I_LOCATION_ID']) $selected = " selected";
                       else $selected = '';
                       $o .= "<option {$selected} value='".$row['I_LOCATION_ID']."'>".$row['vDescription']."</option>";
                }
            }
			$o .="</select>";
		
	
		}
			return $o;
		/*
				$return ='';
				$return .='<input type="hidden" name="'.$field.'" id="'.$id.'" value = "'.$value.'"/>';
				$return .='<input type="text"   value = "'.$lokasi.'"/>';
				
				return  $return;
		*/	
	}
	//combobox company
	function searchProblem() {
	
		$tipe = $this->input->get('_param');
                $iDivId = $this->input->get('_tipe');
		$data = array();
	
                if ($iDivId == 0) {
                    $sql = "SELECT iCompanyId, vCompName
					FROM hrd.company order by iCompanyId";
                } else {
                    $sql = "SELECT a.`iProblemCatId`, b.`item` FROM ss.`default_pic_company` a 
							INNER JOIN ss.`support` b ON a.`iProblemCatId` = b.`id` WHERE a.`iCompanyId` ='".$tipe."' ";
                }
                
		
		$query = $this->dbset->query($sql);
		if ($query->num_rows > 0) {
			foreach($query->result_array() as $line) {
	
				$row_array['value'] = trim($line['item']);
				$row_array['id']    = $line['iProblemCatId'];
	
				array_push($data, $row_array);
			}
		}
	
		echo json_encode($data);
		exit;
	}
	public function insertbox_transaksi_support_request_CompanyId_support($field, $id) {
		$iCompanyId='';
		$cNip = $this->user->gNIP;
		$sql = "SELECT a.iCompanyId
				FROM hrd.company a, hrd.employee b WHERE a.iCompanyId = b.iCompanyId and b.cNip = '".$cNip."' ";
		$query = $this->dbset->query($sql);
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$iCompanyId=$row->iCompanyId;
				
		}
		
            $o = "<select name='".$field."' id='".$id."'>";
            $o .= "<option value='0'>Pilih</option>";
            $sql = "Select iCompanyId as iCompanyId, vCompName as vCompName
                    from hrd.company";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $result = $query->result_array();
                foreach($result as $row) {
                       if ($iCompanyId == $row['iCompanyId']) $selected = " selected";
                       else $selected = '';
                       $o .= "<option {$selected} value='".$row['iCompanyId']."'>".$row['vCompName']."</option>";
                }
            }
			

            $o .= "</select>";
			
			return $o;
    }
	
	public function updateBox_transaksi_support_request_CompanyId_support($field, $id, $value) {
	$o =	'<script>
					$(document).ready(function(){
							  $("#transaksi_support_request_CompanyId_support").change(function(){
								//alert("change");
								$("#transaksi_support_request_support_type").val(0);

							  });
							});
				</script>';
       if ($this->input->get('action') == 'view') {
            $sql = "Select vCompName as vDescription 
                    from hrd.company where iCompanyId = '{$value}'";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $row = $query->row();
                $o .= $row->vDescription;
            }
				$o .='<input type="hidden" name="'.$field.'" id="'.$id.'" value = "'.$value.'"/>';
        } else {

            $o .= "<select name='".$field."' id='".$id."'>";
            $o .= "<option value='0'>Pilih</option>";
            $sql = "Select iCompanyId as iCompanyId, vCompName as vDescription 
                    from hrd.company order by iCompanyId";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $result = $query->result_array();
                foreach($result as $row) {
                       if ($value == $row['iCompanyId']) $selected = " selected";
                       else $selected = '';
                       $o .= "<option {$selected} value='".$row['iCompanyId']."'>".$row['vDescription']."</option>";
                }
            }
		}

            $o .= "</select>";
			
			return $o;
    }
	
	//company pada grid
	public function listBox_transaksi_support_request_CompanyId_support($value, $pk, $name, $rowData) {

		$sql = "SELECT a.vCompName from hrd.company a where a.iCompanyId ='{$value}'";
		$query = $this->dbset->query($sql);
		$nama_group1 = '-';
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$nama_group1 = $row->vCompName;
		}
		
		return $nama_group1;
	}
	
	public function listBox_transaksi_support_request_pic($value, $pk, $name, $rowData) {
		
		$nama_company = '';
		$company = explode(',', $value);
		$i=0;
		foreach($company as $v) {
			if ($i%2) 
				$nama_company .= "<b>".$this->getEmployeeName($v)."</b><br/>";
			else $nama_company .= $this->getEmployeeName($v)."<br/>";
			
			$i++;
		}
		return $nama_company;
		
	}
	
	
	
	public function listBox_transaksi_support_request_support_type($value, $pk, $name, $rowData) {

		$sql = "SELECT a.item from ss.support a where a.id ='{$value}'";
		$query = $this->dbset->query($sql);
		$nama_group1 = '-';
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$nama_group1 = $row->item;
		}
		
		return $nama_group1;
	}
	
	
	
	
	function listBox_transaksi_support_request_requestor($value, $pk, $name, $rowData) {

		$sql = "SELECT a.vName, a.cNip from hrd.employee a where a.cNip = '{$value}'";
		$query = $this->dbset->query($sql);
		$nama_group = '-';
		$nama ='-';
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$nama = $row->cNip;
			$nama_group = $row->vName;
		}
		
		return $nama_group." - ".$nama;
	}
	
	
	function insertBox_transaksi_support_request_filename($field, $id) {
		
		$data['date'] = date('Y-m-d H:i:s');	
		return $this->load->view('master_transaksi_problem_file',$data,TRUE);
	
	}
	function insertBox_transaksi_support_request_Assignment($field, $id) {	
	
		$sql ="SELECT a.`activity_id`, a.`activity` FROM ss.`activity_type` a ORDER BY a.`activity`";
		$data['activity'] = $this->dbset->query($sql)->result_array();
		
		return $this->load->view('assignment',$data,TRUE);
	
	}
	function updateBox_transaksi_support_request_Assignment($field, $id, $value, $rowData) {
		//print_r($rowData);
		$ids =$rowData['id'];
		//$this->db->select(array('ss.raw_pics.*', 'hrd.employee.vName'), false);
		//$this->db->where(array('ss.raw_pics.id' => $ids));
		//$this->db->order_by('ss.raw_pics.id', 'ASC');
		//$this->db->join('hrd.employee', 'ss.raw_pics.cPIC= hrd.employee.cNip', 'inner');
		$sql ="SELECT assign FROM ss.`raw_problems` a INNER JOIN ss.`support_type` s WHERE a.`typeId` = s.`typeId` and a.`id` ='{$ids}'";
		$query = $this->dbset->query($sql);
		$asiign ='';
		if ($query->num_rows() > 0) {
                $row = $query->row();
                $asiign = $row->assign;
            }
		if($asiign=='Y'){
			$sql2 ="SELECT r.activity_id, a.activity , r.`cPIC`, e.`vName` FROM ss.`raw_pics` r INNER JOIN hrd.`employee` e ON r.`cPIC` = e.`cNip` JOIN ss.`activity_type` a ON r.`activity_id` = a.`activity_id` WHERE r.`id` ='{$ids}' ";
			$data['pic'] = $this->dbset->query($sql2)->result_array();
			//$sql2 ="SELECT a.iActivityID AS typeId, (SELECT activity FROM ss.activity_type WHERE activity_id = a.iActivityID) AS typeName 
			//		FROM ss.default_pic a WHERE a.iProblemTypeID = '".$rowData['typeId']."'";
			//$data['activity'] = $this->dbset->query($sql2)->result_array();
		}else{
			$data['pic']="";
		}
		return $this->load->view('assignment',$data,true);
	}
	
	function updateBox_transaksi_support_request_filename($field, $id, $value, $rowData) {

	 	$id_brosur=$rowData['id'];
		$data['rows'] = $this->db->get_where('ss.attachment_file', array('id_ssid'=>$id_brosur))->result_array();
		//$data['rows'] = $this->db->get_where('brosur.brochure_file', array('id_brochure'=>1))->result_array();
		return $this->load->view('master_transaksi_problem_file',$data,TRUE);
	}
	
	//--------------------------------------------------------------//
	function searchTypeId() {
	
		$tipe = $this->input->get('_param');
                $iDivId = $this->input->get('_tipe');
		$data = array();
	
                if ($iDivId == 0) {
                    $sql = "Select typeId as typeId, typeName as typeName 
							from ss.support_type order by typeName";
                } else {
                    $sql = "SELECT DISTINCT(a.`iProblemTypeID`), b.`typeName` FROM ss.default_pic a INNER JOIN 
							ss.`support_type` b ON a.`iProblemTypeID` = b.`typeId`  WHERE iProblemCatID ='".$tipe."' ";
                }
                
		
		$query = $this->dbset->query($sql);
		if ($query->num_rows > 0) {
			foreach($query->result_array() as $line) {
	
				$row_array['value'] = trim($line['typeName']);
				$row_array['id']    = $line['iProblemTypeID'];
	
				array_push($data, $row_array);
			}
		}
	
		echo json_encode($data);
		exit;
	}
	public function insertBox_transaksi_support_request_support_type($field, $id) {
	$url = base_url().'processor/ss/transaksi/support/request?action=getpicsupport';
	$o =	'<script>
			$(document).ready(function(){
				$("#transaksi_support_request_support_type").change(function(){
					var problem_category=$("#transaksi_support_request_support_type").val();
				
					$.ajax({
						type : "POST",
						dataType : "json",
						url :"'.$url.'",
						data: {
							term: $("#transaksi_support_request_support_type").val(),
						},
						success: function( data ) {
							$("#l_pic2").html("");
							var tampung="";
							$.each(data, function(index, element) {
									$("label[for=transaksi_support_request_Assignment]").hide();
											$("label[for=transaksi_support_request_crImpact]").hide();
											$("label[for=transaksi_support_request_crJustify]").hide();
											
											$("#transaksi_support_request_crImpact").hide();	
											$("#transaksi_support_request_crJustify").hide();
											$("#assigment").hide();
									$("#l_pic2").append("<div id=\'div_"+element.id+"\'><input type=\'hidden\' class=\'nip\' id=\'nip_"+element.id+"\' name=\'nip[]\' value=\'"+element.id+"\'/>"+element.value+" [<span onclick=\'remove_element(\""+element.id+"\");\' style=\'cursor:pointer;color:red;\'> x </span>]</div>");
									tampung = tampung+""+element.id+",";
								
							});									
							
							$("#l_pic2").append("<input type=\'hidden\' name=\'transaksi_support_request_pic\' value=\'"+tampung+"\' />");
										
						},
									
					});

				});
							 
			});
				</script>';
		if ($this->input->get('action') == 'view') {
            $sql = "Select item as item 
                    from ss.support where id = '{$id}'";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $row = $query->row();
                $o .= $row->item;
            }
        } else {
			
            $o .= "<select name='".$field."' id='".$id."'>";
            $o .= "<option value='0'>Pilih</option>";
            $sql = "Select id as id, item as item 
                    from ss.support order by item";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $result = $query->result_array();
                foreach($result as $row) {
                      if ($id == $row['id']) $selected = " selected";
                       else $selected = '';
                       $o .= "<option value='".$row['id']."'>".$row['item']."</option>";
						
					 
                }
            }
		}	

            $o .= "</select>";
			
			return $o;
	}
	public function insertBox_transaksi_support_request_pic($field, $id) {
		$url = base_url().'processor/ss/transaksi/support/request?action=getemployee';
		$o = '
			  <script language="text/javascript">
			  
					$(document).ready(function() {
						var config = {
							source: function( request, response) {
								$.ajax({
									url: "'.$url.'",
									dataType: "json",
									data: {
										term: request.term,
										l_pic2: getLPIC2()
									},
									success: function( data ) {
										response( data );
									}
								});
							},
							select: function(event, ui){
								alert(ui.item.id);
								$("#l_pic2").append("<div id=\'div_"+ui.item.id+"\'><input type=\'hidden\' class=\'nip\' id=\'nip_"+ui.item.id+"\' name=\'nip[]\' value=\'"+ui.item.id+"\'/>"+ui.item.value+" [<span onclick=\'remove_element(\""+ui.item.id+"\");\' style=\'cursor:pointer;color:red;\'> x </span>]</div>");
								
								$(this).val(""); return false;
							},
							minLength: 2,
							autoFocus: true,
						};
	
						$( "transaksi_support_request_pic_text" ).livequery(function() {
						 	$( this ).autocomplete(config);
						});
	
					});
					function remove_element(id) {
						$("#div_"+id).remove();
				    }
					function getLPIC2() {
						var l_pic2 = [];
						$( ".nip" ).each(function() {								
							l_pic2.push($(this).val());
						});
						
						return l_pic2;
					}
		      </script>
			  
			  <input name="'.$id.'" id="'.$id.'" type="hidden"/>
			  <input name="'.$id.'_text" id="'.$id.'_text" type="text" size="50"/>';
		
		$o .= "<div id='l_pic2'>";
		
	
		$o .="</div>";
		
	
		return $o;
	}
	
	public function updateBox_transaksi_support_request_pic($field, $id, $value) {
	
	
		/*$url = base_url().'processor/ss/transaksi/support/request?action=getemployee';
		$o = '
			  <script language="text/javascript">
					$(document).ready(function() {
						
						var url = "'.$url.'";
						var config = {							
							source: function( request, response) {
								$.ajax({
									url: "'.$url.'",
									dataType: "json",
									data: {
										term: request.term,
										l_pic2: getLPIC2()
									},
									success: function( data ) {
										response( data );
									}
								});
							},
							select: function(event, ui){
								//$("#master_problem_transaksi_problem_text").val(ui.item.value);
								//$("#master_problem_transaksi_problem_pic").val(ui.item.id);
								
								$("#l_pic2").append("<div id=\'div_"+ui.item.id+"\'><input class=\'pic\' type=\'hidden\' id=\'nip_"+ui.item.id+"\' name=\'nip[]\' value=\'"+ui.item.id+"\'/>"+ui.item.value+" [<span onclick=\'remove_element(\""+ui.item.id+"\");\' style=\'cursor:pointer;color:red;\'> x </span>]</div>");								
								$(this).val(""); return false;
							},
							minLength: 2,
							autoFocus: true,
						};
	
						$("#transaksi_support_request_pic_text" ).livequery(function() {	
						 	$( this ).autocomplete(config);
						});
						
						function getLPIC2() {
							var l_pic2 = [];
							$( ".nip" ).each(function() {								
								l_pic2.push($(this).val());
							});
							
							return l_pic2;
						}
					});
					
					function remove_element(id) {
						$("#div_"+id).remove();
				    }
		      </script>
			 ';*/
		
		
		$sql = "SELECT a.cNip, a.vName AS nama FROM hrd.employee a where a.cNip = '".$value."'";
		$query = $this->dbset->query($sql);
		if ($query->num_rows() > 0) {
			$row = $query->row();
				
			$nip 	  = $row->cNip;
			$nama 	  = $row->nama.' - '.$row->cNip;
		} else {
			$nip 	  = '';
			$nama 	  = '';
		}
	
		if ($this->input->get('action') != 'view') {
			$o .= '<input name="'.$id.'" id="'.$id.'" type="hidden"/>
			<input name="'.$id.'_text" id="'.$id.'_text" type="text" size="50"/>';
			
		} else {
			$o = $nama;
		} 
		
		$o .= "<div id='l_pic2'>";
		
		$sql = "SELECT pic from ss.raw_problems where pic = '".$value."'";
		$query = $this->dbset->query($sql);
		$company='';
		if ($query->num_rows() > 0) {
			foreach($query->result() as $r) {
				$company = $r->pic;
			}
		}
		
		$nama_company = '';
		$company = explode(',', $company);

		foreach($company as $v) {
			if($v!=""){
			$nama_pic = $this->getEmployeeName($v);
			$o.="<div id='div_".$v."'>
			
					<input class='nip' type='hidden' id='nip_".$v."' name='nip[]' value='".$v."'/>".$nama_pic." - ".$v." 
					[<span onclick='remove_element(\"".$v."\");' style='cursor:pointer;color:red;'> x </span>]</div>";
			}
		}
		
		$o .= "</div>";
	
		return $o;
	}
	/*PIC*/

	
	//-------------------------------------------------------------//
	
	
	function getPic2(){
		$term1 = $this->input->get('term');
		$data = array();
		$term3=$_POST['term'];
	
			$sql2 = "select cPic from ss.default_pic_detail where iProblemCatID ='".$term3."'";
			$query2 = $this->dbset->query($sql2);
			$company='';
			if ($query2->num_rows() > 0) {
				foreach($query2->result() as $r) {
					
					$row_array['value'] =$this->getEmployeeName($r->cPic).' - '.$r->cPic;
					$row_array['id']    = $r->cPic;
					array_push($data, $row_array);
				
				}			
			}
		return json_encode($data);
		exit;
		
	}
	function getPic(){
		$term1 = $this->input->get('term');		
		$term2 = $this->input->get('term1');
		$data = array();
		$term3=$_POST['term'];
		$term4=$_POST['term1'];
		
		
			$pic_exists = "";
			$sql3 = "select assign from ss.support_type where typeId ='".$term3."'";
			$query3 = $this->dbset->query($sql3);
			
			$com='';
			if ($query3->num_rows() > 0) {
				foreach($query3->result() as $r) {
					$com = $r->assign;
					$row_array['value'] = $com;
					$row_array['id']    = $term3;
					array_push($data, $row_array);
				}
			}
			
		
			//untuk pic
			$sql2 = "select cPic from ss.default_pic_detail where iProblemCatID ='".$term4."'";
			$query2 = $this->dbset->query($sql2);
			$company='';
			if ($query2->num_rows() > 0) {
				foreach($query2->result() as $r) {
					
					$row_array['value'] =$this->getEmployeeName($r->cPic).' - '.$r->cPic;
					$row_array['id']    = $r->cPic;
					array_push($data, $row_array);
				
				}			
			}
		return json_encode($data);
		exit;
		
		
	}
	
	function getEmployee() {
		
		$term = $this->input->get('term');		
		$data = array();
		$pic_exists = "";
		foreach($_GET as $key=>$val) {
			if ($key == "l_pic") {
				foreach ($val as $k=>$v) {					
					$pic_exists .= "'".$v."',";
				}
			}
		}
		$pic_exists = substr($pic_exists, 0, strlen($pic_exists)-1);		
		if (strlen($pic_exists) == 0) $qq = "";
		else $qq = " AND a.cNip NOT IN ({$pic_exists})";
		
		$sql = "SELECT a.cNip, a.vName as nama from hrd.employee a where a.cNip like '%".$term."%' or a.vName like '%".$term."%' 
				AND a.iDivisionID = 6 and (a.dResign > date_format(now(), '%Y-%m-%d') or a.dResign = '0000-00-00') 
				".$qq."
				ORDER BY vName ASC";
		$query = $this->dbset->query($sql);
		if ($query->num_rows > 0) {
			foreach($query->result_array() as $line) {
	
				$row_array['value'] = trim($line['nama']).' - '.$line['cNip'];
				$row_array['id']    = $line['cNip'];
	
				array_push($data, $row_array);
			}
		}
	
		echo json_encode($data);
		exit;
	}
	
	function getEmail($id) {
		$sql = "Select vEmail from hrd.employee where cNip = '{$id}'";
		$query = $this->dbset->query($sql);
		$nm_comp = '';
		if ($query->num_rows() > 0) {
			$r = $query->row();
			$nm_comp = $r->vEmail;
		}
		return $nm_comp;
	}
	
	function getCompanyName($id) {
		$sql = "Select vCompName from hrd.company where iCompanyId = '{$id}'";
		$query = $this->dbset->query($sql);
		$nm_comp = '';
		if ($query->num_rows() > 0) {
			$r = $query->row();
			$nm_comp = $r->vCompName;
		}
		return $nm_comp;
	}
	
	function getEmployeeName($id) {
		$sql = "Select vName from hrd.employee where cNip = '{$id}'";
		$query = $this->dbset->query($sql);
		$nm_comp = '';
		if ($query->num_rows() > 0) {
			$r = $query->row();
			$nm_comp = $r->vName;
		}
		
		return $nm_comp;
	}
	function before_update_processor($row, $postData) {
		
		$user = $this->auth->user();
		//print_r($postData); exit;
		//unset($postData['admin']);
		$postData['cUpdatedBy'] = $user->gNIP;
		$postData['tUpdated'] = date('Y-m-d H:i:s');
		return $postData;
	}
	
	public function before_insert_processor($value, $post) {
		$cNip = $this->user->gNIP;
		
		
		$this->load->helper('to_mysql');
		$post['deadline'] = to_mysql($post['deadline']);
		$post['tUpdate'] = date('Y-m-d H:i:s', time());
		$post['cUpdate'] = $this->user->gNIP;
		
		return $post;
	}	
    public function after_insert_processor($fields, $id, $postData) {
	
		$cNip = $this->user->gNIP;
		
		$today = date('Y-m-d H:i:s', mktime());
		
		$ass_pic = array();
		$ass_act = array();
		$ass_nip = array();
		foreach($_POST as $k=>$v) {
			if ($k == 'nip1') {
				$ass_pic[] = $v; 
			}
			
			if ($k == 'actif') {
				$ass_act[] = $v; 
			}
			
			if($k == 'nip'){
				$ass_nip[]=$v;
			}
			
		}
		
		
		$tampung ="";
		$tampungNama='';
		foreach($ass_pic as $value) {
			foreach($value as $k=>$v) {
				if($v!=''){
					$sql_nip[] = "INSERT INTO ss.raw_pics (id,activity_id, cPIC,cAssignedBy) 
								values ('".$id."','".$ass_act[0][$k]."', '".$v."','".$cNip."')";
					$sql_pro[]=	"Insert into ss.`task_predecessor` (id,activity_id,iPredecessor, cUpdatedBy)
								values ('".$id."','".$ass_act[0][$k]."','0','".$v."')";			
					$tampung =$tampung."".$this->getEmail($v).";";
					
				}else{
					$sql_nip[] = "";
					$sql_pro[] = "";
				
				}
			}
		}
		$tampung_nip ='';
		
			
			foreach($ass_nip as $value) {
				foreach($value as $k=>$v) {
					$tampung_nip = $tampung_nip."".$v.",";
					$tampungNama = $tampungNama."".$this->getEmployeeName($v).",";
				}
			}
		
		
			$sql2 = "DELETE FROM ss.raw_pics where id = '{$id}'";
			$this->dbset->query($sql2);
			if(!empty($sql_nip)){
				foreach($sql_nip as $v) {
					try {
						if($v!=''){
							$this->dbset->query($v);
						}
					}catch(Exception $e) {
						die('Error');
					}
				}
			}
			if(!empty($sql_pro)){
				foreach($sql_pro as $v) {
					try {
						if($v!=''){
							$this->dbset->query($v);
						}
					}catch(Exception $e) {
						die('Error');
					}
				}
			}
		
		//print_r($_POST);
		//$today = date('Y-m-d H:i:s', mktime());
		//$jam = date ('H:i:s', mktime());
		//$cNip = $this->user->gNIP;
		//print_r($ass_pic);
		//print_r($ass_act);
		//$sql_pic = array();
		//print_r($_POST);
		/*foreach($ass_pic as $value) {
			foreach($value as $k=>$v) {
				$sql_pic[] = "INSERT INTO ss.raw_pics (id,activity_id, cPIC, tAssigned,cAssignedBy) 
							values ('".$id."','".$ass_act[0][$k]."', '".$v."', '".$today."', '".$cNip."')";
			//	$sql_pro[] ="INSERT INTO ss.problem_act_predecessor(id,activity_id,iPredecessor,cUpdatedBy)
			//				value ('".$id."','".$ass_act[0][$k]."', '(Null)', '".$cNip."')";		
			//	$sql_row[] ="INSERT INTO ss.raw_problems(SSID,requestor, problem_subject,problem_description,pic,typeId,support_type,activity_id,companyId_support,LocationId,Priority,deadline,crJustify,crImpact,date_posted,posted_by,assignTime, last_update_by,approveNip,approveDate)
			//				values('".$id."','".$_POST['requestor']."','".$_POST['transaksi_support_request_problem_subject']."','".$_POST['transaksi_support_request_problem_description']."','".$v."','".$_POST['typeId']."','".$_POST['support_type']."','".$ass_act[0][$k]."','".$_POST['CompanyId_support']."','".$_POST['V_LOCATION_NAME']."','".$_POST['Priority']."','".$_POST['deadline']."','".$_POST['transaksi_support_request_crJustify']."','".$_POST['transaksi_support_request_crImpact']."','".$today."','".$_POST['requestor']."','".$jam."','".$cNip."','".$_POST['transaksi_support_request_Approval']."','".$today."')";
				//$sql_up[] = "UPDATE ss.raw_problems SET typeId='".$_POST['typeId']."', approveNip ='".$date1."', crJustify ='".$_POST['transaksi_support_request_crJustify']."',approveNip ='".$_POST['transaksi_support_request_Approval']."',crImpact ='".$_POST['transaksi_support_request_crImpact']."' where id='".$id."'";
			}
		}
		*/
		
		//print_r($sql_row);
		//exit;
		
		//$jam = date ('H:i:s', mktime());
		//$this->saveToSSPIC($post);
		
		$sql = "Update ss.raw_problems set  posted_by  ='{$cNip}', last_update_by = '{$cNip}' where id='".$id."'";
        $this->dbset->query($sql);
		$update = "update ss.raw_problems set pic ='".$tampung_nip."' where id='".$id."'";
		$this->dbset->query($update);
		
		
		//$user = $this->auth->user();
		/*if(isset($postData['nip'])){
			$nip = $postData['nip'];
			foreach($nip as $k => $v) {
				$this->db->insert('ss.raw_pics', array('id'=>$id,'cPIC'=>$v,'lDeleted'=>0,'tAssigned'=>date('Y-m-d H:i:s'),'cAssignedBy'=>$cNip));
			}
		}*/
		//print_r($postData['actif']);
		//return TRUE;
		
		//print_r($_POST);
		
		//$sql2 = "DELETE FROM ss.raw_pics where id = '{$id}'";
		//$this->dbset->query($sql2);
		
		$date1 = date('d M Y H:i:s');
		//$sql3 = "UPDATE ss.raw_problems SET typeId='".$_POST['typeId']."', approveNip ='".$date1."', crJustify ='".$_POST['transaksi_support_request_crJustify']."',approveNip ='".$_POST['transaksi_support_request_Approval']."',crImpact ='".$_POST['transaksi_support_request_crImpact']."' where id='".$id."'";
		//$this->dbset->query($sql3);
		/*foreach($sql_pic as $q) {
			try {
				$this->dbset->query($q);
			}catch(Exception $e) {
				die('Error');
			}
		}
		/*foreach($sql_row as $k) {
			try {
				$this->dbset->query($k);
			}catch(Exception $e) {
				die('Error');
			}
		}
		foreach($sql_pro as $v) {
			try {
				$this->dbset->query($v);
			}catch(Exception $e) {
				die('Error');
			}
		}
		*/
		/*foreach($ass_pic as $value) {
			foreach($value as $k=>$v) {
			//untuk to kirim email
			}	
		}*/
		if(isset($_POST['typeId']))
		{
			$typeid = $_POST['typeId'];
			//print_r($_POST['typeId']);
			$sql5 ="SELECT assign FROM ss.`support_type` s WHERE  s.`typeId`  ='{$typeid}'";
			$query5 = $this->dbset->query($sql5);
			$asiign ='';
			if ($query5->num_rows() > 0) {
					$row = $query5->row();
					$asiign = $row->assign;
				}
			$prio='';
			if($_POST['Priority']==1){
				$prio="Emergency";
			}elseif($_POST['Priority']==2){
				$prio="High";
			}elseif($_POST['Priority']==3){
				$prio="Medium";
			}elseif($_POST['Priority']==4){
				$prio="Low";
			}
			if($asiign=='N'){
				$to = "mahpudin@novellpharm.com";
				$cc = $tampung;
				$subject="New : ".$_POST['transaksi_support_request_problem_subject'];
				$a="<html>
							<body>
							<head>
							
							<style type='text/css'>
								.tabel { 
										background-color:#DEDEDE ;									
										border:1px solid #000000 ;
										font-family: Tahoma ; 
										font-size:12px ;
										font-color:#000000 ;
										}
								.tabel2 { 									
										font-family: Tahoma ; 
										font-size:12px ;
										font-color:#000000 ;
										}
								A:link, A:visited, A:active { text-decoration: underline }
							</style>
							</head>
							<body>
							<table border='0' class='tabel2'>
													
							 <tr><td colspan='3'>&nbsp;</td></tr>
							 <tr>
									<td>Subject</td><td>:</td><td>".$_POST['transaksi_support_request_problem_subject']."</td>
							 </tr>
							 <tr>
									<td>Statement</td><td>:</td><td>".$_POST['transaksi_support_request_problem_description']."</td>
							 </tr>	
							 <tr>
									<td>Person In Change</td><td>:</td><td>".$tampungNama."</td>
							 </tr>	
							 <tr>
									<td>Requestor</td><td>:</td><td>".$this->getEmployeeName($_POST['transaksi_support_request_requestor'])."</td>
							 </tr>
							<tr>
									<td>Deadline</td><td>:</td><td>".$_POST['deadline']."</td>
							 </tr>
							<tr>
									<td>Finish</td><td>:</td><td>00-00-0000 00:00:00</td>
							 </tr>
							 <tr>
									<td>Dikirim Oleh </td><td>:</td><td>".$this->getEmployeeName($cNip)."</td>
							 </tr>
											
							<tr><td colspan='3'>&nbsp;</td></tr>
							
							";
						$a .="</table>
					<br/> 
					Demikian, mohon segera follow up  pada aplikasi ERP SS. Terimakasih.<br><br><br>
					Post Master
					</body>
				</html>";
					$content=$a;
					$path="";
				$this->lib_utilitas->send_email($to, $cc, $subject, $content,$path);
			
			}
			
				
			
		}	
	
    }
	
	
    
    public function after_update_processor($fields, $id, $postData) {	
		$cNip = $this->user->gNIP;
		$today = date('Y-m-d H:i:s', mktime());
		$jam = date ('H:i:s', mktime());
		$sql = "Update ss.raw_problems set  last_update_by  ='{$cNip}',assignTime ='{$jam}' where id='".$id."'";
        $this->dbset->query($sql);
			//print_r($id);
		$ids = $rowdata['id'];
		$user = $this->auth->user();
		$nip = $postData['nip'];
		//$ac = $rowdata['activity_id'];
		$this->db->where($ids, $updateId);
		foreach($nip as $k => $v) {
			$this->db->insert('ss.raw_pics', array('id'=>$id,'cPIC'=>$v,'lDeleted'=>0,'tAssigned'=>date('Y-m-d H:i:s'),'cAssignedBy'=>$cNip));
		}
		return TRUE;
	
    }
	
	function saveToSSPIC($pot) {
		print_r("ini adalah ".$pot);
		//exit;
		$_company = '';
		$_pic     = '';
		$categoryId = $pot['id'];
		
		foreach($_POST as $key=>$value) {
			if ($key == '_company') {
				foreach($value as $k=>$v) {
					$_company .= $v."_";
				}
			}

			if ($key == 'nip') {
				foreach($value as $k=>$v) {
					$_pic .= $v.",";
				}
			}
		}
		$_company = substr($_company, 0, strlen($_company)-1);
		$_pic     = substr($_pic, 0, strlen($_pic)-1);
	
		//
		$sql = "SELECT * from ss.ss_pic where categoryId = '{$categoryId}'";
		$query = $this->dbset->query($sql);
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$picId = $row->id;
			$sql = "UPDATE ss.ss_pic set companyId = '{$_company}', pic = '{$_pic}' where id = '{$picId}'";
		} else {
			$sql = "INSERT INTO ss.ss_pic (categoryId, companyId, pic) 
					VALUES ('{$categoryId}', '{$_company}', '{$_pic}')";
		}		
		//echo $sql;
		//exit;
		
		$this->dbset->query($sql);
		
	}
	
	function manipulate_insert_button($buttons) {
		$sql ="SELECT id FROM ss.raw_problems a WHERE (a.`eApproveReject` ='0' OR a.`confirm_date` ='(Null)')
				AND a.`pic` ='N13986'";
		$query = $this->dbset->query($sql);
		$ids=0;
		$id='';
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$id = $row->id;
			$ids++;
    	}
		if($ids==0){
			unset($buttons['save']);
			$save_draft = '<button onclick="javascript:save_draft_btn(\'transaksi_support_request\', \''.base_url().'transaksi_support_request\', this)" class="ui-button-text icon-save" id="button_save_draft_soi_mikrobiologi">Save as Draft</button>';
			$save = '<button onclick="javascript:save_btn_multiupload(\'transaksi_support_request\', \''.base_url().'processor/ss/transaksi/support/request?company_id='.$this->input->get('company_id').'&group_id='.$this->input->get('group_id').'&modul_id='.$this->input->get('modul_id').'\', this)" class="ui-button-text icon-save" id="button_save_support_request">Save</button>';
			$js = $this->load->view('master_transaksi_problem_js');
			$buttons['save'] = $save.$js;
		}else{
			//unset($button['save']);
			$buttons['save'] = '<button onclick="javascript:save_draft_btn(\'transaksi_support_request\', \''.base_url().'processor/ss/transaksi/support/request?company_id='.$this->input->get('company_id').'&group_id='.$this->input->get('group_id').'&modul_id='.$this->input->get('modul_id').'\', this)" class="ui-button-text icon-save" id="button_save_setprio_reg">Save</button>';
			
		}
		
		
		//$buttons['save'] = $save_draft.$save.$js;
		return $buttons;
		
	}
	function insertCheck_transaksi_support_request_pic($value, $field, $rows) {
		$cNip = $this->user->gNIP;
		$sql ="SELECT id FROM ss.raw_problems WHERE requestor = '".$cNip."' 
				AND taskStatus = 'Finish' AND (confirm_date IS NULL AND rejectDate IS NULL) 
				AND DATEDIFF(CURRENT_TIMESTAMP, actual_finish) > 14 ";
		$query = $this->dbset->query($sql);
		$j=0;
		$id='';
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$id = $row->id;
			$j++;
    	}
		if($j > 0) {
			return 'belum bisa menginput permintaan support karena masih terdapat <'.$j.' di-confirm/reject> yang belum di respon';
		} 
		else {
			return TRUE;
		}
	}
	
	/*public function manipulate_grid_button($button) {
		$sql ="SELECT id FROM ss.raw_problems a WHERE (a.`eApproveReject` ='1' OR a.`confirm_date` ='(Null)')
				AND a.`pic` ='".$this->user->gNIP."'";
		$query = $this->dbset->query($sql);
		$ids=0;
		$id='';
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$id = $row->id;
			$ids++;
    	}
		if($ids==0){
			
		}else{
			unset($button['create']);
		}
		
		return $button;
    }
	*/
	
 
    public function manipulate_update_button($button) {
        if ($this->input->get('action') == 'view') {
                unset($button['update']);
        }
       $this->load->validator(1);
        return $button;
    }

    
	
    function hapusfile($path, $file_name, $table, $lastId){
		$path = $path."/".$lastId;
		//$path = $path."/".$lastId;
		$path = str_replace("\\", "/", $path);
		print_r($path);
		if (is_array($file_name)) {			
			$list_dir  = $this->readDirektory($path);
			$list_sql  = $this->readSQL($table, $lastId);
			asort($file_name);		
			asort($list_dir);		
			asort($list_sql);
			
			foreach($list_dir as $v) {				
				if (!in_array($v, $file_name)) {				
					//unlink($path.''.$v);	
				}			
			}
			foreach($list_sql as $v) {
				if (!in_array($v, $file_name)) {
					$del = "delete from ss.".$table." where id_ssid = {$lastId} and vFilename= '{$v}'";
					mysql_query($del);	
				}
				
			}
		} else {
			$this->readDirektory($path, 1);
			$this->readSQL($table, $lastId, 1);
		}
	} 
	
	function readSQL($table, $lastId, $empty="") {
		$list_file = array();
		if (empty($empty)) {
			$sql = "SELECT vFilename from ss.".$table." where id_ssid=".$lastId;
			$query = mysql_query($sql);
			while($row = mysql_fetch_array($query)) {	
				$list_file[] = $row['vFilename'];
			}
			
			$x = $list_file;
		} else {			
			$sql = "SELECT vFilename from ss.".$table." where id_ssid=".$lastId;
			$query = mysql_query($sql);
			$sql2 = array();
			while($row = mysql_fetch_array($query, MYSQL_ASSOC)) {
				$sql2[] = "DELETE FROM ss.".$table." where id_ssid=".$lastId." and vFilename='".$row['vFilename']."'";			
			}
			
			foreach($sql2 as $q) {
				try {
					mysql_query($q);
				}catch(Exception $e) {
					die($e);
				}
			}
			
		  $x = "";
		}
		
		return $x;
	}
	function readDirektory($path, $empty="") {
		$filename = array();
				
		if (empty($empty)) {
			if ($handle = opendir($path)) {		
				while (false !== ($entry = readdir($handle))) {
				   if ($entry != '.' && $entry != '..' && $entry != '.svn') {			   		
						$filename[] = $entry;
					}
				}		
				closedir($handle);
			}
				
			$x =  $filename;
		} else {
			if ($handle = opendir($path)) {		
				while (false !== ($entry = readdir($handle))) {
				   if ($entry != '.' && $entry != '..' && $entry != '.svn') {			   		
						unlink($path."/".$entry);					
					}
				}		
				closedir($handle);
			}
			
			$x = "";
		}
		
		return $x;
	}
	function pic() {
		$term = $this->input->get('term');
		$data = array();
		$sql = "SELECT a.cNip, a.vName as nama from hrd.employee a where a.cNip like '%".$term."%' or a.vName like '%".$term."%' 
				AND a.iDivisionID = 6 and (a.dResign > date_format(now(), '%Y-%m-%d') or a.dResign = '0000-00-00') ORDER BY vName ASC";
		$query = $this->dbset->query($sql);
		if ($query->num_rows > 0) {
			foreach($query->result_array() as $line) {
	
				$row_array['value'] = trim($line['nama']).' - '.$line['cNip'];
				$row_array['id']    = $line['cNip'];
	
				array_push($data, $row_array);
			}
		}
		echo json_encode($data);exit();
	}

    public function output(){
            $this->index($this->input->get('action'));
    }
}