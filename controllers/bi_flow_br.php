 <?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class bi_flow_br extends MX_Controller { 
    function __construct() {
        parent::__construct();
        $this->load->library('auth');
        $this->db = $this->load->database('hrd',false, true);
        
        $company_id              = substr($url, strrpos($url, '/') + 1);
        $this->sess_auth->company_id = $company_id;

        $this->user = $this->auth->user();

        $this->load->library('lib_sub_core');
        $this->modul_id = $this->input->get('modul_id');
        $this->iModul_id = $this->lib_sub_core->getIModulID($this->input->get('modul_id'));

        $this->title = 'Bussiness Requirement';
        $this->url = 'bi_flow_br';
        $this->urlH = 'bi_flow';		

        // $this->id_head 	 = $this->input->get('aidiH');
        $this->modul_idH = $this->input->get('modul_id');
        $this->group_idH = $this->input->get('group_id');      
        
        $this->header_table = 'hrd.biflow';   
        $this->header_table_pk = 'id';
        $this->header_table_key = 'cKode';

        $this->urlpath = 'erpss/'.str_replace("_","/", $this->url);
        $this->urlpathH = 'erpss/'.str_replace("_","/", $this->urlH);

        $this->maintable = 'hrd.biflow_br';    
        $this->main_table = $this->maintable;   
        $this->main_table_pk = 'id';
        $this->company_id   = $this->input->get('company_id');

        $this->cKode = $this->input->get('cKode');            

        $this->pathfile = 'files/erpss/biflow';

    }

    function index($action = '') {
        $action = $this->input->get('action');
        //Bikin Object Baru Nama nya $grid      
        $grid = new Grid;
        $grid->setTitle($this->title);
        $grid->setTable($this->maintable);      
        $grid->setUrl($this->url);

        $grid->addList('del','mKeterangan','dSubmit','iValidate','iSubmit');
        $grid->setSortBy('id');
        $grid->setSortOrder('DESC');  

        $grid->addFields('mKeterangan','dSubmit', 'mFeedback', 'form_history'); 

        $grid->setWidth('mKeterangan', '250');
        $grid->setAlign('mKeterangan', 'left');
        $grid->setLabel('mKeterangan', 'Description');

        $grid->setWidth('iValidate', '120');
        $grid->setAlign('iValidate', 'center');
        $grid->setLabel('iValidate', 'Validasi');

        $grid->setWidth('dSubmit', '110');
        $grid->setAlign('dSubmit', 'center');
        $grid->setLabel('dSubmit', 'Tgl Submit');

        $grid->setWidth('iSubmit', '110');
        $grid->setAlign('iSubmit', 'center');
        $grid->setLabel('iSubmit', 'Status Submit');

        $grid->setLabel('mFeedback', 'Feedback');

        $grid->setLabel('form_history', 'Summary');

        $grid->setWidth('del', '20');
        $grid->setAlign('del', 'center');
        $grid->setLabel('del','del');

        $grid->setLabel('mKeterangan','Description');

        // join tabel
        // $grid->setJoinTable('purchasing.poir_header', 'poir_header.cPONumber = biflow_br.vKodeRef', 'inner');
        // $grid->setJoinTable('hrd.employee', 'employee.cNip = poir_header.cCreatedBy', 'inner');
        // $grid->setJoinTable('cost.supplier', 'supplier.id = poir_header.iSupplierId', 'inner');
    
        $grid->setQuery('biflow_br.lDeleted = 0 ', null);

        $this->cKode = $this->input->get('cKode');            
        $grid->setInputGet('cKode', $this->cKode);            
        $grid->setQuery('biflow_br.cKode', $this->input->get('cKode'));
        $grid->setForeignKey($this->input->get('cKode'));

        $this->aidiH = $this->input->get('id_head');            
        $grid->setInputGet('aidiH', $this->aidiH);

        $grid->changeFieldType('iSubmit', 'combobox','',array('' => 'Pilih', 0 => 'Need To Be Submit', 1 => 'Submitted'));
        $grid->changeFieldType('iValidate', 'combobox','',array('' => 'Pilih', 0 => 'Need To Be Validate', 1 => 'Valid', 2 => 'Not Valid'));
        $grid->changeFieldType('lDeleted', 'combobox','',array( 0 => 'Ya', 1 => 'Tidak'));

        // $grid->setSearch('vKodeRef');
        
        $grid->setRequired('mKeterangan'); 
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
                $post       = $this->input->post();
                $isUpload   = $this->input->get('isUpload');

               
                    $idForm     = $post['id_form_upload'];
                    $id_field   = $post['iM_modul_fields'];
                    $data_dok   = $this->db->get_where('erp_privi.sys_masterdok', array('iM_modul_fields' => $id_field, 'ldeleted' => 0))->row_array();
                    $path       = realpath($data_dok['filepath'].'/');
                    $tgl        = date('Y-m-d H:i:s');

                    if($isUpload) {
                        $lastId = $this->input->get('lastId');
                        if(!file_exists($path."/".$lastId)){
                            if (!mkdir($path."/".$lastId, 0777, true)) { //id review
                                $r['message']   = 'Failed Upload , Failed create Folder!';
                                $r['status']    = FALSE;
                                $r['last_id']   = $lastId;
                                echo json_encode($r);
                                die('Failed upload, try again!');
                            }
                        }

                        $arrFileKet = $_POST[$idForm.'_keterangan'];
                        $filesKey   = $idForm.'_file';

                        $i = 0;
                        foreach ($_FILES[$filesKey]["error"] as $key => $error) {
                            if ($error == UPLOAD_ERR_OK) {
                                $tmp_name       = $_FILES[$filesKey]["tmp_name"][$key];
                                $name           = $_FILES[$filesKey]["name"][$key];
                                $name_generate  = $this->lib_sub_core->generateFilename($name);

                                if(move_uploaded_file($tmp_name, $path."/".$lastId."/".$name_generate)) {
                                        $insert['iM_modul_fields']      = $id_field;
                                        $insert['idHeader_File']        = $lastId;
                                        $insert['vFilename']            = $name;
                                        $insert['vFilename_generate']   = $name_generate;
                                        $insert['tKeterangan']          = $arrFileKet[$i];
                                        $insert['dCreate']              = $tgl;
                                        $insert['cCreate']              = $this->user->gNIP;    
                                        $insert['iDeleted']             = 0;
                                        $this->db->insert('hrd.group_file_upload', $insert);

                                    $i++;

                                } else {
                                    echo "Upload ke folder gagal";
                                }
                            }
                        }

                        $r['message']   = 'Data Berhasil di Simpan!';
                        $r['status']    = TRUE;
                        $r['last_id']   = $this->input->get('lastId');
                        echo json_encode($r);
                        
                    }  else {
                        echo $grid->saved_form();
                    }

                break;
            case 'update':
                    $grid->render_form($this->input->get('id'));
                    break;
            case 'updateproses':
                echo $grid->updated_form();
                break;
            case 'getFormDetail':
                echo $this->getFormDetail();
                break;
            case 'updateprosesx':
                $isUpload   = $this->input->get('isUpload');
                $post       = $this->input->post();
                $idForm     = $post['id_form_upload'];
                $id_field   = $post['iM_modul_fields'];
                $data_dok   = $this->db->get_where('erp_privi.sys_masterdok', array('iM_modul_fields' => $id_field, 'ldeleted' => 0))->row_array();
                $lastId     = $post[$this->url.'_'.$this->main_table_pk];
                $path       = realpath($data_dok['filepath'].'/');

                $filesUpload= $this->db->get_where('hrd.group_file_upload', array('iM_modul_fields' => $id_field, 'idHeader_File' => $lastId, 'iDeleted' => 0))->result_array();
                if($isUpload) {

                    if(!file_exists($path."/".$lastId)){
                        if (!mkdir($path."/".$lastId, 0777, true)) { //id review
                            die('Failed upload, try again!');
                        }
                    }
    
                    $fileid             = null;
                    $tgl                = date('Y-m-d H:i:s');
                    $j                  = 0;
    
                    $arrFileID          = $_POST[$idForm.'_id'];
    
                    foreach ($arrFileID as $val_id) {
                        $fileid = ( strlen($fileid) > 0 ) ? $fileid.',"'.$val_id.'"' : '"'.$val_id.'"';
                    }
    
                    if($fileid!=''){
                        $sql1 = "UPDATE hrd.group_file_upload SET iDeleted = 1, dUpdate = ?, cUpdate = ? WHERE iM_modul_fields = ? AND idHeader_File = ? AND iFile NOT IN (".$fileid.") ";
                        $this->db->query($sql1, array($tgl, $this->user->gNIP, $id_field, $lastId));
                    }
    
                    foreach ($arrFileID as $val_id) {
                        if ( $val_id != "" ){
                            try{
                                $updateFile['dUpdate']  = $tgl;
                                $updateFile['cUpdate']  = $this->user->gNIP;
                                $this->db->where('iM_modul_fields', $id_field);
                                $this->db->where('idHeader_File', $lastId);
                                $this->db->update('hrd.group_file_upload', $updateFile);
                            } catch (Exception $ex){
                                die($ex);
                            }
                        }
                    }

                    $filesKey   = $idForm.'_file';
                    $arrFileKet = $_POST[$idForm.'_keterangan'];
                    if (isset($_FILES[$filesKey]))  {
                        $i=0;
                        foreach ($_FILES[$filesKey]["error"] as $key => $error) {
                            if ($error == UPLOAD_ERR_OK) {
                                $tmp_name       = $_FILES[$filesKey]["tmp_name"][$key];
                                $name           = $_FILES[$filesKey]["name"][$key];
                                $name_generate  = $this->lib_sub_core->generateFilename($name);

                                if(move_uploaded_file($tmp_name, $path."/".$lastId."/".$name_generate)) {
                                    $insert['iM_modul_fields']      = $id_field;
                                    $insert['idHeader_File']        = $lastId;
                                    $insert['vFilename']            = $name;
                                    $insert['vFilename_generate']   = $name_generate;
                                    $insert['tKeterangan']          = $arrFileKet[$i];
                                    $insert['dCreate']              = $tgl;
                                    $insert['cCreate']              = $this->user->gNIP;    
                                    $insert['iDeleted']             = 0;
                                    $this->db->insert('hrd.group_file_upload', $insert);

                                    $i++;
                                    $j++;

                                } else {
                                    echo "Upload ke folder gagal";
                                }
                            }

                        }

                    }

                    $r['message']   = 'Data Berhasil di Simpan!';
                    $r['status']    = TRUE;
                    $r['last_id']   = $this->input->get('lastId');
                    echo json_encode($r);exit();
                }  else {
                    echo "bawah";
                    exit;
                    echo $grid->updated_form();
                }
                break;      
            case 'download':
                $this->load->helper('download');        
                $name = $this->input->get('file');
                $id = $_GET['id'];
                $tempat = $_GET['path'];    
                $path = file_get_contents('./'.$tempat.'/'.$id.'/'.$name);    
                force_download($name, $path);
                break;   
            case 'delete':
                $postData = $this->input->post();
                    $id = $postData['id'];

                    $datanya  = $this->db->get_where($this->maintable, array($this->main_table_pk => $id))->row_array();

                    $kode_br = $datanya['cKode_br'];

                    $sql = "SELECT * 
                                    FROM biflow_br_log
                                    WHERE cKode_br =  '".$kode_br."'
                                    ORDER BY id DESC
                                    LIMIT 1
                                ";
                    $get_id = $this->db->query($sql)->row_array();
                    
                    $up['lDeleted'] = 1;

                    $this->db->where(array('id' => $get_id['id'], 'cKode_br' => $kode_br));
                    $this->db->update('hrd.biflow_br_log', $up);
                    // echo print_r($kode_br);exit;
                
                echo $grid->delete_row();
                break;
            case 'delete_referensi':
                echo $this->deleteDetailItemReceiving();
                break; 
            case 'download':
                $this->download($this->input->get('file'));
                break;
            case 'searchPIC':
                $this->searchPIC();
                break;
            case 'get_no_po':
                echo $this->getNoPo();
                break;
            default:
                $grid->render_grid();
                break;
        }
    }

    function searchPIC (){
        $term = $this->input->get('term'); 
        $data = array(); 

        $sql = "SELECT e.cNip, e.vName FROM hrd.employee e 
                WHERE e.lDeleted = 0 AND ( e.dresign = '0000-00-00' OR e.dresign > DATE(NOW()) ) 
                    AND ( e.cNip LIKE '%{$term}%' OR e.vName LIKE '%{$term}%' ) 
                ORDER BY e.vName ASC ";
    
        $query = $this->db->query($sql);
        if ($query->num_rows > 0) {         
            foreach($query->result_array() as $line) {
                $row_array['id']        = trim($line['cNip']);
                $row_array['value']     = trim($line['cNip']).' - '.trim($line['vName']); 
                array_push($data, $row_array);
            }
        }
        
        echo json_encode($data);
        exit;  
    }

    function getNoPo(){
        $term = $this->input->get('term');
        $company = $this->input->get('company_id');
        $sql  = 'SELECT a.cPONumber
                    FROM purchasing.lpb_detail a
                    WHERE a.cPONumber LIKE "%'.$term.'%" AND a.lDeleted = 0
                    #AND a.iCompanyID = "'.$company.'"
                    GROUP BY a.cPONumber
                    ORDER BY a.cPONumber ASC
                    LIMIT 100 ';
        $lines = $this->db->query($sql)->result_array();
        
        $return_arr = array();
        $i=0;
        foreach($lines as $line) {
            $row_array["value"] = trim($line["cPONumber"]);
            $row_array["id"] = trim($line["cPONumber"]);
            array_push($return_arr, $row_array);
        }
        echo json_encode($return_arr);exit();
    }

    function searchBox_bi_flow_br_vKodeRef($rowData, $id) {
        $o = '<input type="hidden" id="'.$id.'" name="'.$id.'" value="">';
        $o .= '<input type="text" id="'.$id.'_id" name="'.$id.'_id" value="">';

        $o .= "<script>";

        $o .= "$(document).ready(function(){
                    var config1 = {
                        source: base_url+'processor/".$this->urlpath."?action=get_no_po&company_id=".$this->company_id."',                  
                        select: function(event, ui){

                            var i = $('#".$id."_id').index(this);
                            $('#".$id."_id').eq(i).val(ui.item.value);  
                            $('#".$id."').eq(i).val(ui.item.id); 

                        },
                        minLength: 2,
                        autoFocus: true,
                    };
                    $('#".$id."_id').livequery(function(){
                        $(this).autocomplete(config1);
                        var i = $('#".$id."_id').index(this);
                        $(this).keypress(function(e, ui){
                            if(e.which != 13) {
                                $('#".$id."').eq(i).val('');
                            }     

                            if(e.which == 13){
                                reload_grid('grid_kemas_master_line');
                            }     

                        });
                        $(this).blur(function(){
                            if($('#".$id."').eq(i).val() == '') {
                                $(this).val(''); 
                            }           
                        }); 
                    });

                    // Untuk searchbox
                    // Synchronize two input fields
                    $('#".$id."_id').bind('keyup paste', function() {
                        $('#".$id."').val($(this).val());
                    });

                });";

        $o .= "</script>";
        
        return $o;
    }

    function listBox_bi_flow_br_storage_c_stoname($value, $pk, $name, $rowData) {
        $value = str_replace("ø", "&deg;", strtoupper($value));
		return $value;
    }

    function listBox_bi_flow_br_del($value, $pk, $name, $rowData){
        // print_r($this->input->get());exit; 
        $id_head = $this->input->get('aidiH');
        $cKode = $rowData->cKode;
        // print_r($cKode);exit;

        $urlDelete = base_url()."processor/".$this->urlpath."?action=delete&cKode=".$cKode."&foreign_key=".$cKode."&id=".$pk."&modul_id=".$this->input->get('modul_id');

        // Cek Submit Header
        $this->db->where($this->header_table_pk, $id_head);
        $dHead = $this->db->get($this->header_table)->row_array();

        // Cek Submit Maintable
        $this->db->where($this->main_table_pk, $rowData->{$this->main_table_pk});
        $datanya = $this->db->get($this->main_table)->row_array();

        if($dHead['iSubmit'] == 1 || $datanya['iSubmit'] == 1){
            $delete = '';
        }else{
            $delete = '<script type"text/javascript">
                            function '.$this->url.'_del_po(id_head, pk){
                                custom_confirm("Yakin ?", function(){
                                    $.ajax({
                                        url: "'.base_url().'processor/'.$this->urlpath.'?action=delete&cKode='.$cKode.'&foreign_key='.$cKode.'&id="+pk+"&modul_id='.$this->input->get('modul_id').'",
                                        type: \'POST\',                                     
                                        data: {id : pk},
                                        success: function(data){                                            
                                            // $.get("'.base_url().'processor/'.$this->urlpathH.'?action=update&id="+id_head+"&foreign_key=0&company_id='.$this->company_id.'&group_id='.$this->group_idH.'&modul_id='.$this->modul_idH.'", function(data){
                                            //             $("div#form_'.$this->urlH.'").html(data);               
                                            //         });
                                            reload_grid("grid_'.$this->url.'");
                                        },
                                        error: function(){
                                            _custom_alert("Gagal Hapus Data!", "Error!", "info", "'.$this->urlpath.'", 1, 5000);
                                        }    
                                    });

                                    $("#alert_dialog_form").dialog("close");
                                });
                            }
                        </script>';

            $delete .= '<a href="#" class="wql_receiving_po_del" title="delete" onclick="'.$this->url.'_del_po('.$id_head.', '.$pk.')" ><center><span class="ui-icon ui-icon-trash"></span></center></a>';

        }

        return $delete;
    }

    function listBox_Action($row, $actions){
        unset($actions['edit']);
        $row        = get_object_vars($row);

        // Cek Submit Header
        $sqlCek     = "SELECT a.iSubmit 
                        FROM ".$this->header_table." a
                        WHERE a.cKode = '".$row['cKode']."' ";
        $cekSubmit  = $this->db->query($sqlCek)->row_array();

        $submitFisik = (!empty($cekSubmit))?$cekSubmit['iSubmit']:0;

        // Cek Submit Maintable
        $this->db->where($this->main_table_pk, $row[$this->main_table_pk]);
        $datanya = $this->db->get($this->main_table)->row_array();
            
        $url        = base_url()."processor/".$this->urlpath."?action=update&cKode=".$row['cKode']."&foreign_key=".$row['cKode']."&id=".$row[$this->main_table_pk]."&aidiH=".$this->input->get('aidiH')."&modul_id=".$this->input->get('modul_id')."&group_id=".$this->input->get('group_id')."&company_id=".$this->input->get('company_id');
        $urlView        = base_url()."processor/".$this->urlpath."?action=view&cKode=".$row['cKode']."&foreign_key=".$row['cKode']."&id=".$row[$this->main_table_pk]."&aidiH=".$this->input->get('aidiH')."&modul_id=".$this->input->get('modul_id')."&group_id=".$this->input->get('group_id')."&company_id=".$this->input->get('company_id');
        $urlDelete        = base_url()."processor/".$this->urlpath."?action=delete&cKode=".$row['cKode']."&foreign_key=".$row['cKode']."&id=".$row[$this->main_table_pk]."&aidiH=".$this->input->get('aidiH')."&modul_id=".$this->input->get('modul_id')."&group_id=".$this->input->get('group_id')."&company_id=".$this->input->get('company_id');

        $edit       = "<script type'text/javascript'>
                            function edit_btn_".$this->url."(url, title) {
                                browse_with_no_close(url, title);
                            }
                        </script>";
        $edit       .= "<a href='#' onclick='javascript:edit_btn_".$this->url."(\"".$url."\", \"SETUP GROUPS\");'><center><span class='ui-icon ui-icon-pencil'></span></center></a>";

        $view       = "<script type'text/javascript'>
                            function view_btn_".$this->url."(url, title) {
                                browse_with_no_close(url, title);
                            }
                        </script>";
        $view       .= "<a href='#' onclick='javascript:view_btn_".$this->url."(\"".$urlView."\", \"SETUP GROUPS\");'><center><span class='ui-icon ui-icon-lightbulb'></span></center></a>";

        // $delete  = "<script type'text/javascript'>
        //                     function del_btn_".$this->url."(url, title) {
        //                         browse_with_no_close(url, title);
        //                     }
        //                 </script>";
        // $delete     .= "<a href='#' onclick='javascript:del_btn_".$this->url."(\"".$urlDelete."\", \"SETUP GROUPS\");'><center><span class='ui-icon ui-icon-trash'></span></center></a>";


        $actions['edit'] = $edit;
        $actions['view'] = $view;
        // $actions['delete']   = $delete;

        // if($datanya['iSubmit'] == 0 && $datanya['iValidate'] == 1) {
        //     $actions['edit'] = $edit;
        // }

        // Untuk Open Editing
        $isOpenEditing = $this->lib_sub_core->getOpenEditing($this->input->get('modul_id'), $this->input->get('aidiH'));

        if ($submitFisik == 1 || $datanya['iSubmit'] == 1){
            //unset($actions['delete']);
            if ($isOpenEditing) {
            } else {
                unset($actions['edit']);
            }
        } else {
            //if ($row['iSubmit'] == 0){
                // $actions['edit'] = $edit;
            //}
        }
         
        return $actions;
    }

    //Jika Ingin Menambahkan Seting grid seperti button edit enable dalam kondisi tertentu
    public function manipulate_grid_button($button){
        unset($button['create']);
        $url        = base_url()."processor/".$this->urlpath."?action=create&foreign_key=".$this->input->get('cKode')."&cKode=".$this->input->get('cKode')."&company_id=".$this->input->get('company_id')."&group_id=".$this->input->get('group_id')."&modul_id=".$this->input->get('modul_id');
        $btn_baru   = '<span class="icon-add ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" onclick="browse_with_no_close(\''.$url.'\', \''.$this->title.'\')">Tambah Data Baru</span>';
        
        $cekSubmit  = $this->db->get_where($this->header_table, array('cKode' => $this->input->get('cKode')))->row_array();

        // Untuk Open Editing
        $isOpenEditing = $this->lib_sub_core->getOpenEditing($this->input->get('modul_id'), $this->input->get('id_head'));

        if ( $cekSubmit['iSubmit'] == 1){
            if($isOpenEditing){
                array_unshift($button, $btn_baru);
            }else{
                unset($button['create']);
                $button['create'] = 'Data sudah disubmit';
            }
        } else {
            array_unshift($button, $btn_baru);
        }

        return $button;
        
    }

    function insertBox_bi_flow_br_mKeterangan($field, $id) {
        $o = '<input type="hidden" name="cKode" value="'.$this->input->get('cKode').'">';
        $o .= '<input type="hidden" name="isdraft" id="isdraft">';
        $o .= '<div class="col-sm-6">';
        $o .= '<textarea id="'.$id.'" name="'.$field.'" class="ckeditorField form-control required-ckeditor" colspan="2"></textarea>';
        $o .= '<script>
                    $(function () {
                        CKEDITOR.replace(' . $id . ',{
                            height: "300px",
                            width: "650px"             
                        });
                    });

                </script>';
        $o .= '</div>';

        return $o;
    }
    
    function updateBox_bi_flow_br_mKeterangan($field, $id, $value, $rowData) {
        $o = '<input type="hidden" name="cKode" value="'.$this->input->get('cKode').'">';
        $o .= '<input type="hidden" name="isdraft" id="isdraft">';
        $o .= '<div class="col-sm-6">';
        $o .= '<textarea id="'.$id.'" name="'.$field.'" class="ckeditorField form-control required-ckeditor" colspan="2">'.nl2br($value).'</textarea>';
        $o .= '<script>
                    $(function () {
                        CKEDITOR.replace(' . $id . ',{
                            height: "300px",
                            width: "650px"             
                        });
                    });

                </script>';
        $o .= '</div>';

        return $o;
    }

    function insertBox_bi_flow_br_dSubmit($field, $id) {
        $o = '<b>Auto after submit</b>';

        return $o;
    }
    
    function updateBox_bi_flow_br_dSubmit($field, $id, $value, $rowData) {
        $o = '<b>Auto after submit</b>';
        if(!empty($rowData[$field])){
            $o = $rowData[$field];
        }

        return $o;
    }

    function insertBox_bi_flow_br_form_history($field, $id)
    {
        $o = '<b> - </b>';

        return $o;
    }

    function updateBox_bi_flow_br_form_history($field, $id, $value, $rowData)
    {
        // print_r($rowData);
        $data['id'] = $id;
        $data['field'] = $field;
        
        // // $roHistory = $this->db->get_where('hrd.biflow_br_history', array('id' => $id))->row();
        
        // // $id_hist = $roHistory->cKode_history;

        $post = $this->input->get();
        $cKode = $post['cKode'];

        $sql = 'SELECT br.mKeterangan, br.dSubmit, br.mFeedback, br.mValidate,  
                    if(br.iValidate = 1, "Valid", if(br.iValidate = 2, "Not Valid", "Need To Be Validate")) AS Validasi,
                    if(br.iSubmit = 1 AND br.lDeleted = 0, "Submitted", IF (br.lDeleted = 1, "Deleted", "Need To Be Submit")) AS Status
                FROM  hrd.biflow_br_log br
                WHERE br.cKode_br = "' . $rowData['cKode_br'] . '"
                AND br.iSubmit NOT IN (0)
                ORDER BY br.dSubmit ASC
        ';

        $datasql = $this->db->query($sql)->result_array();
        $data['datasql'] = $datasql;   

        // echo '<pre>'.$sql;
        // exit; 
        // print_r($datasql);exit;

        $o = $this->load->view('partial/modul/'.$this->url.'_history', $data, true);
        return $o;
    }

    function insertBox_bi_flow_br_mFeedback($field, $id) {
       
        $o = '<b> - </b>';

        return $o;
    }

    function updateBox_bi_flow_br_mFeedback($field, $id, $value, $rowData) {
        // print_r($this->input->get());exit;
        $data = $this->input->get();
        $id = $data['id'];

        // Get ID Header Table
        $dHeader  = $this->db->get_where($this->header_table, array($this->header_table_key => $this->cKode))->row_array();
        $id_head = $dHeader['id'];
        $cKode = $dHeader['cKode'];
        
        // print_r($datanya);exit;

        $data_hist = "SELECT * 
                        FROM biflow_history 
                        WHERE cKode =  '".$cKode."'
                        ORDER BY id DESC 
                        LIMIT 1
                    ";
        $getdata = $this->db->query($data_hist)->row_array();

        // Cek Submit Maintable
        $datanya  = $this->db->get_where($this->maintable, array($this->main_table_pk => $id))->row_array();

        // print_r($dHeader);exit;

        // if(($getdata['iApprove'] == 0 && $datanya['iSubmit'] == 0) || ($getdata['iApprove'] == 2 && $datanya['iSubmit'] = 0)){
        if($getdata['iApprove'] == 1){

            $o = '<input type="hidden" name="cKode" value="'.$this->input->get('cKode').'">';
            $o .= '<input type="hidden" name="isdraft" id="isdraft">';
            // $o .= '<textarea id="'.$id.'" name="'.$field.'" class="ckeditorField form-control required-ckeditor" colspan="2">'.nl2br($value).'</textarea>';
            $o .= '<textarea name="'.$field.'" id="'.$id.'" style="width: 650px; height: 100px;" size="250" maxlength ="250">'.nl2br($value).'</textarea>';

        }else{

            $o = "<b> - </b>";
            
        }
        
        // print_r($datanya['iSubmit']);exit;
        return $o;
    }

    //Standart Setiap table harus memiliki dCreate , cCreate, dupdate, cUpdate
    function before_insert_processor($row, $postData) {
        $postData['dCreate']    = date('Y-m-d H:i:s');
        $postData['cCreate']    = $this->user->gNIP;

        if ($postData['isdraft'] == 'true') {
            $postData['iSubmit'] = 0;
            if(empty($postData['dSubmit'])){
                unset($postData['dSubmit']);
            }
        } else {
            $postData['iSubmit'] = 1;
            $postData['dSubmit'] = date('Y-m-d H:i:s');
            $postData['cSubmit'] = $this->user->gNIP;
        }

        $postData['cKode_br']   = $this->generateCodeDetail($postData['cKode']);

        return $postData; 
    }

    function before_update_processor($row, $postData) {
        $postData = $this->input->post();

    	$postData['dUpdate'] 	= date('Y-m-d H:i:s');
        $postData['cUpdate']	= $this->user->gNIP;

        $id = $postData['bi_flow_br_id'];
        $feedback = $postData['mFeedback'];
        $ket = $postData['mKeterangan'];

        $nowDate = date('Y_m_d_H_i_s');
        
        // Insert History

        $datanya  = $this->db->get_where($this->maintable, array($this->main_table_pk => $id))->row_array();

        $cKode = $postData['cKode'];
        $dup = $postData['dUpdate'];
        $kHist = $datanya['cKode_history'];
        $kode_br = $datanya['cKode_br'];

        $cKode_log = $cKode . '-HIST-' . $nowDate;

        // echo print_r($postData);exit;
        $sql2 = "SELECT *
                FROM biflow_br a
                WHERE a.lDeleted = 0
                AND a.cKode = '" . $cKode . "'
                AND a.cKode_br = '" . $kode_br . "'";
        $rows = $this->db->query($sql2)->result_array();
        // echo print_r($datainsert);
        // exit;

        if ($postData['isdraft'] == 'true') {
            // $postData['iSubmit'] = 0;
            foreach ($rows as $key => $row) {
                $insert = array();
                foreach ($row as $k => $r) {
                    // $insert[$keyHist] = $kodeHist;
                    $insert['cKode_br_log'] = $cKode_log;
                    $insert[$k] = $r;
                    $insert['mFeedback'] = $feedback;
                    $insert['mKeterangan'] = $ket;
                    $insert['dSubmit'] = $dup;
                }
                unset($insert['id']);
                $this->db->insert('hrd.biflow_br_log', $insert);
    
                // echo print_r($insert);
                // exit;
    
            }
            if(empty($postData['dSubmit'])){
                unset($postData['dSubmit']);
            }
        } else {
            $postData['iSubmit'] = 1;
            $postData['dSubmit'] = date('Y-m-d H:i:s');
            $postData['cSubmit'] = $this->user->gNIP;
            foreach ($rows as $key => $row) {
                $update = array();
                foreach ($row as $k => $r) {
                    // $insert[$keyHist] = $kodeHist;
                    $update['cKode_br_log'] = $cKode_log;
                    $update[$k] = $r;
                    $update['mFeedback'] = $feedback;
                    $update['mKeterangan'] = $ket;
                    $update['iSubmit'] = 1;
                    $update['dSubmit'] = date('Y-m-d H:i:s');
                    $update['cSubmit'] = $this->user->gNIP;
                }

                unset($update['id']);
                $this->db->insert('hrd.biflow_br_log', $update);
             

            }
        }

        return $postData; 
    }    

    function after_insert_processor($fields, $id, $postData){
        $postData = $this->input->post();

    	$postData['dCreate'] 	= date('Y-m-d H:i:s');
        $postData['cCreate']	= $this->user->gNIP;

        $id = $postData['bi_flow_br_id'];
        $feedback = $postData['mFeedback'];
        $ket = $postData['mKeterangan'];

        $nowDate = date('Y_m_d_H_i_s');
        
        // Insert History BR

        $datanya  = $this->db->get_where($this->maintable, array($this->main_table_pk => $id))->row_array();

        $cKode = $postData['cKode'];
        $dup = $postData['dUpdate'];
        $kHist = $datanya['cKode_history'];
        $kode_br = $datanya['cKode_br'];

        $cKode_log = $cKode . '-HIST-' . $nowDate;

        // echo print_r($postData);exit;
        $sql2 = "SELECT *
                FROM biflow_br a
                WHERE a.lDeleted = 0
                AND a.cKode = '" . $cKode . "'
                ORDER BY a.id DESC
                LIMIT 1";
        $rows = $this->db->query($sql2)->result_array();
        // echo print_r($datainsert);
        // exit;


        if ($postData['isdraft'] == 'true') {
            // $postData['iSubmit'] = 0;
            foreach ($rows as $key => $row) {
                $insert = array();
                foreach ($row as $k => $r) {
                    // $insert[$keyHist] = $kodeHist;
                    $insert['cKode_br_log'] = $cKode_log;
                    $insert[$k] = $r;
                    $insert['mFeedback'] = $feedback;
                    $insert['mKeterangan'] = $ket;
                    // $insert['dSubmit'] = $dup;
                }
                unset($insert['id']);
                $this->db->insert('hrd.biflow_br_log', $insert);
    
                // echo print_r($insert);
                // exit;
    
            }
            if(empty($postData['dSubmit'])){
                unset($postData['dSubmit']);
            }
        } else {
            // $postData['iSubmit'] = 1;
            // $postData['dSubmit'] = date('Y-m-d H:i:s');
            // $postData['cSubmit'] = $this->user->gNIP;
            foreach ($rows as $key => $row) {
                $update = array();
                foreach ($row as $k => $r) {
                    // $insert[$keyHist] = $kodeHist;
                    $update['cKode_br_log'] = $cKode_log;
                    $update[$k] = $r;
                    $update['mFeedback'] = $feedback;
                    $update['mKeterangan'] = $ket;
                    $update['iSubmit'] = 1;
                    $update['dSubmit'] = date('Y-m-d H:i:s');
                    $update['cSubmit'] = $this->user->gNIP;
                }

                unset($update['id']);
                $sql = "SELECT * 
                                FROM biflow_br_log
                                WHERE cKode_br =  '".$kode_br."'
                                ORDER BY id DESC
                                LIMIT 1
                            ";
                $get_id = $this->db->query($sql)->row_array();

                // echo print_r($get_id);
                // exit;
                $this->db->insert('hrd.biflow_br_log', $update);
                // $this->db->where(array('id' =>  $get_id['id'], 'cKode_br' =>  $kode_br, 'lDeleted' => 0/*, 'cKode_br_log' => */));
                // $this->db->update('hrd.biflow_br_log', $update);
                // echo print_r($update);
                // exit;
            }
        }

        $postData['cKode_br']   = $this->generateCodeDetail($postData['cKode']);

        return $postData; 
    }

    function after_update_processor($fields, $id, $postData) {
         
    }

    //Ini Merupakan Standart Reject yang digunakan di erp
    public function getApptableLog($modul_id)
    {
        $sql = 'select *
                        from erp_privi.m_application a
                        join erp_privi.m_modul b on b.iM_application=a.iM_application
                        where b.idprivi_modules = "' . $modul_id . '"
                        limit 1';

        $data = $this->db->query($sql)->row_array();

        return $data;
    }

    function manipulate_insert_button($buttons) { 
        unset($buttons['save']);
        unset($buttons['save_back']);
        unset($buttons['cancel']);

        $dHeader  = $this->db->get_where($this->header_table, array($this->header_table_key => $this->cKode))->row_array();

        $data['folderApp'] = 'erpss';
        $data['urlH'] = $this->urlH;
        $data['idH'] = $dHeader[$this->header_table_pk];

        $data['modulH'] = $this->modul_idH;

        $data['url'] = $this->url;
        $js = $this->load->view('js/custom_js', $data, TRUE);

        $save_draft = '<button type="button"
                        name="button_create_'.$this->url.'"
                        id="button_create_'.$this->url.'"
                        class="icon-save ui-button" 
                        onclick="javascript:save_btn_'.$this->url.'(\''.$this->url.'\', \' '.base_url().'processor/'.$this->urlpath.'?draft=true&cKode='.$this->input->get('foreign_key').'&company_id='.$this->input->get('company_id').'&group_id='.$this->input->get('group_id').'&modul_id='.$this->input->get('modul_id').'\', this, true )">Save as Draft</button>'; 

        $save = '<button type="button"
                        name="button_create_'.$this->url.'"
                        id="button_create_'.$this->url.'"
                        class="icon-save ui-button" 
                        onclick="javascript:save_btn_'.$this->url.'(\''.$this->url.'\', \' '.base_url().'processor/'.$this->urlpath.'?draft=false&cKode='.$this->input->get('foreign_key').'&company_id='.$this->input->get('company_id').'&group_id='.$this->input->get('group_id').'&modul_id='.$this->input->get('modul_id').'\', this, false )">Save & Submit</button>';

        $buttons['save_back'] = $save_draft.$save.$js;

        $buttons['cancel']  =  "<script type='text/javascript'>
                                    function cancel_btn_".$this->url."(grid, url, dis) {     
                                        $('#alert_dialog_form').dialog('close');
                                    }
                                </script>";

        $buttons['cancel'] .= "<button type='button'
                                name='button_cancel_".$this->url."'
                                id='button_cancel_".$this->url."'
                                class='icon-cancel ui-button'
                                onclick='javascript:cancel_btn_".$this->url."(\"".$this->url."\", \"".base_url()."processor/".$this->urlpath."?cKode=".$this->input->get('foreign_key')."&company_id=".$this->input->get('company_id')."&group_id=".$this->input->get('group_id')."&modul_id=".$this->input->get('modul_id')."\", this)'>
                                Close 
                            </button>";
        
        return $buttons;
    }

    function manipulate_update_button($buttons, $rowData){
        unset($buttons['update']);
        unset($buttons['update_back']);
        unset($buttons['cancel']);

        $dHeader  = $this->db->get_where($this->header_table, array($this->header_table_key => $this->cKode))->row_array();

        $data['folderApp'] = 'erpss';
        $data['urlH'] = $this->urlH;
        $data['idH'] = $dHeader[$this->header_table_pk];
        $data['modulH'] = $this->modul_idH;

        $sqlCek     = 'SELECT t.iSubmit 
                        FROM '.$this->main_table.' f
                        JOIN '.$this->header_table.' t ON f.cKode = t.cKode
                        WHERE f.id = '.$rowData[$this->main_table_pk];
        $cekSubmit  = $this->db->query($sqlCek)->row_array();
        $submitFisik= (!empty($cekSubmit))?$cekSubmit['iSubmit']:0;

        $data['url'] = $this->url;
        $js = $this->load->view('js/custom_js', $data, TRUE);

        $update_draft = '<button type="button"
                            name="button_update_draft_'.$this->url.'"
                            id="button_update_draft_'.$this->url.'"
                            class="ui-button-text icon-save"
                            onclick="javascript:update_btn_'.$this->url.'(\''.$this->url.'\', \' '.base_url().'processor/'.$this->urlpath.'?draft=true&cKode='.$this->input->get('foreign_key').'&company_id='.$this->input->get('company_id').'&group_id='.$this->input->get('group_id').'&modul_id='.$this->input->get('modul_id').'\', this, true)">Update As Draft</button>';

        $update_open = '<button type="button"
                            name="button_update_draft_'.$this->url.'"
                            id="button_update_draft_'.$this->url.'"
                            class="ui-button-text icon-save"
                            onclick="javascript:update_btn_'.$this->url.'(\''.$this->url.'\', \' '.base_url().'processor/'.$this->urlpath.'?draft=true&cKode='.$this->input->get('foreign_key').'&company_id='.$this->input->get('company_id').'&group_id='.$this->input->get('group_id').'&modul_id='.$this->input->get('modul_id').'\', this, true)">Update Open Editing</button>';

        $update = '<button type="button"
                    name="button_update_draft_'.$this->url.'"
                    id="button_update_draft_'.$this->url.'"
                    class="ui-button-text icon-save"
                    onclick="javascript:update_btn_'.$this->url.'(\''.$this->url.'\', \' '.base_url().'processor/'.$this->urlpath.'?draft=false&cKode='.$this->input->get('foreign_key').'&company_id='.$this->input->get('company_id').'&group_id='.$this->input->get('group_id').'&modul_id='.$this->input->get('modul_id').'\', this, false)">Update & Submit</button>';

        $buttons['update_back'] = $update_draft.$update.$js;
            
        $buttons['cancel']  =  "<script type='text/javascript'>
                                    function cancel_btn_".$this->url."(grid, url, dis) {      
                                        $('#alert_dialog_form').dialog('close');
                                    }
                                </script>";
        $buttons['cancel'] .= "<button type='button'
                                    name='button_cancel_".$this->url."'
                                    id='button_cancel_".$this->url."'
                                    class='icon-cancel ui-button'
                                    onclick='javascript:cancel_btn_".$this->url."(\"".$this->url."\", \"".base_url()."processor/".$this->urlpath."?cKode=".$this->input->get('foreign_key')."&company_id=".$this->input->get('company_id')."&group_id=".$this->input->get('group_id')."&modul_id=".$this->input->get('modul_id')."\", this)'>
                                    Close 
                                </button>";


        // Untuk Open Editing
        $isOpenEditing = $this->lib_sub_core->getOpenEditing($this->input->get('modul_id'), $this->input->get('aidiH'));
        
        if ($this->input->get('action') == 'view' || $rowData['iSubmit'] == 1 || $submitFisik == 1){
            if ($isOpenEditing) {
                $buttons['update_back'] = $update_open.$js;
            } else {
                unset($buttons['update_back']);
            }
        }
        
        return $buttons;
    }

    function whoAmI($nip) { 
        $sql = 'select b.vDescription as vdepartemen,a.*,b.*,c.iLvlemp 
                        from hrd.employee a 
                        join hrd.msdepartement b on b.iDeptID=a.iDepartementID
                        join hrd.position c on c.iPostId=a.iPostID
                        where a.cNip ="'.$nip.'"
                        ';
        
        $data = $this->db->query($sql)->row_array();
        return $data;
    }

    function download($vFilename) { 
        $this->load->helper('download');        
        $name = $vFilename;
        $id = $_GET['id'];
        $tempat = $_GET['path'];    
        $path = file_get_contents('./files/folder_app/'.$tempat.'/'.$id.'/'.$name);    
        force_download($name, $path);


    }

    //Output
    public function output(){
        $this->index($this->input->get('action'));
    }

    function generateCodeDetail($kodeHeader){
        $sql = "SELECT a.cKode_br
                FROM ".$this->main_table." a
                WHERE a.lDeleted = 0
                AND a.cKode = '".$kodeHeader."'
                ORDER BY a.id DESC
                LIMIT 1 ";
        $row = $this->db->query($sql)->row_array();

        if(!empty($row)){
            $nilai = str_replace($kodeHeader.'-', "", $row['cKode_br']);
            $int = intval($nilai);
            if ($int > 0) {
                $nilai = $int + 1;
            } else {
                $nilai = 1;
            }

            $generated = $kodeHeader.'-'.$nilai;
        }else{
            $generated = $kodeHeader.'-1';
        }

        return $generated;
    }

    function generateCodeDetail_2($kodeHeader){
        $sql = "SELECT a.cKode_br_i
                FROM hrd.biflow_br_item a
                WHERE a.lDeleted = 0
                AND a.cKode_br = '".$kodeHeader."'
                ORDER BY a.id DESC
                LIMIT 1 ";
        $row = $this->db->query($sql)->row_array();

        if(!empty($row)){
            $nilai = str_replace($kodeHeader.'-', "", $row['cKode_br_i']);
            $int = intval($nilai);
            if ($int > 0) {
                $nilai = $int + 1;
            } else {
                $nilai = 1;
            }

            $generated = $kodeHeader.'-'.$nilai;
        }else{
            $generated = $kodeHeader.'-1';
        }

        return $generated;
    }

}
