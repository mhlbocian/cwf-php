<h3>Sample queries</h3>
<!-- /////////////////////////////////////////////////////////////////////// -->
<p><b>SQL Create table</b></p>
<?php
$code = <<<'HERE'
<?php
use Framework\Database\Query;
use Framework\Database\Statement;
$query = (new Query(Statement::CREATE))
        ->Table("new_table")
        ->IfNotExists()
        ->Colspec("col1", "integer primary key")
        ->Colspec("col2", "text")
        ->Colspec("col3", "text");

echo $query;
HERE;
highlight_string($code);
?>
<p>
    <code>CREATE TABLE IF NOT EXISTS new_table (col1 integer primary key,
        col2 text, col3 text)</code>
</p>
<!-- /////////////////////////////////////////////////////////////////////// -->
<p><b>SQL Delete</b></p>
<?php
$code = <<<'HERE'
<?php
use Framework\Database\Query;
use Framework\Database\Statement;
use Framework\Database\Operator;

$query = (new Query(Statement::DELETE))
        ->Table("users")
        ->Where("id", Operator::Eq, 1234);

echo $query;
HERE;
highlight_string($code);
?>
<p>
    <code>DELETE FROM users WHERE id = :w0</code>
</p>
<!-- /////////////////////////////////////////////////////////////////////// -->
<p><b>SQL Select</b></p>
<?php
$code = <<<'HERE'
<?php
use Framework\Database\Query;
use Framework\Database\Statement;
$query = (new Query(Statement::SELECT))
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
<p>
    <code>SELECT DISTINCT col1, col2, col3 FROM sample_table ORDER BY col1 ASC,
        col2 DESC LIMIT 10 OFFSET 0</code>
</p>
<!-- /////////////////////////////////////////////////////////////////////// -->
<p><b>SQL Update</b></p>
<?php
$code = <<<'HERE'
<?php
use Framework\Database\Query;
use Framework\Database\Operator;
use Framework\Database\Statement;
    
$query = (new Query(Statement::UPDATE))
        ->Table("users")
        ->Columns("username")
        ->Values("new_username")
        ->Where("id", Operator::Eq, 1);
echo $query;
HERE;
highlight_string($code);
?>
<p>
    <code>UPDATE users SET username = :0 WHERE id = :w0</code>
</p>
<!-- /////////////////////////////////////////////////////////////////////// -->
<p><b>SQL Where/And/Or</b></p>
<?php
$code = <<<'HERE'
<?php
use Framework\Database\Query;
use Framework\Database\Statement;
use Framework\Database\Operator;

$query = (new Query(Statement::SELECT))
        ->Distinct()
        ->Table("books")
        ->Columns("id", "author", "title", "isbn")
        ->Where("year", Operator::GEq, 1990)
        ->And("country", Operator::Eq, "PL")
        ->Or("country", Operator::Like, "DE")
        ->OrderBy("title")
        ->Limit(10)
        ->Offset(0);

echo $query;
HERE;
highlight_string($code);
?>
<p>
    <code>SELECT DISTINCT id, author, title, isbn FROM books WHERE year >= :w0
        AND country = :w1 OR country LIKE :w2 ORDER BY title ASC
        LIMIT 10 OFFSET 0</code>
</p>
<!-- /////////////////////////////////////////////////////////////////////// -->
<p><b>Estabilish database connection and execute query</b></p>
<?php
$code = <<<'HERE'
<?php
use Framework\Database\Connection;
use Framework\Database\Statement;
use Framework\Database\Query;
use Framework\Database\Statement;

$conn = new Connection(); // "default" connection

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
<!-- /////////////////////////////////////////////////////////////////////// -->
<p><b>PDO SQL parameters</b></p>
<p>All values are passed as PDO parameters, as shown in the example below.</p>
<?php
$code = <<<'HERE'
<?php
use Framework\Database\Query;
use Framework\Database\Statement;
use Framework\Database\Operator;

$query = (new Query(Statement::UPDATE))
        ->Table("books")
        ->Columns("old", "status")
        ->Values("yes", "for sold")
        ->Where("year", Operator::GEq, 1990)
        ->And("country", Operator::Eq, "PL")
        ->Or("country", Operator::Like, "DE")
        ->OrderBy("title")
        ->Limit(10)
        ->Offset(0);

echo $query . PHP_EOL;
var_dump($query->Params());
HERE;
highlight_string($code);
?>
<code>UPDATE books SET old = :0, status = :1 WHERE year >= :w0 AND
    country = :w1 OR country LIKE :w2</code>
<pre>
array(5) {
  [0]=>
  string(3) "yes"
  [1]=>
  string(8) "for sold"
  ["w0"]=>
  int(1990)
  ["w1"]=>
  string(2) "PL"
  ["w2"]=>
  string(2) "DE"
}
</pre>