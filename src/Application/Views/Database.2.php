<?php

use Framework\Config;

$cfg = Config::Fetch("database");
?>
<p><b>Available connections</b></p>
<ul>
    <?php foreach ($cfg as $conn_name => $conn_params): ?>
        <li><?= $conn_name ?>
            (<?php foreach ($conn_params as $param => $value): ?>
                <?php if ($param == "password") continue; ?>
                <?= $param ?>: <i><?= $value ?></i>
            <?php endforeach; ?>)</li>
    <?php endforeach; ?>
</ul>