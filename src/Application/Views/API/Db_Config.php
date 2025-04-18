<p><b>Database drivers</b></p>
<ul>
    <li>SQLite</li>
    <li>MySQL</li>
</ul>
<p><b>Configuration file format</b></p>
<p>Configuration file is stored in <code>Config/database.json</code>.</p>
<pre>
{
    "connection_name": {
        "driver": "driver_name",
        "database": "database_name",
        "host": "hostname",
        "port": 1234,
        "username": "db_username",
        "password": "db_password"
    }
}
</pre>
<p><b>NOTICE:</b> Some fields are necessary and depends on driver.</p>
<p>
    When <code>Framework\Database</code> object is created, you must
    specify connection name, otherwise framework fetch configuration from
    connection named <code>default</code>.
</p>
<p>For example, we have only one connection (default) with driver SQLite. The
    configuration file is shown below.</p>
<pre>
{
    "default": {
        "driver": "sqlite",
        "database": "database.sqlite",
    }
}
</pre>
<p>
    During estabilishing connection, the driver creates database file named
    <code>database.sqlite</code> in folder <code>Data</code>.
</p>
<p>
    <?php
    $code = <<<'HERE'
<?php
use Framework\Database;
$connection = new Database(); // "default" connection
HERE;
    highlight_string($code);
    ?>
</p>