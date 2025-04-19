<p>
    To activate authentication module create <code>authentication.json</code>
    file in the <code>Config</code> directory. By default, bootstrap invokes the
    <code>Framework\Auth::Init()</code> method.
    When <code>authentication.json</code> file is not present, further execution
    of module's code is omitted.
</p>
<p><b>Drivers</b></p>
<ul>
    <li>Database <i>(tested on SQLite and MySQL)</i></li>
    <li>JSON <i>(data stored in the .json format in the Data directory)</i></li>
    <li>LDAP <i>(not yet implemented)</i></li>
</ul>
<h4>Structure of <code>authentication.json</code> (for database driver)</h4>
<pre>
{
    "driver": "database",
    "connection": "default",
    "users_table": "users",
    "groups_table": "groups",
    "memberships_table": "memberships"
}
</pre>
<h4>Structure of <code>authentication.json</code> (for json driver)</h4>
<pre>
{
    "driver": "json",
    "users_file": "users",
    "groups_file": "groups",
}
</pre>