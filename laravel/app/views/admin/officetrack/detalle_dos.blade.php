<table>
    <tr>
        <td>Motivo</td>
        <td><?php echo $data->motivo;?></td>
    </tr>
    <tr>
        <td>Observacion</td>
        <td><?php echo $data->observacion;?></td>
    </tr>
    <?php
    if ( !empty($data->problema_img1) )
    {
        ?>
        <tr>
            <td>Problema IMG1</td>
            <td>
                <img src="data:image/jpg;base64,<?php echo $data->problema_img1;?>" style="width: 120px;" />
            </td>
        </tr>
        <?php
    }
    
    if ( !empty($data->problema_img2) )
    {
        ?>
        <tr>
            <td>Problema IMG2</td>
            <td>
                <img src="data:image/jpg;base64,<?php echo $data->problema_img2;?>" style="width: 120px;" />
            </td>
        </tr>
        <?php
    }
    
    if ( !empty($data->modem_img1) )
    {
        ?>
        <tr>
            <td>Modem IMG1</td>
            <td>
                <img src="data:image/jpg;base64,<?php echo $data->modem_img1;?>" style="width: 120px;" />
            </td>
        </tr>
        <?php
    }
    
    if ( !empty($data->modem_img2) )
    {
        ?>
        <tr>
            <td>Modem IMG2</td>
            <td>
                <img src="data:image/jpg;base64,<?php echo $data->modem_img2;?>" style="width: 120px;" />
            </td>
        </tr>
        <?php
    }
    
    if ( !empty($data->tap_img1) )
    {
        ?>
        <tr>
            <td>Tap IMG1</td>
            <td>
                <img src="data:image/jpg;base64,<?php echo $data->tap_img1;?>" style="width: 120px;" />
            </td>
        </tr>
        <?php
    }
    
    if ( !empty($data->tap_img2) )
    {
        ?>
        <tr>
            <td>Tap IMG2</td>
            <td>
                <img src="data:image/jpg;base64,<?php echo $data->tap_img2;?>" style="width: 120px;" />
            </td>
        </tr>
        <?php
    }
    
    if ( !empty($data->tv_img1) )
    {
        ?>
        <tr>
            <td>Tv IMG1</td>
            <td>
                <img src="data:image/jpg;base64,<?php echo $data->tv_img1;?>" style="width: 120px;" />
            </td>
        </tr>
        <?php
    }
    
    if ( !empty($data->tv_img2) )
    {
        ?>
        <tr>
            <td>Tv IMG2</td>
            <td>
                <img src="data:image/jpg;base64,<?php echo $data->tv_img2;?>" style="width: 120px;" />
            </td>
        </tr>
        <?php
    }
    ?>
</table>