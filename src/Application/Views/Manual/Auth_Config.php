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
<h4>Structure of <code>authentication.json</code> (for JSON driver)</h4>
<pre>
{
    "driver": "json",
    "users_file": "users",
    "groups_file": "groups",
}
</pre>
<h4>Data validation</h4>
<p>
    Each field like user name, full name, password, group name and its description
    is passed through <code>Framework\Auth::CheckFmt</code> function. If you
    want to change the pattern for each field you can add this section, to the
    <code>Config/authentication.json</code>. Default patterns are shown below:
</p>
<pre>
"format": {
    "username": "[\\w][\\w.]{4,}",
    "fullname": ".{5,}",
    "password": ".{8,}",
    "groupname": "[\\w][\\w.]{4,}",
    "description": ".{5,}"
}
</pre>
<p>
    If you omit this section, default values are loaded. You don't have to
    specify all fields.
</p>