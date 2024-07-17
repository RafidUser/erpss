<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class default_pic_module extends MX_Controller {
	private $sess_auth;
	private $dbset;
        private $dbset2;
	private $url;
        var $idprivi_apps;        
			
    function __construct() {
        parent::__construct();
        $this->sess_auth = new Zend_Session_Namespace('auth');
		$this->load->library('auth');
		$this->load->library('lib_utilitas');
		$this->user = $this->auth->user();
        $this->url = 'default_pic_module'; 
        $this->dbset = $this->load->database('hrd', true);        
    }
    function index($action = '') {		
          
            $grid = new Grid;	
            $grid->setTitle('default PIC');		
            $grid->setTable('ss.default_pic');		
            $grid->setUrl('default_pic_module');	
            $grid->addList('iProblemTypeID','Activity','cPIC');
            $grid->addFields('iProblemTypeID','Activity','cPIC');
            $grid->setSearch('iProblemTypeID');
			$grid->setLabel('iProblemTypeID','Problem Type');
			$grid->setLabel('Activity','Activity');
			$grid->setLabel('cPIC','Person in Change');
			$grid->setWidth('iProblemTypeID','300');
			$grid->setWidth('activity','300');
			$grid->setWidth('cPIC','285');
           
			$this->idProb = $this->input->get('idProb');            
            $grid->setInputGet('_idProb', $this->idProb);            
	    	$grid->setQuery('ss.default_pic.iProblemCatID', intval($this->input->get('_idProb')));
			$grid->setQuery('ss.default_pic.ldeleted', 0);
            
			
            $grid->setForeignKey($this->input->get('idProb'));
   
            switch ($action) {
                    case 'json':
                            $grid->getJsonData();
                            break;
					case 'searchActiv' :
							$this->searchActiv();
							break;
                    case 'view':
                            $grid->render_form($this->input->get('id'), true);
                            break;
                    case 'create':
                            $grid->render_form();
                            break;
                    case 'createproses':
                            echo $grid->saved_form();
                            break;
					case 'getemployee':
							echo $this->getEmployee();
							break;
					case 'npl':
							echo $this->npl();
							break;
                    case 'update':
                            $grid->render_form($this->input->get('id'));
                            break;
                    case 'updateproses':
                            echo $grid->updated_form();
                            break;
                    case 'delete':
                            echo $grid->delete_row();
                            break;
                    default:
                            $grid->render_grid();
                            break;
            }
        }               
   
	public function insertBox_default_pic_module_Activity($field, $id) {
	
			$o='';
            $o  .= "<select class='required' name='".$field."' id='".$id."'>";
            $o .= "<option value='(Null)'>Pilih</option>";
            $sql = "SELECT a.`activity_id`, a.`activity` FROM ss.`activity_type` a ORDER BY a.`activity_id` asc";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $result = $query->result_array();
                foreach($result as $row) {
                      if ($id == $row['activity_id']) $selected = " selected";
                       else $selected = '';
                       $o .= "<option value='".$row['activity_id']."'>".$row['activity']."</option>";
                }
            }
		

            $o .= "</select>";
			
			return $o;
			
	}
	public function updateBox_default_pic_module_Activity($field, $id, $value, $rowData) {
		$sql = "SELECT DISTINCT(a.`iActivityID`) FROM ss.`default_pic_detail` a INNER JOIN ss.`default_pic` b WHERE a.`iDefaultPicID` ='".$rowData['id']."'";
		$query = $this->dbset->query($sql);
		$ids ='';
		if ($query->num_rows() > 0) {
			$result = $query->result_array();
			foreach($result as $row) {
				   $ids =$row['iActivityID'];
			}
		}
		$o  = "<select name='".$field."' id='".$id."'>";
		$o .= "<option value='0'>Pilih</option>";
		$sql = "Select activity_id as activity_id, activity as activity 
				from ss.activity_type order by activity_id";
		$query = $this->dbset->query($sql);
		if ($query->num_rows() > 0) {
			$result = $query->result_array();
			foreach($result as $row) {
				   if ($ids== $row['activity_id']) $selected = " selected"; 
				   else $selected = '';
				   $o .= "<option {$selected} value='".$row['activity_id']."'>".$row['activity']."</option>";
				  
			}
		}
		   $o .= "</select>";
	
	
		
		return $o;
	}
	function searchActiv() {
	
		$tipe = $this->input->get('_param');
                $iDivId = $this->input->get('_tipe');
		$data = array();
	
                if ($iDivId == 0) {
                    $sql = "Select activity, activity_id from ss.activity_type";
                } else {
                    $sql = "SELECT a.activity_id AS activity_id, b.activity FROM ss.problem_act a inner join ss.activity_type b on a.activity_id = b.activity_id  where typeId ='".$tipe."' order by activity";
                }
                
		
		$query = $this->dbset->query($sql);
		if ($query->num_rows > 0) {
			foreach($query->result_array() as $line) {
	
				$row_array['value'] = trim($line['activity']);
				$row_array['id']    = $line['activity_id'];
	
				array_push($data, $row_array);
			}
		}
	
		echo json_encode($data);
		exit;
	}
	public function updateBox_default_pic_module_iProblemTypeID($field, $id, $value,$rowData) {

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
		
			 $url = base_url().'processor/ss/default/pic/module?action=searchActiv&company_id='.$this->input->get('company_id').'&modul_id=24&group_id=2669';
		
		$o .= "<script type='text/javascript'>
					$(document).ready(function() {                            
							$('#default_pic_module_iProblemTypeID').change(function() {
									loadData('default_pic_module_Activity', $('#default_pic_module_iProblemTypeID option:selected').val(), $(this).val());
							
							});
					});

					function loadData(control, param, tipe) {
							$.get('".$url."', 
							{_control:control, _param:param, _tipe:tipe},  function(data) {
								//alert(data);
									if (data.error == undefined) {
											$('#'+control).empty();
											$('#'+control).append('<option value=\'0\'>[Pilih]</option>');
											for (var x=0;x<data.length;x++) {						
													$('#'+control).append($('<option></option>').val(data[x].id).text(data[x].value));	
											}
									} else {
											alert('Data Error');
											return false;
									}

							},'json');
					}
			  </script>";
			
	
	return $o;
	}
	public function insertBox_default_pic_module_iProblemTypeID($field, $id) {
	if ($this->input->get('action') == 'view') {
            $sql = "Select typeId as typeId, typeName as typeName 
                    from ss.support_type where typeId = '{$id}'";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $row = $query->row();
                $o .= $row->typeName;
            }
        } else {
			$o='';
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
			$url = base_url().'processor/ss/default/pic/module?action=searchActiv&company_id='.$this->input->get('company_id').'&modul_id=24&group_id=2669';
        
        $o .= "<script type='text/javascript'>
                    $(document).ready(function() {                            
                            $('#default_pic_module_iProblemTypeID').change(function() {
                                    loadData('default_pic_module_Activity', $('#default_pic_module_iProblemTypeID option:selected').val(), $(this).val());
							
							});
                    });

                    function loadData(control, param, tipe) {
                            $.get('".$url."', 
                            {_control:control, _param:param, _tipe:tipe},  function(data) {
								//alert(data);
                                    if (data.error == undefined) {
                                            $('#'+control).empty();
                                            $('#'+control).append('<option value=\'0\'>[Pilih]</option>');
                                            for (var x=0;x<data.length;x++) {						
                                                    $('#'+control).append($('<option></option>').val(data[x].id).text(data[x].value));	
                                            }
                                    } else {
                                            alert('Data Error');
                                            return false;
                                    }

                            },'json');
                    }
              </script>";
			return $o;
			
	}
	
	 public function insertBox_default_pic_module_cPIC($field, $id) {
		$url = base_url().'processor/ss/default/pic/module?action=getemployee';
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
										l_pic5: getLPIC5()
									},
									success: function( data ) {
										response( data );
									}
								});
							},
							select: function(event, ui){								
								$("#l_pic5").append("<div id=\'div_"+ui.item.id+"\'><input type=\'hidden\' class=\'nip\' id=\'nip_"+ui.item.id+"\' name=\'nip[]\' value=\'"+ui.item.id+"\'/>"+ui.item.value+" [<span onclick=\'remove_element(\""+ui.item.id+"\");\' style=\'cursor:pointer;color:red;\'> x </span>]</div>");
								
								$(this).val(""); return false;
							},
							minLength: 2,
							autoFocus: true,
						};
	
						$( "#default_pic_module_cPIC_text" ).livequery(function() {
						 	$( this ).autocomplete(config);
						});
	
					});
					function remove_element(id) {
						$("#div_"+id).remove();
				    }
					function getLPIC5() {
						var l_pic5 = [];
						$( ".nip" ).each(function() {								
							l_pic5.push($(this).val());
						});
						
						return l_pic5;
					}
		      </script>
			  
			  <input name="'.$id.'" id="'.$id.'" type="hidden"/>
			  <input name="'.$id.'_text" id="'.$id.'_text" type="text" size="50"/>';
		
		$o .= "<div id='l_pic5'>";
		
	
		$o .="</div>";
		$o .= '<input type="hidden" value="'.$this->input->get('foreign_key').'" id="default_pic_module_iProblemCatID" name="default_pic_module_iProblemCatID">';
	
		return $o;
	}
      public function updateBox_default_pic_module_cPIC($field, $id, $value, $rowData) {
		$url = base_url().'processor/ss/group/pic/module?action=getemployee';
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
										l_pic5: getLPIC5()
									},
									success: function( data ) {
										response( data );
									}
								});
							},
							select: function(event, ui){								
								$("#l_pic5").append("<div id=\'div_"+ui.item.id+"\'><input type=\'hidden\' class=\'nip\' id=\'nip_"+ui.item.id+"\' name=\'nip[]\' value=\'"+ui.item.id+"\'/>"+ui.item.value+" [<span onclick=\'remove_element(\""+ui.item.id+"\");\' style=\'cursor:pointer;color:red;\'> x </span>]</div>");
								
								$(this).val(""); return false;
							},
							minLength: 2,
							autoFocus: true,
						};
	
						$( "#group_pic_module_cPIC_text" ).livequery(function() {
						 	$( this ).autocomplete(config);
						});
	
					});
					function remove_element(id) {
						$("#div_"+id).remove();
				    }
					function getLPIC5() {
						var l_pic5 = [];
						$( ".nip" ).each(function() {								
							l_pic5.push($(this).val());
						});
						
						return l_pic5;
					}
		      </script>
			  
			<input name="'.$id.'" id="'.$id.'" type="hidden"/>
			<input name="'.$id.'_text" id="'.$id.'_text" type="text" size="50"/>
			 ';
		
		
		$o .= "<div id='l_pic5'>";
		
		$sql = "SELECT cPic from ss.default_pic_detail where iDefaultPicID = '".$rowData['id']."'";
		$query = $this->dbset->query($sql);
		$i=0;
		$vName = '';
			if ($query->num_rows > 0) {
				foreach($query->result_array() as $value) {
					foreach($value as $k=>$v) {
					$nama_pic = $this->getEmployeeName($v);
					$o.="<div id='div_".$v."'>
					
							<input class='nip' type='hidden' id='nip_".$v."' name='nip[]' value='".$v."'/>".$nama_pic." - ".$v." 
							[<span onclick='remove_element(\"".$v."\");' style='cursor:pointer;color:red;'> x </span>]</div>";
					
					}
				}
			}
			
		
		$o .= "</div>";
	
		return $o;
	} 
	
	public function before_insert_processor($value, $post) {
	
		$post['tUpdate'] = date('Y-m-d H:i:s', time());
		$post['cUpdate'] = $this->user->gNIP;
		return $post;
	}	
	 
	 function npl() {
		$term = $this->input->get('term');
		$data = array();
		$location_exists = "";
		foreach($_GET as $key=>$val) {
			if ($key == "l_location") {
				foreach ($val as $k=>$v) {					
					$location_exists .= "'".$v."',";
				}
			}
		}
		$location_exists = substr($location_exists, 0, strlen($location_exists)-1);		
		if (strlen($location_exists) == 0) $qq = "";
		else $qq = " AND a.I_LOCATION_ID NOT IN ({$location_exists})";
		
		$sql = "SELECT a.I_LOCATION_ID, a.V_LOCATION_NAME  from hrd.worklocation a where a.V_LOCATION_NAME like '%".$term."%'  
			".$qq." ORDER BY I_LOCATION_ID ASC";
		$query = $this->dbset->query($sql);
		if ($query->num_rows > 0) {
			foreach($query->result_array() as $line) {

				$row_array['value'] = trim($line['V_LOCATION_NAME']);
				$row_array['id']    = $line['I_LOCATION_ID'];

				array_push($data, $row_array);
			}
		}

		echo json_encode($data);
		exit;
	}
	 function getEmployee() {
		
		$term = $this->input->get('term');		
		$data = array();
		$pic_exists = "";
		foreach($_GET as $key=>$val) {
			if ($key == "l_pic5") {
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
     
       
	public function manipulate_grid_button($button) {    	
    	unset($button['create']);
    	$url = base_url()."processor/ss/default/pic/module?action=create&foreign_key=".$this->input->get('idProb')."&idProb=".$this->input->get('id')."&company_id=".$this->input->get('company_id')."&group_id=".$this->input->get('group_id')."&modul_id=".$this->input->get('modul_id');
    	$btn_baru  = "<script type='text/javascript'>
		    	function add_btn_$this->url(url, title) {
		    		browse_with_no_close(url, title);
		    	}    	 
    		</script>
    	";
    	$btn_baru .= '<span class="icon-add ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" onclick="add_btn_'.$this->url.'(\''.$url.'\', \'DEFAULT GROUP\')">Add New</span>';
    	
    	array_unshift($button, $btn_baru);
   
    	return $button;
    	
    }
	
	public function listBox_Action($row, $actions) {
		//print_r($row);
    	unset($actions['view']);
    	unset($actions['edit']);	
    	$url = base_url()."processor/ss/default/pic/module?action=update&iProblemCatID=".$row->iProblemCatID."&foreign_key=".$row->iProblemCatID."&id=".$row->id;
    	$edit  = "<script type'text/javascript'>
    							function edit_btn_".$this->url."(url, title) {
    								browse_with_no_close(url, title);
    							}
    						</script>";
    	$edit .= "<a href='#' onclick='javascript:edit_btn_".$this->url."(\"".$url."\", \"SETUP GROUPS\");'><center><span class='ui-icon ui-icon-pencil'></span></center></a>";
    	$actions['edit'] = $edit;
    	 
    	return $actions;
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
	 function getActivityName($id) {
		$sql = "Select activity from ss.activity_type where activity_id = '{$id}'";
		$query = $this->dbset->query($sql);
		$ac = '';
		if ($query->num_rows() > 0) {
			$r = $query->row();
			$ac = $r->activity;
		}
		
		return $ac;
	}
	function listBox_default_pic_module_iProblemTypeID($value, $pk, $name, $rowData) {
		//print_r($rowData);
		$ids =$rowData->iProblemTypeID;
		
		$sql = "Select typeId as typeId, typeName as typeName 
                    from ss.support_type where typeId = '{$ids}'";
		$query = $this->dbset->query($sql);
		$ac = '';
		if ($query->num_rows() > 0) {
			$r = $query->row();
			$ac = $r->typeName;
		}
		
		return $ac;
	
		
	}
	function listBox_default_pic_module_Activity($value, $pk, $name, $rowData) {
		
			$ids =$rowData->id;
			$sql = "SELECT distinct(iActivityID) FROM ss.`default_pic_detail` WHERE iDefaultPicID='{$ids}'";
			$query = $this->dbset->query($sql);
			$i=0;
			$vName = '';
			if ($query->num_rows > 0) {
				foreach($query->result_array() as $v) {
					if ($i%2) 
						$vName .= "<b>".$this->getActivityName($v['iActivityID'])."</b><br/>";
					else $vName .= $this->getActivityName($v['iActivityID'])."<br/>";
					$i++;
				}
			}
			return $vName;
		
	
		
	}
	function listBox_default_pic_module_cPIC($value, $pk, $name, $rowData) {
		
			$ids =$rowData->id;
			$sql = "SELECT cPic FROM ss.`default_pic_detail` WHERE iDefaultPicID='{$ids}'";
			$query = $this->dbset->query($sql);
			$i=0;
			$vName = '';
			if ($query->num_rows > 0) {
				foreach($query->result_array() as $v) {
					if ($i%2) 
						$vName .= "<b>".$this->getEmployeeName($v['cPic'])."</b><br/>";
					else $vName .= $this->getEmployeeName($v['cPic'])."<br/>";
					
					$i++;
				}
			}
			return $vName;
		
	
		
	}
	public function after_insert_processor($fields, $id, $post) {
			$ass_nip = array();
			foreach($_POST as $k=>$v) {
				if ($k == 'nip') {
					$ass_nip[] = $v; 
				}
				
			}
			foreach($ass_nip as $value) {
				foreach($value as $k=>$v) {
					$sql_nip[] = "INSERT INTO ss.default_pic_detail(iDefaultPicID,iActivityID, cPic) 
								values ('".$id."', '".$post['Activity']."','".$v."')";
				}
			}
			foreach($sql_nip as $v) {
				try {
					$this->dbset->query($v);
				}catch(Exception $e) {
					die('Error');
				}
			}
		
	}
  
	public function after_update_processor($fields, $id, $post) {
		$sql = "delete from ss.default_pic_detail where iDefaultPicID ='".$id."' ";
		$this->dbset->query($sql);
		$ass_nip = array();
		foreach($_POST as $k=>$v) {
			if ($k == 'nip') {
				$ass_nip[] = $v; 
			}
			
		}
		foreach($ass_nip as $value) {
			foreach($value as $k=>$v) {
				$sql_nip[] = "INSERT INTO ss.default_pic_detail(iDefaultPicID,iActivityID, cPic) 
							values ('".$id."', '".$post['Activity']."','".$v."')";
			}
		}
		foreach($sql_nip as $v) {
			try {
				$this->dbset->query($v);
			}catch(Exception $e) {
				die('Error');
			}
		}
	}
		
        
        public function manipulate_insert_button($button) {
            unset($button['save']);
            unset($button['save_back']);
            unset($button['cancel']);
            $button['save_back']  =  "<script type='text/javascript'>
                                            function create_btn_back_".$this->url."(grid, url, dis) {		
                                                    //var idprivi_apps = $('#group_pic_module_idprivi_apps').val();
                                                    //url += '&idprivi_apps='+idprivi_apps;
                                                   // alert($('#group_pic_module_iProblemCatID').val())
                                                    var req = $('#form_create_'+grid+' input.required, #form_create_'+grid+' select.required, #form_create_'+grid+' textarea.required');
                                                    var conf=0;
                                                    var alert_message = '';
                                                    var tot_err = 0;
                                                    var adaDiStockOpname = 0;
                                                    var statusStockOpname = 0;
                                                    $.each(req, function(i,v){
                                                            $(this).removeClass('error_text');
                                                            if($(this).val() == '') {
                                                                    var id = $(this).attr('id');
                                                                    var label = $(\"label[for=\''+id+\'']\").text();
                                                                    label = label.replace('*','');
                                                                    alert_message += '<br /><b>'+label+'</b> '+required_message;			
                                                                    $(this).addClass('error_text');			
                                                                    conf++;
                                                            }		
                                                    })
                                                    if(conf > 0) {
                                                            //$('html, body').animate({scrollTop:$('#'+grid).offset().top - 20}, 'slow');
                                                            _custom_alert(alert_message,'Error!','info',grid, 1, 5000);
                                                    }
                                                    else {
													custom_confirm(comfirm_message,function(){
                                                                    $.ajax({
                                                                            url: $('#form_create_'+grid).attr('action'),
                                                                            type: 'post',
                                                                            data: $('#form_create_'+grid).serialize(),
                                                                            success: function(data) {
                                                                                    var o = $.parseJSON(data);
                                                                                    var info = 'Error';
                                                                                    var header = 'Error';
                                                                                    var last_id = o.last_id;
                                                                                    var foreign_id = $('#default_pic_module_iProblemCatID').val();
																					//alert(foreign_id);
																					
                                                                                    
                                                                                    if(o.status == true) {
                                                                                            //alert(foreign_id);
                                                                                            /*$.get(url+'&action=update&id='+last_id+'&foreign_key='+foreign_id, function(data) {
                                                                                                    $('div#grid_wraper_'+grid).html(data);
                                                                                                    $('html, body').animate({scrollTop:$('#'+grid).offset().top - 20}, 'slow');
                                                                                            });
                                                                                            $.get(url, function(data){
                                                                                                    $('div#grid_wraper_'+grid).html(data);
                                                                                                    $('html, body').animate({scrollTop:$('#'+grid).offset().top - 20}, 'slow');
                                                                                            })*/
																							$('#grid_'+grid).trigger('reloadGrid');
																							//alert(o.message);
                                            												$.get(url+'&action=update&id='+last_id+'&foreign_key='+foreign_id+'&idProb='+foreign_id, function(data) {
																									//$('div#form_'+grid).html(data);
																									$('#alert_dialog_form').html(data);
																									//$('html, body').animate({scrollTop:$('#'+grid).offset().top - 20}, 'slow');
																							});
                                                                                            info = 'info';
                                                                                            header = 'Info';
                                                                                    }
                                                                                    _custom_alert(o.message,header,info, grid, 1, 20000);
                                                                            }
                                                                    })
                                                            });
                                                    }
                                            }
                                      </script>";
	$button['save_back'] .= "<button type='button'
							name='button_create_".$this->url."'
							id='button_create_".$this->url."'
							class='icon-save ui-button'
							onclick='javascript:create_btn_back_".$this->url."(\"".$this->url."\", \"".base_url()."processor/ss/default/pic/module?idProb=".$this->input->get('foreign_key')."&company_id=".$this->input->get('company_id')."&group_id=".$this->input->get('group_id')."&modul_id=".$this->input->get('modul_id')."\", this)'>Save
							</button>";
                $button['cancel']  =  "<script type='text/javascript'>
										function cancel_btn_".$this->url."(grid, url, dis) {	                                                
											/*$.get(url, function(data){
												$('div#grid_wraper_'+grid).html(data);
												$('html, body').animate({scrollTop:$('#'+grid).offset().top - 20}, 'slow');
											 })
											 info = 'info';
											 header = 'Info';*/
											 //_custom_alert(o.message,header,info, grid, 1, 20000);
                                                                                         $('#alert_dialog_form').dialog('close');
										}
								  </script>";
			$button['cancel'] .= "<button type='button'
								name='button_cancel_".$this->url."'
								id='button_cancel_".$this->url."'
								class='icon-save ui-button'
								onclick='javascript:cancel_btn_".$this->url."(\"".$this->url."\", \"".base_url()."processor/ss/default/pic/module?ids=".$this->input->get('foreign_key')."&company_id=".$this->input->get('company_id')."&group_id=".$this->input->get('group_id')."&modul_id=".$this->input->get('modul_id')."\", this)'>Close 
								</button>";
                
        
            
            return $button;
        }
        
        public function manipulate_update_button($button) {
            unset($button['update']);
            unset($button['update_back']);
            unset($button['cancel']);
            
            $button['update_back']  =  "<script type='text/javascript'>
                                            function update_btn_back_".$this->url."(grid, url, dis) {		
                                                    //var idprivi_apps = $('#group_pic_module_idprivi_apps').val();
                                                    //url += '&idprivi_apps='+idprivi_apps;
                                                    
                                                    var req = $('#form_update_'+grid+' input.required, #form_update_'+grid+' select.required, #form_update_'+grid+' textarea.required');
                                                    var conf=0;
                                                    var alert_message = '';
                                                    var tot_err = 0;
                                                    var adaDiStockOpname = 0;
                                                    var statusStockOpname = 0;
                                                    $.each(req, function(i,v){
                                                            $(this).removeClass('error_text');
                                                            if($(this).val() == '') {
                                                                    var id = $(this).attr('id');
                                                                    var label = $(\"label[for=\''+id+\'']\").text();
                                                                    label = label.replace('*','');
                                                                    alert_message += '<br /><b>'+label+'</b> '+required_message;			
                                                                    $(this).addClass('error_text');			
                                                                    conf++;
                                                            }		
                                                    })
                                                    if(conf > 0) {
                                                            //$('html, body').animate({scrollTop:$('#'+grid).offset().top - 20}, 'slow');
                                                            _custom_alert(alert_message,'Error!','info',grid, 1, 5000);
                                                    }
                                                    else {
                                                            custom_confirm(comfirm_message,function(){
                                                                    $.ajax({
                                                                            url: $('#form_update_'+grid).attr('action'),
                                                                            type: 'post',
                                                                            data: $('#form_update_'+grid).serialize(),
                                                                            success: function(data) {
                                                                                    var o = $.parseJSON(data);
                                                                                    var info = 'Error';
                                                                                    var header = 'Error';
                                                                                    var last_id = o.last_id;
                                                                                    var foreign_id = o.foreign_id;
                                                                                    if(o.status == true) {
                                                                                            //alert(idprivi_apps);
                                                                                            /*$.get(url+'&action=update&id='+last_id+'&foreign_key='+foreign_id, function(data) {
                                                                                                    $('div#grid_wraper_'+grid).html(data);
                                                                                                    $('html, body').animate({scrollTop:$('#'+grid).offset().top - 20}, 'slow');
                                                                                            });
                                                                                            $.get(url, function(data){
                                                                                                    $('div#grid_wraper_'+grid).html(data);
                                                                                                    $('html, body').animate({scrollTop:$('#'+grid).offset().top - 20}, 'slow');
                                                                                            })*/
                                            												reload_grid('grid_'+grid);
																							//alert(o.message);
                                                                                            info = 'info';
                                                                                            header = 'Info';
                                                                                    }
                                                                                    _custom_alert(o.message,header,info, grid, 1, 20000);
                                                                            }
                                                                    })
                                                            });
                                                    }
                                            }
                                      </script>";
		$button['update_back'] .= "<button type='button'
							name='button_update_".$this->url."'
							id='button_update_".$this->url."'
							class='icon-save ui-button'
							onclick='javascript:update_btn_back_".$this->url."(\"".$this->url."\", \"".base_url()."processor/privilege2/priv2/setup/modules?idprivi_apps=".$this->input->get('foreign_key')."&company_id=".$this->input->get('company_id')."&group_id=".$this->input->get('group_id')."&modul_id=".$this->input->get('modul_id')."\", this)'>Save
							</button>";
                
                $button['cancel']  =  "<script type='text/javascript'>
										function cancel_btn_".$this->url."(grid, url, dis) {	                                                
											/*$.get(url, function(data){
												$('div#grid_wraper_'+grid).html(data);
												$('html, body').animate({scrollTop:$('#'+grid).offset().top - 20}, 'slow');
											 })
											 info = 'info';
											 header = 'Info';*/
											 //_custom_alert(o.message,header,info, grid, 1, 20000);
                                                                                         $('#alert_dialog_form').dialog('close');
										}
								  </script>";
			$button['cancel'] .= "<button type='button'
								name='button_cancel_".$this->url."'
								id='button_cancel_".$this->url."'
								class='icon-save ui-button'
								onclick='javascript:cancel_btn_".$this->url."(\"".$this->url."\", \"".base_url()."processor/privilege2/priv2/setup/groups?idProb=".$this->input->get('foreign_key')."&company_id=".$this->input->get('company_id')."&group_id=".$this->input->get('group_id')."&modul_id=".$this->input->get('modul_id')."\", this)'>Close 
								</button>";
                
       
                if ($this->input->get('action') == 'view') unset($button['update_back']);
                else '';
            
            return $button;
        }

	public function output(){
		$this->index($this->input->get('action'));
	}
}
?>