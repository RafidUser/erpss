<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class bi_flow extends MX_Controller
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

        $this->title = 'Project Requirement';
        $this->url = 'bi_flow';
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
            'iSubmit' => array('label' => 'Status Submit', 'width' => 150, 'align' => 'center', 'search' => true),
            // 'iApprove' => array('label' => 'Status Approval', 'width' => 150, 'align' => 'center', 'search' => true),
            'iApprove_validasi' => array('label' => 'Status Validasi', 'width' => 150, 'align' => 'center', 'search' => true),
            'ss_raw_problems.iStatus' => array('label' => 'Status Project', 'width' => 150, 'align' => 'center', 'search' => true),
            'setting_prioritas_detail.iSortApproved' => array('label' => 'Priority Direksi', 'width' => 120, 'align' => 'center', 'search' => false),
        );

        $datagrid['setQuery'] = array(
            0 => array('vall' => 'biflow.lDeleted', 'nilai' => 0),
        );

        $datagrid['jointableinner'] = array(
            0 => array('hrd.ss_raw_problems' => 'ss_raw_problems.id = biflow.raw_id'),
            // 1 => array('hrd.ss_raw_pic' => 'ss_raw_pic.rawid = ss_raw_problems.id AND ss_raw_pic.Deleted = "No" AND ss_raw_pic.iRoleId = 1 AND ss_raw_pic.pic is not null'),
        );

        $datagrid['jointableleft'] = array(
            0 => array('hrd.ss_raw_pic' => 'ss_raw_pic.rawid = ss_raw_problems.id AND ss_raw_pic.Deleted = "No" AND ss_raw_pic.iRoleId = 1 AND ss_raw_pic.pic is not null'),
            1 => array('hrd.ss_project_status' => 'ss_project_status.id = ss_raw_problems.iStatus AND ss_project_status.iDeleted = 0'),
            2 => array('hrd.setting_prioritas_detail' => 'setting_prioritas_detail.rawid = ss_raw_problems.id AND setting_prioritas_detail.lDeleted = 0'),
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
        $grid->changeFieldType('iSubmit', 'combobox', '', array('' => '--Pilih--', 0 => 'Draft - Need to be Submit', 1 => 'Submited'));
        $grid->changeFieldType('iApprove', 'combobox', '', array('' => '--Pilih--', 0 => 'Waiting Approval', 1 => 'Rejected', 2 => 'Approved'));
        $grid->changeFieldType('iApprove_validasi', 'combobox', '', array('' => '--Pilih--', 0 => 'Waiting Validation', 1 => 'Revise', 2 => 'Validated'));
        
        $grid->changeFieldType('ss_raw_problems.iStatus', 'combobox', '', array('' => '--Pilih--', 3 => 'Analysis and Design', 5 => 'Development'
                                , 6 => 'UAT', 8 => 'Postponed', 9 => 'Queue', 11 => 'Preliminary & Feasibility', 13 => 'Finish'
                                , 14 => 'Canceled', 15 => 'Work In Progress', 16 => 'Requirement Submitted', 17 => 'Requirement Accepted'
                                , 18 => 'Waiting for Specs', 19 => 'Requirement Need to be Revise'));

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

        $grid->searchOperand('ss_raw_problems.iStatus', 'eq');
        $grid->setGridView('grid');

        switch ($action) {
            case 'json':
                $grid->getJsonData();
                break;
            case 'detail_sub':
                echo $this->detail_sub();
                break;
            case 'load_formula':
                echo $this->load_formula();
                break;
            case 'getData2':
                echo $this->getDataValidasiKategori2();
                break;
            case 'getData':
                echo $this->getDataValidasiKategori();
                break;
            case 'getDataParam':
                echo $this->getDataValidasiKategoriParam();
                break;
            case 'getDataValidasi':
                echo $this->getDataValidasi();
                break;
            case 'getDataValidasiSub':
                echo $this->getDataValidasiSub();
                break;
            case 'uploadFile':
                echo $this->lib_sub_core->uploadFile($this);

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
                // echo 'kesini';
                // exit;
                //$this->lib_sub_core->downloadFile($this);
                $this->download();
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
            case 'getPicList':
                $get = $this->input->get();
                $term = $this->input->get('term');
                $datas["term"] = $term;
                $term = trim($this->input->get('term'));
                $company_id = $this->input->get('company_id');
                $sql = 'SELECT a.cNip AS valval,CONCAT_WS(" - ",a.cNip,a.vName) AS showshow
                            FROM hrd.employee a
                            WHERE 
                            ( a.cNip LIKE "%' . $term . '%"  OR  a.vName LIKE "%' . $term . '%" )
                            order by a.vName
                            limit 20
                            ';
                $arr = $this->db->query($sql)->result_array();
                // echo '<pre>'.$sql;
                // exit;
                $dt = $this->db->query($sql);
                $data = array();
                if ($dt->num_rows > 0) {
                    $row_array['value'] = "-----Pilih----";
                    $row_array['id'] = "";

                    array_push($data, $row_array);
                    foreach ($dt->result_array() as $line) {

                        $row_array['value'] = trim($line["showshow"]);
                        $row_array['id'] = trim($line["valval"]);

                        array_push($data, $row_array);
                    }
                } else {
                    $row_array['value'] = "NOT FOUND";
                    $row_array['id'] = "";

                    array_push($data, $row_array);
                }
                $datas["nilai"] = $data;
                echo json_encode($datas);
                break;
            default:
                $grid->render_grid();
                break;
        }
    }


    public function detail_sub()
    {
        $post = $this->input->get();
        $data['post'] = $post;

        $rowD = $this->db->get_where('hrd.biflow_kategori', array('cKode_kategori' => $post['cKode_kategori']))->row();

        if ($rowD->lHas_param == 2) {
            $o = $this->load->view('partial/modul/summary/_summary_detail_param', $data, true);
        } else {
            $o = $this->load->view('partial/modul/summary/_summary_detail', $data, true);
        }
        echo $o;
    }

    public function getDataValidasiKategoriParam()
    {
        // print_r($_GET);
        // exit;
        $param = $this->input->get();

        $rowD = $this->db->get_where('hrd.biflow_iat_history', array('id' => $param['idRow']))->row();

        $id_hist = $rowD->cKode_history;
        $cKode_iat = $rowD->cKode_iat;
        $where = " ";

        $page = $_GET['page']; // get the requested page
        $limit = $_GET['rows']; // get how many rows we want to have into the grid
        $sidx = $_GET['sidx']; // get index row - i.e. user click to sort
        $sord = $_GET['sord']; // get the direction
        $responce = '';
        $count = 0;
        if (!$sidx) {
            $sidx = 1;
        }

        $sql = '
                SELECT a.id,b.vName,if(a.iStatus_bi=2,"Yes","No") as iStatus_bi,a.mKeterangan_bi
                ,if(a.iStatus_sa=2,"Yes","No") as iStatus_sa,a.mKeterangan_sa
                FROM hrd.biflow_iat_param_history a
                JOIN hrd.biflow_ms_parameter b ON b.cKode_parameter=a.cKode_parameter
                WHERE a.lDeleted=0
                #AND a.cKode_history="NPL-BFL-22-10-005-HIST-2022_10_06_12_03_14"
                #AND a.cKode_iat="NPL-BFL-22-10-005-1"
                AND a.cKode_history = "' . $id_hist . '"
                AND a.cKode_iat = "' . $cKode_iat . '"
        ';

        $query = $this->db->query($sql . $where);
        // print_r($this->db->last_query());
        // exit;
        $count = $query->num_rows();

        if ($count > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages) {
            $page = $total_pages;
        }

        $start = $limit * $page - $limit;
        if ($start > 0) {
            $start = $start;
        } else {
            $start = 0;
        }

        $sql_grp_limit = ' ORDER BY ' . $sidx . ' ' . $sord . '   LIMIT ' . $start . ' , ' . $limit . ' ';
        //  echo '<pre>'.$sql.$where.$sql_grp_limit;
        //  exit;
        $query = $this->db->query($sql . $where . $sql_grp_limit);

        if ($query->num_rows() > 0) {
            $responce = '';
            $i = 0;
            foreach ($query->result_array() as $row) {

                $responce->rows[$i]['id'] = $row['id'];
                $responce->rows[$i]['cell'] = array(
                    $row['vName']
                    , $row['iStatus_bi']
                    , $row['mKeterangan_bi']
                    , $row['iStatus_sa']
                    , $row['mKeterangan_sa'],
                );
                $i++;

            }

        }

        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;

        echo json_encode($responce);

    }
    public function getDataValidasiKategori()
    {
        // print_r($_GET);
        // exit;
        $param = $this->input->get();
        $id_hist = $param['id_hist'];
        $where = " ";

        $page = $_GET['page']; // get the requested page
        $limit = $_GET['rows']; // get how many rows we want to have into the grid
        $sidx = $_GET['sidx']; // get index row - i.e. user click to sort
        $sord = $_GET['sord']; // get the direction
        $responce = '';
        $count = 0;
        if (!$sidx) {
            $sidx = 1;
        }

        $rowD = $this->db->get_where('hrd.biflow_kategori', array('cKode_kategori' => $param['cKode_kategori']))->row();

        $sql = '
                SELECT  a.id,if(a.iValidate=2,"Not Valid","Valid") AS validasi,a.cKode_history
                ,h.mKeterangan,h.mValidate
                FROM ' . $rowD->cTable_history . ' a
                JOIN ' . $rowD->cTable_main . ' h ON h.' . $rowD->cTable_main_pk . '=a.' . $rowD->cTable_main_pk . '
                WHERE a.lDeleted=0 AND h.lDeleted=0
                AND a.cKode_history = "' . $id_hist . '"
        ';

        $query = $this->db->query($sql . $where);
        // print_r($this->db->last_query());
        // exit;
        $count = $query->num_rows();

        if ($count > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages) {
            $page = $total_pages;
        }

        $start = $limit * $page - $limit;
        if ($start > 0) {
            $start = $start;
        } else {
            $start = 0;
        }

        $sql_grp_limit = ' ORDER BY ' . $sidx . ' ' . $sord . '   LIMIT ' . $start . ' , ' . $limit . ' ';
        //  echo '<pre>'.$sql.$where.$sql_grp_limit;
        //  exit;
        $query = $this->db->query($sql . $where . $sql_grp_limit);

        if ($query->num_rows() > 0) {
            $responce = '';
            $i = 0;
            foreach ($query->result_array() as $row) {
                $responce->rows[$i]['id'] = $row['id'];
                $responce->rows[$i]['cell'] = array(
                    $row['mKeterangan']
                    , $row['validasi']
                    , $row['mValidate'],
                );
                $i++;

            }

        }

        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;

        echo json_encode($responce);

    }
    
    public function getDataValidasiKategori2()
    {
        // print_r($_GET);
        // exit;
        $param = $this->input->get();
        $id_hist = $param['id_hist'];
        $where = " ";

        $page = $_GET['page']; // get the requested page
        $limit = $_GET['rows']; // get how many rows we want to have into the grid
        $sidx = $_GET['sidx']; // get index row - i.e. user click to sort
        $sord = $_GET['sord']; // get the direction
        $responce = '';
        $count = 0;
        if (!$sidx) {
            $sidx = 1;
        }

        $rowD = $this->db->get_where('hrd.biflow_kategori', array('cKode_kategori' => $param['cKode_kategori']))->row();

        $sql = '
                SELECT  a.id,if(a.iValidate=2,"Not Valid","Valid") AS validasi,a.cKode_history
                ,h.mKeterangan,h.mValidate,f.vFilename,a.vModul
                FROM ' . $rowD->cTable_history . ' a
                JOIN ' . $rowD->cTable_main . ' h ON h.' . $rowD->cTable_main_pk . '=a.' . $rowD->cTable_main_pk . '
                LEFT JOIN ' . $rowD->cTable_main . '_file f ON f.' . $rowD->cTable_main_pk . '=h.' . $rowD->cTable_main_pk . '
                WHERE a.lDeleted=0 AND h.lDeleted=0 AND f.iDeleted=0
                AND a.cKode_history = "' . $id_hist . '"
        ';

        $query = $this->db->query($sql . $where);
        // print_r($this->db->last_query());
        // exit;
        $count = $query->num_rows();

        if ($count > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages) {
            $page = $total_pages;
        }

        $start = $limit * $page - $limit;
        if ($start > 0) {
            $start = $start;
        } else {
            $start = 0;
        }

        $sql_grp_limit = ' ORDER BY ' . $sidx . ' ' . $sord . '   LIMIT ' . $start . ' , ' . $limit . ' ';
        //  echo '<pre>'.$sql.$where.$sql_grp_limit;
        //  exit;
        $query = $this->db->query($sql . $where . $sql_grp_limit);

        if ($query->num_rows() > 0) {
            $responce = '';
            $i = 0;
            foreach ($query->result_array() as $row) {
                $responce->rows[$i]['id'] = $row['id'];
                $responce->rows[$i]['cell'] = array(
                    $row['vModul']
                    , $row['mKeterangan']
                    , $row['validasi']
                    , $row['mValidate'],
                );
                $i++;

            }

        }

        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;

        echo json_encode($responce);

    }

    public function getDataValidasi()
    {
        // print_r($_GET);
        // exit;
        $param = $this->input->get();
        $cKode = $param['cKode'];
        $where = " ";

        $page = $_GET['page']; // get the requested page
        $limit = $_GET['rows']; // get how many rows we want to have into the grid
        $sidx = $_GET['sidx']; // get index row - i.e. user click to sort
        $sord = $_GET['sord']; // get the direction
        $responce = '';
        $count = 0;
        if (!$sidx) {
            $sidx = 1;
        }

        $sql = '
                SELECT a.id,a.dApprove as tgl,b.vName AS pic, if(a.iApprove=2,"Approve","Revise") AS setatus,a.cKode
                ,c.vFilename,c.iDeleted,c.vFilename_generate
                FROM hrd.biflow_history a
                JOIN hrd.employee b ON b.cNip=a.cApprove
                JOIN hrd.biflow_fbk_file_history c ON c.cKode_history=a.cKode_history AND c.iDeleted=0
                WHERE a.lDeleted=0
                #AND a.cKode= "NPL-BFL-22-10-003"
                AND a.cKode = "' . $cKode . '"
        ';

        $query = $this->db->query($sql . $where);
        // print_r($this->db->last_query());
        // exit;
        $count = $query->num_rows();

        if ($count > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages) {
            $page = $total_pages;
        }

        $start = $limit * $page - $limit;
        if ($start > 0) {
            $start = $start;
        } else {
            $start = 0;
        }

        $sql_grp_limit = ' ORDER BY ' . $sidx . ' ' . $sord . '   LIMIT ' . $start . ' , ' . $limit . ' ';
        //  echo '<pre>'.$sql.$where.$sql_grp_limit;
        //  exit;
        $query = $this->db->query($sql . $where . $sql_grp_limit);

        if ($query->num_rows() > 0) {
            $responce = '';
            $i = 0;
            foreach ($query->result_array() as $row) {
                $filenya = $row['vFilename'];
                $vFilename = $row['vFilename'];
                $vFilename_generate = $row['vFilename_generate'];

                if (file_exists('./' . $vFilename_generate)) {
                    $link = base_url() . 'processor/' . $this->urlpath . '?action=download&vFilename_generate=' . $vFilename_generate . '&vFilename=' . $vFilename;
                    $linknya = '<a class="ui-button-text" href="javascript:;" onclick="window.location=\'' . $link . '\'">' . $filenya . '</a>&nbsp;&nbsp;&nbsp;';
                } else {
                    $linknya = $filenya;
                }
                $responce->rows[$i]['id'] = $row['id'];
                $responce->rows[$i]['cell'] = array(
                    $row['tgl']
                    , $row['pic']
                    , $row['setatus']
                    , $linknya,
                );
                $i++;

            }

        }

        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;

        echo json_encode($responce);

    }

    public function getDataValidasiSub()
    {
        // print_r($_GET);
        // exit;
        $param = $this->input->get();
        $roHistory = $this->db->get_where('hrd.biflow_history', array('id' => $_GET['idRow']))->row();

        $page = $_GET['page']; // get the requested page
        $limit = $_GET['rows']; // get how many rows we want to have into the grid
        $sidx = $_GET['sidx']; // get index row - i.e. user click to sort
        $sord = $_GET['sord']; // get the direction
        $responce = '';
        $count = 0;
        if (!$sidx) {
            $sidx = 1;
        }

        //$id_hist = 'NPL-BFL-22-10-003-HIST-2022-10-05 16:51:06';
        $id_hist = $roHistory->cKode_history;

        $sql = '
            SELECT a.*
            FROM hrd.biflow_kategori a
            WHERE a.lDeleted=0
            AND a.lNeed_valid = 2
        ';

        $query = $this->db->query($sql);
        $count = $query->num_rows();

        if ($count > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages) {
            $page = $total_pages;
        }

        $start = $limit * $page - $limit;
        if ($start > 0) {
            $start = $start;
        } else {
            $start = 0;
        }

        $sql_grp_limit = 'ORDER BY a.iUrut ASC  LIMIT ' . $start . ' , ' . $limit . ' ';
        // echo '<pre>'.$sql.$where.$sql_grp_limit;
        // exit;
        $query = $this->db->query($sql . $where . $sql_grp_limit);

        if ($query->num_rows() > 0) {
            $responce = '';
            $i = 0;
            foreach ($query->result_array() as $row) {
                $cKode_kategori = $row['cKode_kategori'];
                $sql_st = 'SELECT if(COUNT(*) > 0,"Not Valid","Valid") as validasi
                            FROM ' . $row['cTable_history'] . ' sub
                            WHERE sub.lDeleted=0
                            AND  sub.iValidate = 2
                            AND sub.' . $row['cTable_history_pk'] . ' = "' . $id_hist . '"
                            ';
                $dSt = $this->db->query($sql_st)->row_array();

                $url = base_url() . 'processor/erpss/bi/flow';
                $btn_detail = '<a href="#" onClick="javascript:browse_with_no_close(\'' . $url . '?action=detail_sub&act=' . $this->input->get('action') . '&cKode_kategori=' . $cKode_kategori . '&id_hist=' . $id_hist . '&company_id=' . $this->input->get('company_id') . '&modul_id=0&group_id=0\',\'DATA\')">' . $dSt['validasi'] . '</a>';

                $responce->rows[$i]['id'] = $row['id'];
                $responce->rows[$i]['cell'] = array(
                    $row['vName']
                    , $btn_detail,

                );
                $i++;

            }

        }

        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;

        echo json_encode($responce);
    }

    public function getDataValidasiSub_old()
    {
        // print_r($_GET);
        // exit;
        $param = $this->input->get();
        $roHistory = $this->db->get_where('hrd.biflow_history', array('id' => $_GET['idRow']))->row();

        $page = $_GET['page']; // get the requested page
        $limit = $_GET['rows']; // get how many rows we want to have into the grid
        $sidx = $_GET['sidx']; // get index row - i.e. user click to sort
        $sord = $_GET['sord']; // get the direction
        $responce = '';
        $count = 0;
        if (!$sidx) {
            $sidx = 1;
        }

        //$id_hist = 'NPL-BFL-22-10-003-HIST-2022-10-05 16:51:06';
        $id_hist = $roHistory->cKode_history;
        $sql = '
                SELECT  2 AS id,"Input Field Master" AS kategori,if(a.iValidate=2,"Not Valid","Valid") AS validasi,a.cKode_history
                ,"-" AS modul,h.mKeterangan,f.vFilename
                FROM hrd.biflow_ifm_history a
                JOIN hrd.biflow_ifm h ON h.cKode_ifm=a.cKode_ifm
                LEFT JOIN hrd.biflow_ifm_file f ON f.cKode_ifm=h.cKode_ifm
                WHERE a.lDeleted=0 AND a.cKode_history="' . $id_hist . '" AND h.lDeleted=0 AND f.iDeleted=0

                UNION
                SELECT  3 AS id,"Input Awal Transaksi" AS kategori,if(a.iValidate=2,"Not Valid","Valid") AS validasi,a.cKode_history
                ,h.vModul AS modul,h.mKeterangan,f.vFilename
                FROM hrd.biflow_iat_history a
                JOIN hrd.biflow_iat h ON h.cKode_iat=a.cKode_iat
                LEFT JOIN hrd.biflow_iat_file f ON f.cKode_iat=h.cKode_iat
                WHERE a.lDeleted=0 AND a.cKode_history="' . $id_hist . '" AND h.lDeleted=0 AND f.iDeleted=0

                UNION
                SELECT  4 AS id,"Output Akhir Transaksi" AS kategori,if(a.iValidate=2,"Not Valid","Valid") AS validasi,a.cKode_history
                ,"-" AS modul,h.mKeterangan,f.vFilename
                FROM hrd.biflow_oat_history a
                JOIN hrd.biflow_oat h ON h.cKode_oat=a.cKode_oat
                LEFT JOIN hrd.biflow_oat_file f ON f.cKode_oat=h.cKode_oat
                WHERE a.lDeleted=0 AND a.cKode_history="' . $id_hist . '" AND h.lDeleted=0 AND f.iDeleted=0

                UNION
                SELECT  5 AS id,"Laporan Akhir Transaksi" AS kategori,if(a.iValidate=2,"Not Valid","Valid") AS validasi,a.cKode_history
                ,"-" AS modul,h.mKeterangan,f.vFilename
                FROM hrd.biflow_lpaw_history a
                JOIN hrd.biflow_lpaw h ON h.cKode_lpaw=a.cKode_lpaw
                LEFT JOIN hrd.biflow_lpaw_file f ON f.cKode_lpaw=h.cKode_lpaw
                WHERE a.lDeleted=0 AND a.cKode_history="' . $id_hist . '" AND h.lDeleted=0 AND f.iDeleted=0

                UNION
                SELECT  6 AS id,"Laporan Proses Akhir" AS kategori,if(a.iValidate=2,"Not Valid","Valid") AS validasi,a.cKode_history
                ,"-" AS modul,h.mKeterangan,f.vFilename
                FROM hrd.biflow_lpak_history a
                JOIN hrd.biflow_lpak h ON h.cKode_lpak=a.cKode_lpak
                LEFT JOIN hrd.biflow_lpak_file f ON f.cKode_lpak=h.cKode_lpak
                WHERE a.lDeleted=0 AND a.cKode_history="' . $id_hist . '" AND h.lDeleted=0 AND f.iDeleted=0

        ';

        $query = $this->db->query($sql . $where);
        $count = $query->num_rows();

        if ($count > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages) {
            $page = $total_pages;
        }

        $start = $limit * $page - $limit;
        if ($start > 0) {
            $start = $start;
        } else {
            $start = 0;
        }

        $sql_grp_limit = 'ORDER BY id ASC  LIMIT ' . $start . ' , ' . $limit . ' ';
        // echo '<pre>'.$sql.$where.$sql_grp_limit;
        // exit;
        $query = $this->db->query($sql . $where . $sql_grp_limit);

        if ($query->num_rows() > 0) {
            $responce = '';
            $i = 0;
            foreach ($query->result_array() as $row) {

                $responce->rows[$i]['id'] = $row['id'];
                $responce->rows[$i]['cell'] = array(
                    $row['kategori']
                    , $row['validasi']
                    , $row['modul']
                    , $row['mKeterangan']
                    , $row['vFilename'],
                );
                $i++;

            }

        }

        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;

        echo json_encode($responce);
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
                #AND e.iCompanyID = {$company_id}
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

    public function listBox_bi_flow_cRequestor($value, $pk, $name, $rowData)
    {
        // get value
        $sql = "SELECT CONCAT_WS(' - ', a.cNip, a.vName) AS showshow
                FROM hrd.employee a
                WHERE a.lDeleted = 0
                AND a.cNip = '" . $value . "' ";
        $row = $this->db->query($sql)->row_array();

        $o = $row['showshow'];

        return $o;
    }

    public function listBox_bi_flow_ss_raw_pic_pic($value, $pk, $name, $rowData)
    {
        // get value
        $sql = "SELECT CONCAT_WS(' - ', a.cNip, a.vName) AS showshow
                FROM hrd.employee a
                WHERE a.lDeleted = 0
                AND a.cNip = '" . $value . "' ";
        $row = $this->db->query($sql)->row_array();

        $o = $row['showshow'];

        return $o;
    }

    public function listBox_bi_flow_ss_raw_problems_iStatus($value, $pk, $name, $rowData)
    {
        // get value
        $sql = "SELECT a.cStatus, b.iStatus, a.id
                FROM hrd.ss_project_status a
                JOIN hrd.ss_raw_problems b ON b.iStatus = a.id 
                WHERE b.iStatus = '" . $value . "' 
                AND b.id = '".$rowData->raw_id."'
                ";
        $row = $this->db->query($sql)->row_array();

        $o = $row['cStatus'];
        // print_r($o); exit;
        return $o;
    }

    public function listBox_bi_flow_itemas_c_teamc($value, $pk, $name, $rowData)
    {
        $sql = "SELECT TRIM(a.c_descr) AS c_descr
                FROM sales.divisi a
                WHERE a.lDeleted = 0 AND a.c_teamc = '" . $rowData->itemas__c_teamc . "' ";
        $row = $this->db->query($sql)->row_array();

        $r = $row['c_descr'];

        return $r;
    }

    public function listBox_bi_flow_iHet_ub($value, $pk, $name, $rowData)
    {
        $r = number_format($value, 3, ',', '.');

        return $r;
    }

    public function listBox_bi_flow_iHet_kp($value, $pk, $name, $rowData)
    {
        $r = number_format($value, 3, ',', '.');

        return $r;
    }

    public function listBox_bi_flow_itemas_n_scpri($value, $pk, $name, $rowData)
    {
        $r = number_format($value, 3, ',', '.');

        return $r;
    }

    public function listBox_bi_flow_iComp_mnf($value, $pk, $name, $rowData)
    {
        $sql = "SELECT a.vCompName
                FROM hrd.company a
                WHERE a.lDeleted = 0 AND a.iCompanyId = '" . $value . "' ";
        $row = $this->db->query($sql)->row_array();

        $r = $row['vCompName'];

        return $r;
    }

    public function listBox_bi_flow_upb_cTeam_pd($value, $pk, $name, $rowData)
    {
        $upb = $this->db->get_where('plc3.upb', array('vUpb_no' => $rowData->vUpb_no))->row_array();
        $team = $this->db->get_where('plc3.team', array('cTeam' => $upb['cTeam_pd']))->row_array();
        if (isset($team['vTeam'])) {
            return $team['vTeam'];
        } else {
            return $value;
        }
    }

    public function searchBox_bi_flow_ss_raw_pic_pic($rowData, $id)
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

    public function searchBox_bi_flow_cRequestor($rowData, $id)
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
                    // $actiName = $data['vNama_activity'];
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

    public function insertBox_bi_flow_form_detail($field, $id)
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

    public function updateBox_bi_flow_form_detail($field, $id, $value, $rowData)
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
            $lblbutton = "Reject";
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
                <input type="hidden" name="modul_id" value="' . $this->input->get('modul_id') . '" />
                <input type="hidden" name="group_id" value="' . $this->input->get('group_id') . '" />
                <input type="hidden" name="iapprove" value="' . $id . '" />
                <input type="hidden" name="iM_modul_activity" value="' . $this->input->get('iM_modul_activity') . '" />

                <textarea id="remark_' . $this->url . '_approval" name="vRemark"></textarea>
        <button type="button" onclick="submit_ajax(\'form_' . $this->url . '_approve\')">' . $lblbutton . '</button>';

        $echo .= '</div>';
        $echo .= '</form>';
        return $echo;
    }

    public function approve_process()
    {
        $post = $this->input->post();
        $cNip = $this->user->gNIP;
        $vName = $this->user->gName;
        $pk = $post[$this->main_table_pk];
        $vRemark = $post['vRemark'];
        $modul_id = $post['modul_id'];
        $id_activity = $post['iM_modul_activity'];
        $iapprove = $post['iapprove'];
        $nowDate = date('Y-m-d H:i:s');
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

        $this->lib_sub_core->InsertActivityModule($headData['vUpb_no'], $modul_id, $pk, $activity['iM_activity'], $activity['iSort'], $vRemark, $iapprove);

        if ($iapprove == 2) {
        } else {
            //delete log
            $deleteLog['lDeleted'] = 1;
            $deleteLog['tUpdate'] = date('Y-m-d H:i:s');
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

            $this->db->where($this->main_table_pk, $pk);
            $this->db->update($this->maintable, $updateReset);
        }

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
        unset($postData[$this->main_table_pk]);
        $postData['tCreate'] = date('Y-m-d H:i:s');
        $postData['cCreate'] = $this->user->gNIP;
        $postData['iCompanyID'] = $this->input->get('company_id');

        $postData['lDeleted'] = 0;

        $postData['iSubmit'] = 0;

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
        // print_r($postData); exit;
        $postData['tUpdate'] = date('Y-m-d H:i:s');
        $postData['cUpdate'] = $this->user->gNIP;

        if ($postData['isdraft'] == true) {
            // $postData['iSubmit'] = 0;
        } else {
            $postData['iSubmit'] = 1;
            $postData['dSubmit'] = date('Y-m-d H:i:s');
            $postData['cSubmit'] = $this->user->gNIP;
            $postData['iApprove_validasi'] = 0;

            // Validasi Sub Controller
            $message = '';
            
            $arrValid = array(
                0 => array('name' => 'Business Requirement', 'table' => 'hrd.biflow_br', 'submit' => true),
                1 => array('name' => 'Informasi Input Field Master', 'table' => 'hrd.biflow_ifm', 'submit' => true),
                2 => array('name' => 'Informasi Input Output', 'table' => 'hrd.biflow_iat', 'submit' => true),
                3 => array('name' => 'Tabel Laporan Proses', 'table' => 'hrd.biflow_lpaw', 'submit' => true),
                4 => array('name' => 'SOP Signed', 'table' => 'hrd.biflow_sop', 'submit' => true),
                5 => array('name' => 'Stakeholder', 'table' => 'hrd.biflow_sth', 'submit' => true),
                6 => array('name' => 'Flowchart', 'table' => 'hrd.biflow_flw', 'submit' => true),
                7 => array('name' => 'User Access Matrix', 'table' => 'hrd.biflow_uam', 'submit' => true),
                8 => array('name' => 'Rumus Perhitungan Modul', 'table' => 'hrd.biflow_for', 'submit' => true),
                // 9 => array('name' => 'Feedback BI', 'table' => 'hrd.biflow_fbk', 'submit' => true),
            );

            $arVal = array(
                1 => array('name' => 'Feedback BI', 'table' => 'hrd.biflow_fbk', 'submit' => true),
            );
            // $arrValid = array(
            //     0 => array('name' => 'Business Requirement', 'table' => 'hrd.biflow_br', 'submit' => true),
            //     1 => array('name' => 'Informasi Input Field Master', 'table' => 'hrd.biflow_ifm', 'submit' => true),
            //     2 => array('name' => 'Informasi Input Awal Transaksi', 'table' => 'hrd.biflow_iat', 'submit' => true),
            //     3 => array('name' => 'Informasi Output Akhir Transaksi', 'table' => 'hrd.biflow_oat', 'submit' => true),
            //     4 => array('name' => 'Tabel Laporan Proses Awal', 'table' => 'hrd.biflow_lpaw', 'submit' => true),
            //     5 => array('name' => 'Tabel Laporan Proses Akhir', 'table' => 'hrd.biflow_lpak', 'submit' => true),
            //     6 => array('name' => 'SOP Signed', 'table' => 'hrd.biflow_sop', 'submit' => true),
            //     7 => array('name' => 'Stakeholder', 'table' => 'hrd.biflow_sth', 'submit' => true),
            //     8 => array('name' => 'Flowchart', 'table' => 'hrd.biflow_flw', 'submit' => true),
            //     9 => array('name' => 'User Access Matrix', 'table' => 'hrd.biflow_uam', 'submit' => true),
            //     10 => array('name' => 'Rumus Perhitungan Modul', 'table' => 'hrd.biflow_for', 'submit' => true),
            // );

            foreach ($arrValid as $key => $valid) {
                // Validasi Minimal Satu
                $sqlMin = "SELECT *
                            FROM " . $valid['table'] . " a
                            WHERE a.lDeleted = 0
                            AND a.cKode = '" . $postData['cKode'] . "' 
                        ";
                $rowMin = $this->db->query($sqlMin)->row_array();

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
                    // print_r($rowM); exit;

                    if ($val['table'] == 'hrd.biflow_fbk') {

                        $sql_fbk = "SELECT *
                                    FROM erp_privi.m_modul_log_activity_ss ss
                                    WHERE ss.lDeleted = 0
                                    AND ss.idprivi_modules = '" . $postData['modul_id'] . "'
                                    AND ss.iKey_id = '" . $postData['id'] . "'
                                    AND ss.iApprove = 1
                                    ORDER BY ss.iM_modul_log_activity DESC 
                                    LIMIT 1
                                    ";
                                    // echo '<pre>'.$sql_param;
                                    // exit;
                        $rowsfbk = $this->db->query($sql_fbk)->result_array();
                        // $row_fb = $this->db->query($sql_fbk);

                        // print_r($row_fb); exit;
                        if (empty($rowM)&&(!empty($rowsfbk))){
                            $message .= '<br><b>' . $val['name'] . '</b> Minimal harus ada 1 data!';
                        } 

                    }

                if (empty($rowMin)) {
                    $message .= '<br><b>' . $valid['name'] . '</b> Minimal harus ada 1 data!';
                } else if (empty($rowM)&&(!empty($rowsfbk))){
                    $message .= '<br><b>' . $val['name'] . '</b> Minimal harus ada 1 data!';
                } else {
                    
                }

                // Validasi Submit Sub Controller
                if ($valid['submit'] == true) {
                    $sql = "SELECT count(*) total
                            FROM " . $valid['table'] . " a
                            WHERE a.lDeleted = 0
                            AND a.iSubmit = 0
                            AND a.cKode = '" . $postData['cKode'] . "' ";
                    $data = $this->db->query($sql)->row_array();

                    if (!empty($data['total'])) {
                        $message .= '<br>Masih ada ' . $data['total'] . ' <b>' . $valid['name'] . '</b> yang belum Submit!';
                    }
                }
                if ($val['submit'] == true) {
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
                }
            }
        }

        if (!empty($message)) {
            $msg['status'] = false;
            $msg['message'] = $message;
            echo json_encode($msg);exit;
        }
        // print_r($postData);exit;

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

            // Jika submit maka update status project menjadi "Requirement Submitted" & tampil di Setting Prioritas Project
            $sql = "SELECT *
                    FROM hrd.ss_raw_problems ss
                    WHERE ss.Deleted = 'No'
                    AND ss.id = '".$post['raw_id']."' ";
            $row = $this->db->query($sql)->row_array();

            if(!empty($row) && $row['iStatus'] != '16'){
                $update['iStatus']  = 16;
                // $update['eAcceptanceStat']  = "Accepted";
                // $update['eProject_priority']= "Y";
                $this->db->where('id', $post['raw_id']);
                $this->db->update('hrd.ss_raw_problems', $update);
            }


            // Send Notifikasi
            $subject= $this->title . ' -> Submit';
            $content= 'Diberitahukan telah ada proses <b>Submit</b> ' . $this->title;

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
                        AND bi.cKode = '".$postData['cKode']."' ";
            $rowH = $this->db->query($sqlH)->row_array();

            $nipCc = $this->lib_utilitas->nipNotifProjectRequirement($this->user->gNIP, $postData['raw_id']);

            $to    = $postData['cRequestor'].",".$this->user->gNIP.",".$nipCc;
            $cc    = '';

            $this->lib_erpss->generateAndSendNotificationPersonal($team, $subject, $content, $data, $to, $cc);
            // END Send Notifikasi

            // Insert Log Activity
            $activities = $this->lib_sub_core->get_current_module_activities($this->modul_id, $postData[$this->main_table_pk]);
            if ($postData['isdraft'] != true && count($activities) > 0) {
                $act = $activities[0];
                $this->lib_sub_core->InsertActivityModule($this->ViewUPB($id), $this->modul_id, $id, $act['iM_activity'], $act['iSort']);

                // get Modul ID dari Modul Validasi (Modul Berikutnya)
                $sql = "SELECT a.idprivi_modules
                    FROM erp_privi.privi_modules a
                    WHERE a.isDeleted = 0
                    AND a.vPathModule = 'erpss/validasi' ";
                $row = $this->db->query($sql)->row_array();
                $modul_id = $row['idprivi_modules'];
                $vRemark = 'Submit Project Requirement';

                // Insert Log Revise untuk Modul Sebelumnya (Project Requirement)
                $this->lib_sub_core->InsertActivityModule($this->ViewUPB($id), $modul_id, $id, $act['iM_activity'], $act['iSort'], $vRemark);
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
            // print_r($AuthModul);exit;
            $buttons['save'] = $iframe . $save_draft . $js;
        } else {
            // print_r($AuthModul);exit;
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
                    $update_draft = '<button id="button_update_draft_' . $this->url . '" class="ui-button-text icon-save" >Update as Draft</button>';

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

                    $update = '<button onclick="javascript:update_btn_back(\'' . $this->url . '\', \' ' . base_url() . 'processor/' . $this->urlpath . '?company_id=' . $this->input->get('company_id') . '&modul_id=' . $this->input->get('modul_id') . '&iM_modul_activity=' . $act['iM_modul_activity'] . '&group_id=' . $this->input->get('group_id') . 'modul_id=' . $this->input->get('modul_id') . ' \',this,false )"  id="button_update_submit_"' . $this->url . '"  class="ui-button-text icon-save" >Update & Submit</button>';

                    $approve = '<button onclick="javascript:load_popup(\' ' . base_url() . 'processor/' . $this->urlpath . '?action=approve&iM_modul_activity=' . $act['iM_modul_activity'] . '&iM_activity=' . $act['iM_activity'] . '&' . $this->main_table_pk . '=' . $peka . '&iupb_id=' . $iupb_id . '&company_id=' . $this->input->get('company_id') . '&group_id=' . $this->input->get('group_id') . '&modul_id=' . $this->input->get('modul_id') . ' \')"  id="button_approve_"' . $this->url . '"  class="ui-button-text icon-save" >Approve</button>';

                    $reject = '<button onclick="javascript:load_popup(\' ' . base_url() . 'processor/' . $this->urlpath . '?action=reject&iM_modul_activity=' . $act['iM_modul_activity'] . '&iM_activity=' . $act['iM_activity'] . '&' . $this->main_table_pk . '=' . $peka . '&iupb_id=' . $iupb_id . '&company_id=' . $this->input->get('company_id') . '&group_id=' . $this->input->get('group_id') . '&modul_id=' . $this->input->get('modul_id') . ' \' )"  id="button_reject_"' . $this->url . '"  class="ui-button-text icon-save" >Reject</button>';

                    $confirm = '<button onclick="javascript:load_popup(\' ' . base_url() . 'processor/' . $this->urlpath . '?action=confirm&iM_modul_activity=' . $act['iM_modul_activity'] . '&iM_activity=' . $act['iM_activity'] . '&' . $this->main_table_pk . '=' . $peka . '&iupb_id=' . $iupb_id . '&company_id=' . $this->input->get('company_id') . '&group_id=' . $this->input->get('group_id') . '&modul_id=' . $this->input->get('modul_id') . ' \')"  id="button_approve_"' . $this->url . '"  class="ui-button-text icon-save" >Confirm</button>';

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
        $vFilename = $_GET['vFilename'];
        $vFilename_generate = $_GET['vFilename_generate'];
        $files = file_get_contents('./' . $vFilename_generate);
        force_download($vFilename, $files);
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
                $nomorlike = $serialCompany . $_valField["vKode"] . date('y') . "-" . date('m') . "-";
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
