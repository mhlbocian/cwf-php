<?php

use Framework\Url;
?>
<div style="width: 20%; float: left;">
    <h3>Database support</h3>
    <ul style="list-style: none; padding: 0; margin: 0;">
        <li><a href="<?= Url::Site("Main/Database/1") ?>">Drivers</a></li>
        <li><a href="<?= Url::Site("Main/Database/2") ?>">Connections</a></li>
        <li><a href="<?= Url::Site("Main/Database/3") ?>">Sample queries</a></li>
    </ul>
</div>
<div style="width: 80%; float: right;">
    <td rowspan="2"><?= $subpage ?></td>
</div>
<br style="clear: both" />