<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    class master_groups extends MX_Controller {
    private $sess_auth;
    private $dbset;
	var $categoryId;
    function __construct() {
        parent::__construct();
        $this->sess_auth = new Zend_Session_Namespace('auth'); 
        $this->dbset = $this->load->database('hrd', true);
        $this->url = 'master_groups'; 
    }
    
    function index($action = '') {
    	$action = $this->input->get('action');
		
    	//Bikin Object Baru Nama nya $grid		
        $grid = new Grid;		
        $grid->setTitle('Group PIC');		
        $grid->setTable('hrd.ss_picgroup');		
        $grid->setUrl('master_groups');
        $grid->addList('groupName', 'locationId', 'pic');//'lPersen', 'yPersen',
        $grid->addFields('groupName','locationId', 'pic');
		
		$grid->setLabel('groupName', 'Group Name');
		$grid->setWidth('groupName', '100');
		$grid->setAlign('groupName', 'left');
		
		$grid->setLabel('locationId', 'Coverage Area');
		$grid->setWidth('locationId', '230');
		$grid->setAlign('locationId', 'left');
		
		$grid->setLabel('pic', 'Person In Charge');
		$grid->setWidth('pic', '200');
		$grid->setAlign('pic', 'left');
		
		$this->categoryId = $this->input->get('categoryId');
		$grid->setForeignKey($this->categoryId);
		
		$grid->setInputGet('_categoryId', intval($this->categoryId));
		$grid->setQuery('hrd.ss_picgroup.categoryId', intval($this->input->get('_categoryId')));            
       
	//set search
        //$grid->setSearch('_field1_', '_field2_');
		
        //set required
        $grid->setRequired('groupName', 'locationId', 'pic');	//Field yg mandatori

        $grid->setGridView('grid');

        switch ($action) {
                case 'json':
                        $grid->getJsonData();
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
                case 'update':
                        $grid->render_form($this->input->get('id'));
                        break;
                case 'updateproses':
                        echo $grid->updated_form();
                        break;
				case 'getemployee':
						echo $this->getEmployee();
						break;
				case 'getlocation':
						echo $this->getLocation();
						break;
                case 'delete':
                        echo $grid->delete_row();
                        break;
                default:
                        $grid->render_grid();
                        break;
        }
    }   
	
	public function listBox_master_groups_locationId($value, $pk, $name, $rowData) {
		$nama_company = '';
		$company = explode('_', $rowData->locationId);
		$i=0;
		foreach($company as $v) {
			if ($i%2) 
				$nama_company .= "<b>".$this->getLocationName($v)."</b><br/>";
			else $nama_company .= $this->getLocationName($v)."<br/>";
			$i++;
		}
		
		return $nama_company;
		
	}
	
	public function listBox_master_groups_pic($value, $pk, $name, $rowData) {
		
		$nama_company = '';
		$company = explode('_', $rowData->pic);
		$i=0;
		foreach($company as $v) {
			if ($i%2) 
				$nama_company .= "<b>".$this->getEmployeeName($v)."</b><br/>";
			else $nama_company .= $this->getEmployeeName($v)."<br/>";
			
			$i++;
		}
		//$nama_company = substr($nama_company, 0, strlen($nama_company));
		
		return $nama_company;		
	}
	
	/*PIC*/
	public function insertBox_master_groups_pic($field, $id) {
		$url = base_url().'processor/ss/master/groups?action=getemployee&company_id=3&modul_id=34&group_id=2368';
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
								$("#l_pic2").append("<div id=\'div2_"+ui.item.id+"\'><input type=\'hidden\' class=\'nip2\' id=\'nip2_"+ui.item.id+"\' name=\'nip2[]\' value=\'"+ui.item.id+"\'/>"+ui.item.value+" [<span onclick=\'remove_element(\""+ui.item.id+"\");\' style=\'cursor:pointer;color:red;\'> x </span>]</div>");
								
								$(this).val(""); return false;
							},
							minLength: 2,
							autoFocus: true,
						};
	
						$( "#master_groups_pic_text" ).livequery(function() {
						 	$( this ).autocomplete(config);
						});
	
					});
					function remove_element(id) {
						$("#div_"+id).remove();
				    }
					function getLPIC2() {
						var l_pic2 = [];
						$( ".nip2" ).each(function() {								
							l_pic2.push($(this).val());
						});
						
						return l_pic2;
					}
		      </script>
			  
			  <input name="'.$id.'" id="'.$id.'" type="hidden"/>
			  <input name="'.$id.'_text" id="'.$id.'_text" type="text" size="50"/>';
		
		$o .= "<div id='l_pic2'></div>";
	
		return $o;
	}
	
	public function updateBox_master_groups_pic($field, $id, $value) {
		
	
		$url = base_url().'processor/ss/master/groups?action=getemployee&company_id=3&modul_id=34&group_id=2368';
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
								//$("#master_groups_pic_text").val(ui.item.value);
								//$("#master_groups_pic").val(ui.item.id);
								
								$("#l_pic2").append("<div id=\'div2_"+ui.item.id+"\'><input class=\'nip2\' type=\'hidden\' id=\'nip2_"+ui.item.id+"\' name=\'nip2[]\' value=\'"+ui.item.id+"\'/>"+ui.item.value+" [<span onclick=\'remove_element(\""+ui.item.id+"\");\' style=\'cursor:pointer;color:red;\'> x </span>]</div>");								
								$(this).val(""); return false;
							},
							minLength: 2,
							autoFocus: true,
						};
	
						$( "#master_groups_pic_text" ).livequery(function() {	
						 	$( this ).autocomplete(config);
						});
						
						function getLPIC2() {
							var l_pic2 = [];
							$( ".nip2" ).each(function() {								
								l_pic2.push($(this).val());
							});
							
							return l_pic2;
						}
					});
					
					function remove_element2(id) {
						$("#div2_"+id).remove();
				    }
		      </script>
			 ';
		
		
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
			$o .= $nama;
		} 
		
		$o .= "<div id='l_pic2'>";
		$sql = "SELECT pic from hrd.ss_picgroup where picGroupId = '".$this->input->get('id')."'";
		$query = $this->dbset->query($sql);
		if ($query->num_rows() > 0) {
			foreach($query->result() as $r) {
				$company = $r->pic;
			}
		}
		$nama_company = '';
		$company = explode('_', $company);

		foreach($company as $v) {
			$nama_pic = $this->getEmployeeName($v);
			$o.="<div id='div2_".$v."'>
					<input class='nip2' type='hidden' id='nip2_".$v."' name='nip2[]' value='".$v."'/>".$nama_pic." - ".$v." 
					[<span onclick='remove_element2(\"".$v."\");' style='cursor:pointer;color:red;'> x </span>]</div>";
		}
		
		$o .= "</div>";
	
		return $o;
	}
	/*PIC*/
	
	/*LOCATION*/
	public function insertBox_master_groups_locationId($field, $id) {
		$url = base_url().'processor/ss/master/groups?action=getlocation&company_id=3&modul_id=34&group_id=2368';
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
										l_loc: getLLOC()
									},
									success: function( data ) {
										response( data );
									}
								});
							},
							select: function(event, ui){								
								$("#l_loc").append("<div id=\'div3_"+ui.item.id+"\'><input type=\'hidden\' class=\'location\' id=\'location_"+ui.item.id+"\' name=\'location[]\' value=\'"+ui.item.id+"\'/>"+ui.item.value+" [<span onclick=\'remove_element2(\""+ui.item.id+"\");\' style=\'cursor:pointer;color:red;\'> x </span>]</div>");
								
								$(this).val(""); return false;
							},
							minLength: 2,
							autoFocus: true,
						};
	
						$( "#master_groups_locationId_text" ).livequery(function() {
						 	$( this ).autocomplete(config);
						});
	
					});
					function remove_element3(id) {
						$("#div3_"+id).remove();
				    }
					function getLLOC() {
						var l_loc = [];
						$( ".location" ).each(function() {								
							l_loc.push($(this).val());
						});
						
						return l_loc;
					}
		      </script>
			  
			  <input name="'.$id.'" id="'.$id.'" type="hidden"/>
			  <input name="'.$id.'_text" id="'.$id.'_text" type="text" size="50"/>
			  <input name="'.$this->url.'_categoryId" id="'.$this->url.'_categoryId" type="hidden" value = "'.$this->input->get('foreign_key').'"/>';
		
		$o .= "<div id='l_loc'></div>";
	
		return $o;
	}
	
	public function updateBox_master_groups_locationId($field, $id, $value) {
		
	
		$url = base_url().'processor/ss/master/groups?action=getlocation&company_id=3&modul_id=34&group_id=2368';
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
										l_loc: getLLOC()
									},
									success: function( data ) {
										response( data );
									}
								});
							},
							select: function(event, ui){								
								$("#l_loc").append("<div id=\'div3_"+ui.item.id+"\'><input type=\'hidden\' class=\'location\' id=\'location_"+ui.item.id+"\' name=\'location[]\' value=\'"+ui.item.id+"\'/>"+ui.item.value+" [<span onclick=\'remove_element2(\""+ui.item.id+"\");\' style=\'cursor:pointer;color:red;\'> x </span>]</div>");
								
								$(this).val(""); return false;
							},
							minLength: 2,
							autoFocus: true,
						};
	
						$( "#master_groups_locationId_text" ).livequery(function() {
						 	$( this ).autocomplete(config);
						});
	
					});
					function remove_element3(id) {
						$("#div3_"+id).remove();
				    }
					function getLLOC() {
						var l_loc = [];
						$( ".location" ).each(function() {								
							l_loc.push($(this).val());
						});
						
						return l_loc;
					}
		      </script>
			 ';
		
		
		$sql = "SELECT a.I_LOCATION_ID as cNip, a.V_LOCATION_NAME AS nama FROM hrd.worklocation a where a.I_LOCATION_ID = '".$value."'";
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
			$o .= $nama;
		} 
		
		$o .= "<div id='l_loc'>";
		$sql = "SELECT locationId as pic from hrd.ss_picgroup where picGroupId = '".$this->input->get('id')."'";
		$query = $this->dbset->query($sql);
		if ($query->num_rows() > 0) {
			foreach($query->result() as $r) {
				$company = $r->pic;
			}
		}
		$nama_company = '';
		$company = explode('_', $company);

		foreach($company as $v) {
			$nama_pic = $this->getLocationName($v);
			$o.="<div id='div3_".$v."'>
					<input class='location' type='hidden' id='location_".$v."' name='location[]' value='".$v."'/>".$nama_pic." - ".$v." 
					[<span onclick='remove_element3(\"".$v."\");' style='cursor:pointer;color:red;'> x </span>]</div>";
		}
		
		$o .= "</div>";
		$o .= '<input name="'.$this->url.'_categoryId" id="'.$this->url.'_categoryId" type="hidden" value = "'.$this->input->get('foreign_key').'"/>';
	
		return $o;
	}
	/*PIC*/
	
	function getEmployee() {
	
		$term = $this->input->get('term');		
		$data = array();
		$pic_exists = "";
		foreach($_GET as $key=>$val) {
			if ($key == "l_pic2") {
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
	
	function getLocation() {
	
		$term = $this->input->get('term');		
		$data = array();
		$loc_exists = "";
		foreach($_GET as $key=>$val) {
			if ($key == "l_loc") {
				foreach ($val as $k=>$v) {					
					$loc_exists .= "'".$v."',";
				}
			}
		}
		$loc_exists = substr($loc_exists, 0, strlen($loc_exists)-1);		
		if (strlen($loc_exists) == 0) $qq = "";
		else $qq = " AND a.I_LOCATION_ID NOT IN ({$loc_exists})";
		
		$sql = "SELECT a.I_LOCATION_ID as cNip, a.V_LOCATION_NAME as nama from hrd.worklocation a where a.V_LOCATION_NAME like '%".$term."%' ".$qq."
				ORDER BY V_LOCATION_NAME ASC";
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
	
	function getLocationName($id) {
		$sql = "Select V_LOCATION_NAME from hrd.worklocation where I_LOCATION_ID = '{$id}'";
		$query = $this->dbset->query($sql);
		$nm_comp = '';
		if ($query->num_rows() > 0) {
			$r = $query->row();
			$nm_comp = $r->V_LOCATION_NAME;
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
	
	public function listBox_action($row, $actions) {
		//$actions['delete'] = '';		
		//return $actions;
		//print_r($row);
		
		unset($actions['view']);
    	unset($actions['edit']);
    	unset($actions['delete']);
        	
    	$url = base_url()."processor/ss/master/groups?action=update&categoryId=".$row->categoryId."&foreign_key=".$row->categoryId."&id=".$row->picGroupId;
    	$edit  = "<script type'text/javascript'>
    							function edit_btn_".$this->url."(url, title) {
    								browse_with_no_close(url, title);
    							}
    						</script>";
    	$edit .= "<a href='#' onclick='javascript:edit_btn_".$this->url."(\"".$url."\", \"GROUP PIC\");'><center><span class='ui-icon ui-icon-pencil'></span></center></a>";
    	$actions['edit'] = $edit;
		
		return $actions;
	}
    
    public function after_insert_processor($fields, $id, $post) {
        $cNip = $this->sess_auth->gNIP;
		$_location = '';
		$_pic      = '';
		foreach($_POST as $key=>$value) {
			if ($key == 'location') {
				foreach($value as $k=>$v) {
					$_location .= $v."_";
				}
			}
			
			if ($key == 'nip2') {
				foreach($value as $k=>$v) {
					$_pic .= $v."_";
				}
			}
		}
		$_location = substr($_location, 0, strlen($_location)-1);
		$_pic     = substr($_pic, 0, strlen($_pic)-1);
		
		//		
		$sql = "UPDATE hrd.ss_picgroup set locationId = '{$_location}', pic = '{$_pic}' where picGroupId = '{$id}'";
        $this->dbset->query($sql);
    }
    
    public function after_update_processor($fields, $id, $post) {
        $cNip = $this->sess_auth->gNIP;
		$_location = '';
		$_pic      = '';
		foreach($_POST as $key=>$value) {
			if ($key == 'location') {
				foreach($value as $k=>$v) {
					$_location .= $v."_";
				}
			}
			
			if ($key == 'nip2') {
				foreach($value as $k=>$v) {
					$_pic .= $v."_";
				}
			}
		}
		$_location = substr($_location, 0, strlen($_location)-1);
		$_pic     = substr($_pic, 0, strlen($_pic)-1);
		
		//		
		$sql = "UPDATE hrd.ss_picgroup set locationId = '{$_location}', pic = '{$_pic}' where picGroupId = '{$id}'";
        $this->dbset->query($sql);
       
    }
    
	public function manipulate_grid_button($button) {    	
    	unset($button['create']);
    	$url = base_url()."processor/ss/master/groups?action=create&foreign_key=".$this->input->get('categoryId')."&category_id=".$this->input->get('categoryId')."&company_id=".$this->input->get('company_id')."&group_id=".$this->input->get('group_id')."&modul_id=".$this->input->get('modul_id');
    	$btn_baru  = "<script type='text/javascript'>
		    	function add_btn_$this->url(url, title) {
		    		browse_with_no_close(url, title);
		    	}    	 
    		</script>
    	";
    	//$btn_baru .= '<button id="button_add_perso_master_agama" name="button_add_perso_master_agama"
    	//		onclick="javascript:add_btn_'.$this->url.'(\''.$url.'\', \'MASTER AGAMA\');" class="icon-add ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary">Add New</button>';
    	$btn_baru .= '<span class="icon-add ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" onclick="add_btn_'.$this->url.'(\''.$url.'\', \'SETUP MODULES\')">Add New</span>';
    	
    	array_unshift($button, $btn_baru);
   
    	return $button;
    	
    }
	
	public function manipulate_insert_button($button) {
            unset($button['save']);
            unset($button['save_back']);
            unset($button['cancel']);
            
            $button['save_back']  =  "<script type='text/javascript'>
                                            function create_btn_back_".$this->url."(grid, url, dis) {		
                                                    //var idprivi_apps = $('#priv2_setup_modules_idprivi_apps').val();
                                                    //url += '&idprivi_apps='+idprivi_apps;
                                                    
                                                    var req = $('#form_create_'+grid+' input.required, #form_create_'+grid+' select.required, #form_create_'+grid+' textarea.required');
                                                    var conf=0;
                                                    var alert_message = '';
                                                    var tot_err = 0;
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
                                                                                    var foreign_id = o.foreign_id;
                                                                                    
                                                                                    if(o.status == true) {
                                                                                            //alert(foreign_id);
																							$('#grid_'+grid).trigger('reloadGrid');
																							//alert(o.message);
                                            												$.get(url+'&action=update&id='+last_id+'&foreign_key='+foreign_id+'&category_id='+foreign_id, function(data) {
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
							onclick='javascript:create_btn_back_".$this->url."(\"".$this->url."\", \"".base_url()."processor/ss/master/groups?category_id=".$this->input->get('foreign_key')."&company_id=".$this->input->get('company_id')."&group_id=".$this->input->get('group_id')."&modul_id=".$this->input->get('modul_id')."\", this)'>Save Modules
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
								onclick='javascript:cancel_btn_".$this->url."(\"".$this->url."\", \"".base_url()."processor/privilege2/priv2/setup/groups?idprivi_apps=".$this->input->get('foreign_key')."&company_id=".$this->input->get('company_id')."&group_id=".$this->input->get('group_id')."&modul_id=".$this->input->get('modul_id')."\", this)'>Close 
								</button>";
            
            return $button;
        }
        
        public function manipulate_update_button($button) {
            unset($button['update']);
            unset($button['update_back']);
            unset($button['cancel']);
            
            $button['update_back']  =  "<script type='text/javascript'>
                                            function update_btn_back_".$this->url."(grid, url, dis) {		
                                                    //var idprivi_apps = $('#priv2_setup_modules_idprivi_apps').val();
                                                    //url += '&idprivi_apps='+idprivi_apps;
                                                    
                                                    var req = $('#form_update_'+grid+' input.required, #form_update_'+grid+' select.required, #form_update_'+grid+' textarea.required');
                                                    var conf=0;
                                                    var alert_message = '';
                                                    var tot_err = 0;
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
							onclick='javascript:update_btn_back_".$this->url."(\"".$this->url."\", \"".base_url()."processor/ss/master/groups?category_id=".$this->input->get('foreign_key')."&company_id=".$this->input->get('company_id')."&group_id=".$this->input->get('group_id')."&modul_id=".$this->input->get('modul_id')."\", this)'>Save Modules
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
								onclick='javascript:cancel_btn_".$this->url."(\"".$this->url."\", \"".base_url()."processor/ss/master/groups?category_id=".$this->input->get('foreign_key')."&company_id=".$this->input->get('company_id')."&group_id=".$this->input->get('group_id')."&modul_id=".$this->input->get('modul_id')."\", this)'>Close 
								</button>";
                
                if ($this->input->get('action') == 'view') unset($button['update_back']);
                else '';
            
            return $button;
        }

    public function output(){
            $this->index($this->input->get('action'));
    }
}