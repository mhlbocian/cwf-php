<p>Users and groups data is stored in the JSON file format.</p>
<pre>
<b>users.json</b>

{
    "user1": {
        "fullname": user_fullname,
        "groups": [
            group1, group2, ...
        ],
        "password": password_hash
    },
    "user2": {
        ...
    }
}

<b>groups.json</b>

{
    "group1": {
        "description": group_description,
        "members": [
            user1, user2, ...
        ]
    },
    "group2": {
        ...
    }
}
</pre>