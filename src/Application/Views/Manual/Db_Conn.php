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
<!-- /////////////////////////////////////////////////////////////////////// -->
<p><b>Estabilish database connection and execute query</b></p>
<?php
$code = <<<'HERE'
<?php
use Framework\{Database, Query};
use Framework\Query\{Operator, Statement};

$conn = new Database(); // "default" connection

$query = (new Query(Statement::SELECT))
        ->Table("sample_table")
        ->Columns("col1", "col2", "col3")
        ->OrderBy("col1")
        ->OrderBy("col2", false)
        ->Offset(0)
        ->Limit(10);

$conn->Query($query);
HERE;
highlight_string($code);
?>