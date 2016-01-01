<table>
    <tr>
        <td>Lat. / Lng.</td>
        <td><?php echo $data->x;?> / <?php echo $data->y;?></td>
    </tr>
    <tr>
        <td>Casa</td>
        <td>
            <img src="data:image/jpg;base64,<?php echo $data->casa_img1;?>" style="width: 120px;" />
        </td>
    </tr>
</table>