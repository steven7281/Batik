<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Batik Alomani</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/register.css">
</head>

<body>
    <div class="register-container">
        <div class="register-box">
            <form class="needs-validation" novalidate action="proses/proses_register.php" method="POST">
                <div class="register-title">Register</div>
                <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama">
                <input type="email" class="form-control" id="username" name="username" placeholder="Email">
                <input type="number" class="form-control" id="nohp" name="nohp" placeholder="No Telepon">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                <input type="password" class="form-control" id="password2" placeholder="Validasi Password">
                <div class="form-floating">
                <textarea 
                    class="form-control" 
                    placeholder="Alamat lengkap" 
                    id="alamat" 
                    name="alamat" 
                    style="height: 100px; resize: none;"></textarea>
                <label for="alamat">Alamat</label>
                </div>
                <button type="submit" class="btn btn-register-main" name="input_user_validate"
                    value="1234">Register</button>
            </form>
            <div class="register-links">
                Sudah Punya Akun ? <a href="login.php">Login</a>
            </div>
    <script>
        document.querySelector("form").addEventListener("submit", function (e) {
            const password = document.getElementById("password").value;
            const password2 = document.getElementById("password2").value;

            if (password !== password2) {
                e.preventDefault(); // hentikan form submit
                alert("Password Harus Sama!");
            }
        });
    </script>

</body>

</html>