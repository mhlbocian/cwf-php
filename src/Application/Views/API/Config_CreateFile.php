<p>
    <code>Framework\Config</code> class allows to create new JSON files. Let's
    look at the code below.
</p>
<?php
$code = <<<HERE
        <?php
        Framework\Config::Set("new_file", "some_key", "some_value");
        Framework\Config::Update("new_file");
        HERE;
highlight_string($code);
?>
<p>
    When <code>Config/new_file.json</code> exists, the code above only change
    the value of <code>some_key</code> without touching the rest.
    When <code>Config/new_file.json</code> does not exists, after invoking
    <code>Update</code> the file is created with the contents:
</p>
<pre>
<b>Config/new_file.json:</b>
{"some_key":"some_value"}
</pre>