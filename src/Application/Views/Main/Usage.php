<h3>Framework usage</h3>
<p>Coding apps in CWF-PHP is easy, but you <b>must</b> follow theese simple
    conditions.</p>
<p><b>Basic directory structure</b></p>
<pre>
[D] Application                 (application directory)
    [D] Controllers             (controllers directory)
        [...]
    [D] Models                  (models directory)
        [...]
    [D] Views                   (views directory)
        [...]
[D] Config                      (configuration files directory)
    [.] application.json        (application config)
    [.] authentication.json     (auth api config)
    [.] database.json           (database connections config)
    [...]                       (custom json files)
[D] Data                        (application data directory)
    [...]                       (SQLite databases, uploaded files, etc.)
[D] Framework                   (CWF-PHP sources)
    [...]
[D] Public                      (public http server directory)
    [.] index.php               (bootstrap app and parse URL route)
[D] Static                      (non-php files for internal CWF-PHP functions)
[.] bootstrap.php               (framework bootstrap)
</pre>

<p><b>Classes namespace convention</b></p>
<p>
    Each class (like controller, model, framework file) must be located in a
    separate namespace. CWF-PHP autoloader function follows directory structure
    and class names for finding files to include.
</p>
<p>
    Eg. For controller Test, which source is in <code>class Test</code> and the
    file is located in <code>Application\Controllers</code> directory, the class
    <b>must be</b> inside <code>Application\Controllers</code> namespace and the
    file name <b>must be</b> the same as class name (in eg <code>Test.php</code>).
</p>
<?php
$example = <<<'CODE'
<?php
use Application\Controllers;
    
class Test {
    // your code
}
CODE;
highlight_string($example);
?>

<p><b>Routing and control flow</b></p>
<p>
    When the <code>index.php</code> file is accessed via http, the <code>PATH_INFO</code>
    data is transferred into <code>Framework\Router</code> object. <code>PATH_INFO</code>
    value is the string after slash at the end of <code>index.php</code>. For
    example: <code>index.php/controller/action/arg1/arg2...</code>. If no
    <code>PATH_INFO</code> is provided, default action in default controller is
    executed. When only controller name is available, default action is performed.
</p>
<p>You can change router settings in the <code>application.json</code> file in
    the section "router".</p> You can change theese parameters:
<ul>
    <li><code>namespace</code> - controllers namespace</li>
    <li><code>default_controller</code> - default controller name</li>
    <li><code>default_action</code> - default action name</li>
</ul>
<p>
    You should not change the <code>namespace</code> key, as is meant all the
    controllers code should be in the <code>Application\Controllers</code> directory.
</p>
<p>
    When the controller or action does not exist, <code>Router</code> throws
    the <code>Router_Exception</code>. By default, then <code>index.php</code> script
    redirects an action to default action in default controller. You can change this
    behaviour, by manually editing the code inside <code>catch (Router_Exception)</code>
    section.
</p>

<p><b>Minimal configuration</b></p>
<p>
    For minimal configuration you must have two files in <code>Config</code>
    directory: <code>application.json</code> and <code>database.json</code>.
    Assume, we have a configuration:
</p>
<ul>
    <li>Web server: PHP Development server</li>
    <li>Protocol: HTTP</li>
    <li>Hostname: localhost</li>
    <li>Port: 8000</li>
    <li>DOCROOT: Public</li>
    <li>DOCROOT URL path: /</li>
    <li>Database engine: SQLite (database file: mydb.sqlite)</li>
</ul>
<pre>
<b>application.json</b>
{
    "application": {
        "name": "your-app-name",
        "description": "Your app description",
        "version": "app-version"
    },
    "router": {
        "namespace": "Application\\Controllers",
        "default_controller": "Main",
        "default_action": "Index"
    },
    "url": {
        "protocol": "http",
        "host": "localhost",
        "port": 8000,
        "path": "/",
        "index": "index.php",
        "omit_index": false
    }
}
</pre>
<p>
    <b>"path"</b> means where the index.php file is accessible for the web server
    via URL. If your Public directory is further in the URL path, like:<br/>
    <code>http://hostname/app_dir/index.php</code><br/>
    change it to <code>/app_dir/</code> in this case.
</p>

<p>
    <b>"index"</b> if you want to change default index.php file name, remember to
    change this config file key.
</p>

<p>
    <b>"omit_index"</b> in the URL section means, that to create URL for the
    specific controller/action, include "index.php" in the address. If you have
    rewrite engine you can set this to true. Then, instead of:<br/>
    <code>http://hostname/index.php/Controller/Action/Arg1/Arg2...</code><br/>
    you have:<br/>
    <code>http://hostname/Controller/Action/Arg1/Arg2...</code>
</p>

<pre>
<b>database.json</b>
{
    "default" : {
        "driver": "sqlite",
        "database": "mydb.sqlite"
    }
}
</pre>
<p>
    You can have multiple connections, but when Framework\Database object is
    created without connection name, it loads configuration with "default" name.
    More information about database configuration:
    <a href="<?= Framework\Url::Site("/Main/API/Db_Config") ?>">here</a>
</p>
<p><b>Write simple webpage</b></p>
<p>
    For configruation above, we write a simple action <code>Index</code> in the
    <code>Main</code> controller.
</p>
<pre>
<b>Application\Controllers\Main.php</b>
</pre>
<?php
$code = <<<HERE
<?php

namespace Application\Controllers;
        
class Main {
    public function Index(): void {
        echo "Hello, world!";
    }
}
        
HERE;
highlight_string($code);
?>
<p>
    When you type <code>http://localhost:8000/index.php/Main/Index</code> or just
    <code>http://localhost:8000</code> you should see "Hello, world!" on the screen.
</p>