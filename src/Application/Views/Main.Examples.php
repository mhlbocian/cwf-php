<p><b>Reading config files</b></p>
<?php
$example = <<<'CODE'
    <?php
    Framework\Config::Get("sample_key", "sample");
    CODE;
highlight_string($example);
?>
<b>Result:</b> <?= Framework\Config::Get("sample_key", "sample") ?>