<p>
    In this example, we have a <code>Config/test.json</code> file with the
    contents shown below:
</p>
<pre>
<b>Config/test.json</b>
{
    "key1": {
        "subkey1": "value1"
    },
    "key2": "value2"
}
</pre>
<p>
    To update a key and add a new one in the file,
    use <code>Config::Set</code> method.
</p>
<?php
$code = <<<HERE
        <?php
        Framework\Config::Set("test", "key2", "new_value");
        Framework\Config::Set("test", "key3", [
                "subkey2" => "value3"
            ]);
        Framework\Config::Update("test");
        HERE;
highlight_string($code);
?>
<p>
    <b>Notice:</b> Without invoking <code>Config::Update</code> method, all the
    changes will remain in the memory during code execution.
</p>
<p>
    After changes, the <code>Config/test.json</code> is updated with a new
    content:
</p>
<pre>
<b>Config/test.json</b>
{
    "key1": {
        "subkey1": "value1"
    },
    "key2": "new_value",
    "key3": {
        "subkey2" => "value3"
    }
}
</pre>