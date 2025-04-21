<p>
    Class <code>Framework\Data\Json</code> supports data maniupulation on the same
    way, as it do <code>Framework\Config</code>, due to implementing the same
    interface. The key difference is the location, where JSON files are stored
    and its purpose, especially for data storing, instead of configuration.
</p>
<p>For manipulating JSON data files, use <code>Framework\Data\Json</code> class.</p>
<pre>
<b>Data/test.json</b>
{
    "key1": {
        "subkey1": "value1"
    },
    "key2": "value2"
}
</pre>
<p>Example code:</p>
<?php
$code = <<<HERE
        <?php
        \$value = Framework\Data\Json::Fetch("test")["key1"];
        echo \$value . PHP_EOL;
        Framework\Data\Json::Set("test", "key2", "new value");
        Framework\Data\Json::Update("test");
        \$value2 = Framework\Data\Json::Fetch("test")["key2"];
        echo \$value2 . PHP_EOL;
        HERE;
highlight_string($code);
?>
<p>Output:</p>
<pre>
value1
new value
</pre>