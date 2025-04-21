<p>
    All framework config files are stored in the <code>Config</code> directory.
    Config API allows you, to create your custom config files and updating
    existing ones.
</p>

<pre>
<b>Namespace:</b> Framework;
</pre>
<p><b>Class reference</b></p>
<pre>
<b>Implements interface:</b> Framework\Interfaces\Data_Json

[M] static Config::Exists(string $file): bool

    <b>Input:</b>
        string $file - config file name

    <b>Example use:</b>
        &lt;?php
        Framework\Config::Exists("application");

    <b>Return value:</b>
        true - config file exists
        false - config file does not exist

[M] static Fetch(string $file): array

    <b>Input:</b>
        string $file - config file name

    <b>Example use:</b>
        &lt;?php
        Framework\Config::Fetch("application");

    <b>Return value:</b>
        array - all JSON data as associative array
    
    <b>Throws:</b>
        Exception - when config file does not exist

[M] static Get(string $file, string $key): mixed

    <b>Input:</b>
        string $file - config file name
        string $key - JSON key

    <b>Example use:</b>
        &lt;?php
        Framework\Config::Get("application", "router");

    <b>Return value:</b>
        mixed - data stored under the key
    
    <b>Throws:</b>
        Exception - when the key does not exist

[M] static Set(string $file, string $key, mixed $value): void

    <b>Input:</b>
        string $file - config file name
        string $key - JSON key
        mixed $value - value to be stored under the key

    <b>Example use:</b>
        &lt;?php
        Framework\Config::Set("application", "router", [
            "namespace" => "Application\\Controllers",
            "default_controller" => "Main",
            "default_action" => "Index"
        ]);
        // invoke Config::Update to update the file contents

[M] static Unset(string $file, string $key): void;

    <b>Input:</b>
        string $file - config file name
        string $key - JSON key

    <b>Example use:</b>
        &lt;?php
        Framework\Config::Unset("application", "router");
        // invoke Config::Update to update the file contents

[M] static Update(string $file): void;
    <b>Input:</b>
        string $file - config file name

    <b>Example use:</b>
        &lt;?php
        Framework\Config::Unset("application", "router");
        Framework\Config::Update();
</pre>