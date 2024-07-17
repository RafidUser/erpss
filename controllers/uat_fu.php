 <?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class uat_fu extends MX_Controller { 
    function __construct() {
        parent::__construct();
        $this->load->library('auth');
        $this->load->library('lib_sub_core');
        $this->load->library('lib_erpss');
        $this->load->library('lib_utilitas');
        $this->db = $this->load->database('hrd',false, true);
        
        $company_id = substr($url, strrpos($url, '/') + 1);
        $this->sess_auth->company_id = $company_id;

        $this->user = $this->auth->user();

        $this->team = $this->lib_erpss->hasTeam($this->user->gNIP);
        $this->teamID = $this->lib_erpss->hasTeamID($this->user->gNIP);
        $this->isAdmin = $this->lib_erpss->isAdmin($this->user->gNIP);

        $this->load->library('lib_sub_core');
        $this->modul_id = $this->input->get('modul_id');
        $this->iModul_id = $this->lib_sub_core->getIModulID($this->input->get('modul_id'));

        $this->title = 'File UAT';
        $this->url = 'uat_fu';
        $this->urlH = 'bi_flow';

        // $this->id_head    = $this->input->get('aidiH');
        $this->modul_idH = $this->input->get('modul_id');
        $this->group_idH = $this->input->get('group_id');      
        
        $this->header_table = 'hrd.biflow';   
        $this->header_table_pk = 'id';
        $this->header_table_key = 'cKode';

        $this->urlpath = 'erpss/'.str_replace("_","/", $this->url);
        $this->urlpathH = 'erpss/'.str_replace("_","/", $this->urlH);

        $this->maintable = 'hrd.biflow_fu';    
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

        $grid->addList('del','upload_dok','mKeterangan','dSubmit','iSubmit'); 
        $grid->setSortBy('id');
        $grid->setSortOrder('DESC');  

        $grid->addFields('upload_dok','mKeterangan','dSubmit', 'mFeedback', 'form_history');

        $grid->setWidth('upload_dok', '150');
        $grid->setAlign('upload_dok', 'left');
        $grid->setLabel('upload_dok', 'Upload Dokumen');

        $grid->setWidth('vModul', '200');
        $grid->setAlign('vModul', 'left');
        $grid->setLabel('vModul', 'Nama Modul');

        $grid->setWidth('mKeterangan', '200');
        $grid->setAlign('mKeterangan', 'left');
        $grid->setLabel('mKeterangan', 'Keterangan');

        $grid->setWidth('iValidate', '100');
        $grid->setAlign('iValidate', 'center');
        $grid->setLabel('iValidate', 'Validasi');

        $grid->setWidth('iSubmit', '100');
        $grid->setAlign('iSubmit', 'center');
        $grid->setLabel('iSubmit', 'Status Submit');

        $grid->setWidth('dSubmit', '100');
        $grid->setAlign('dSubmit', 'center');
        $grid->setLabel('dSubmit', 'Tgl Submit');

        $grid->setLabel('mFeedback', 'Feedback');

        $grid->setLabel('form_history', 'Summary');

        $grid->setWidth('del', '20');
        $grid->setAlign('del', 'center');
        $grid->setLabel('del','del');

        $grid->setLabel('upload_dok', 'Upload Dokumen');
        $grid->setLabel('vModul', 'Nama Modul');
        $grid->setLabel('mKeterangan', 'Keterangan');
        $grid->setLabel('validasi', 'Validasi');
        $grid->setLabel('parameter', 'Parameter Validasi');

        // join tabel
        // $grid->setJoinTable('purchasing.poir_header', 'poir_header.cPONumber = biflow_fu.vKodeRef', 'inner');
        // $grid->setJoinTable('hrd.employee', 'employee.cNip = poir_header.cCreatedBy', 'inner');
        // $grid->setJoinTable('cost.supplier', 'supplier.id = poir_header.iSupplierId', 'inner');
    
        $grid->setQuery('biflow_fu.lDeleted = 0 ', null);

        $this->cKode = $this->input->get('cKode');            
        $grid->setInputGet('cKode', $this->cKode);            
        $grid->setQuery('biflow_fu.cKode', $this->input->get('cKode'));
        $grid->setForeignKey($this->input->get('cKode'));

        $this->aidiH    = $this->input->get('id_head');            
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
                // print_r($this->input->get());exit;
                $post     = $this->input->post();
                $isUpload = $this->input->get('isUpload');
                $lastId   = $this->input->get('lastId');

                $nowDate = date('Y_m_d_H_i_s');

                // get cKode_fu
                $sql = "SELECT a.cKode_fu
                        FROM hrd.biflow_fu a
                        WHERE a.id = '".$lastId."' ";
                $row = $this->db->query($sql)->row_array();
                $cKode_fu = $row['cKode_fu'];

                $cKode_fu_log = $cKode_fu . '-HIST-' . $nowDate;

                if($isUpload){
                    $filePath   = 'bi_flow';
                    $pathf      = "files/erpss/" . date("Ym") . "/" . $filePath;
                    $path       = realpath($pathf);

                    $fpath  = $pathf . "/" . $lastId;

                    $ltDir  = '';
                    $patharr = explode("/", $pathf);
                    $ii = 0;
                    foreach ($patharr as $kpp => $vpp) {
                        $sasa = array();
                        if ($ii <> 0) {
                            for ($i = 0; $i <= $ii; $i++) {
                                if ($i <> 0) {
                                    $sasa[] = $patharr[$i];
                                }
                            }
                            $papat = implode("/", $sasa);
                            $path = realpath("files");
                            if (!file_exists($path . "/" . $papat)) {
                                if (!mkdir($path . "/" . $papat, 0777, true)) {
                                    die('Failed upload, try again!' . $papat);
                                }
                            }
                        }
                        $ii++;
                    }

                    $path   = realpath($pathf);

                    if (!file_exists($path . "/" . $lastId)) {
                        if (!mkdir($path . "/" . $lastId, 0777, true)) {
                            die('Failed upload, try again!----' . $path);
                        }
                    }

                    $mKeterangan = array();
                    foreach ($_POST as $key => $value) {
                        if ($key == 'mKeterangan') {
                            array_push($mKeterangan, $value);
                        }
                    }

                    // FILE UPLOAD DOKUMEN
                    $i = 0;
                    if (isset($_FILES['upload_dok_upload_file'])) {
                        foreach ($_FILES['upload_dok_upload_file']["error"] as $key => $error) {
                            if ($error == UPLOAD_ERR_OK) {
                                $tmp_name           = $_FILES['upload_dok_upload_file']["tmp_name"][$key];
                                $name               = $_FILES['upload_dok_upload_file']["name"][$key];
                                $data['filename']   = $name;
                                $data['dInsertDate'] = date('Y-m-d H:i:s');
                                $filenameori        = $name;
                                $now_u              = date('Y_m_d__H_i_s');
                                $name_generate      = $this->generateFilename($name, $i);
                                $file_generate      = $pathf.'/'.$lastId.'/'.$name_generate;
                                $file_path          = $path.'/'.$lastId.'/'.$name_generate;

                                if (move_uploaded_file($tmp_name, $file_path)) {
                                    $datainsert                         = array();
                                    $datainsert['cKode_fu']            = $cKode_fu;
                                    $datainsert['idHeader_File']        = $lastId;
                                    $datainsert['dCreate']              = date('Y-m-d H:i:s');
                                    $datainsert['cCreate']              = $this->user->gNIP;
                                    $datainsert['vFilename']            = $name;
                                    $datainsert['vFilename_generate']   = $file_generate;
                                    $datainsert['tKeterangan']          = $mKeterangan[$i];

                                    $insertdataa                         = array();
                                    $insertdataa['cKode_fu']             = $cKode_fu;
                                    $insertdataa['cKode_fu_log']         = $cKode_fu_log;
                                    $insertdataa['idHeader_File']        = $lastId;
                                    $insertdataa['dCreate']              = date('Y-m-d H:i:s');
                                    $insertdataa['cCreate']              = $this->user->gNIP;
                                    $insertdataa['vFilename']            = $name;
                                    $insertdataa['vFilename_generate']   = $file_generate;
                                    $insertdataa['tKeterangan']          = $mKeterangan[$i];

                                    if (($this->db->insert('hrd.biflow_fu_file', $datainsert)) && ($this->db->insert('hrd.biflow_fu_file_log', $insertdataa))){
                                    } else {
                                        echo $this->db->last_query();
                                    }

                                    $i++;
                                } else {
                                    echo "Upload ke folder gagal";
                                }
                            }
                        }
                    }

                    $r['message']   = "Data Berhasil Disimpan";
                    $r['status']    = TRUE;
                    $r['last_id']   = $this->input->get('lastId');
                    echo json_encode($r);exit();
                }else{
                    echo $grid->saved_form();
                }

                break;
            case 'update':
                    $grid->render_form($this->input->get('id'));
                    break;
            case 'updateproses':
                $post     = $this->input->post();
                $isUpload = $this->input->get('isUpload');
                $lastId   = $this->input->get('lastId');

                $nowDate = date('Y_m_d_H_i_s');

                // get cKode_fu
                $sql = "SELECT a.cKode_fu
                        FROM hrd.biflow_fu a
                        WHERE a.id = '".$lastId."' ";
                $row = $this->db->query($sql)->row_array();
                $cKode_fu = $row['cKode_fu'];

                $cKode_fu_log = $cKode_fu . '-HIST-' . $nowDate;

                if($isUpload){
                    $filePath   = 'bi_flow';
                    $pathf      = "files/erpss/" . date("Ym") . "/" . $filePath;
                    $path       = realpath($pathf);

                    $fpath  = $pathf . "/" . $lastId;

                    $ltDir  = '';
                    $patharr = explode("/", $pathf);
                    $ii = 0;
                    foreach ($patharr as $kpp => $vpp) {
                        $sasa = array();
                        if ($ii <> 0) {
                            for ($i = 0; $i <= $ii; $i++) {
                                if ($i <> 0) {
                                    $sasa[] = $patharr[$i];
                                }
                            }
                            $papat = implode("/", $sasa);
                            $path = realpath("files");
                            if (!file_exists($path . "/" . $papat)) {
                                if (!mkdir($path . "/" . $papat, 0777, true)) {
                                    die('Failed upload, try again!' . $papat);
                                }
                            }
                        }
                        $ii++;
                    }

                    $path   = realpath($pathf);

                    if (!file_exists($path . "/" . $lastId)) {
                        if (!mkdir($path . "/" . $lastId, 0777, true)) {
                            die('Failed upload, try again!----' . $path);
                        }
                    }

                    $mKeterangan = array();
                    foreach ($_POST as $key => $value) {
                        if ($key == 'mKeterangan') {
                            array_push($mKeterangan, $value);
                        }
                    }

                    // FILE UPLOAD DOKUMEN
                    $i = 0;
                    if (isset($_FILES['upload_dok_upload_file'])) {

                        $sql = "SELECT * 
                                        FROM biflow_fu_log
                                        WHERE cKode_fu =  '".$cKode_fu."'
                                        ORDER BY id DESC
                                        LIMIT 1
                                    ";
                        $get_id = $this->db->query($sql)->row_array();

                        // Delete upload dokumen sebelumnya
                        $update['iDeleted'] = 1;
                        $update['dUpdate']  = date('Y-m-d H:i:s');
                        $update['cUpdate']  = $this->user->gNIP;

                        $this->db->where(array('cKode_fu' => $cKode_fu, 'iDeleted' => 0));
                        $this->db->update('hrd.biflow_fu_file', $update);

                        $up['iDeleted']             = 0;
                        $up['dUpdate']              = date('Y-m-d H:i:s');
                        $up['cUpdate']              = $this->user->gNIP;

                        $this->db->where(array('id' => $get_id['id'], 'cKode_fu' => $cKode_fu, 'iDeleted' => 0));
                        $this->db->update('hrd.biflow_fu_file_log', $up);

                        foreach ($_FILES['upload_dok_upload_file']["error"] as $key => $error) {
                            if ($error == UPLOAD_ERR_OK) {
                                $tmp_name           = $_FILES['upload_dok_upload_file']["tmp_name"][$key];
                                $name               = $_FILES['upload_dok_upload_file']["name"][$key];
                                $data['filename']   = $name;
                                $data['dInsertDate'] = date('Y-m-d H:i:s');
                                $filenameori        = $name;
                                $now_u              = date('Y_m_d__H_i_s');
                                $name_generate      = $this->generateFilename($name, $i);
                                $file_generate      = $pathf.'/'.$lastId.'/'.$name_generate;
                                $file_path          = $path.'/'.$lastId.'/'.$name_generate;

                                if (move_uploaded_file($tmp_name, $file_path)) {
                                    $datainsert                         = array();
                                    $datainsert['cKode_fu']            = $cKode_fu;
                                    $datainsert['idHeader_File']        = $lastId;
                                    $datainsert['dCreate']              = date('Y-m-d H:i:s');
                                    $datainsert['cCreate']              = $this->user->gNIP;
                                    $datainsert['vFilename']            = $name;
                                    $datainsert['vFilename_generate']   = $file_generate;
                                    $datainsert['tKeterangan']          = $mKeterangan[$i];

                                    $insertdataa                         = array();
                                    $insertdataa['cKode_ifm']            = $cKode_ifm;
                                    $insertdataa['cKode_ifm_log']        = $cKode_ifm_log;
                                    $insertdataa['idHeader_File']        = $lastId;
                                    $insertdataa['dCreate']              = date('Y-m-d H:i:s');
                                    $insertdataa['cCreate']              = $this->user->gNIP;
                                    $insertdataa['vFilename']            = $name;
                                    $insertdataa['vFilename_generate']   = $file_generate;
                                    $insertdataa['tKeterangan']          = $mKeterangan[$i];

                                    if (($this->db->insert('hrd.biflow_fu_file', $datainsert)) && ($this->db->insert('hrd.biflow_fu_file_log', $insertdataa))){
                                    } else {
                                        echo $this->db->last_query();
                                    }

                                    $i++;
                                } else {
                                    echo "Upload ke folder gagal";
                                }
                            }
                        }
                    }

                    $r['message']   = "Data Berhasil Disimpan";
                    $r['status']    = TRUE;
                    $r['last_id']   = $this->input->get('lastId');
                    echo json_encode($r);exit();
                }else{
                    echo $grid->updated_form();
                }

                break;
            case 'download':
                $name = $this->input->get('name');
                $tempat = $this->input->get('path');

                ob_start();
                if(!empty($tempat) && file_exists($tempat)){ /*check keberadaan file*/
                    header("Pragma:public");
                    header("Expired:0");
                    header("Cache-Control:must-revalidate");
                    header("Content-Control:public");
                    header("Content-Description: File Transfer");
                    header("Content-Type: application/octet-stream");
                    header("Content-Disposition: inline; filename=\"".basename($tempat)."\"");
                    header("Content-Transfer-Encoding:binary");
                    header("Content-Length:".filesize($tempat));
                    while (ob_get_level()) {
                        ob_end_clean();
                    }
                    readfile($tempat);
                    exit();
                }else{
                    echo "The File does not exist.";
                }
                break;
            case 'download_x':
                $this->load->helper('download');
                $name = $this->input->get('name');                
                $tempat = $this->input->get('path');
                $path = file_get_contents('./'.$tempat);
                force_download($name, $path);

                break;   
            case 'delete':
                echo $grid->delete_row();
                break;
            case 'delete_referensi':
                echo $this->deleteDetailItemReceiving();
                break; 
            case 'download_xx':
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

    function searchBox_uat_fu_vKodeRef($rowData, $id) {
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

    function listBox_uat_fu_upload_dok($value, $pk, $name, $rowData) {
        $rowData = get_object_vars($rowData);

        $sql = "SELECT *
                FROM hrd.biflow_fu_file a
                WHERE a.cKode_fu = '".$rowData['cKode_fu']."'
                AND a.iDeleted = 0
                ORDER BY a.id DESC ";
        $row = $this->db->query($sql)->row_array();

        $linknya = '';
        if (!empty($row)) {
            if(file_exists($row['vFilename_generate'])) {
                $link = base_url().'processor/'.$this->urlpath.'?action=download&path='.addslashes($row['vFilename_generate']).'&name='.addslashes($row['vFilename']);
                $linknya = '<a class="" href="javascript:;" onclick="window.open(\''.$link.'\', \'_blank\')">'.$row['vFilename'].'</a> <br><br>';
            }else{
                $linknya = $row['vFilename'].' [No File] <br><br>';
            }
        }

        $o = $linknya;

        return $o;
    }

    function listBox_uat_fu_validasi($value, $pk, $name, $rowData){
        $sql = "SELECT COUNT(case when a.iStatus_bi = 1 then 1 else null END) AS jumlah_no
                ,COUNT(case when a.iStatus_bi = 2 then 1 else null END) AS jumlah_yes
                ,COUNT(*) AS jumlah_total
                FROM hrd.biflow_fu_param a
                WHERE a.lDeleted = 0
                AND a.cKode_fu = '".$rowData->cKode_fu."' ";
        $row = $this->db->query($sql)->row_array();

        $o = 'Jumlah Yes: '.$row['jumlah_yes'];
        $o .= '<br>Jumlah No: '.$row['jumlah_no'];
        $o .= '<br>Jumlah Total: '.$row['jumlah_total'];

        return $o;
    }

    /*function listBox_bi_flow_iValidate($value, $pk, $name, $rowData){
        $sql = "";
        $row = $this->db->query($sql)->row_array();

        $o = 'Jumlah Yes: '.$row['jumlah_yes'];
        $o .= '<br>Jumlah No: '.$row['jumlah_no'];
        $o .= '<br>Jumlah Total: '.$row['jumlah_total'];

        return $o;
    }*/

    function listBox_uat_fu_del($value, $pk, $name, $rowData){
        // print_r($this->input->get());exit;
        $id_head = $this->input->get('aidiH');
        $cKode = $rowData->cKode;

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
        $sqlCek     = "SELECT a.iSubmit_uat
                        FROM ".$this->header_table." a
                        WHERE a.cKode = '".$row['cKode']."' ";
        $cekSubmit  = $this->db->query($sqlCek)->row_array();

        $submitFisik = (!empty($cekSubmit))?$cekSubmit['iSubmit_uat']:0;

        // Cek Submit Maintable
        $this->db->where($this->main_table_pk, $row[$this->main_table_pk]);
        $datanya = $this->db->get($this->main_table)->row_array();
            
        $url        = base_url()."processor/".$this->urlpath."?action=update&cKode=".$row['cKode']."&foreign_key=".$row['cKode']."&id=".$row[$this->main_table_pk]."&modul_id=".$this->input->get('modul_id')."&group_id=".$this->input->get('group_id')."&company_id=".$this->input->get('company_id');
        $urlView        = base_url()."processor/".$this->urlpath."?action=view&cKode=".$row['cKode']."&foreign_key=".$row['cKode']."&id=".$row[$this->main_table_pk]."&modul_id=".$this->input->get('modul_id')."&group_id=".$this->input->get('group_id')."&company_id=".$this->input->get('company_id');
        $urlDelete        = base_url()."processor/".$this->urlpath."?action=delete&cKode=".$row['cKode']."&foreign_key=".$row['cKode']."&id=".$row[$this->main_table_pk]."&modul_id=".$this->input->get('modul_id')."&group_id=".$this->input->get('group_id')."&company_id=".$this->input->get('company_id');

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

        if ($submitFisik == 1 || $datanya['iSubmit'] == 1){
            //unset($actions['delete']);
            unset($actions['edit']);
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

        if ( $cekSubmit['iSubmit_uat'] == 1){
            unset($button['create']);
            $button['create'] = 'Data sudah disubmit';
        } else {
            array_unshift($button, $btn_baru);
        }

        return $button;
        
    }

    function insertBox_uat_fu_mKeterangan($field, $id) {
        $o = '<input type="hidden" name="cKode" value="'.$this->input->get('cKode').'">';
        $o .= '<input type="hidden" name="isdraft" id="isdraft">';
        $o .= '<div class="col-sm-6">';
        $o .= '<textarea id="'.$id.'" name="'.$field.'" class="ckeditorField form-control" colspan="2"></textarea>';
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
    
    function updateBox_uat_fu_mKeterangan($field, $id, $value, $rowData) {
        $o = '<input type="hidden" name="cKode" value="'.$this->input->get('cKode').'">';
        $o .= '<input type="hidden" name="isdraft" id="isdraft">';
        $o .= '<div class="col-sm-6">';
        $o .= '<textarea id="'.$id.'" name="'.$field.'" class="ckeditorField form-control" colspan="2">'.nl2br($value).'</textarea>';
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

    function insertBox_uat_fu_upload_dok($field, $id)
    {   
        $o = '<input type="file" id="'.$id.'" name="'.$field.'_upload_file[]" class="input_rows1 fileupload multi multifile" size="15" >';

        return $o;
    }

    function updateBox_uat_fu_upload_dok($field, $id, $value, $rowData)
    {   
        // get data file
        $sql = "SELECT *
                FROM hrd.biflow_fu_file a
                WHERE a.cKode_fu = '".$rowData['cKode_fu']."'
                AND a.iDeleted = 0
                ORDER BY a.id DESC ";
        $row = $this->db->query($sql)->row_array();
        $required = 'required';
        if(!empty($row)){
            $required = '';
        }

        $linknya = '';
        if (!empty($row)) {
            if(file_exists($row['vFilename_generate'])) {
                $link = base_url().'processor/'.$this->urlpath.'?action=download&path='.addslashes($row['vFilename_generate']).'&name='.addslashes($row['vFilename']);
                $linknya = '<a class="" href="javascript:;" onclick="window.open(\''.$link.'\', \'_blank\')">'.$row['vFilename'].'</a> <br><br>';
            }else{
                $linknya = $row['vFilename'].' [No File] <br><br>';
            }
        }

        $o = $linknya;
        $o .= '<input type="file" id="'.$id.'" name="'.$field.'_upload_file[]" class="input_rows1 fileupload multi multifile" size="15" >';
        $o .= '<input type="hidden" name="cKode_fu" value="'.$rowData['cKode_fu'].'">';

        return $o;
    }

    function insertBox_uat_fu_parameter($field, $id) {
        $data['table']      = "hrd.biflow_fu_param";
        $data['table_key']  = "cKode_fu";
        $data['id']         = $id;
        $data['field']      = $field;
        $data['url']        = $this->url;
        $o = $this->load->view('partial/modul/biflow_tabel_validasi', $data, TRUE);

        return $o;
    }
    
    function updateBox_uat_fu_parameter($field, $id, $value, $rowData) {
        $data['table']      = "hrd.biflow_fu_param";
        $data['table_key']  = "cKode_fu";
        $data['id']         = $id;
        $data['field']      = $field;
        $data['url']        = $this->url;
        $data['rowData']    = $rowData;
        $o = $this->load->view('partial/modul/biflow_tabel_validasi', $data, TRUE);

        return $o;
    }

    function insertBox_uat_fu_dSubmit($field, $id) {
        $o = '<b>Auto after submit</b>';

        return $o;
    }
    
    function updateBox_uat_fu_dSubmit($field, $id, $value, $rowData) {
        $o = '<b>Auto after submit</b>';
        if(!empty($rowData[$field])){
            $o = $rowData[$field];
        }

        return $o;
    }

    function insertBox_uat_fu_mFeedback($field, $id) {
       
        $o = '<b> - </b>';

        return $o;
    }

    function updateBox_uat_fu_mFeedback($field, $id, $value, $rowData) {
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

        // print_r($getdata['iApprove']);exit;

        // if(($getdata['iApprove'] == 0 && $datanya['iSubmit'] == 0) || ($getdata['iApprove'] == 2 && $datanya['iSubmit'] = 0)){
        if($getdata['iApprove'] == 1){

            $o = '<input type="hidden" name="cKode" value="'.$this->input->get('cKode').'">';
            $o .= '<input type="hidden" name="isdraft" id="isdraft">';
            // $o .= '<textarea id="'.$id.'" name="'.$field.'" class="ckeditorField form-control required-ckeditor" colspan="2">'.nl2br($value).'</textarea>';
            $o .= '<textarea name="'.$field.'" id="'.$id.'" style="width: 650px; height: 100px;" size="250" maxlength ="250">'.nl2br($value).'</textarea>';

        }else{

            $o = "<b> - </b>";

        }
        return $o;
        // print_r($datanya['iSubmit']);exit;
    }

    function insertBox_uat_fu_form_history($field, $id)
    {
        $o = '<b> - </b>';

        return $o;
    }

    function updateBox_uat_fu_form_history($field, $id, $value, $rowData)
    {
        // print_r($rowData);-
        $data['id'] = $id;
        $data['field'] = $field;

        $post = $this->input->get();
        $cKode = $post['cKode'];

        $sql = 'SELECT fu.mKeterangan, fu.dSubmit, ff.vFilename, fu.mFeedback, ff.vFilename_generate,
                    -- if(fu.iValidate = 1, "Valid", if(fu.iValidate = 2, "Not Valid", "Need To Be Validate")) AS Validasi,
                    if(fu.iSubmit = 1 AND fu.lDeleted = 0, "Submitted", IF (fu.lDeleted = 1, "Deleted", "Need To Be Submit")) AS Status
                FROM hrd.biflow_fu_log fu
                LEFT JOIN hrd.biflow_fu_file_log ff ON ff.cKode_fu = fu.cKode_fu 
                WHERE fu.cKode_fu = "' . $rowData['cKode_fu'] . '"
                AND fu.iSubmit NOT IN (0)
                GROUP BY fu.id
                ORDER BY fu.dSubmit ASC
        ';

        $datasql = $this->db->query($sql)->result_array();
        $data['datasql'] = $datasql;

        $o = $this->load->view('partial/modul/'.$this->url.'_history', $data, true);
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

        // validasi keterangan
        if(empty($postData['mKeterangan'])){
            $data['status'] = false;
            $data['message'] = 'Keterangan Required!';
            echo json_encode($data);exit;
        }

        $postData['cKode_fu']  = $this->generateCodeDetail($postData['cKode']);

        return $postData;
    }

    function before_update_processor($row, $postData) {
        // print_r($postData);
        $postData['dUpdate']    = date('Y-m-d H:i:s');
        $postData['cUpdate']    = $this->user->gNIP;

        $id = $postData['uat_fu_id'];
        $feedback = $postData['mFeedback'];
        $ket = $postData['mKeterangan'];

        $nowDate = date('Y_m_d_H_i_s');

        // Insert History

        $datanya  = $this->db->get_where($this->maintable, array($this->main_table_pk => $id))->row_array();

        $cKode = $datanya['cKode'];
        $dup = $postData['dUpdate'];
        $kHist = $datanya['cKode_history'];
        $kode_fu = $datanya['cKode_fu'];

        $cKode_log = $cKode . '-HIST-' . $nowDate;

        // echo print_r($postData);exit;
        $sql2 = "SELECT *
                FROM biflow_fu f
                WHERE f.lDeleted = 0
                AND f.cKode = '" . $cKode . "'
                AND f.cKode_fu = '" . $kode_fu . "'";
        $rows = $this->db->query($sql2)->result_array();
        // echo print_r($datainsert);
        // exit;

        if ($postData['isdraft'] == 'true') {
            $postData['iSubmit'] = 0;
            foreach ($rows as $key => $row) {
                $insert = array();
                foreach ($row as $k => $r) {
                    // $insert[$keyHist] = $kodeHist;
                    $insert['cKode_fu_log'] = $cKode_log;
                    $insert[$k] = $r;
                    $insert['mFeedback'] = $feedback;
                    $insert['mKeterangan'] = $ket;
                    // $insert['dSubmit'] = $dup;
                }
                unset($insert['id']);
                $this->db->insert('hrd.biflow_fu_log', $insert);
    
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
                    $update['cKode_fu_log'] = $cKode_log;
                    $update[$k] = $r;
                    $update['mFeedback'] = $feedback;
                    $update['mKeterangan'] = $ket;
                    $update['dSubmit'] = $dup;
                    $update['iSubmit'] = 1;
                    $update['cSubmit'] = $this->user->gNIP;
                }

                unset($update['id']);
                $sql = "SELECT * 
                                FROM biflow_fu_log
                                WHERE cKode_fu =  '".$kode_fu."'
                                ORDER BY id DESC
                                LIMIT 1
                            ";
                $get_id = $this->db->query($sql)->row_array();

                // echo print_r($get_id);
                // exit;
                $this->db->insert('hrd.biflow_fu_log', $update);
                // $this->db->where(array('id' =>  $get_id['id'], 'cKode_fu' =>  $kode_fu, 'lDeleted' => 0/*, 'cKode_br_log' => */));
                // $this->db->update('hrd.biflow_fu_log', $update);
                // echo print_r($update);
                // exit;
            }
        }

        // validasi keterangan
        if(empty($postData['mKeterangan'])){
            $data['status'] = false;
            $data['message'] = 'Keterangan Required!';
            echo json_encode($data);exit;
        }

        return $postData; 
    }    

    function after_insert_processor($fields, $id, $postData){

        $postData = $this->input->post();

    	$postData['dCreate'] 	= date('Y-m-d H:i:s');
        $postData['cCreate']	= $this->user->gNIP;

        $id = $postData['uat_fu_id'];
        $feedback = $postData['mFeedback'];
        $ket = $postData['mKeterangan'];

        $nowDate = date('Y_m_d_H_i_s');
        
        // Insert History fu

        $datanya  = $this->db->get_where($this->maintable, array($this->main_table_pk => $id))->row_array();

        $cKode = $postData['cKode'];
        $dup = $postData['dUpdate'];
        $kHist = $datanya['cKode_history'];
        $kode_fu = $datanya['cKode_fu'];

        $cKode_log = $cKode . '-HIST-' . $nowDate;

        // echo print_r($postData);exit;
        $sql2 = "SELECT *
                FROM biflow_fu f
                WHERE f.lDeleted = 0
                AND f.cKode = '" . $cKode . "'
                ORDER BY f.id DESC
                LIMIT 1 ";
        $rows = $this->db->query($sql2)->result_array();
        // echo print_r($rows);
        // exit;


        if ($postData['isdraft'] == 'true') {
            // $postData['iSubmit'] = 0;
            foreach ($rows as $key => $row) {
                $insert = array();
                foreach ($row as $k => $r) {
                    // $insert[$keyHist] = $kodeHist;
                    $insert['cKode_fu_log'] = $cKode_log;
                    $insert[$k] = $r;
                    $insert['mFeedback'] = $feedback;
                    $insert['mKeterangan'] = $ket;
                    // $insert['dSubmit'] = $dup;
                }
                unset($insert['id']);
                $this->db->insert('hrd.biflow_fu_log', $insert);
    
                // echo print_r($insert);
                // exit;
    
            }
            if(empty($postData['dSubmit'])){
                unset($postData['dSubmit']);
            }
        } else {
            // print_r($postData);

            // $postData['iSubmit'] = 1;
            // $postData['dSubmit'] = date('Y-m-d H:i:s');
            // $postData['cSubmit'] = $this->user->gNIP;
            foreach ($rows as $key => $row) {
                $update = array();
                foreach ($row as $k => $r) {
                    // $update[$keyHist] = $kodeHist;
                    $update['cKode_fu_log'] = $cKode_log;
                    $update[$k] = $r;
                    $update['mFeedback'] = $feedback;
                    $update['mKeterangan'] = $ket;
                    $update['iSubmit'] = 1;
                    $update['dSubmit'] = date('Y-m-d H:i:s');
                    $update['cSubmit'] = $this->user->gNIP;
                }

                unset($update['id']);
                $sql = "SELECT * 
                        FROM biflow_fu_log
                        WHERE cKode_fu =  '".$kode_fu."'
                        ORDER BY id DESC
                        LIMIT 1
                            ";
                $get_id = $this->db->query($sql)->row_array();

                // echo print_r($get_id);
                // exit;
                $this->db->insert('hrd.biflow_fu_log', $update);
                // $this->db->where(array('id' =>  $get_id['id'], 'cKode_fu' =>  $kode_fu, 'lDeleted' => 0/*, 'cKode_br_log' => */));
                // $this->db->update('hrd.biflow_fu_log', $update);
                // echo print_r($update);
                // exit;
            }
            // if ($postData['iSubmit'] == 1) {
                
                // Send Notifikasi
                $subject= $this->title . ' -> Submit';
                $content= 'Diberitahukan telah ada proses <b>Submit</b> ' . $this->title;
    
                $sqlDataNotif = "SELECT ss.id AS 'SSID',
                                ss.problem_subject AS 'Project Name',
                                CONCAT_WS(' - ', e1.cNip, e1.vName) AS 'Requestor',
                                CONCAT_WS(' - ', e2.cNip, e2.vName) AS 'Project Manager',
                                CONCAT_WS(' - ', e3.cNip, e3.vName) AS 'PIC BI',
                                CONCAT_WS(' - ', e4.cNip, e4.vName) AS 'System Analyst'
                                FROM hrd.biflow_fu bf
                                JOIN hrd.biflow bi ON bi.cKode = bf.cKode
                                JOIN hrd.ss_raw_problems ss ON ss.id = bi.raw_id
                                JOIN hrd.ss_raw_pic rp ON rp.rawid = ss.id 
                                JOIN hrd.employee e1 ON e1.cNip = bi.cRequestor
                                JOIN hrd.employee e2 ON e2.cNip = rp.pic
                                JOIN hrd.employee e3 ON e3.cNip = bi.cPic_bi
                                JOIN hrd.employee e4 ON e4.cNip = bi.cSystem_analyst
                                WHERE bi.lDeleted = 0 AND ss.Deleted = 'No' AND rp.Deleted = 'No' 
                                AND e1.lDeleted = 0 AND e2.lDeleted = 0 AND e3.lDeleted = 0 AND e4.lDeleted = 0
                                AND rp.iRoleId = 1
                                -- AND ss.id = '".$post['raw_id']."' 
                                AND bf.cKode = '".$postData['cKode']."' 
                                GROUP BY ss.id ";
                $data  = $this->db->query($sqlDataNotif)->row_array();
    
                // get data maintable
                $sqlH = "SELECT bi.*, rp.pic AS project_manager, e.cUpper AS upper_bi
                            FROM hrd.biflow_fu bf
                            JOIN hrd.biflow bi ON bi.cKode = bf.cKode
                            JOIN hrd.ss_raw_pic rp ON rp.rawid = bi.raw_id
                            JOIN hrd.employee e ON e.cNip = bi.cPic_bi
                            WHERE bi.lDeleted = 0
                            AND bf.cKode = '".$postData['cKode']."' 
                            GROUP BY bf.cKode ";
                $rowH = $this->db->query($sqlH)->row_array();
                
                $nipCc = $this->lib_utilitas->nipNotifProjectRequirement($this->user->gNIP, $data['SSID']);
                
                $to    = $rowH['cSystem_analyst'].",".$rowH['cPic_bi'].",".$this->user->gNIP;
                $cc    = $rowH['project_manager'].",".$rowH['upper_bi'];
                
                $this->lib_erpss->generateAndSendNotificationPersonalUat($team, $subject, $content, $data, $to, $cc);
    
                // print_r($postData);
    
                // // Insert Log Activity
                // $activities = $this->lib_sub_core->get_current_module_activities($this->modul_id, $postData[$this->main_table_pk]);
                // if ($postData['isdraft'] != true && count($activities) > 0) {
                //     $act = $activities[0];
                //     $this->lib_sub_core->InsertActivityModule($this->ViewUPB($id), $this->modul_id, $id, $act['iM_activity'], $act['iSort']);
                // }
            // }
        }

        $postData['cKode_fu']   = $this->generateCodeDetail($postData['cKode']);

        return $postData; 

    }

    function after_update_processor($fields, $id, $postData) {
        
        $post = $this->input->post();
        // print_r($postData);

        if ($postData['iSubmit'] == 1) {
            // Send Notifikasi
            $subject= $this->title . ' -> Submit';
            $content= 'Diberitahukan telah ada proses <b>Submit</b> ' . $this->title;

            $sqlDataNotif = "SELECT ss.id AS 'SSID',
                            ss.problem_subject AS 'Project Name',
                            CONCAT_WS(' - ', e1.cNip, e1.vName) AS 'Requestor',
                            CONCAT_WS(' - ', e2.cNip, e2.vName) AS 'Project Manager',
                            CONCAT_WS(' - ', e3.cNip, e3.vName) AS 'PIC BI',
                            CONCAT_WS(' - ', e4.cNip, e4.vName) AS 'System Analyst'
                            FROM hrd.biflow_fu bf
                            JOIN hrd.biflow bi ON bi.cKode = bf.cKode
                            JOIN hrd.ss_raw_problems ss ON ss.id = bi.raw_id
                            JOIN hrd.ss_raw_pic rp ON rp.rawid = ss.id 
                            JOIN hrd.employee e1 ON e1.cNip = bi.cRequestor
                            JOIN hrd.employee e2 ON e2.cNip = rp.pic
                            JOIN hrd.employee e3 ON e3.cNip = bi.cPic_bi
                            JOIN hrd.employee e4 ON e4.cNip = bi.cSystem_analyst
                            WHERE bi.lDeleted = 0 AND ss.Deleted = 'No' AND rp.Deleted = 'No' 
                            AND e1.lDeleted = 0 AND e2.lDeleted = 0 AND e3.lDeleted = 0 AND e4.lDeleted = 0
                            AND rp.iRoleId = 1
                            -- AND ss.id = '".$post['raw_id']."' 
                            AND bf.cKode = '".$postData['cKode']."' 
                            GROUP BY ss.id ";
            $data  = $this->db->query($sqlDataNotif)->row_array();

            // get data maintable
            $sqlH = "SELECT bi.*, rp.pic AS project_manager, e.cUpper AS upper_bi
                        FROM hrd.biflow_fu bf
                        JOIN hrd.biflow bi ON bi.cKode = bf.cKode
                        JOIN hrd.ss_raw_pic rp ON rp.rawid = bi.raw_id
                        JOIN hrd.employee e ON e.cNip = bi.cPic_bi
                        WHERE bi.lDeleted = 0
                        AND bf.cKode = '".$postData['cKode']."' 
                        GROUP BY bf.cKode ";
            $rowH = $this->db->query($sqlH)->row_array();
            
            $nipCc = $this->lib_utilitas->nipNotifProjectRequirement($this->user->gNIP, $data['SSID']);
            
            $to    = $rowH['cSystem_analyst'].",".$rowH['cPic_bi'].",".$this->user->gNIP;
            $cc    = $rowH['project_manager'].",".$rowH['upper_bi'];
            
            $this->lib_erpss->generateAndSendNotificationPersonalUat($team, $subject, $content, $data, $to, $cc);

            // print_r($postData);

            // // Insert Log Activity
            // $activities = $this->lib_sub_core->get_current_module_activities($this->modul_id, $postData[$this->main_table_pk]);
            // if ($postData['isdraft'] != true && count($activities) > 0) {
            //     $act = $activities[0];
            //     $this->lib_sub_core->InsertActivityModule($this->ViewUPB($id), $this->modul_id, $id, $act['iM_activity'], $act['iSort']);
            // }
        }
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

        // Button Tampil Sesuai Departemen
        $arrDeptAssigned = array('MIS');
        $arrTeam = explode(',', $this->team);
        if(array_intersect($arrDeptAssigned, $arrTeam)){
            $buttons['save_back'] = $save_draft.$save.$js;
        }else{
            $buttons['save_back'] = '<span style="color:red;" title="'.implode('_', $arrDeptAssigned).'">You\'re Dept not Authorized</span>';
        }

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

        $sqlCek     = 'SELECT t.iSubmit_uat
                        FROM '.$this->main_table.' f
                        JOIN '.$this->header_table.' t ON f.cKode = t.cKode
                        WHERE f.id = '.$rowData[$this->main_table_pk];
        $cekSubmit  = $this->db->query($sqlCek)->row_array();
        $submitFisik= (!empty($cekSubmit))?$cekSubmit['iSubmit_uat']:0;

        $data['url'] = $this->url;
        $js = $this->load->view('js/custom_js', $data, TRUE);

        $update_draft = '<button type="button"
                            name="button_update_draft_'.$this->url.'"
                            id="button_update_draft_'.$this->url.'"
                            class="ui-button-text icon-save"
                            onclick="javascript:update_btn_'.$this->url.'(\''.$this->url.'\', \' '.base_url().'processor/'.$this->urlpath.'?draft=true&cKode='.$this->input->get('foreign_key').'&company_id='.$this->input->get('company_id').'&group_id='.$this->input->get('group_id').'&modul_id='.$this->input->get('modul_id').'\', this, true)">Update As Draft</button>'; 

        $update = '<button type="button"
                    name="button_update_draft_'.$this->url.'"
                    id="button_update_draft_'.$this->url.'"
                    class="ui-button-text icon-save"
                    onclick="javascript:update_btn_'.$this->url.'(\''.$this->url.'\', \' '.base_url().'processor/'.$this->urlpath.'?draft=false&cKode='.$this->input->get('foreign_key').'&company_id='.$this->input->get('company_id').'&group_id='.$this->input->get('group_id').'&modul_id='.$this->input->get('modul_id').'\', this, false)">Update & Submit</button>';

        // Button Tampil Sesuai Departemen
        $arrDeptAssigned = array('MIS');
        $arrTeam = explode(',', $this->team);
        if(array_intersect($arrDeptAssigned, $arrTeam)){
            $buttons['update_back'] = $update_draft.$update.$js;
        }else{
            $buttons['update_back'] = '<span style="color:red;" title="'.implode('_', $arrDeptAssigned).'">You\'re Dept not Authorized</span>';
        }
            
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

            
        if ($this->input->get('action') == 'view' || $rowData['iSubmit'] == 1 || $submitFisik == 1){
            unset($buttons['update_back']);
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
        $sql = "SELECT a.cKode_fu
                FROM ".$this->main_table." a
                WHERE a.cKode = '".$kodeHeader."'
                ORDER BY CHAR_LENGTH(a.cKode_fu) DESC, a.cKode_fu DESC
                LIMIT 1 ";
        $row = $this->db->query($sql)->row_array();

        if(!empty($row)){
            $nilai = str_replace($kodeHeader.'-', "", $row['cKode_fu']);
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

    function generateFilename($filename, $urut = 0)
    {
        $exDot = explode('.', $filename);
        $ext = $exDot[count($exDot) - 1];
        $generated = str_replace(' ', '_', $filename);
        $generated = str_replace('.' . $ext, '', $generated);
        $generated = preg_replace('/[^A-Za-z0-9\-]/', '_', $generated);
        $dateNow = date('Y_m_d__H_i_s');
        $nameGenerated = $urut . '__' . $dateNow . '__' . $generated . '.' . $ext;
        return $nameGenerated;
    }

}
