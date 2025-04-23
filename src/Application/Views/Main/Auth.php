<?php
$auth = Framework\Auth::Instance();
?>
<h3>Users</h3>
<ul>
    <?php foreach ($users as $username => $fullname): ?>
        <li>
            <?= $username ?> (<?= $fullname ?>) [
            <?php foreach ($auth->UserInfo($username)["groups"] as $group): ?>
                '<?= $group ?>'
            <?php endforeach; ?>]
        </li>
    <?php endforeach; ?>
</ul>
<p><b>Add user</b></p>
<form action="<?= \Framework\Url::Site("/Forms/AddUser") ?>" method="post">
    <label for="inpUsername">Username:</label>
    <input type="text" name="inpUsername" required minlength="5" />
    <label for="inpFullname">Full name:</label>
    <input type="text" name="inpFullname" required minlength="5" />
    <label for="inpPassword">Password:</label>
    <input type="password" name="inpPassword" required minlength="8" />
    <button type="submit">Add user</button>
</form>
<p><b>Delete user</b></p>
<form action="<?= \Framework\Url::Site("/Forms/DelUser") ?>" method="post">
    <label for="inpUsername">Username:</label>
    <input type="text" name="inpUsername"/>
    <button type="submit">Delete user</button>
</form>
<?php if ($status == "usersuccess"): ?>
    <p style="color: green;">
        Operation succeeded
    </p>
<?php endif; ?>
<?php if ($status == "userexists"): ?>
    <p style="color: red;">
        User already exists
    </p>
<?php endif; ?>
<?php if ($status == "usernotexists"): ?>
    <p style="color: red;">
        User not exists
    </p>
<?php endif; ?>
<?php if ($status == "userfailed"): ?>
    <p style="color: red;">
        Operation failed
    </p>
<?php endif; ?>

<p>&HorizontalLine;&HorizontalLine;&HorizontalLine;</p>

<h3>Groups</h3>
<ul>
    <?php foreach ($groups as $groupname => $description): ?>
        <li>
            <?= $groupname ?> (<?= $description ?>): [
            <?php foreach ($auth->UserFetch($groupname) as $username => $fullname): ?>
                '<?= $username ?>'
            <?php endforeach; ?>]
        </li>
    <?php endforeach; ?>
</ul>
<p><b>Add group</b></p>
<form action="<?= \Framework\Url::Site("/Forms/AddGroup") ?>" method="post">
    <label for="inpGroupname">Group name:</label>
    <input type="text" name="inpGroupname" />
    <label for="inpDescription">Description:</label>
    <input type="text" name="inpDescription" required minlength="5" />
    <button type="submit">Add group</button>
</form>
<p><b>Delete group</b></p>
<form action="<?= \Framework\Url::Site("/Forms/DelGroup") ?>" method="post">
    <label for="inpGroupname">Group name:</label>
    <input type="text" name="inpGroupname" required/>
    <button type="submit">Delete group</button>
</form>
<?php if ($status == "groupsuccess"): ?>
    <p style="color: green;">
        Operation succeeded
    </p>
<?php endif; ?>
<?php if ($status == "groupexists"): ?>
    <p style="color: red;">
        Group already exists
    </p>
<?php endif; ?>
<?php if ($status == "groupnotexists"): ?>
    <p style="color: red;">
        Group not exists
    </p>
<?php endif; ?>
<?php if ($status == "groupfailed"): ?>
    <p style="color: red;">
        Operation failed
    </p>
<?php endif; ?>

<p>&HorizontalLine;&HorizontalLine;&HorizontalLine;</p>

<h3>Membership</h3>
<form action="<?= \Framework\Url::Site("/Forms/Membership") ?>" method="post">
    <label for="inpUsername">Username:</label>
    <input type="text" name="inpUsername" required />
    <label for="inpGroupname">Group name:</label>
    <input type="text" name="inpGroupname" required />
    <label for="inpAction">Add user</label>
    <input type="radio" name="inpAction" value="addUser" checked />
    <label for="inpAction">Delete user</label>
    <input type="radio" name="inpAction" value="delUser"/>
    <button type="submit">Perform action</button>
</form>
<?php if ($status == "membershipsuccess"): ?>
    <p style="color: green;">
        Operation succeeded
    </p>
<?php endif; ?>
<?php if ($status == "membershipexists"): ?>
    <p style="color: red;">
        User already in the group
    </p>
<?php endif; ?>
<?php if ($status == "membershipfailed"): ?>
    <p style="color: red;">
        Operation failed
    </p>
<?php endif; ?>