<?php
//session_start();
if (!empty($_SESSION['username_alomani'])) {
    header('location:index.php');
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.122.0">
    <title>Alomani</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/5.3/examples/sign-in/">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/login-style.css">
</head>

<body>
    <main class="register-container">
        <div class="register-box">
            <form class="needs-validation" novalidate action="proses/proses_login.php" method="post">
                <div class="text-center mb-4">
                    <img src="assets/images/logo.png" alt="Batik Alomani Logo" style="max-width: 150px;">
                </div>
                <div class="register-title">Please Login</div>
                <div class="form-floating">
                    <input name="username" type="text" class="form-control" id="floatingInput"
                        placeholder="name@example.com" required>
                    <label for="floatingInput">Email atau No Telepon</label>
                    <div class="valid-feedback">
                        Masukan email atau No Hp
                    </div>
                </div>
                <div class="form-floating">
                    <input name="password" type="password" class="form-control" id="floatingPassword"
                        placeholder="Password" required>
                    <label for="floatingPassword">Password</label>
                    <div class="invalid-feedback">
                        Masukan password.
                    </div>
                </div>
                <div class="checkbox mb-3 text-center">
                    <label>
                        <input class="form-check-input" type="checkbox" value="remember-me">Remember me
                    </label>
                </div>
                <button type="submit" class="btn btn-register-main" name="submit_validate"
                    value="abc">Login</button>
                <div class="register-links text-center">
                    Belum Punya Akun ? <a href="register.php">Daftar</a> 
                </div>
                <p class="mt-5 mb-3 text-muted text-center">&copy; Batik Alomani -<?php echo date("Y") ?></p>
            </form>
        </div>
    </main>

    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (() => {
            'use strict'

            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            const forms = document.querySelectorAll('.needs-validation')

            // Loop over them and prevent submission
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }

                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>

    </body>

</html>