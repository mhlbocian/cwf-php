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
<h4>Database driver - table specification</h4>
<p>
    <code>Framework\Auth</code> requires only 3 tables with columns specification 
    shown below. You <strong>must</strong> provide exact names for each column.
    Additional columns are allowed. Table names should be specified in the
    <code>authentication.json</code> file. When table names are not specified,
    default values are loaded, such as <code>users</code>, <code>groups</code>
    and <code>memberships</code>
</p>
<pre>
<b>TABLE: users</b>

+==============+==============+==============+
|   username   |   fullname   |   password   |
+==============+==============+==============+
| varchar(255) | varchar(255) | varchar(255) |
+==============+==============+==============+

PRIMARY KEY: username

<b>TABLE: groups</b>

+==============+==============+
|  groupname   |  description |
+==============+==============+
| varchar(255) | varchar(255) |
+==============+==============+

PRIMARY KEY: groupname

<b>TABLE: memberships</b>

+==============+==============+
|   username   |   groupname  |
+==============+==============+
| varchar(255) | varchar(255) |
+==============+==============+

FOREIGN KEYS: username -> users.username, groupname -> groups.groupname
</pre>
<p style="text-align: right;">
    <a href="<?= Framework\Url::Site("Authenticate") ?>">Authentication tests site</a>
</p>
