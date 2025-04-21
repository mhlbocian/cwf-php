<h3>Log in user</h3>
<p>Remember to create a user account first, if you have an empty database.</p>
<form action="<?= \Framework\Url::Site("/Forms/Login") ?>" method="post">
    <label for="inpUsername">Username:</label>
    <input type="text" name="inpUsername" required/>
    <label for="inpPassword">Password:</label>
    <input type="password" name="inpPassword" required/>
    <button type="submit" name="btnSubmit" size="20">Sign in</button>
</form>
<?php if($status == "fail"): ?>
    <p style="color: red;">
        Invalid username or password
    </p>
<?php endif; ?>