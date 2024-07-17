<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class validasi extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        $url = $_SERVER['HTTP_REFERER'];
        $this->load->library('auth');
        $this->load->library('lib_sub_core');
        $this->load->library('lib_erpss');
        $this->load->library('lib_utilitas');

        $this->db = $this->load->database('hrd', false, true);
        $this->user = $this->auth->user();
        $this->company_id = substr($url, strrpos($url, '/') + 1);

        $this->modul_id = $this->input->get('modul_id');
        $this->iModul_id = $this->lib_sub_core->getIModulID($this->input->get('modul_id'));
        $this->iCompanyID = $this->input->get('company_id');

        $this->team = $this->lib_erpss->hasTeam($this->user->gNIP);
        $this->teamID = $this->lib_erpss->hasTeamID($this->user->gNIP);
        $this->isAdmin = $this->lib_erpss->isAdmin($this->user->gNIP);

        $this->title = 'Validasi';
        $this->url = 'validasi';
        $this->urlpath = 'erpss/' . str_replace("_", "/", $this->url);

        $this->maintable = 'hrd.biflow';
        $this->main_table = $this->maintable;
        $this->main_table_pk = 'id';

        $datagrid['islist'] = array(
            'cKode' => array('label' => 'Autogenerate Code', 'width' => 110, 'align' => 'center', 'search' => true),
            'raw_id' => array('label' => 'SSID', 'width' => 80, 'align' => 'center', 'search' => true),
            'ss_raw_problems.problem_subject' => array('label' => 'Project Name', 'width' => 250, 'align' => 'left', 'search' => true),
            'cRequestor' => array('label' => 'Requestor', 'width' => 200, 'align' => 'left', 'search' => true),
            'ss_raw_pic.pic' => array('label' => 'Project Manager', 'width' => 200, 'align' => 'left', 'search' => true),
            // 'iSubmit' => array('label' => 'Status Submit', 'width' => 150, 'align' => 'center', 'search' => true),
            'iApprove_validasi' => array('label' => 'Status Validasi', 'width' => 150, 'align' => 'center', 'search' => true),
            'setting_prioritas_detail.iSortApproved' => array('label' => 'Priority Direksi', 'width' => 120, 'align' => 'center', 'search' => false),
        );

        $datagrid['setQuery'] = array(
            0 => array('vall' => 'biflow.lDeleted', 'nilai' => 0),
            1 => array('vall' => 'biflow.iSubmit', 'nilai' => 1),
        );

        $datagrid['jointableinner'] = array(
            0 => array('hrd.ss_raw_problems' => 'ss_raw_problems.id = biflow.raw_id'),
        );

        $datagrid['jointableleft'] = array(
            0 => array('hrd.ss_raw_pic' => 'ss_raw_pic.rawid = ss_raw_problems.id AND ss_raw_pic.Deleted = "No" AND ss_raw_pic.iRoleId = 1 AND ss_raw_pic.pic IS NOT NULL'),
            1 => array('hrd.setting_prioritas_detail' => 'setting_prioritas_detail.rawid = ss_raw_problems.id AND setting_prioritas_detail.lDeleted = 0'),
        );

        $datagrid['shortBy'] = array("biflow.id" => "DESC");

        $this->datagrid = $datagrid;
    }

    public function index($action = '')
    {
        $grid = new Grid;
        $grid->setTitle($this->title);
        $grid->setTable($this->maintable);
        $grid->setUrl($this->url);

        // $grid->changeSearch('dTgl_upb', 'betweenDate');

        // $grid->changeFieldType('iHold', 'combobox', '', array('' => '--Pilih--', 0 => 'Tidak', 1 => 'Ya'));
        // $grid->changeFieldType('iSubmit', 'combobox', ' ', array('' => '--Pilih--', 0 => 'Draft - Need to be Submit', 1 => 'Submited'));
        $grid->changeFieldType('iApprove_validasi', 'combobox', '', array('' => '--Pilih--', 0 => 'Waiting Validation', 1 => 'Revise', 2 => 'Validated'));

        /*$grid->setGroupBy($this->setGroupBy);*/
        /*Untuk Field*/

        $grid->addFields('form_detail');
        foreach ($this->datagrid as $kv => $vv) {
            /*Untuk List*/
            if ($kv == 'islist') {
                foreach ($vv as $list => $vlist) {
                    $grid->addList($list);
                    foreach ($vlist as $kdis => $vdis) {
                        if ($kdis == 'label') {
                            $grid->setLabel($list, $vdis);
                        }
                        if ($kdis == 'width') {
                            $grid->setWidth($list, $vdis);
                        }
                        if ($kdis == 'align') {
                            $grid->setAlign($list, $vdis);
                        }
                        if ($kdis == 'search' && $vdis == true) {
                            $grid->setSearch($list);
                        }
                    }
                }
            }

            /*Untuk Short List*/
            if ($kv == 'shortBy') {
                foreach ($vv as $list => $vlist) {
                    $grid->setSortBy($list);
                    $grid->setSortOrder($vlist);
                }
            }

            if ($kv == 'inputGet') {
                foreach ($vv as $list => $vlist) {
                    $grid->setInputGet($list, $vlist);
                }
            }

            if ($kv == 'jointableinner') {
                foreach ($vv as $list => $vlist) {
                    foreach ($vlist as $tbjoin => $onjoin) {
                        $grid->setJoinTable($tbjoin, $onjoin, 'inner');
                    }
                }
            }

            if ($kv == 'jointableleft') {
                foreach ($vv as $list => $vlist) {
                    foreach ($vlist as $tbjoin => $onjoin) {
                        $grid->setJoinTable($tbjoin, $onjoin, 'left');
                    }
                }
            }

            if ($kv == 'setQuery') {
                foreach ($vv as $list => $vlist) {
                    $grid->setQuery($vlist['vall'], $vlist['nilai']);
                }
            }
        }

        /* validasi maingrid  */
        // $grid->setQuery('upb.iCompanyID = '.$this->company_id, null);

        $grid->setGridView('grid');

        switch ($action) {
            case 'json':
                $grid->getJsonData();
                break;
            case 'load_formula':
                echo $this->load_formula();
                break;
            case 'uploadFile':
                echo $this->lib_sub_core->uploadFile($this);

                // $postData = $this->input->post();
                // $lastId = $this->input->get('lastId');
                // // INSERT HISTORY
                // // get IM Modul id tipe Upload Grid
                // $sql = "SELECT b.iM_modul_fields
                //         FROM erp_privi.m_modul a
                //         JOIN erp_privi.m_modul_fields b ON b.iM_modul = a.iM_modul
                //         WHERE b.iM_jenis_field = 16 # Upload Grid
                //         AND a.idprivi_modules = '" . $this->modul_id . "' ";
                // $row = $this->db->query($sql)->row_array();

                // // get File Upload Group
                // $sqlFile = "SELECT *
                //             FROM ps.group_file_upload a
                //             WHERE a.iM_modul_fields = '" . $row['iM_modul_fields'] . "'
                //             AND a.idHeader_File = '" . $lastId . "' ";
                // $rowFile = $this->db->query($sqlFile)->result_array();

                // // get data Head
                // $sql2 = "SELECT a.cKode_het, a.cCreate, a.tCreate, a.cUpdate, a.tUpdate
                //             FROM ps.biflow a
                //             WHERE a.id = '" . $lastId . "' ";
                // $row2 = $this->db->query($sql2)->row_array();

                // $cKode_het = $row2['cKode_het'];
                // if (!empty($postData['cKode_het'])) {
                //     $cKode_het = $postData['cKode_het'];
                // }

                // // Insert History
                // $insert['cKode_het'] = $cKode_het;
                // $insert['c_iteno'] = $postData['c_iteno'];
                // $insert['iComp_mnf'] = $postData['iComp_mnf'];
                // $insert['cInisiator'] = $postData['cInisiator'];
                // $insert['iHet_ub'] = $postData['iHet_ub'];
                // $insert['iHet_kp'] = $postData['iHet_kp'];
                // $insert['mKeterangan'] = $postData['mKeterangan'];
                // $insert['cPic_update'] = $postData['cPic_update'];
                // $insert['iCompanyID'] = $postData['company_id'];

                // if (!empty($row2['tCreate'])) {
                //     $insert['cCreate'] = $row2['cCreate'];
                //     $insert['tCreate'] = $row2['tCreate'];
                // }

                // if (!empty($row2['tUpdate'])) {
                //     $insert['cUpdate'] = $row2['cUpdate'];
                //     $insert['tUpdate'] = $row2['tUpdate'];
                // }

                // $insert['lDeleted'] = 0;

                // if ($this->db->insert('ps.biflow_history', $insert)) {
                //     $insert_id = $this->db->insert_id();
                //     // Insert History File
                //     foreach ($rowFile as $key => $val) {
                //         $insert2['iHistory'] = $insert_id;
                //         $insert2['iFile'] = $val['iFile'];
                //         $insert2['vFilename'] = $val['vFilename'];
                //         $insert2['vFilename_generate'] = $val['vFilename_generate'];
                //         $insert2['tKeterangan'] = $val['tKeterangan'];
                //         $insert2['dCreate'] = $val['dCreate'];
                //         $insert2['cCreate'] = $val['cCreate'];
                //         $insert2['dUpdate'] = $val['dUpdate'];
                //         $insert2['cUpdate'] = $val['cUpdate'];
                //         $insert2['iDeleted'] = $val['iDeleted'];
                //         $this->db->insert('ps.biflow_history_file', $insert2);
                //     }
                // } else {
                //     echo $this->db->last_query();
                //     exit();
                // }

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
            case 'view':
                $grid->render_form($this->input->get('id'), true);
                break;
            case 'updateproses':
                $this->lib_sub_core->prepare_before_update($this);
                echo $grid->updated_form();
                break;
            case 'delete':
                echo $grid->delete_row();
                break;
            /*Option Case*/
            case 'getFormDetail':
                echo $this->getFormDetail();
                break;
            case 'get_data_prev':
                echo $this->lib_sub_core->get_data_prev_newPath($this->urlpath);
                break;
            /*Confirm*/
            case 'confirm':
                echo $this->confirm_view();
                break;
            case 'confirm_process':
                echo $this->confirm_process();
                break;
            /*Confirm*/
            case 'approve':
                echo $this->approve_view(2);
                break;
            case 'reject':
                echo $this->approve_view(1);
                break;
            case 'approve_process':
                echo $this->approve_process();
                break;
            case 'download':
                $this->lib_sub_core->downloadFile($this);
                break;
            case 'load_detail':
                echo $this->load_detail();
                break;
            case 'load_pic':
                echo $this->load_pic();
                break;
            case 'get_produk':
                echo $this->getProduk();
                break;
            case 'get_produk_all':
                echo $this->getProdukAll();
                break;
            case 'get_pic':
                echo $this->getPic();
                break;
            default:
                $grid->render_grid();
                break;
        }
    }

    public function getProduk()
    {
        $term = trim($this->input->get('term'));
        $company_id = $this->input->get('company_id');
        $current_iteno = $this->input->get('current_iteno');

        $sql = 'SELECT a.c_iteno, a.c_itnam ,a.c_undes, a.n_scpri, b.c_descr, d.c_nmkateg
                FROM sales.itemas a
                LEFT JOIN sales.divisi b ON b.c_teamc = a.c_teamc AND b.lDeleted = 0
                LEFT JOIN sales.jenis c on c.c_jenis = a.c_jenis AND c.lDeleted = 0 AND c.iCompanyID = a.iCompanyID
                LEFT JOIN sales.kategori d ON d.c_kategori = c.c_kategori AND d.lDeleted = 0
                WHERE a.lDeleted = 0
                AND a.iCompanyID = "' . $company_id . '"
                AND ( a.c_iteno LIKE "%' . $term . '%"   OR  a.c_itnam LIKE "%' . $term . '%"  )
                AND a.c_iteno NOT IN (
                    SELECT m.c_iteno
                    FROM ps.biflow m
                    WHERE m.lDeleted = 0
                    AND m.c_iteno <> "' . $current_iteno . '"
                )
                LIMIT 20';
        // echo '<pre>'.$sql;exit;
        $lines = $this->db->query($sql)->result_array();

        $return_arr = array();
        $i = 0;
        foreach ($lines as $line) {
            $row_array["value"] = trim($line["c_iteno"] . ' - ' . $line["c_itnam"]);
            $row_array["id"] = trim($line["c_iteno"]);
            $row_array["c_itnam"] = trim($line["c_itnam"]);
            $row_array["c_undes"] = trim($line["c_undes"]);
            $row_array["n_scpri"] = number_format($line["n_scpri"]);
            $row_array["c_teamc"] = trim($line["c_descr"]);
            $row_array["c_kategori"] = trim($line["c_nmkateg"]);
            array_push($return_arr, $row_array);
        }
        echo json_encode($return_arr);
        exit();
    }

    public function getProdukAll()
    {
        $term = trim($this->input->get('term'));
        $company_id = $this->input->get('company_id');
        $current_iteno = $this->input->get('current_iteno');

        $sql = 'SELECT a.c_iteno, a.c_itnam
                FROM sales.itemas a
                WHERE a.lDeleted = 0
                AND (a.c_iteno LIKE "%' . $term . '%"   OR  a.c_itnam LIKE "%' . $term . '%")
                LIMIT 20';
        // echo '<pre>'.$sql;exit;
        $lines = $this->db->query($sql)->result_array();

        $return_arr = array();
        $i = 0;
        foreach ($lines as $line) {
            $row_array["value"] = trim($line["c_iteno"] . ' - ' . $line["c_itnam"]);
            $row_array["id"] = trim($line["c_iteno"]);
            array_push($return_arr, $row_array);
        }
        echo json_encode($return_arr);
        exit();
    }

    public function getPic()
    {
        $term = trim($this->input->get('term'));
        $company_id = $this->input->get('company_id');
        $data = array();

        $sql = "SELECT e.cNip, e.vName FROM hrd.employee e
                WHERE e.lDeleted = 0 AND ( e.dresign = '0000-00-00' OR e.dresign > DATE(NOW()) )
                AND ( e.cNip LIKE '%{$term}%' OR e.vName LIKE '%{$term}%' )
                AND e.iCompanyID = {$company_id}
                ORDER BY e.vName ASC ";
        $query = $this->db->query($sql);
        if ($query->num_rows > 0) {
            foreach ($query->result_array() as $line) {
                $row_array['id'] = trim($line['cNip']);
                $row_array['value'] = trim($line['cNip']) . ' - ' . trim($line['vName']);
                array_push($data, $row_array);
            }
        }

        echo json_encode($data);
        exit;
    }

    public function listBox_validasi_cRequestor($value, $pk, $name, $rowData)
    {
        // get value
        $sql = "SELECT CONCAT_WS(' - ', a.cNip, a.vName) AS showshow
                FROM hrd.employee a
                WHERE a.lDeleted = 0
                AND a.cNip = '".$value."' ";
        $row = $this->db->query($sql)->row_array();

        $o = $row['showshow'];

        return $o;
    }

    public function listBox_validasi_ss_raw_pic_pic($value, $pk, $name, $rowData)
    {
        // get value
        $sql = "SELECT CONCAT_WS(' - ', a.cNip, a.vName) AS showshow
                FROM hrd.employee a
                WHERE a.lDeleted = 0
                AND a.cNip = '".$value."' ";
        $row = $this->db->query($sql)->row_array();

        $o = $row['showshow'];

        return $o;
    }

    public function listBox_validasi_itemas_c_teamc($value, $pk, $name, $rowData)
    {
        $sql = "SELECT TRIM(a.c_descr) AS c_descr
                FROM sales.divisi a
                WHERE a.lDeleted = 0 AND a.c_teamc = '" . $rowData->itemas__c_teamc . "' ";
        $row = $this->db->query($sql)->row_array();

        $r = $row['c_descr'];

        return $r;
    }

    public function listBox_validasi_iHet_ub($value, $pk, $name, $rowData)
    {
        $r = number_format($value, 3, ',', '.');

        return $r;
    }

    public function listBox_validasi_iHet_kp($value, $pk, $name, $rowData)
    {
        $r = number_format($value, 3, ',', '.');

        return $r;
    }

    public function listBox_validasi_itemas_n_scpri($value, $pk, $name, $rowData)
    {
        $r = number_format($value, 3, ',', '.');

        return $r;
    }

    public function listBox_validasi_iComp_mnf($value, $pk, $name, $rowData)
    {
        $sql = "SELECT a.vCompName
                FROM hrd.company a
                WHERE a.lDeleted = 0 AND a.iCompanyId = '" . $value . "' ";
        $row = $this->db->query($sql)->row_array();

        $r = $row['vCompName'];

        return $r;
    }

    public function listBox_validasi_upb_cTeam_pd($value, $pk, $name, $rowData)
    {
        $upb = $this->db->get_where('plc3.upb', array('vUpb_no' => $rowData->vUpb_no))->row_array();
        $team = $this->db->get_where('plc3.team', array('cTeam' => $upb['cTeam_pd']))->row_array();
        if (isset($team['vTeam'])) {
            return $team['vTeam'];
        } else {
            return $value;
        }
    }

    public function searchBox_validasi_ss_raw_pic_pic($rowData, $id)
    {
        // get nip PM
        $sqlPm = "SELECT a.vContent
                    FROM hrd.ss_sysparam a
                    WHERE a.cVariable = 'MGR' ";
        $rowPm = $this->db->query($sqlPm)->row_array();
        $listPm = '';
        if(!empty($rowPm['vContent'])){
            $arrPm = explode(',', $rowPm['vContent']);
            foreach ($arrPm as $kpm => $pm) {
                $listPm .= "'".$pm."'";
                if($kpm != count($arrPm)-1){
                    $listPm .= ",";
                }
            }
        }

        $sql = "SELECT a.cNip AS valval, CONCAT_WS(' - ', a.cNip, a.vName) AS showshow
                FROM hrd.employee a
                WHERE a.lDeleted = 0
                AND (a.dresign = '0000-00-00' OR a.dresign >= NOW())
                AND a.cNip IN(".$listPm.") 
                ORDER BY a.vName ASC";
        $rows = $this->db->query($sql)->result_array();
        
        $option = '<option value="">-- Belum Dipilih --</option>';
        foreach ($rows as $key => $val) {
            $option .= '<option value="'.$val['valval'].'">'.$val['showshow'].'</option>';
        }
        
        $o = '<select id="'.$id.'" name="'.$id.'">';
        $o .= $option;
        $o .= '</select>';
        
        
        $o .= '<script>
                    $("#'.$id.'").select2();
                </script>';

        return $o;
    }

    public function searchBox_validasi_cRequestor($rowData, $id)
    {
        $o = '<input type="hidden" id="' . $id . '" name="' . $id . '" value="">';
        $o .= '<input type="text" id="' . $id . '_id" name="' . $id . '_id" value="">';

        $o .= "<script>";

        $o .= "$(document).ready(function(){
                    var config1 = {
                        source: base_url+'processor/" . $this->urlpath . "?action=get_pic&company_id=" . $this->company_id . "',
                        select: function(event, ui){

                            var i = $('#" . $id . "_id').index(this);
                            $('#" . $id . "_id').eq(i).val(ui.item.value);
                            $('#" . $id . "').eq(i).val(ui.item.id);

                        },
                        minLength: 2,
                        autoFocus: true,
                    };
                    $('#" . $id . "_id').livequery(function(){
                        $(this).autocomplete(config1);
                        var i = $('#" . $id . "_id').index(this);
                        $(this).keypress(function(e, ui){
                            if(e.which != 13) {
                                $('#" . $id . "').eq(i).val('');
                            }

                            if(e.which == 13){
                                reload_grid('grid_" . $this->url . "');
                            }

                        });
                        $(this).blur(function(){
                            if($('#" . $id . "').eq(i).val() == '') {
                                $(this).val('');
                            }
                        });
                    });

                    // Untuk searchbox
                    // Synchronize two input fields
                    // $('#" . $id . "_id').bind('keyup paste', function() {
                    //     $('#" . $id . "').val($(this).val());
                    // });

                });";

        $o .= "</script>";

        return $o;
    }

    public function load_detail()
    {
        $post = $this->input->post();
        $field_id = $post['field_id'];
        $idexp = $post['id_pk'];
        $fieldDet = $this->db->get_where('erp_privi.m_modul_fields', array('lDeleted' => 0, 'iM_modul_fields' => $field_id))->row_array();
        if (!empty($fieldDet)) {
            $loadView = $fieldDet['vFile_detail'];
            $sqlDet = $fieldDet['vSource_input'];
            $sqlFile = $fieldDet['vSource_input_edit'];

            $upload = $this->db->query($sqlFile, array($idexp))->result_array();
            $detail = $this->db->query($sqlDet, array($idexp))->row_array();

            $data['rows'] = $detail;
            $data['upload'] = $upload;
            $data['id'] = $post['id'];
            $viewDetail = $this->load->view('partial/modul/' . $loadView, $data, true);
            echo $viewDetail;
            exit();
        } else {
            echo "Field Tidak Ditemukan";
            exit();
        }
    }

    public function load_pic()
    {
        $get = $this->input->get();
        $term = $get['term'];

        $sql = 'SELECT cNip AS id, CONCAT(cNip, " - ", vName) AS value FROM hrd.employee WHERE ( cNip LIKE "%' . $term . '%" OR vName LIKE "%' . $term . '%" ) AND dResign = "0000-00-00" ';
        $data = $this->db->query($sql)->result_array();

        echo json_encode($data);
        exit();
    }

    public function getFormDetail()
    {
        $post = $this->input->post();
        $get = $this->input->get();
        $data['html'] = "";
        $dFields = $this->lib_sub_core->getFields($this->iModul_id);
        $hate_emel = "";

        if ($get['formaction'] == 'update') {
            $aidi = $get['id'];
        } else {
            $aidi = 0;
        }

        $hate_emel .= '
            <table class="hover_table" style="width:99%; border: 1px solid #dddddd; text-align: center; margin-left: 5px; border-collapse: collapse" cellspacing="0" cellpadding="1">
                <thead>
                    <tr style="width: 100%; border: 1px solid #dddddd; background: #b3d2ea; border-collapse: collapse">
                        <th style="border: 1px solid #dddddd;">Activity Name</th>
                        <th style="border: 1px solid #dddddd;">Status</th>
                        <th style="border: 1px solid #dddddd;">at</th>
                        <th style="border: 1px solid #dddddd;">by</th>
                        <th style="border: 1px solid #dddddd;">Remark</th>
                    </tr>
                </thead>
                <tbody>';

        $hate_emel .= $this->getHistoryActivity($this->modul_id, $aidi, true);

        $hate_emel .= '
                </tbody>
            </table>
            <br>
            <br>
            <hr>
        ';

        if (!empty($dFields)) {

            foreach ($dFields as $form_field) {

                $data_field['iM_jenis_field'] = $form_field['iM_jenis_field'];

                $data_field['form_field'] = $form_field;
                $data_field['get'] = $get;
                $data_field['post'] = $post;

                $controller = $this->url;
                $folderpath = str_replace(str_replace('_', '/', $this->url), '', $this->urlpath);
                $data_field['id'] = $controller . '_' . $form_field['vNama_field'];
                //$data_field['field']= $controller.'_'.$form_field['vNama_field'] ;
                $data_field['field'] = $form_field['vNama_field'];

                $data_field['act'] = $get['act'];
                $data_field['hasTeam'] = $this->team;
                $data_field['hasTeamID'] = $this->teamID;
                $data_field['isAdmin'] = $this->isAdmin;
                $data_field['urlpath'] = $this->urlpath;
                $data_field['folderpath'] = $folderpath;

                /*untuk keperluad file upload*/
                if ($form_field['iM_jenis_field'] == 7) {
                    $data_field['tabel_file'] = $form_field['vTabel_file'];
                    $data_field['tabel_file_pk'] = $this->main_table_pk;
                    $data_field['tabel_file_pk_id'] = $form_field['vTabel_file_pk_id'];

                    $path = 'files/plc/dok_tambah';
                    $createname_space = $this->url;
                    $tempat = 'dok_tambah';
                    $FOLDER_APP = 'plc';

                    $data_field['path'] = $path;
                    $data_field['FOLDER_APP'] = $FOLDER_APP;
                    $data_field['createname_space'] = $createname_space;
                    $data_field['tempat'] = $tempat;

                    if ($form_field['iRequired'] == 1) {
                        $data_field['field_required'] = 'required';
                    } else {
                        $data_field['field_required'] = '';
                    }
                }
                /*untuk keperluad file upload*/

                $return_field = "";
                if ($get['formaction'] == 'update') {
                    $id = $get['id'];

                    $sqlGetMainvalue = 'select * from ' . $this->main_table . ' where lDeleted=0 and ' . $this->main_table_pk . '= ' . $id . '   ';
                    /* echo '<pre>'.$sqlGetMainvalue;
                    exit; */
                    $dataHead = $this->db->query($sqlGetMainvalue)->row_array();

                    $data_field['dataHead'] = $dataHead;
                    $data_field['main_table_pk'] = $this->main_table_pk;

                    if ($form_field['iM_jenis_field'] == 6 || $form_field['iM_jenis_field'] == 5) {
                        $data_field['vSource_input'] = $form_field['vSource_input_edit'];
                    } else {
                        $data_field['vSource_input'] = $form_field['vSource_input'];
                    }
                    $return_field = $this->load->view('v3_form_detail_update', $data_field, true);
                } else {
                    $vSource_input = $form_field['vSource_input'];
                    $vSource_input = str_replace('%comp%', $this->input->get('company_id'), $vSource_input);
                    $data_field['vSource_input'] = $vSource_input;
                    $return_field = $this->load->view('v3_form_detail', $data_field, true);
                    /*$return_field = $this->load->view('v3_form_detail',$data_field,true);*/
                }

                $hate_emel .= '  <div class="rows_group" style="overflow:fixed;">
                                    <label for="' . $controller . '_form_detail_' . $form_field['vNama_field'] . '" class="rows_label">' . $form_field['vDesciption'] . '
                                    ';
                if ($form_field['iRequired'] == 1) {
                    $hate_emel .= '<span class="required_bintang">*</span>';
                    $data_field['field_required'] = 'required';
                } else {
                    $data_field['field_required'] = '';
                }

                if ($form_field['vRequirement_field'] != "") {
                    $hate_emel .= '<span style="float:right;" title="' . $form_field['vRequirement_field'] . '" class="ui-icon ui-icon-info"></span>';
                } else {
                    $hate_emel .= '';
                }
                $hate_emel .= '      </label>
                                    <div class="rows_input">' . $return_field . '</div>
                                </div>';
            }
        } else {
            $hate_emel .= 'Field belum disetting';
        }

        $hate_emel .= '<input type="hidden" name="isdraft" id="isdraft">';
        $hate_emel .= '<input type="hidden" name="company_id" value="' . $this->input->get('company_id') . '">';
        // print_r($hate_emel);exit();
        $data["html"] .= $hate_emel;
        return json_encode($data);
    }

    function getHistoryActivity($modul_id, $iKey_id, $showDeleted = false){
        $getApp = $this->getApptableLog($modul_id);
        $applogTable = $getApp ['vTable_log_activity'];

        $sql = 'SELECT a.*,b.vNama_activity, a.dCreate, c.vName, a.vRemark,c.cNip,
                    #IF(a.iApprove = 2, "Approve" , IF(a.iApprove = 1, "Reject", "-")) AS setatus
                    IF(a.iM_activity=1,"Submitted",
                        IF(a.iApprove = 2, "Valid" , IF(a.iApprove = 1, "Revise", "-")) 
                    )AS setatus,
                    IF(b.iM_activity=3,"Validation", b.vNama_activity)AS vNama_activity
                    ,a.lDeleted
                FROM erp_privi.'.$applogTable.' a 
                JOIN erp_privi.m_activity b ON b.iM_activity=a.iM_activity
                JOIN hrd.employee c ON c.cNip=a.cCreated
                WHERE a.idprivi_modules ="'.$modul_id.'"
                    AND a.iKey_id ="'.$iKey_id.'"';
        $sql .= ( $showDeleted == true ) ? '' : ' AND a.lDeleted = 0';   
        $sql .= ' ORDER BY a.iM_modul_log_activity ASC';

        // echo '<pre>'.$sql;
        // exit;
        $query = $this->db->query($sql);
        $jmlRow = $query->num_rows();
        
        $html = '';

        if ($jmlRow > 0) {
            $rows = $query->result_array();
            $i=1;
            $length = count($rows);
            foreach ($rows as $data ) {
                $kolor = (($data['lDeleted']) == 1) ? '#f2807e' : '#ffffff';  
                $html .='
                    <tr style="border: 1px solid #dddddd; border-collapse: collapse; background: '.$kolor.'; ">
                        <td style="border: 1px solid #dddddd; width: 10%; text-align: center;">
                        ';

                    // cek sudoers 
                    $isSudoers = $this->lib_sub_core->isSudoers($this->logged_nip);
                    $actiName = $data['vNama_activity'];
                    $iM_modul_log_activity = $data['iM_modul_log_activity'];
                    if( ($isSudoers) and ($data['lDeleted'] != 1 ) and ($i===$length) ){
                        $company_id =  $_GET['company_id'];
                        $group_id =  $_GET['group_id'];

                        $sget_modul = 'SELECT a.vPathModule
                                        FROM erp_privi.privi_modules a
                                        WHERE a.idprivi_modules= '.$modul_id.' 
                                        ';
                        $dmodul = $this->db->query($sget_modul)->row();

                        $str = $dmodul->vPathModule;
                        $exp = explode('/',$str);
                        $array = array();
                        foreach ($exp as $key => $value) {
                            if($key != 0){
                                array_push($array,$value);
                            }
                        }

                        $folder_app = $exp[0];
                        $controller = implode("_", $array);

                        $param =    "&modul_id=".$modul_id. "&id=".$iKey_id. "&group_id=".$group_id."&company_id=".$company_id."&table_log=".$applogTable."&iM_modul_log_activity=".$iM_modul_log_activity;
                        $urlDelete  = base_url() . "processor/schedulercheck/subcore_controller?action=delete_activity".$param;

                        $urlModul  = base_url() . "processor/".$folder_app."/".$controller."?";

                        /*$actiName       .= "<script type'text/javascript'>
                                            function del_btn_act_".$modul_id."_".$iKey_id."(url,urlsub, title) {
                                                custom_confirm('Delete Selected Record?', function(){
                                                    $.get(urlsub, function(data) {
                                                        if (data > 0) {
                                                            $.get(url + '&action=update&foreign_key=0&group_id='+".$group_id."+'&company_id='+".$company_id."+'&id='+".$iKey_id."+'&modul_id='+".$modul_id.", function(data) {
                                                                $('div#form_' + '".$controller."').html(data);
                                                            });
                                                        } else {
                                                            alert('Delete Failed!');
                                                            return false;
                                                        }
                                                    }); 
                                                }); 
                                            }
                                        </script>";
                        $actiName       .= "<a href='#' onclick='javascript:del_btn_act_".$modul_id."_".$iKey_id."(\"".$urlModul."\",\"".$urlDelete."\", \"Delete Activity\");'><center><span class='ui-icon ui-icon-trash'></span></center></a>";*/

                    }

                $html .='<span class="">'.$actiName.'</span>';

                $html .='</td>
                        <td style="border: 1px solid #dddddd; width: 10%; text-align: center;">
                            <span class="">'.$data['setatus'].'</span>
                        </td>
                        <td style="border: 1px solid #dddddd; width: 15%; text-align: center;">
                            <span class="">'.$data['dCreate'].'</span>
                        </td>
                        <td style="border: 1px solid #dddddd; width: 30%; text-align: left;">
                            <span class="">'.$data['cNip'].' - '.$data['vName'].'</span>
                        </td>
                        <td style="border: 1px solid #dddddd; width: 30%; text-align: left;">
                            <span class="">'.$data['vRemark'].'</span>
                        </td>
                    </tr>';


                $i++;
            }
        }else{
            $html .='
                    <tr style="border: 1px solid #dddddd; border-collapse: collapse; background: #ffffff; ">
                        <td colspan="5" style="border: 1px solid #dddddd; text-align: center;">
                            <span class="">No Data</span>
                        </td>
                    </tr>';


        }

        return $html;
    }

    public function listBox_Action($row, $actions)
    {

        /* Validasi Action */
        /* 1. row hanya bisa diedit oleh inisiator */

        $row = get_object_vars($row);
        $peka = $row[$this->main_table_pk];
        $getLastactivity = $this->lib_sub_core->getLastactivity($this->modul_id, $peka);
        $isOpenEditing = $this->lib_sub_core->getOpenEditing($this->modul_id, $peka);

        // get NIP Author
        $AuthModul = $this->lib_sub_core->getAuthorModul($this->modul_id);
        $nipAuthor = explode(',', $AuthModul['vNip_author']);

        if ($getLastactivity == 0) {
        } else {
            if ($isOpenEditing) {
            } else {
                unset($actions['edit']);
            }
        }

        // Kondisi Khusus jika lvl 2 keatas hanya bisa View
        // $sql = "SELECT b.iLvlemp
        //         FROM hrd.employee a
        //         JOIN hrd.position b ON b.iPostId = a.iPostID
        //         WHERE a.lDeleted = 0
        //         AND a.cNip = '" . $this->user->gNIP . "' ";
        // $row = $this->db->query($sql)->row_array();

        // if ($row['iLvlemp'] > 1) {
        //     if (!in_array($this->user->gNIP, $nipAuthor)) {
        //         unset($actions['edit']);
        //     }
        // }

        return $actions;
    }

    public function insertBox_validasi_form_detail($field, $id)
    {
        $get = $this->input->get();
        $post = $this->input->post();
        foreach ($get as $kget => $vget) {
            if ($kget != "action") {
                $in[] = $kget . "=" . $vget;
            }
            if ($kget == "action") {
                $in[] = "act=" . $vget;
            }
        }
        $g = implode("&", $in);
        $return = '<script>
                var sebelum = $("label[for=\'' . $this->url . '_form_detail\']").parent();
                $("label[for=\'' . $this->url . '_form_detail\']").remove();
                sebelum.attr("id","' . $id . '");
                sebelum.html("");
                sebelum.removeAttr("class");
                sebelum.removeAttr("style");
                $.ajax({
                    url: base_url+"processor/' . $this->urlpath . '?action=getFormDetail&formaction=addnew&' . $g . '",
                    type: "post",
                    data: iupb_id=0,
                    success: function(data) {
                        var o = $.parseJSON(data);
                        sebelum.html(o.html);
                    }
                });
            </script>';
        return $return;
    }

    public function updateBox_validasi_form_detail($field, $id, $value, $rowData)
    {
        $get = $this->input->get();
        $post = $this->input->post();
        foreach ($get as $kget => $vget) {
            if ($kget != "action") {
                $in[] = $kget . "=" . $vget;
            }
            if ($kget == "action") {
                $in[] = "act=" . $vget;
            }
        }
        $g = implode("&", $in);
        $return = '<script>
                var sebelum = $("label[for=\'' . $this->url . '_form_detail\']").parent();
                $("label[for=\'' . $this->url . '_form_detail\']").remove();
                sebelum.attr("id","' . $id . '");
                sebelum.html("");
                sebelum.removeAttr("class");
                sebelum.removeAttr("style");
                $.ajax({
                    url: base_url+"processor/' . $this->urlpath . '?action=getFormDetail&formaction=update&' . $g . '",
                    type: "post",
                    data: iupb_id=0,
                    success: function(data) {
                        var o = $.parseJSON(data);
                        sebelum.html(o.html);
                    }
                });
            </script>';
        return $return;
    }

    //Ini Merupakan Standart Approve yang digunakan di erp
    public function approve_view($id = 0)
    {
        $lblbutton = "-";
        if ($id == 1) {
            $lblbutton = "Revise";
        }
        if ($id == 2) {
            $lblbutton = "Approve";
        }
        $echo = '<script type="text/javascript">
                     function submit_ajax(form_id) {
                        var remark = $("#remark_' . $this->url . '_approval").val();
                        if ( "' . $id . '" == "1" && remark == "" ){
                            alert("Remark Tidak Boleh Kosong");
                            return false;
                        } else {
                            return $.ajax({
                                url     : $("#"+form_id).attr("action"),
                                type    : $("#"+form_id).attr("method"),
                                data    : $("#"+form_id).serialize(),
                                success : function(data) {
                                    var o           = $.parseJSON(data);
                                    var last_id     = o.last_id;
                                    var group_id    = o.group_id;
                                    var modul_id    = o.modul_id;
                                    var company_id  = o.company_id;
                                    var url         = "' . base_url() . 'processor/' . $this->urlpath . '";
                                    if(o.status == true) {
                                        $("#alert_dialog_form").dialog("close");
                                        $.get(url+"?action=update&id="+last_id+"&foreign_key=0&company_id="+company_id+"&group_id="+group_id+"&modul_id="+modul_id, function(data) {
                                            $("div#form_' . $this->url . '").html(data);
                                        });
                                    }else{
                                        _custom_alert(o.message, "Error!", "info", "' . $this->url . '", 1, 5000);
                                    }
                                    reload_grid("grid_' . $this->url . '");
                                }

                            })
                        }

                     }
                 </script>';
        $echo .= '<h1>' . $lblbutton . '</h1><br />';
        $echo .= '<form id="form_' . $this->url . '_approve" action="' . base_url() . 'processor/' . $this->urlpath . '?action=approve_process" method="post">';
        $echo .= '<div style="vertical-align: top;">';
        $echo .= 'Remark :
                <input type="hidden" name="' . $this->main_table_pk . '" value="' . $this->input->get($this->main_table_pk) . '" />
                <input type="hidden" name="company_id" value="' . $this->input->get('company_id') . '" />
                <input type="hidden" name="modul_id" value="' . $this->input->get('modul_id') . '" />
                <input type="hidden" name="group_id" value="' . $this->input->get('group_id') . '" />
                <input type="hidden" name="iapprove" value="' . $id . '" />
                <input type="hidden" name="iM_modul_activity" value="' . $this->input->get('iM_modul_activity') . '" />
                <input type="hidden" name="cKode" value="' . $this->input->get('cKode') . '" />
                <input type="hidden" name="raw_id" value="' . $this->input->get('raw_id') . '" />

                <textarea id="remark_' . $this->url . '_approval" name="vRemark"></textarea>
        <button type="button" onclick="submit_ajax(\'form_' . $this->url . '_approve\')">' . $lblbutton . '</button>';

        $echo .= '</div>';
        $echo .= '</form>';
        return $echo;
    }

    public function approve_process()
    {
        // print_r($this->input->post());exit;
        $post = $this->input->post();
        $cNip = $this->user->gNIP;
        $vName = $this->user->gName;
        $pk = $post[$this->main_table_pk];
        $vRemark = $post['vRemark'];
        $modul_id = $post['modul_id'];
        $id_activity = $post['iM_modul_activity'];
        $iapprove = $post['iapprove'];
        $nowDate = date('Y-m-d H:i:s');
        $nowDate2 = date('Y_m_d_H_i_s');
        $arrValue = array(
            'vFieldName' => 0,
            'dFieldName' => $nowDate,
            'cFieldName' => $cNip,
            'tFieldName' => $vRemark,
        );

        $sqlActivity = 'SELECT ap.vTable_log_activity, ac.vFieldName, ac.dFieldName, ac.cFieldName, ac.tFieldName, ac.iM_activity, ac.iSort, m.idprivi_modules, m.iM_modul
                        FROM erp_privi.m_modul_activity ac
                        JOIN erp_privi.m_modul m ON ac.iM_modul = m.iM_modul
                        JOIN erp_privi.m_application ap ON m.iM_application = ap.iM_application
                        WHERE ac.iM_modul_activity = ? ';
        $activity = $this->db->query($sqlActivity, array($id_activity))->row_array();
        $headData = $this->db->get_where($this->maintable, array($this->main_table_pk => $pk))->row_array();

        // Validasi Approval / Revise
        // Validasi Sub Controller
        $message = '';
        $arrValid = array(
            0 => array('name' => 'Business Requirement',
                'table' => 'hrd.biflow_br',
                'key' => 'cKode_br',
                'validate' => true),
            1 => array('name' => 'Informasi Input Field Master',
                'table' => 'hrd.biflow_ifm',
                'key' => 'cKode_ifm',
                'validate' => true),
            2 => array('name' => 'Informasi Input Output',
                'table' => 'hrd.biflow_iat',
                'key' => 'cKode_iat',
                'validate' => true),
            3 => array('name' => 'Tabel Laporan Proses',
                'table' => 'hrd.biflow_lpaw',
                'key' => 'cKode_lpaw',
                'validate' => true),
            4 => array('name' => 'SOP Signed',
                'table' => 'hrd.biflow_sop',
                'key' => 'cKode_sop',
                'validate' => true),
            5 => array('name' => 'Stakeholder',
                'table' => 'hrd.biflow_sth',
                'key' => 'cKode_sth',
                'validate' => true),
            6 => array('name' => 'Flowchart',
                'table' => 'hrd.biflow_flw',
                'key' => 'cKode_flw',
                'validate' => true),
            7 => array('name' => 'User Access Matrix',
                'table' => 'hrd.biflow_uam',
                'key' => 'cKode_uam',
                'validate' => true),
            8 => array('name' => 'Formula',
                'table' => 'hrd.biflow_for',
                'key' => 'cKode_for',
                'validate' => true),
            // 9 => array('name' => 'Feedback',
            //     'table' => 'hrd.biflow_fbk',
            //     'key' => 'cKode_fbk',
            //     'validate' => false),
        );

        $arVal = array(
            1 => array('name' => 'Feedback', 'table' => 'hrd.biflow_fbk',  'key' => 'cKode_fbk', 'validate' => false),
        );

        foreach ($arrValid as $key => $valid) {
            // Validasi jika belum dipilih sama sekali
            $sql1 = "SELECT count(*) AS total
                    FROM " . $valid['table'] . " a
                    WHERE a.lDeleted = 0
                    AND a.iSubmit_validasi = 0
                    AND a.cKode = '" . $post['cKode'] . "' ";
            $data1 = $this->db->query($sql1)->row_array();

            if (!empty($data1['total'])) {
                $message .= '<br>Masih ada ' . $data1['total'] . ' ' . $valid['name'] . ' yang belum submit!';
            } 
            // print_r($rowMin); exit;
                foreach ($arVal as $k => $val) {
                    // Validasi Minimal Satu
                    $sqlM = "SELECT *
                                FROM " . $val['table'] . " b
                                WHERE b.lDeleted = 0
                                AND b.cSubmit_fbk = 1
                                AND b.cKode = '" . $postData['cKode'] . "' 
                            ";
                    $rowM = $this->db->query($sqlM)->row_array();

                if ($val['validate'] == false) {
                    $sql1 = "SELECT count(*) jml
                            FROM " . $val['table'] . " b
                            WHERE b.lDeleted = 0
                            AND if (b.cSubmit_fbk = 1,b.iSubmit = 0, b.iSubmit_validasi = 0)
                            AND b.cKode = '" . $postData['cKode'] . "' ";
                    $data1 = $this->db->query($sql1)->row_array();
                    // print_r($sql1);exit;

                    if (!empty($data1['jml'])){
                        $message .= '<br>Masih ada ' . $data1['jml'] . ' <b>' . $val['name'] . '</b> yang belum Submit!';
                    } 
                }


            // if ($valid['table'] == 'hrd.biflow_fbk') {

            //     $sql_fbk = "SELECT *
            //                 FROM hrd.biflow_fbk b
            //                 WHERE b.lDeleted = 0
            //                 AND b.cSubmit_fbk = 2
            //                 AND b.cSubmit_fbk NOT IN (1)
            //                 AND b.cKode = '" . $post['cKode'] . "'
            //             ";
            //     $rowM = $this->db->query($sql_fbk)->row_array();
            //                 // echo '<pre>'.$sql_param;
            //                 // exit;

            //     if (empty($rowM)) {
            //         $message .= '<br><b>' . $valid['name'] . '</b> Minimal harus ada 1 data!';
            //     }

            // }

            // Validasi Valid SA
            if ($valid['validate'] == true) {
                // Validasi jika belum dipilih sama sekali
                $sql2 = "SELECT count(*) AS total
                        FROM " . $valid['table'] . " a
                        WHERE a.lDeleted = 0
                        AND a.iValidate = 0
                        #AND (a.iValidate = 0 OR a.iValidate = 2) 
                        AND a.cKode = '" . $post['cKode'] . "' ";
                $data2 = $this->db->query($sql2)->row_array();

                if (!empty($data2['total'])) {
                    $message .= '<br>Masih ada ' . $data2['total'] . ' ' . $valid['name'] . ' yang belum valid!';
                }
            }
            }
        }

        if ($iapprove == 2) {

            // validasi
            foreach ($arrValid as $key => $valid) {
                // Validasi Valid SA
                if ($valid['validate'] == true) {
                    // Validasi jika Not Valid
                    $sql3 = "SELECT count(*) AS total
                            FROM " . $valid['table'] . " a
                            WHERE a.lDeleted = 0
                            AND a.iValidate = 2
                            AND a.iSubmit_validasi = 1
                            AND a.cKode = '" . $post['cKode'] . "' ";
                    $data3 = $this->db->query($sql3)->row_array();

                    if (!empty($data3['total'])) {
                        $message .= '<br>Masih ada ' . $data3['total'] . ' ' . $valid['name'] . ' yang tidak valid!';
                    }
                }
            }
        } else {
            
        }

        if (!empty($message)) {
            $msg['status'] = false;
            $msg['message'] = $message;
            $msg['last_id'] = $post[$this->main_table_pk];
            $msg['group_id'] = $post['group_id'];
            $msg['modul_id'] = $post['modul_id'];
            $msg['company_id'] = $post['company_id'];
            echo json_encode($msg);exit;
        }
        // End Validasi

        
        // Insert History
        $rowH = $this->db->get_where($this->maintable, array($this->main_table_pk => $pk, 'lDeleted' => 0))->row();
        $cKode = $rowH->cKode;
        $id_H = $rowH->id;

        $cKode_history = $cKode . '-HIST-' . $nowDate2;
        $kodeHist = 'HIST-' . $nowDate2;

        // // Menginput Tanggal Start Analisa pada Project Properties
        // $sql = ' SELECT a.id,a.raw_id ,a.dApprove_validasi, srb.dSubmit_requirement
        //         ,if(a.iApprove_validasi=2, DATE_ADD( dApprove_validasi, INTERVAL 1 + 
        //             IF(
        //                 (WEEK(dApprove_validasi) <> WEEK(DATE_ADD(dApprove_validasi, INTERVAL 1 DAY)))
        //                 OR (WEEKDAY(DATE_ADD(dApprove_validasi, INTERVAL 1 DAY)) IN (5, 6)),
        //                 2,0) DAY ) , "") AS tgl_analisa
        //         FROM hrd.biflow a
        //         JOIN hrd.ss_raw_problems srb ON srb.id = a.raw_id
        //         WHERE a.id = "'.$pk.'" 
        //         AND a.lDeleted = 0
        //     ';
        // $datas = $this->db->query($sql)->row_array();
        // $tgl_analisa = $datas['tgl_analisa'];
        // print_r($tgl_analisa); exit;

        $datainsert = array();
        $datainsert['cKode_history'] = $cKode_history;
        $datainsert['cKode'] = $cKode;
        $datainsert['iApprove'] = $iapprove;
        $datainsert['dApprove'] = $nowDate;
        $datainsert['cApprove'] = $cNip;
        $datainsert['vApprove'] = $vRemark;
        if ($this->db->insert('hrd.biflow_history', $datainsert)) {
            // Insert History

            /* print_r($arrValid);
            echo '<br>';
            exit; */

            foreach ($arrValid as $key => $valid) {
                // if ($valid['validate'] == true) {
                    // Insert ke tabel history
                    $sql2 = "SELECT *
                            FROM " . $valid['table'] . " a
                            WHERE a.lDeleted = 0
                            AND a.cKode = '" . $post['cKode'] . "'";
                    $rows = $this->db->query($sql2)->result_array();
                    /* print_r($rows);
                    exit; */
                    $tableHist = $valid['table'] . '_history';
                    $keyHist = $valid['key'] . '_hist';

                    foreach ($rows as $key => $row) {
                        $insert = array();
                        foreach ($row as $k => $r) {
                            // $insert[$keyHist] = $kodeHist;
                            $insert['cKode_history'] = $cKode_history;
                            $insert[$k] = $r;
                        }
                        unset($insert['id']);
                        $this->db->insert($tableHist, $insert);
                        // print_r($this->db->last_query());

                    }

                    if ($valid['table'] == 'hrd.biflow_iat') {
                        
                        // jika input awal transaksi maka simpan log history parameter juga
                        $table_iat_param = $valid['table']."_param";
                        $table_iat_param_hist = $valid['table']."_param_history";

                        $sql_param = "SELECT *
                            FROM " . $table_iat_param." a
                            WHERE a.lDeleted = 0
                            AND a.cKode = '" . $post['cKode'] . "'";
                            // echo '<pre>'.$sql_param;
                            // exit;
                        $rowsParam = $this->db->query($sql_param)->result_array();

                        $tableHist = $valid['table'] . '_history';
                        $keyHist = $valid['key'] . '_hist';

                        foreach ($rowsParam as $key => $row) {
                            $insert2 = array();
                            foreach ($row as $k => $r) {
                                $insert2['cKode_history'] = $cKode_history;
                                $insert2[$k] = $r;
                            }
                            unset($insert2['id']);
                            $this->db->insert($table_iat_param_hist, $insert2);
                            // print_r($this->db->last_query());
                        }

                    }

                    if ($valid['table'] == 'hrd.biflow_fbk') {
                        // jika input awal transaksi maka simpan log history file juga
                        $table_fbk_file = $valid['table']."_file";
                        $table_fbk_file_hist = $valid['table']."_file_history";

                        $sql_file = "SELECT *
                            FROM ".$table_fbk_file." a
                            WHERE a.iDeleted = 0
                            AND a.cKode = '".$post['cKode']."'";
                        $rowsParam = $this->db->query($sql_file)->result_array();

                        foreach ($rowsParam as $key => $row) {
                            $insert2 = array();
                            foreach ($row as $k => $r) {
                                $insert2['cKode_history'] = $cKode_history;
                                $insert2[$k] = $r;
                            }
                            unset($insert2['id']);
                            $this->db->insert($table_fbk_file_hist, $insert2);
                            // print_r($this->db->last_query());
                        }

                    }
                // }

                // Update Isubmit jadi 0
                // $update['iSubmit'] = 0;
                // $this->db->where(array('cKode' => $post['cKode'], 'lDeleted' => 0, ));
                // $this->db->update($valid['table'], $update);
            }
            // End Insert History
            // exit;

            // Insert Log Activity
            $this->lib_sub_core->InsertActivityModule($headData['vUpb_no'], $modul_id, $pk, $activity['iM_activity'], $activity['iSort'], $vRemark, $iapprove);

            if ($iapprove == 2) {

                // validasi
                foreach ($arrValid as $key => $valid) {
                    // Validasi Valid SA
                    if ($valid['validate'] == true) {
                        // Validasi jika Not Valid
                        $sql3 = "SELECT count(*) AS total
                                FROM " . $valid['table'] . " a
                                WHERE a.lDeleted = 0
                                AND a.iValidate = 2
                                AND a.iSubmit_validasi = 1
                                AND a.cKode = '" . $post['cKode'] . "' ";
                        $data3 = $this->db->query($sql3)->row_array();

                        if (!empty($data3['total'])) {
                            $message .= '<br>Masih ada ' . $data3['total'] . ' ' . $valid['name'] . ' yang tidak valid!';
                        }
                    }
                }
                // End validasi
            
                // Menginput Tanggal Start Analisa pada Project Properties
                $sql = ' SELECT a.id,a.raw_id ,a.dApprove_validasi, srb.dSubmit_requirement
                        ,if(a.iApprove_validasi=2, DATE_ADD( dApprove_validasi, INTERVAL 1 + 
                        IF(
                        (WEEK(dApprove_validasi) <> WEEK(DATE_ADD(dApprove_validasi, INTERVAL 1 DAY)))
                        OR (WEEKDAY(DATE_ADD(dApprove_validasi, INTERVAL 1 DAY)) IN (5, 6)),
                        2,0) DAY ) , "") AS tgl_analisa
                        FROM hrd.biflow a
                        JOIN hrd.ss_raw_problems srb ON srb.id = a.raw_id
                        WHERE a.id = "'.$pk.'" 
                        AND a.lDeleted = 0
                        ';
                $datas = $this->db->query($sql)->row_array();
                $tgl_analisa = $datas['tgl_analisa'];
                // print_r($datas);exit;

                // Update status project menjadi "Queue"
                $updateSs['iStatus'] = 9;
                $updateSs['dSubmit_requirement'] = $tgl_analisa;
                $this->db->where('id', $post['raw_id']);
                $this->db->update('hrd.ss_raw_problems', $updateSs);

            } else {
                // Update status project menjadi "Requirement need to be revised"
                $updateSs['iStatus'] = 19;
                $this->db->where('id', $post['raw_id']);
                $this->db->update('hrd.ss_raw_problems', $updateSs);

                //delete log Modul ini
                $deleteLog['lDeleted'] = 1;
                $deleteLog['dupdate'] = date('Y-m-d H:i:s');
                $deleteLog['cUpdate'] = $cNip;
                $this->db->where('idprivi_modules', $modul_id);
                $this->db->where('iKey_id', $pk);
                $this->db->update('erp_privi.' . $activity['vTable_log_activity'], $deleteLog);

                // get Modul ID dari Modul Project Requirement (Modul Sebelumnya)
                $sql = "SELECT a.idprivi_modules
                    FROM erp_privi.privi_modules a
                    WHERE a.isDeleted = 0
                    AND a.vPathModule = 'erpss/bi_flow' ";
                $row = $this->db->query($sql)->row_array();
                $modul_id = $row['idprivi_modules'];

                // Insert Log Revise untuk Modul Sebelumnya (Project Requirement)
                $this->lib_sub_core->InsertActivityModule($headData['vUpb_no'], $modul_id, $pk, $activity['iM_activity'], $activity['iSort'], $vRemark, $iapprove);

                //delete log Modul Sebelumnya (Project Requirement)
                $deleteLog['lDeleted'] = 1;
                $deleteLog['dupdate'] = date('Y-m-d H:i:s');
                $deleteLog['cUpdate'] = $cNip;
                $this->db->where('idprivi_modules', $modul_id);
                $this->db->where('iKey_id', $pk);
                $this->db->update('erp_privi.' . $activity['vTable_log_activity'], $deleteLog);

                //mendapatkan list field pada main table
                $sqlMainFields = 'SHOW COLUMNS FROM ' . $this->maintable;
                $mainFields = $this->db->query($sqlMainFields)->result_array();
                $arrFields = array();

                foreach ($mainFields as $field) {
                    array_push($arrFields, $field['Field']);
                }

                $allActivity = $this->db->get_where('erp_privi.m_modul_activity', array('iM_modul' => $activity['iM_modul']))->result_array();
                $updateReset = array();
                foreach ($allActivity as $act) {
                    foreach ($arrValue as $key => $value) {
                        $field = $act[$key];
                        if (in_array($field, $arrFields) && !array_key_exists($field, $updateReset)) {
                            $updateReset[$field] = $value;
                        }
                    }
                }

                // Update iSubmit modul Project Requirement jadi 0 & iApprove_validasi jadi 1
                $updateReset['iSubmit'] = 0;
                $updateReset['iApprove_validasi'] = 1;
                $this->db->where($this->main_table_pk, $pk);
                $this->db->update($this->maintable, $updateReset);

                // Update iSubmit (Sub Controller per kategori) menjadi 0 hanya untuk yg Not Valid
                foreach ($arrValid as $key => $valid) {
                    $updSub['iSubmit'] = 0;
                    $updSub['iSubmit_validasi'] = 0;
                    $this->db->where(array('lDeleted' => 0, 'iValidate' => 2, 'cKode' => $post['cKode']));
                    $this->db->update($valid['table'], $updSub);
                }

                // Update iSubmit (Sub Controller per kategori) menjadi 0 untuk yg Valid
                foreach ($arrValid as $key => $valid) {
                    $updSub['iSubmit'] = 0;
                    $updSub['iSubmit_validasi'] = 0;
                    $this->db->where(array('lDeleted' => 0, 'iValidate' => 1, 'cKode' => $post['cKode']));
                    $this->db->update($valid['table'], $updSub);
                }
            }
        }

        // Send Notifikasi
        $proses = '';
        if($iapprove == '2'){
            $proses = 'Approve';
        }else if($iapprove == '1'){
            $proses = 'Revise';
        }
        $subject= $this->title . ' -> Approval';
        $content= 'Diberitahukan telah ada proses <b>'.$proses.'</b> ' . $this->title;

        $sqlDataNotif = "SELECT ss.id AS 'SSID',
                        ss.problem_subject AS 'Project Name',
                        CONCAT_WS(' - ', e1.cNip, e1.vName) AS 'Requestor',
                        CONCAT_WS(' - ', e2.cNip, e2.vName) AS 'Project Manager'
                        FROM hrd.biflow bi
                        JOIN hrd.ss_raw_problems ss ON ss.id = bi.raw_id
                        JOIN hrd.ss_raw_pic rp ON rp.rawid = ss.id 
                        JOIN hrd.employee e1 ON e1.cNip = bi.cRequestor
                        JOIN hrd.employee e2 ON e2.cNip = rp.pic
                        WHERE bi.lDeleted = 0 AND ss.Deleted = 'No' AND rp.Deleted = 'No' AND e1.lDeleted = 0 AND e2.lDeleted = 0
                        AND rp.iRoleId = 1
                        AND ss.id = '".$post['raw_id']."' ";
        $data  = $this->db->query($sqlDataNotif)->row_array();

        // get data maintable
        $sqlH = "SELECT * 
                    FROM hrd.biflow bi
                    WHERE bi.lDeleted = 0
                    AND bi.cKode = '".$post['cKode']."' ";
        $rowH = $this->db->query($sqlH)->row_array();

        $nipCc = $this->lib_utilitas->nipNotifProjectRequirement($this->user->gNIP, $post['raw_id']);

        $to    = $rowH['cRequestor'].",".$this->user->gNIP;
        $cc    = $nipCc;

        // $rowsD = $this->db_kanban0->get_where('kanban.monitor_dr_d', array('cReqPenyimpangan' => $postData[$this->main_table_key]))->result_array();
        // foreach ($rowsD as $ad) {
        //     $to   = ( $to == '' ) ? $ad['cPic'] : $to.','.$ad['cPic'];
        // }

        $this->lib_erpss->generateAndSendNotificationPersonal($team, $subject, $content, $data, $to, $cc);

        $data['status'] = true;
        $data['last_id'] = $post[$this->main_table_pk];
        $data['group_id'] = $post['group_id'];
        $data['modul_id'] = $post['modul_id'];
        $data['company_id'] = $post['company_id'];
        return json_encode($data);
    }

    //Ini Merupakan Standart Confirm yang digunakan di erp
    public function confirm_view()
    {
        $echo = '<script type="text/javascript">
                     function submit_ajax(form_id) {
                        return $.ajax({
                            url     : $("#"+form_id).attr("action"),
                            type    : $("#"+form_id).attr("method"),
                            data    : $("#"+form_id).serialize(),
                            success : function(data) {
                                var o           = $.parseJSON(data);
                                var last_id     = o.last_id;
                                var group_id    = o.group_id;
                                var modul_id    = o.modul_id;
                                var company_id    = o.company_id;
                                var url         = "' . base_url() . 'processor/' . $this->urlpath . '";
                                if(o.status == true) {
                                    $("#alert_dialog_form").dialog("close");
                                         $.get(url+"?action=update&id="+last_id+"&foreign_key=0&company_id=company_id&group_id="+group_id+"&modul_id="+modul_id, function(data) {
                                         $("div#form_' . $this->url . '").html(data);
                                    });
                                }
                                reload_grid("grid_' . $this->url . '");
                            }

                         })
                     }
                 </script>';
        $echo .= '<h1>Confirm</h1><br />';
        $echo .= '<form id="form_' . $this->url . '_confirm" action="' . base_url() . 'processor/' . $this->urlpath . '?action=confirm_process" method="post">';
        $echo .= '<div style="vertical-align: top;">';
        $echo .= 'Remark :
                <input type="hidden" name="' . $this->main_table_pk . '" value="' . $this->input->get($this->main_table_pk) . '" />
                <input type="hidden" name="modul_id" value="' . $this->input->get('modul_id') . '" />
                <input type="hidden" name="group_id" value="' . $this->input->get('group_id') . '" />
                <input type="hidden" name="iM_modul_activity" value="' . $this->input->get('iM_modul_activity') . '" />

                <textarea name="vRemark"></textarea>
        <button type="button" onclick="submit_ajax(\'form_' . $this->url . '_confirm\')">Confirm</button>';

        $echo .= '</div>';
        $echo .= '</form>';
        return $echo;
    }

    public function confirm_process()
    {
        $post = $this->input->post();
        $cNip = $this->user->gNIP;
        $vName = $this->user->gName;
        $pk = $post[$this->main_table_pk];
        $vRemark = $post['vRemark'];
        $modul_id = $post['modul_id'];
        $id_activity = $post['iM_modul_activity'];

        $activity = $this->db->get_where('erp_privi.m_modul_activity', array('iM_modul_activity' => $id_activity, 'lDeleted' => 0))->row_array();
        $headData = $this->db->get_where($this->maintable, array($this->main_table_pk => $pk))->row_array();

        $this->lib_sub_core->InsertActivityModule($headData['vUpb_no'], $modul_id, $pk, $activity['iM_activity'], $activity['iSort'], $vRemark, 2);

        $data['status'] = true;
        $data['last_id'] = $post[$this->main_table_pk];
        $data['group_id'] = $post['group_id'];
        $data['modul_id'] = $post['modul_id'];
        return json_encode($data);
    }

    /*Confirm View*/

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

    //Standart Setiap table harus memiliki tCreate , cCreate, tUpdate, cUpdate
    public function before_insert_processor($row, $postData)
    {
        $postData['tCreate'] = date('Y-m-d H:i:s');
        $postData['cCreate'] = $this->user->gNIP;
        $postData['iCompanyID'] = $this->input->get('company_id');

        $postData['lDeleted'] = 0;

        $postData['iSubmit'] = 0;

        $sql = ' SELECT a.id,a.raw_id ,a.dApprove_validasi, srb.dSubmit_requirement
                ,if(a.iApprove_validasi=2, DATE_ADD( dApprove_validasi, INTERVAL 1 + 
                    IF(
                        (WEEK(dApprove_validasi) <> WEEK(DATE_ADD(dApprove_validasi, INTERVAL 1 DAY)))
                        OR (WEEKDAY(DATE_ADD(dApprove_validasi, INTERVAL 1 DAY)) IN (5, 6)),
                        2,0) DAY ) , "") AS tgl_analisa
                FROM hrd.biflow a
                JOIN hrd.ss_raw_problems srb ON srb.id = a.raw_id
                WHERE a.lDeleted = 0
            ';
        $datas = $this->db->query($sql)->result();

        if ($postData['isdraft'] == true) {
            $postData['iSubmit'] = 0;
        } else {
            $postData['iSubmit'] = 1;
        }

        if ($postData['tUpdate'] == '') {
            unset($postData['tUpdate']);
        }

        $postData = $this->lib_sub_core->getAutoNumberModule($this->iModul_id, $postData, $this->url);

        return $postData;
    }

    public function before_update_processor($row, $postData)
    {
        $postData['tUpdate'] = date('Y-m-d H:i:s');
        $postData['cUpdate'] = $this->user->gNIP;

        if ($postData['isdraft'] == true) {
            $postData['iSubmit_validasi'] = 0;
        } else {
            $postData['iSubmit_validasi'] = 1;
            $postData['dSubmit_validasi'] = date('Y-m-d H:i:s');
            $postData['cSubmit_validasi'] = $this->user->gNIP;
        }

        return $postData;
    }

    public function after_insert_processor($fields, $id, $postData)
    {
        $post = $this->input->post();

        // // get IM Modul id tipe Upload Grid
        // $sql = "SELECT b.iM_modul_fields
        //         FROM erp_privi.m_modul a
        //         JOIN erp_privi.m_modul_fields b ON b.iM_modul = a.iM_modul
        //         WHERE b.iM_jenis_field = 16 # Upload Grid
        //         AND a.idprivi_modules = '".$this->modul_id."' ";
        // $row = $this->db->query($sql)->row_array();

        // // get File Upload Group
        // $sqlFile = "SELECT *
        //             FROM ps.group_file_upload a
        //             WHERE a.iM_modul_fields = '".$row['iM_modul_fields']."'
        //             AND a.idHeader_File = '".$id."' ";
        // $rowFile = $this->db->query($sqlFile)->result_array();

        // // Insert History
        // $insert['cKode_het'] = $postData['cKode_het'];
        // $insert['c_iteno'] = $postData['c_iteno'];
        // $insert['iComp_mnf'] = $postData['iComp_mnf'];
        // $insert['cInisiator'] = $postData['cInisiator'];
        // $insert['iHet_ub'] = $postData['iHet_ub'];
        // $insert['iHet_kp'] = $postData['iHet_kp'];
        // $insert['mKeterangan'] = $postData['mKeterangan'];
        // $insert['cPic_update'] = $postData['cPic_update'];
        // $insert['iCompanyID'] = $postData['iCompanyID'];
        // $insert['cCreate'] = $postData['cCreate'];
        // $insert['tCreate'] = $postData['tCreate'];
        // $insert['lDeleted'] = $postData['lDeleted'];

        // if($this->db->insert('ps.biflow_history', $insert)){
        //     $insert_id = $this->db->insert_id();
        //     // Insert History File
        //     foreach ($rowFile as $key => $val) {
        //         $insert2['iHistory'] = $insert_id;
        //         $insert2['vFilename'] = $val['vFilename'];
        //         $insert2['vFilename_generate'] = $val['vFilename_generate'];
        //         $insert2['tKeterangan'] = $val['tKeterangan'];
        //         $insert2['dCreate'] = $val['dCreate'];
        //         $insert2['cCreate'] = $val['cCreate'];
        //         $insert2['dUpdate'] = $val['dUpdate'];
        //         $insert2['cUpdate'] = $val['cUpdate'];
        //         $insert2['iDeleted'] = $val['iDeleted'];
        //         $this->db->insert('ps.biflow_history_file', $insert2);
        //     }
        // }
        // // End Insert History

        if ($postData['iSubmit'] == 1) {
            $activities = $this->lib_sub_core->get_current_module_activities($this->modul_id, $id);
            if ($postData['isdraft'] != true && count($activities) > 0) {
                $act = $activities[0];
                $this->lib_sub_core->InsertActivityModule($this->ViewUPB($id), $this->modul_id, $id, $act['iM_activity'], $act['iSort']);
            }
        }
    }

    public function after_update_processor($fields, $id, $postData)
    {
        $post = $this->input->post();

        if ($postData['iSubmit'] == 1) {

            $activities = $this->lib_sub_core->get_current_module_activities($this->modul_id, $postData[$this->main_table_pk]);
            if ($postData['isdraft'] != true && count($activities) > 0) {
                $act = $activities[0];
                $this->lib_sub_core->InsertActivityModule($this->ViewUPB($id), $this->modul_id, $id, $act['iM_activity'], $act['iSort']);
            }
        }
    }

    public function manipulate_grid_button($button)
    {
        // get NIP Author
        $AuthModul = $this->lib_sub_core->getAuthorModul($this->modul_id);
        $nipAuthor = explode(',', $AuthModul['vNip_author']);

        // // Kondisi Khusus jika lvl 2 keatas hanya bisa View
        // $sql = "SELECT b.iLvlemp
        //         FROM hrd.employee a
        //         JOIN hrd.position b ON b.iPostId = a.iPostID
        //         WHERE a.lDeleted = 0
        //         AND a.cNip = '" . $this->user->gNIP . "' ";
        // $row = $this->db->query($sql)->row_array();

        // if ($row['iLvlemp'] > 1) {
        //     if (!in_array($this->user->gNIP, $nipAuthor)) {
        //         unset($button['create']);
        //     }
        // }

        return $button;
    }

    public function manipulate_insert_button($buttons)
    {
        $cNip = $this->user->gNIP;
        $data['upload'] = 'upload_custom_grid';
        $js = $this->load->view('js/standard_js', $data, true);

        $iframe = '<iframe name="' . $this->url . '_frame" id="' . $this->url . '_frame" height="0" width="0"></iframe>';

        $save_draft = '<button id="button_save_draft_' . $this->url . '"  class="ui-button-text icon-save" >Save</button>';

        $save_draft .= '<script>
                           $("#button_save_draft_' . $this->url . '").on("click", function(){
                                // var req = $("#form_create_' . $this->url . ' input.required, #form_create_' . $this->url . ' select.required, #form_create_' . $this->url . ' textarea.required");
                                // var conf = 0;
                                // var alert_message = "";

                                // $.each(req, function(i, v) {
                                //     $(this).removeClass("error_text");
                                //     if ($.trim($(this).val()) == "") {
                                //         var id = $(this).attr("id");
                                //         var label = $("label[for=\'" + id + "\']").text();
                                //         label = label.replace("*", "");
                                //         alert_message += "<br/><b>" + label + "</b>" + required_message;
                                //         $(this).addClass("error_text");
                                //         conf++;
                                //     }
                                // })

                                // if (conf > 0) {
                                //     _custom_alert(alert_message, "Error!", "info", "' . $this->url . '", 1, 5000);
                                // }else{
                                //     save_draft_btn_multiupload(\'' . $this->url . '\', \' ' . base_url() . 'processor/' . $this->urlpath . '?draft=true&company_id=' . $this->input->get('company_id') . '&group_id=' . $this->input->get('group_id') . '&modul_id=' . $this->input->get('modul_id') . '\',this,true )
                                // }

                                save_draft_btn_multiupload(\'' . $this->url . '\', \' ' . base_url() . 'processor/' . $this->urlpath . '?draft=true&company_id=' . $this->input->get('company_id') . '&group_id=' . $this->input->get('group_id') . '&modul_id=' . $this->input->get('modul_id') . '\',this,true )
                            });

                        </script>';

        $save = '<button onclick="javascript:save_btn_multiupload(\'' . $this->url . '\', \' ' . base_url() . 'processor/' . $this->urlpath . '?company_id=' . $this->input->get('company_id') . '&group_id=' . $this->input->get('group_id') . '&modul_id=' . $this->input->get('modul_id') . ' \',this,true )"  id="button_save_submit_' . $this->url . '"  class="ui-button-text icon-save" >Save &amp; Submit</button>';

        $AuthModul = $this->lib_sub_core->getAuthorModul($this->modul_id);
        $ParticipantModul = $this->lib_sub_core->getAuthorModul($this->modul_id);
        $arrParticipantModul = explode(',', $ParticipantModul['vDept_participant']);

        $arrTeam = explode(',', $this->team);
        $nipAuthor = explode(',', $AuthModul['vNip_author']);

        $bFound = (count(array_intersect($arrParticipantModul, $arrTeam))) ? true : false;

        if (in_array($AuthModul['vDept_author'], $arrTeam) || in_array($this->user->gNIP, $nipAuthor) || $this->isAdmin == true || $bFound == true) {

            $buttons['save'] = $iframe . $save_draft . $js;
        } else {
            unset($buttons['save']);
            $buttons['save'] = '<span style="color:red;" title="' . $arrParticipantModul . '">You\'re Dept not Authorized</span>';
        }

        return $buttons;
    }

    public function manipulate_update_button($buttons, $rowData)
    {
        // print_r($rowData);
        $peka = $rowData[$this->main_table_pk];
        $iupb_id = 0;
        $cSumber = $rowData['cSumber'];
        $cTeam_pd = $rowData['cTeam_pd'];
        $cKode_referensi = $rowData['cKode_referensi'];
        $raw_id = $rowData['raw_id'];

        //Load Javascript In Here
        $cNip = $this->user->gNIP;
        $data['upload'] = 'upload_custom_grid';
        $js = $this->load->view('js/standard_js', $data, true);
        $js .= $this->load->view('js/upload_js');

        $iframe = '<iframe name="' . $this->url . '_frame" id="' . $this->url . '_frame" height="0" width="0"></iframe>';

        if ($this->input->get('action') == 'view') {
            unset($buttons['update']);
        } else {

            $sButton = $iframe . $js;

            $isOpenEditing = $this->lib_sub_core->getOpenEditing($this->modul_id, $peka);

            if ($isOpenEditing) {
                $update_draft = '<button onclick="javascript:update_draft_btn(\'' . $this->url . '\', \' ' . base_url() . 'processor/' . $this->urlpath . '?draft=true \',this,true )"  id="button_update_draft_"' . $this->url . '"  class="ui-button-text icon-save" >Update open Editing</button>';
                $sButton .= $update_draft;
            } else {

                $activities = $this->lib_sub_core->get_current_module_activities($this->modul_id, $peka);
                $getLastStatusApprove = $this->lib_sub_core->getLastStatusApprove($this->modul_id, $peka);

                foreach ($activities as $act) {
                    $update_draft = '<button id="button_update_draft_' . $this->url . '" class="ui-button-text icon-save" >Update</button>';

                    $update_draft .= '<script>
                                       $("#button_update_draft_' . $this->url . '").on("click", function(){
                                            for (instance in CKEDITOR.instances) {
                                                CKEDITOR.instances[instance].updateElement();
                                            }
                                            
                                            var req = $("#form_update_' . $this->url . ' input.required, #form_update_' . $this->url . ' select.required, #form_update_' . $this->url . ' textarea.required");
                                            var conf = 0;
                                            var alert_message = "";

                                            $.each(req, function(i, v) {
                                                $(this).removeClass("error_text");
                                                if ($.trim($(this).val()) == "") {
                                                    var id = $(this).attr("id");
                                                    var label = $("label[for=\'" + id + "\']").text();
                                                    label = label.replace("*", "");
                                                    alert_message += "<br/><b>" + label + "</b>" + required_message;
                                                    $(this).addClass("error_text");
                                                    conf++;
                                                }
                                            })

                                            if (conf > 0) {
                                                _custom_alert(alert_message, "Error!", "info", "' . $this->url . '", 1, 5000);
                                            }else{
                                                update_draft_btn(\'' . $this->url . '\', \' ' . base_url() . 'processor/' . $this->urlpath . '?draft=true&modul_id=' . $this->input->get('modul_id') . '&iM_modul_activity=' . $act['iM_modul_activity'] . ' \',this,true );
                                            }
                                        });

                                    </script>';

                    $url_process = base_url() . 'processor/' . $this->urlpath . '?iM_modul_activity=' . $act['iM_modul_activity'] . '&iM_activity=' . $act['iM_activity'] . '&last_id=' . $peka . '&peka=' . $this->main_table_pk . '&' . $this->main_table_pk . '=' . $peka . '&company_id=' . $this->input->get('company_id') . '&group_id=' . $this->input->get('group_id') . '&modul_id=' . $this->input->get('modul_id') . '&action=';

                    $update = '<button onclick="javascript:update_btn_back(\'' . $this->url . '\', \' ' . base_url() . 'processor/' . $this->urlpath . '?raw_id='.$raw_id.'&company_id=' . $this->input->get('company_id') . '&modul_id=' . $this->input->get('modul_id') . '&iM_modul_activity=' . $act['iM_modul_activity'] . '&group_id=' . $this->input->get('group_id') . 'modul_id=' . $this->input->get('modul_id') . ' \',this,false )"  id="button_update_submit_"' . $this->url . '"  class="ui-button-text icon-save" >Update & Submit</button>';

                    $approve = '<button onclick="javascript:load_popup(\' ' . base_url() . 'processor/' . $this->urlpath . '?action=approve&iM_modul_activity=' . $act['iM_modul_activity'] . '&iM_activity=' . $act['iM_activity'] . '&' . $this->main_table_pk . '=' . $peka . '&raw_id='.$raw_id.'&iupb_id=' . $iupb_id . '&cKode=' . $rowData['cKode'] . '&company_id=' . $this->input->get('company_id') . '&group_id=' . $this->input->get('group_id') . '&modul_id=' . $this->input->get('modul_id') . ' \')"  id="button_approve_"' . $this->url . '"  class="ui-button-text icon-save" >Approve</button>';

                    $reject = '<button onclick="javascript:load_popup(\' ' . base_url() . 'processor/' . $this->urlpath . '?action=reject&iM_modul_activity=' . $act['iM_modul_activity'] . '&iM_activity=' . $act['iM_activity'] . '&' . $this->main_table_pk . '=' . $peka . '&raw_id='.$raw_id.'&iupb_id=' . $iupb_id . '&cKode=' . $rowData['cKode'] . '&company_id=' . $this->input->get('company_id') . '&group_id=' . $this->input->get('group_id') . '&modul_id=' . $this->input->get('modul_id') . ' \' )"  id="button_reject_"' . $this->url . '"  class="ui-button-text icon-save" >Revise</button>';

                    $confirm = '<button onclick="javascript:load_popup(\' ' . base_url() . 'processor/' . $this->urlpath . '?action=confirm&iM_modul_activity=' . $act['iM_modul_activity'] . '&iM_activity=' . $act['iM_activity'] . '&' . $this->main_table_pk . '=' . $peka . '&raw_id='.$raw_id.'&iupb_id=' . $iupb_id . '&cKode=' . $rowData['cKode'] . '&company_id=' . $this->input->get('company_id') . '&group_id=' . $this->input->get('group_id') . '&modul_id=' . $this->input->get('modul_id') . ' \')"  id="button_approve_"' . $this->url . '"  class="ui-button-text icon-save" >Confirm</button>';

                    switch ($act['iType']) {
                        case '1':
                            # Update
                            $sButton .= $update_draft . $update;
                            break;
                        case '2':
                            # Approval
                            if ($getLastStatusApprove) {
                                $sButton .= $approve . $reject;
                            } else {
                                $sButton .= 'Last Activity Reject';
                            }

                            break;
                        case '3':
                            # Confirmation
                            if ($getLastStatusApprove) {
                                $sButton .= $confirm;
                            } else {
                                $sButton .= 'Last Activity Reject';
                            }

                            break;
                        default:
                            # Update
                            $sButton .= $update_draft;
                            break;
                    }

                    $arrNipAssign = explode(',', $act['vNip_assigned']);
                    $arrTeam = explode(',', $this->team);

                    $arrTeamID = explode(',', $this->teamID);
                    $arrParticipantModul = explode(',', $act['vDept_participant']);
                    $bFound = (count(array_intersect($arrParticipantModul, $arrTeam))) ? true : false;

                    $arrAssigned = explode(',', $act['vDept_assigned']);
                    $depAssigned = (count(array_intersect($arrAssigned, $arrTeam))) ? true : false;

                    // print_r($arrTeam);

                    if ($depAssigned == true || in_array($this->user->gNIP, $arrNipAssign) || $bFound == true) {

                        $magrAndCief = $this->lib_erpss->managerAndChiefInCode($act['vDept_assigned']);

                        if ($act['iType'] > 1) {
                            $arrmgrAndCief = explode(',', $magrAndCief);
                            if (in_array($this->user->gNIP, $arrmgrAndCief) || in_array($this->user->gNIP, $arrNipAssign)) {
                            } else {
                                $sButton = '<span style="color:red;" title=" /* . print_r($arrmgrAndCief) . */ ">You\'re not Authorized to Approve</span>';
                            }
                        }
                    } else {
                        $sButton = '<span style="color:red;" title="' . $act['vDept_assigned'] . '">You\'re Dept not Authorized</span>';
                    }
                }
            }

            $buttons['update'] = $sButton;
        }

        return $buttons;
    }

    public function whoAmI($nip)
    {
        $sql = 'select b.vDescription as vdepartemen,a.*,b.*,c.iLvlemp
                        from hrd.employee a
                        join hrd.msdepartement b on b.iDeptID=a.iDepartementID
                        join hrd.position c on c.iPostId=a.iPostID
                        where a.cNip ="' . $nip . '"
                        ';

        $data = $this->db->query($sql)->row_array();
        return $data;
    }

    public function download($vFilename)
    {
        $this->load->helper('download');
        $name = $vFilename;
        $id = $_GET['id'];
        $tempat = $_GET['path'];
        $path = file_get_contents('./files/ksk/' . $tempat . '/' . $id . '/' . $name);
        force_download($name, $path);
    }

    //Output
    public function output()
    {
        $this->index($this->input->get('action'));
    }

    private function ViewUPB($id = 0)
    {
        $upb = $this->db->get_where($this->main_table, array($this->main_table_pk => $id, 'lDeleted' => 0))->result_array();
        $arrUPB = array();
        foreach ($upb as $u) {
            if (isset($u['iupb_id'])) {
                array_push($arrUPB, $u['iupb_id']);
            }
        }
        return $arrUPB;
    }

    public function getAutoNumberModule($iModuleId, $postData, $url)
    {
        $this->_ci->db->where(
            array(
                'm_modul_fields.iM_modul' => $iModuleId,
                'm_modul_fields.lDeleted' => 0,
                'm_modul_fields.iM_jenis_field' => 9,
            )
        )
            ->join("erp_privi.m_modul", "m_modul.iM_modul=m_modul_fields.iM_modul")
            ->select("m_modul.vKode,m_modul_fields.vNama_field,m_modul.vTable_name");
        $_qmodule = $this->_ci->db->get('erp_privi.m_modul_fields');
        if ($_qmodule->num_rows() > 0) {
            $_dataFields = $_qmodule->result_array();
            foreach ($_dataFields as $kField => $_valField) {

                //Get Last Request
                $iCompany = $this->_ci->input->get('company_id');
                $serialCompany = $iCompany == 3 ? "NPL-" : "ETC-";
                $nomorlike = $serialCompany . $_valField["vKode"] . date('y') . '-' . date('m') . '-';
                $_fieldWhere = str_replace($url . "_", "", $_valField["vNama_field"]);
                $this->_ci->db->like($_fieldWhere, $nomorlike)
                    ->order_by($_fieldWhere, "DESC");
                $_querow = $this->_ci->db->get($_valField["vTable_name"]);
                $nilai = 1;
                //if($this->_ci->db->affected_rows()==FALSE){
                // echo $this->_ci->db->last_query();
                $row = $_querow->row_array();
                if (isset($row[$_fieldWhere])) {
                    $nilai = str_replace($nomorlike, "", $row[$_fieldWhere]);
                    $int = (int) $nilai;
                    if ($int > 0) {
                        $nilai = $int + 1;
                    } else {
                        $nilai = 1;
                    }
                }
                // }else{
                //   echo $this->_ci->db->last_query();
                //  echo "MODULE DETAILS NOT CONFIG---02";
                //  exit();
                // }

                $nomor = $nomorlike . str_pad($nilai, 3, "0", STR_PAD_LEFT);
                //$postData[$_valField["vNama_field"]]= $nomor;
                $postData[$_fieldWhere] = $nomor;
            }
        }
        return $postData;
    }
}
