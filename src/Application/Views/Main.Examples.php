<h3>Reading config files</h3>
<?php
$example = <<<'CODE'
<?php
use Framework\Config;
    
Config::Get("config_file", "key");
CODE;
highlight_string($example);
?>

<h3>Creating new config files</h3>
<?php
$code = <<<'HERE'
<?php
use Framework\Config;

Config::Set("config_file", "key1", "value1");
Config::Set("config_file", "key2", ["array", "test"=>"example"]);
Config::Update("config_file");
HERE;
highlight_string($code);
?>
<p>Creates new file (if not exist): <code>Config/test_config.json</code>.</p>
<pre>
{"key2":{"0":"array","test":"example"}}
</pre>
<p>Updating existing config files is possible in the same way.</p>