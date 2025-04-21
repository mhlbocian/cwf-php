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
<p>To fetch all the file use <code>Config::Fetch</code> method.</p>
<?php
$code = <<<HERE
        <?php
        \$data = Framework\Config::Fetch("test");
        HERE;
highlight_string($code);
?>
<pre>
<b>$data: array</b>
[
    "key1" => [
        "subkey1" => "value1"
    ],
    "key2" => "value2"
]
</pre>
<p>
    You can also fetch the single key from the config file via
    <code>Config::Get</code> method.
</p>
<?php
$code = <<<HERE
        <?php
        \$data = Framework\Config::Get("test", "key2");
        HERE;
highlight_string($code);
?>
<pre>
<b>$data: string</b> value2
</pre>