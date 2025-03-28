<p><b>Reading config files</b></p>
<p>
    <?php
    $example = <<<'CODE'
    <?php
    use Framework\Config;
    
    Config::Get("sample_key", "sample");
    CODE;
    highlight_string($example);
    ?>
</p>
<p>
    Result: <code><?= Framework\Config::Get("sample_key", "sample"); ?></code>.
    Change the <code>Config/sample.json</code> file to see changes here.
</p>
<p><b>Create new config file</b></p>
<p>
    <?php
    $code = <<<'HERE'
<?php
use Framework\Config;

Config::Set("key1", "example", "new_config");
Config::Set("key2", ["array", "test"=>"example"], "test_config");
Config::Update("test_config");
HERE;
    highlight_string($code);
    ?>
<p/>
<p>Creates new file (if not exist): <code>Config/test_config.json</code>.</p>
<pre>
{"key2":{"0":"array","test":"example"}}
</pre>
<p>Updating existing config files is possible in the same way.</p>