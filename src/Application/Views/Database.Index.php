<?php

use Framework\Url;
?>
<div style="width: 20%; float: left;">
    <p><b>Select subpage</b></p>
    <ul style="list-style: none; padding: 0; margin: 0;">
        <li><a href="<?= Url::Site("Database/Index/1") ?>">Drivers</a></li>
        <li><a href="<?= Url::Site("Database/Index/2") ?>">Connections</a></li>
        <li><a href="<?= Url::Site("Database/Index/3") ?>">Examples</a></li>
    </ul>
</div>
<div style="width: 80%; float: right;">
    <td rowspan="2"><?= $subpage ?></td>
</div>
<br style="clear: both" />