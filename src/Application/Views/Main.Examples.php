<h3>Reading config files</h3>
<?php
$example = <<<'CODE'
<?php
use Framework\Config;
    
Config::Get("sample_key", "sample");
CODE;
highlight_string($example);
?>

<h3>Creating new config files</h3>
<?php
$code = <<<'HERE'
<?php
use Framework\Config;

Config::Set("key1", "example", "test_config");
Config::Set("key2", ["array", "test"=>"example"], "test_config");
Config::Update("test_config");
HERE;
highlight_string($code);
?>
<p>Creates new file (if not exist): <code>Config/test_config.json</code>.</p>
<pre>
{"key2":{"0":"array","test":"example"}}
</pre>
<p>Updating existing config files is possible in the same way.</p>