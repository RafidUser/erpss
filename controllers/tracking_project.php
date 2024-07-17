<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class tracking_project extends MX_Controller
{

    private $sess_auth;
    private $db;
    public $ThisCompany;
    public $BaseLimit0;

    public $company_id;
    public $masterUrl;

    public function __construct()
    {
        parent::__construct();
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
        $this->sess_auth = new Zend_Session_Namespace('auth');

        $this->team = $this->lib_erpss->hasTeam($this->user->gNIP);
        $this->teamID = $this->lib_erpss->hasTeamID($this->user->gNIP);
        $this->isAdmin = $this->lib_erpss->isAdmin($this->user->gNIP);
        $this->BaseLimit0 = 3000;
        $this->user = $this->auth->user();

        $this->title = 'Laporan Tracking Project';
        $this->url = 'tracking_project';
        $this->urlpath = 'erpss/' . str_replace("_", "/", $this->url);

        $url = $_SERVER['HTTP_REFERER'];
        $company_id = substr($url, strrpos($url, '/') + 1);
        $this->sess_auth->company_id = $company_id;
    }

    public function index($action = '')
    {
        $action = $this->input->get('action') ? $this->input->get('action') : 'create';
        $grid = new Grid;
        $grid->setTitle($this->title);
        $grid->setTable('hrd.biflow');
        $grid->setUrl($this->url);

        switch ($action) {
            case 'json':
                $grid->getJsonData();
                break;
            case 'getPic':
                $get = $this->input->get();
                $term = $this->input->get('term');
                $datas["term"] = $term;
                $term = trim($this->input->get('term'));
                $company_id = $this->input->get('company_id');
                
                $sql = 'SELECT a.cNip AS valval, CONCAT_WS(" - ",a.cNip,a.vName) AS showshow
                        FROM hrd.employee a 
                        WHERE a.iCompanyID= "' . $company_id . '"
                            AND ( a.cNip LIKE "%' . $term . '%"  OR  a.vName LIKE "%' . $term . '%" )
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
            case 'getRequest':
                $get = $this->input->get();
                $term = $this->input->get('term');
                $datas["term"] = $term;
                $term = trim($this->input->get('term'));
                $company_id = $this->input->get('company_id');
                
                 // get value
                $sql = "SELECT CONCAT_WS(' - ', a.cNip, a.vName) AS showshow
                        FROM hrd.employee a
                        WHERE a.lDeleted = 0
                        AND a.cNip = '" . $value . "' ";

                return $o;
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
            case 'view':
                $grid->render_form($this->input->get('id'), true);
                break;
            case 'create':
                $rfilter = array(
                    'raw_id' => 'SSID',
                    'ss_raw_problems.problem_subject' => 'Project Name',
                    'cRequestor' => 'Requestor',
                    'pic' => 'Project Manager',
                    'dSubmit' => 'Tgl BI Submit',
                    'iformat' => 'Format',
                );
                $buttons = '<button onclick="javascript:priview_' . $this->url . '(\'preview_' . $this->url . '\', \'' . base_url() . 'processor/' . $this->urlpath . '?action=preview&company_id=' . $this->iCompanyID . '\')" class="ui-button-text icon-save" id="btn_preview">Preview</button>';
                $buttons .= '<button onclick="javascript:reset_' . $this->url . '()" class="ui-button-text icon-save" id="btn_reset">Reset</button>';
                $buttons .= '<script>
        					$("#btn_reset").on( "click", function() {
        						$("#search_grid_' . $this->url . '_raw_id_chzn").val("").trigger("liszt:updated");
                                $("#search_grid_' . $this->url . '_ss_raw_problems.problem_subject_chzn").val("").trigger("liszt:updated");
                                $("#search_grid_' . $this->url . '_pic_chzn").val("").trigger("liszt:updated");
        						$("#search_grid_' . $this->url . '_dSubmit_1").val("");
                                $("#search_grid_' . $this->url . '_dSubmit_2").val("");
                                $("#search_grid_' . $this->url . '_cRequestor_chzn").val("").trigger("liszt:updated");
        						$("#search_grid_' . $this->url . '_iformat_chzn").val("").trigger("liszt:updated");
        					});
    					</script>';

                $data['caption'] = $this->title;
                $data['grid'] = $this->url;
                $data['rfilter'] = $rfilter;
                $data['button'] = $buttons;

                $form = $this->render_search_report($data);
                echo $form;
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
            case 'print':
                $post = $this->input->get();
                $rows = $this->getting_data_form($post);
                $format = intval($post['iformat']);
                $data['rows'] = $rows;
                $data['data'] = $post;
                $data['company_id'] = $this->sess_auth->company_id;
                // print_r($data);
                // exit;
                $filename = 'LAPORAN_TRACKING_PROJECT_' . date('Y_m_d_H_i_s');
                if ($format == 1) {
                    $output = $this->load->view('partial/modul/print_' . $this->url, $data, true);
                    $this->load->library('m_pdf');
                    $pdf = $this->m_pdf->load('c', 'A4');
                    $pdffile = $filename . ".pdf";
                    $pdf->AddPage(
                        'L', // L - landscape, P - portrait
                        '',
                        '',
                        '',
                        '',
                        4, // margin_left
                        4, // margin right
                        10, // margin top
                        0, // margin bottom
                        4, // margin header
                        5
                    ); // margin footer
                    $pdf->shrink_tables_to_fit = 0;
                    // $pdf->SetProtection(array(), 'username', 'rahasia');
                    $pdf->WriteHTML($output);
                    $pdf->Output($pdffile, "D");
                    break;
                } else if ($format == 2) {
                    $output = $this->load->view('partial/modul/print_' . $this->url, $data, true);
                    header("Content-type: application/vnd-ms-excel");
                    header("Content-Disposition: attachment; filename=" . $filename . ".xls");
                    echo $output;

                    break;
                } else {
                    echo "Format Print Tidak Dikenal";
                    exit();
                }
                break;
            case 'preview':
                $post = $this->input->post();
                $s = $this->preview_form($post);
                echo $s;
                break;
            case 'searchPIC':
                $this->searchPIC();
                break;
            case 'download':
                $this->load->helper('download');
                $this->downloadFile($this);
                break;
            default:
                $grid->render_grid();
                break;
        }
    }

    public function downloadFile($ini)
    {
        $ini->load->helper('download');
        $idDok = $_GET['id'];
        $vFilename_generate = $_GET['vFilename_generate'];
        $vFilename = $_GET['vFilename'];
        $files = file_get_contents('./' . $vFilename_generate);
        force_download($vFilename, $files);
    }

    public function print_doc($output)
    {
        $this->load->library('html_to_doc');
        // Initialize class
        $htmltodoc = new HTML_TO_DOC();

        $htmlContent = $output;

        $htmltodoc->createDoc($htmlContent, "document", 1);
    }

    public function searchPIC()
    {
        $term = $this->input->get('term');
        $data = array();

        $sql = "SELECT e.cNip, e.vName FROM hrd.employee e
                WHERE e.lDeleted = 0 AND ( e.dresign = '0000-00-00' OR e.dresign > DATE(NOW()) )
                    AND ( e.cNip LIKE '%{$term}%' OR e.vName LIKE '%{$term}%' )
                    AND e.iCompanyID = ? AND e.iDivisionID = 2
                ORDER BY e.vName ASC ";

        $query = $this->db->query($sql, array($this->iCompanyID));
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

    public function getting_data_form($param)
    {
        /* 
            Array
            (
                [dSubmit_1] => 2022-07-01
                [dSubmit_2] => 2022-07-26
                [cRequest] => NPL-RHL-22-07-0001
                [cRequestor] => 0300001
                [iformat] => 
            )
         */
        // print_r($param);
        // exit;

        $ssid = $param['search_grid_tracking_project_raw_id'];
        $pn = $param['search_grid_tracking_project_ss_raw_problems_problem_subject'];
        $pm = $param['pic'];
        $cRequestor = $param['cRequestor'];
        $dSubmit_1 = $param['dSubmit_1'];
        $dSubmit_2 = $param['dSubmit_2'];

        $where = " ";

        if ($ssid != '') {
            $where .= " and a.raw_id = '" . $ssid . "'";
        }

        if ($pn != '') {
            $where .= " and srb.problem_subject = '" . $pn . "'";
        }

        if ($pm != '') {
            $where .= " and srp.pic = '" . $pm . "'";
        }

        if ($cRequestor != '') {
            $where .= " and a.cRequestor = '" . $cRequestor . "'";
        }

        if ($dSubmit_2 != '') {
            $where .= "
                        and date(a.dSubmit) BETWEEN '" . $dSubmit_1 . "' and '" . $dSubmit_2 . "'
                    ";
        }

        // Get Data 
        $sql = ' SELECT a.id,a.raw_id, srb.problem_subject, srp.pic
                , a.dApprove_validasi ,srb.iSizeProject, srb.dSubmit_requirement
                ,if(a.iApprove_validasi=2,srb.dClosePm, "") AS tgl_finish
                ,(
                    SELECT logger.dCreate
                    FROM erp_privi.privi_modules modul
                    JOIN erp_privi.m_modul_log_activity_ss logger ON logger.idprivi_modules=modul.idprivi_modules
                    WHERE modul.vPathModule="erpss/bi_flow"
                    AND logger.iSort= 1
                    AND logger.iM_activity= 1
                    AND iKey_id= a.id
                    AND modul.isDeleted = 0
                    ORDER BY logger.iM_modul_log_activity ASC
                    LIMIT 1
                ) AS tgl_submit
                ,if(a.iApprove_validasi=2,a.dApprove_validasi,"") as dApprove_validasi
                ,COALESCE(if(a.iApprove_validasi=2,srb.dSubmit_requirement, ""), (if(a.iApprove_validasi=2, DATE_ADD( dApprove_validasi, INTERVAL 1 + 
                        IF(
                            (WEEK(dApprove_validasi) <> WEEK(DATE_ADD(dApprove_validasi, INTERVAL 1 DAY)))
                            OR (WEEKDAY(DATE_ADD(dApprove_validasi, INTERVAL 1 DAY)) IN (5, 6)),
                            2,0) DAY ) , ""))) AS tgl_analisa
                FROM hrd.biflow a
                JOIN hrd.ss_raw_problems srb ON srb.id = a.raw_id
                JOIN hrd.ss_raw_pic srp ON srp.rawid = srb.id 
                        AND srp.Deleted = "No" AND srp.iRoleId = 1 AND srp.pic is not null
                WHERE a.lDeleted = 0 
             
        ';

        $fullQuery = $sql . $where;

        // echo '<pre>'.$fullQuery;
        // exit;

        $datas = $this->db->query($fullQuery)->result();

        $return['datas'] = $datas;
        return $return;
    }

    public function preview_form($post)
    {
        $param = $this->input->post();

        // print_r($this->input->post()); exit;

        $data['post'] = $post;
        $data['grid'] = $this->url;
        $data['urlpath'] = $this->urlpath;
        $data['rows'] = $this->getting_data_form($param);
        $r = $this->load->view('partial/modul/preview_' . $this->url, $data, true);
        return $r;
    }

    public function output()
    {
        $this->index($this->input->get('action'));
    }

    /* ------------------------------------------------------------------------------------
    InsertBox AND UpdateBox
    ------------------------------------------------------------------------------------ */

    /*
    ------------------------------------------------------------------------------------
    Before, After and Manipulate Button
    ------------------------------------------------------------------------------------
     */

    public function render_search_report($data)
    {
        $grid = 'tracking_project';
        $rfilter = $data['rfilter'];
        $rdata = array();

        foreach ($rfilter as $key => $value) {
            $func = "searchBox_" . $key;

            if (!method_exists($grid, $func)) {
                $field = '<input name="search_grid_' . $grid . '_' . $key . '" label="' . $value . '" ftype="varchar" id="search_grid_' . $grid . '_' . $key . '" type="text" class="search_box_' . $grid . '">';
                $rdata[$key] = $field;
            } else {
                $id_field = 'search_grid_' . $this->url . '_' . $key;
                $rdata[$key] = $this->$func($id_field, $key);
            }
        }

        $data['grid'] = $grid;
        $data['rinput'] = $rdata;
        $data['modul_id'] = $this->modul_id;
        $searchData = $this->load->view('partial/modul/src_rpt_js', $data, true);
        return $searchData;
    }

    public function searchBox_pic($id, $name)
    {
        $grid = $this->url;
        $sql = 'SELECT a.cNip AS valval, CONCAT_WS(" - ",a.cNip,a.vName) AS showshow
                FROM hrd.employee a 
                WHERE a.iCompanyID= "' . $this->sess_auth->company_id . '"
                LIMIT 10
                ';
        $rowsAwal = $this->db->query($sql)->result_array();

        $o = '<select id="' . $id . '" name="' . $name . '" class="required search_box_' . $grid . ' ' . $grid . '_choosen" >';
        $o .= '<option value="">--Pilih--</option>';
        foreach ($rowsAwal as $item) {
            $o .= '<option value="' . $item['valval'] . '">' . $item['showshow'] . '</option>';
        }
        $o .= '</select>';

        $o .= '<script>
                    $("#' . $id . '").chosen();
                    $.each($("#' . $id . '"), function(i, v) {
                        resizingChosen(this);
                    });

                    $("#' . $id . '_chzn .chzn-search input").autocomplete({
                        minLength: 3,
                        source: function(request, response) {
                            $.ajax({
                                url: base_url+"processor/' . $this->urlpath . '?action=getPic&company_id=' . $this->input->get('company_id') . '&term="+ request.term,
                                dataType: "json",
                                beforeSend: function() {
                                    $("#' . $id . '_chzn ul.chzn-results").empty();
                                },
                                success: function(data, textStatus) {
                                    $("#' . $id . '").empty();
                                    inilah = data.nilai
                                    $.each(inilah, function(index) {
                                        $("#' . $id . '").append("<option value=\'"+inilah[index].id+"\'>"+inilah[index].value+"</option>");
                                    });
                                    $("#' . $id . '").trigger("liszt:updated");

                                    // isi input dengan term
                                    $("#' . $id . '_chzn .chzn-search").find("input").val(request.term);
                                }
                            });

                        }
                    });

                    function resizingChosen(selector){
                        $(selector).parents().css("overflow", "visible");
                        $(selector).next().css({
                            "width": "350px"
                        });
                        $(selector).next().find(".default").css({
                            "width": "348px"
                        });
                        $(selector).next().find(".chzn-drop").css({
                            "width": "348px"
                        });
                    }
                </script>';

        /* $return = '<select name="' . $name . '" id="' . $id . '" class="required search_box_' . $grid . ' ' . $grid . '_choosen" >';
        $return .= '	<option value="">--Pilih--</option>';
        foreach ($moms as $mom) {
            $return .= '<option value="' . $mom['valval'] . '">' . $mom['showshow'] . '</option>';
        }
        $return .= '</select>';
        $return .= '
						<style>
							#' . $id . '{
								width: 250px;
							}
						</style>
				'; */

        return $o;
    }

    public function searchBox_cRequestor($id, $name)
    {
        $grid = $this->url;
        $sql = 'SELECT a.cNip AS valval, CONCAT_WS(" - ",a.cNip,a.vName) AS showshow
                FROM hrd.employee a 
                WHERE a.iCompanyID= "' . $this->sess_auth->company_id . '"
                LIMIT 10
                ';
        $rowsAwal = $this->db->query($sql)->result_array();

        $o = '<select id="' . $id . '" name="' . $name . '" class="required search_box_' . $grid . ' ' . $grid . '_choosen" >';
        $o .= '<option value="">--Pilih--</option>';
        foreach ($rowsAwal as $item) {
            $o .= '<option value="' . $item['valval'] . '">' . $item['showshow'] . '</option>';
        }
        $o .= '</select>';

        $o .= '<script>
                    $("#' . $id . '").chosen();
                    $.each($("#' . $id . '"), function(i, v) {
                        resizingChosen(this);
                    });

                    $("#' . $id . '_chzn .chzn-search input").autocomplete({
                        minLength: 3,
                        source: function(request, response) {
                            $.ajax({
                                url: base_url+"processor/' . $this->urlpath . '?action=getPic&company_id=' . $this->input->get('company_id') . '&term="+ request.term,
                                dataType: "json",
                                beforeSend: function() {
                                    $("#' . $id . '_chzn ul.chzn-results").empty();
                                },
                                success: function(data, textStatus) {
                                    $("#' . $id . '").empty();
                                    inilah = data.nilai
                                    $.each(inilah, function(index) {
                                        $("#' . $id . '").append("<option value=\'"+inilah[index].id+"\'>"+inilah[index].value+"</option>");
                                    });
                                    $("#' . $id . '").trigger("liszt:updated");

                                    // isi input dengan term
                                    $("#' . $id . '_chzn .chzn-search").find("input").val(request.term);
                                }
                            });

                        }
                    });

                    function resizingChosen(selector){
                        $(selector).parents().css("overflow", "visible");
                        $(selector).next().css({
                            "width": "350px"
                        });
                        $(selector).next().find(".default").css({
                            "width": "348px"
                        });
                        $(selector).next().find(".chzn-drop").css({
                            "width": "348px"
                        });
                    }
                </script>';

        /* $return = '<select name="' . $name . '" id="' . $id . '" class="required search_box_' . $grid . ' ' . $grid . '_choosen" >';
        $return .= '	<option value="">--Pilih--</option>';
        foreach ($moms as $mom) {
            $return .= '<option value="' . $mom['valval'] . '">' . $mom['showshow'] . '</option>';
        }
        $return .= '</select>';
        $return .= '
						<style>
							#' . $id . '{
								width: 250px;
							}
						</style>
				'; */

        return $o;
    }


    public function searchBox_dSubmit($id, $name)
    {
        $return = '<input type="text" name="' . $name . '_1"  id="' . $id . '_1" readonly="readonly"  class="input_rows1" size="12" />';
        $return .= '- Sampai -<script>
                            $("#' . $id . '_1").datepicker({
                                    changeMonth:true,
                                    changeYear:true,
                                    dateFormat:"yy-mm-dd"
                            });
                     </script>';
        $return .= '<input type="text" name="' . $name . '_2"  id="' . $id . '_2" readonly="readonly"  class="input_rows1" size="12" />';
        $return .= '<script>
                        $("#' . $id . '_2").datepicker({
                                changeMonth:true,
                                changeYear:true,
                                dateFormat:"yy-mm-dd"
                        });
                    </script>';
        return $return;
    }

    public function searchBox_iformat($id, $name)
    {
        $grid = $this->url;

        $return = '<select name="' . $name . '" id="' . $id . '" class="required search_box_' . $grid . ' ' . $grid . '_choosen" >';
        $return .= '	<option value="">--Pilih Format--</option>';
        $return .= '	<option value="1">PDF</option>';
        $return .= '	<option value="2">EXCEL</option>';
        $return .= '</select>';
        return $return;
    }
}
