<?php if ($isLogged): ?>

    <div class="container">You are logged on</div>

<?php else: ?>
    <div class="container text-center mt-5">
        <h1>Admin panel</h1>

        <div class="login-form__info bg-success text-light"></div>
        <div class="login-form__error bg-danger text-light"></div>

        <form class="login-form" method="post">
            <div class="form-row text-left">
                <div class="form-group col-12">
                    <label>Name</label><input class="form-control" name="name">
                </div>
                <div class="form-group col-12">
                    <label>Password</label><input class="form-control" name="password" type="password">
                </div>
                <div class="form-group col-12">
                    <button class="btn btn-primary" onclick="doLogin()">ENTER</button>
                </div>
            </div>
        </form>
    </div>

<script>
    function doLogin() {

        let setInfo = function(value) {
            document.querySelector('.login-form__info').innerHTML = value
        };

        let setError = function(value) {
            document.querySelector('.login-form__error').innerHTML = value
        };

        let form = document.querySelector('.login-form');

        ajax('/admin/login', 'name=' + form['name'].value + '&password=' + form['password'].value, function(response) {
            setInfo('You have made a successful login. Now we are redirecting');
            setError(null);

            setTimeout(function() {
                window.location.href = '/'
            }, 3000);

        }, function(response) {
            setError(JSON.parse(response.responseText).join('<BR>'));
            setInfo(null)
        });

        event.preventDefault()
    }
</script>

<?php endif; ?>