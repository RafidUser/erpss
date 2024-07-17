<table id="tbl_<?php echo $grid; ?>" border="1px" class="table_detail_kondisi" style="border-collapse:collapse;">
    <thead>
        <tr>
            <th>No</th>
            <th>SSID</th>
            <th>Nama Project</th>
            <th>Size</th>
            <th>Tgl BI Submit</th>
            <th>Tgl Revise</th>
            <th>Tgl Valid</th>
            <th>Tgl Start Analisa </th>
            <th>Tgl Finish</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $this->load->helper('tanggal');
            $i = 1;

            foreach ($rows['datas'] as $data) {
        ?>
                <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $data->raw_id; ?></td>
                        <td><?php echo $data->problem_subject; ?></td>
                        <td><?php echo $data->iSizeProject; ?></td>

                        <td>
                            <?php 
                                /* get data history submit */
                                $sql2 = 'SELECT modul.idprivi_modules,modul.vPathModule
                                        ,logger.*
                                        FROM erp_privi.privi_modules modul
                                        JOIN erp_privi.m_modul_log_activity_ss logger ON logger.idprivi_modules=modul.idprivi_modules
                                        WHERE 
                                        modul.vPathModule="erpss/bi_flow"
                                        AND logger.iSort=1
                                        AND logger.iM_activity=1
                                        AND iKey_id= "'.$data->id.'"
                                        AND modul.isDeleted = 0
                                        ORDER BY logger.iM_modul_log_activity ASC
                                ';
                                $querys = $this->db->query($sql2);
                                if($querys->num_rows() > 0){
                                    $datasub = $querys->result();
                                    echo '<ul>';
                                    foreach ($datasub as $items ) {
                                        
                                        echo '<li>'.$items->dCreate.'</li>'.'</br>';
                                    }
                                    echo '</ul>';
                                }
                            ?>
                        </td>

                        <td>
                            <?php 
                                /* get data history revise date */
                                $sql = 'SELECT modul.idprivi_modules,modul.vPathModule
                                        ,logger.*
                                        FROM erp_privi.privi_modules modul
                                        JOIN erp_privi.m_modul_log_activity_ss logger ON logger.idprivi_modules=modul.idprivi_modules
                                        WHERE 
                                        modul.vPathModule="erpss/bi_flow"
                                        AND logger.iSort=1
                                        AND logger.iM_activity=3
                                        AND logger.iApprove=1
                                        AND iKey_id= "'.$data->id.'"
                                        AND modul.isDeleted = 0
                                        ORDER BY logger.iM_modul_log_activity ASC
                                ';
                                $query = $this->db->query($sql);
                                if($query->num_rows() > 0){
                                    $datas = $query->result();
                                    foreach ($datas as $item ) {
                                         
                                        echo '<li>'.$item->dCreate.'</li>'.'</br>';
                                    }
                                }
                            ?>
                        </td>
                        <td><?php echo $data->dApprove_validasi; ?></td>
                        <td><?php echo $data->tgl_analisa; ?></td>
                        <td><?php echo $data->tgl_finish; ?></td>
                    </tr>
        <?php
               $i++;
               //}
           }
        ?>
    </tbody>
</table>
<style type="text/css">
    .table_header_mom {
        padding: 10px;
    }

    .table_detail_inner {
        border-collapse: collapse;
    }

    .table_detail_inner thead tr {
        border-collapse: collapse;
        background-color: #548cb6;
        color: #fff;
    }

    .table_detail_inner thead tr th {
        border: 1px solid #ddd;
    }

    .table_detail_inner tbody tr {
        border-collapse: collapse;
        background-color: #fff;
        color: #000;
    }

    .table_detail_inner tbody tr td {
        border: 1px solid #ddd;
        text-align: center;
        padding: 5px;
        vertical-align: top;
    }

    .table_detail_inner tbody tr td li{
        list-style-position: inside;
    }

    .table_kondisi {
        margin: 10px;
    }

    .table_kondisi.dataTables_wrapper {
        width: 800px;
    }

    .table_detail_kondisi thead tr {
        border-collapse: collapse;
        background-color: #548cb6;
        color: #fff;
    }

    .table_detail_kondisi thead tr th {
        border: 1px solid #ddd;
    }

    .table_detail_kondisi tbody tr {
        border-collapse: collapse;
        background-color: #fff;
        color: #000;
    }

    .table_detail_kondisi tbody tr td {
        border: 1px solid #ddd;
        text-align: center;
        padding: 5px;
        vertical-align: top;
    }

    .table_detail_kondisi tbody tr td li{
        list-style-position: inside;
    }

    .table_kondisi_2 {
        margin: 10px;
        width: auto;
    }

    .table_detail_kondisi_2 {
        border-collapse: collapse;
    }

    .table_detail_kondisi_2 thead tr {
        border-collapse: collapse;
        background-color: #548cb6;
        color: #fff;
    }

    .table_detail_kondisi_2 thead tr th {
        border: 1px solid #ddd;
    }

    .table_detail_kondisi_2 tbody tr {
        border-collapse: collapse;
        background-color: #fff;
        color: #000;
    }

    .table_detail_kondisi_2 tbody tr td {
        border: 1px solid #ddd;
        text-align: center;
        padding: 5px;
        vertical-align: top;
    }

    .detail_inner {
        margin: 10px;
    }

    .table-kesimpulan {
        width: 50%;
        margin: 0 auto;
        border-collapse: collapse;
        border: none !important;
    }

    .table-kesimpulan tr td {
        border-collapse: collapse;
        border: 1px solid #fff !important;
    }
</style>