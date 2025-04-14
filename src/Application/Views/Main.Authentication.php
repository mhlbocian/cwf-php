<h3>Authentication</h3>
<p>
    To activate authentication module create <code>auth.json</code> file in the
    <code>Config</code> directory. By default, bootstrap invokes the
    <code>Framework\Auth::Load_Config()</code> method. When <code>auth.json</code>
    file is not present, further execution of module's code is omitted.
</p>
<p><b>Drivers</b></p>
<ul>
    <li>Database <i>(partially implemented)</i></li>
    <li>LDAP <i>(planned for implementation)</i></li>
</ul>
<h4>Structure of <code>auth.json</code></h4>
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
    <code>Framework\Auth</code> requires currently 3 tables with columns specification 
    shown below. You must provide exact names for each column. Additional columns are
    allowed. Table names must be specified in the <code>auth.json</code> file.
</p>
<pre>
<b>TABLE: users</b>

+============+============+=============+
|  username  |  fullname  |   password  |
+============+============+=============+
|    text    |    text    |     text    |
+============+============+=============+

<b>TABLE: groups</b>

+============+=============+
|  groupname | description |
+============+=============+
|    text    |    text     |
+============+=============+

<b>TABLE: memberships</b>

+============+=============+
|  username  |  groupname  |
+============+=============+
|    text    |    text     |
+============+=============+

FOREIGN KEYS: username -> users.username, groupname -> groups.groupname
</pre>
<p style="text-align: right;">
<a href="<?= Framework\Url::Site("Authenticate") ?>">Authentication tests site</a>
</p>