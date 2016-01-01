<table>
    <tr>
        <td>Estado</td>
        <td><?php echo $data->estado;?></td>
    </tr>
    <tr>
        <td>Observacion</td>
        <td><?php echo $data->observacion;?></td>
    </tr>
    <?php
    if ( !empty($data->final_img1) )
    {
        ?>
        <tr>
            <td>Final IMG1</td>
            <td>
                <img src="data:image/jpg;base64,<?php echo $data->final_img1;?>" style="width: 120px;" />
            </td>
        </tr>
        <?php
    }
    
    if ( !empty($data->final_img2) )
    {
        ?>
        <tr>
            <td>Final IMG2</td>
            <td>
                <img src="data:image/jpg;base64,<?php echo $data->final_img2;?>" style="width: 120px;" />
            </td>
        </tr>
        <?php
    }
    
    if ( !empty($data->firma_img) )
    {
        ?>
        <tr>
            <td>Firma IMG</td>
            <td>
                <img src="data:image/jpg;base64,<?php echo $data->firma_img;?>" style="width: 120px;" />
            </td>
        </tr>
        <?php
    }
    
    ?>
</table>