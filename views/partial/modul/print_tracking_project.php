<?php
    $this->load->helper('tanggal');

    

?>
<div style="text-align:center;"width="100%"> 
    <h2 >Tracking Project</h2>
</div>
<table width="50%">
    <tbody>
        <tr>
            <td width="30%">Nama Perusahaan :</td>
            <td>
                <?php 
                    $row = $this->db->get_where('hrd.company', array('iCompanyId' => $company_id))->row();
                    if(!empty($row)){

                        echo $row->vCompName;
                    }
                ?>
            </td>
        </tr>
        <tr>
            <td>Print Date</td>
            <td>
                <?php 
                    echo date('d F Y, H:i:s')
                ?>
            </td>
        </tr>
    </tbody>
</table>
<?php 
    //print_r($data);
    //print_r($data);
    //exit;
    $data['rows'] = $rows;
    $data['param'] = $param;
    $data['grid'] = $grid;
    $data['company_id'] = $company_id;
    echo $return = $this->load->view('partial/modul/_rpt_tbl',$data);
            
        
?>



