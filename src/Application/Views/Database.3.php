<p><b>Sample queries</b></p>
<p><b>SQL Create table</b></p>
<p>
    <?php
    $code = <<<'HERE'
<?php
use Framework\Database\Query;
use Framework\Database\Operation;
$query = (new Query(Operation::CREATE))
        ->Table("new_table")
        ->IfNotExists()
        ->Colspec("col1", "integer primary key")
        ->Colspec("col2", "text")
        ->Colspec("col3", "text");

echo $query;
HERE;
    highlight_string($code);
    ?>
</p>
<p>
    <code>CREATE TABLE IF NOT EXISTS new_table (col1 integer primary key,
        col2 text, col3 text)</code>
<p/>

<p><b>SQL Select</b></p>
<p>
    <?php
    $code = <<<'HERE'
<?php
use Framework\Database\Query;
use Framework\Database\Operation;
$query = (new Query(Operation::SELECT))
        ->Table("sample_table")
        ->Columns("col1", "col2", "col3")
        ->OrderBy("col1")
        ->OrderBy("col2", false)
        ->Offset(0)
        ->Limit(10);

echo $query;
HERE;
    highlight_string($code);
    ?>
</p>
<p>
    <code>SELECT DISTINCT col1, col2, col3 FROM sample_table ORDER BY col1 ASC,
        col2 DESC LIMIT 10 OFFSET 0</code>
</p>

<p><b>Estabilish database connection and execute query</b></p>
<p>
    <?php
    $code = <<<'HERE'
<?php
use Framework\Database\Connection;
use Framework\Database\Operation;
use Framework\Database\Query;
use Framework\Database\Operation;

$conn = new Connection(); // "default" connection

$query = (new Query(Operation::SELECT))
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
</p>