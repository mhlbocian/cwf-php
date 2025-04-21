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