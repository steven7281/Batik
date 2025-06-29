// Fungsi untuk validasi email
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
};

// Data admin
//const adminCredentials = {
    //email: 'admin@admin.com',
    //password: '12345'
//};

// Fungsi untuk validasi password admin
function validateAdminPassword(password) {
    const requirements = {
        length: password.length >= 8,
        letter: /[A-Za-z]/.test(password),
        number: /[0-9]/.test(password),
        special: /[@$!%*#?&]/.test(password)
    };

    // Update UI jika elemen ada
    if (document.getElementById('length')) {
        document.getElementById('length').classList.toggle('valid', requirements.length);
        document.getElementById('length').classList.toggle('invalid', !requirements.length);
    }
    if (document.getElementById('letter')) {
        document.getElementById('letter').classList.toggle('valid', requirements.letter);
        document.getElementById('letter').classList.toggle('invalid', !requirements.letter);
    }
    if (document.getElementById('number')) {
        document.getElementById('number').classList.toggle('valid', requirements.number);
        document.getElementById('number').classList.toggle('invalid', !requirements.number);
    }
    if (document.getElementById('special')) {
        document.getElementById('special').classList.toggle('valid', requirements.special);
        document.getElementById('special').classList.toggle('invalid', !requirements.special);
    }

    return Object.values(requirements).every(req => req === true);
}

// Login utama untuk admin & user
function login(e) {
    e.preventDefault();
    const email = document.getElementById('loginEmail').value;
    const password = document.getElementById('loginPassword').value;
    const errorAlert = document.getElementById('errorAlert');

    if (!email || !password) {
        errorAlert.style.display = 'block';
        errorAlert.textContent = 'Email dan password harus diisi!';
        return;
    }

    // Login admin
    if (email === adminCredentials.email && password === adminCredentials.password) {
        localStorage.setItem('isAdminLoggedIn', 'true');
        localStorage.setItem('adminEmail', email);
        window.location.href = 'dashboard.php';
        return;
    }

    // Login user biasa
    localStorage.setItem('isLoggedIn', 'true');
    localStorage.setItem('currentUser', email);
    localStorage.setItem('username', email.split('@')[0]);
    window.location.href = 'index.php';
}

// Logout admin
function adminLogout() {
    localStorage.removeItem('isAdminLoggedIn');
    localStorage.removeItem('adminEmail');
    window.location.href = 'adminlogin.php';
}

// Logout user
function logout() {
    localStorage.removeItem('isLoggedIn');
    localStorage.removeItem('currentUser');
    localStorage.removeItem('username');
    window.location.href = 'login.php';
}

// Cek status login admin
function checkAdminAuth() {
    const isAdminLoggedIn = localStorage.getItem('isAdminLoggedIn') === 'true';
    const adminEmail = localStorage.getItem('adminEmail');
    if (window.location.pathname.includes('admin-dashboard.html') && !isAdminLoggedIn) {
        window.location.href = 'login.php';
        return false;
    }
    return isAdminLoggedIn && adminEmail === adminCredentials.email;
}

// Cek status login user & update UI
function checkLoginStatus() {
    const currentUser = localStorage.getItem('currentUser');
    if (currentUser) {
        const loginElements = document.querySelectorAll('.login-status');
        loginElements.forEach(element => {
            element.textContent = `Halo, ${currentUser.split('@')[0]}`;
        });
    }
    if (checkAdminAuth()) {
        const adminHeader = document.querySelector('.dashboard-header h2');
        if (adminHeader) {
            const adminEmail = localStorage.getItem('adminEmail');
            adminHeader.textContent = `Selamat Datang, ${adminEmail.split('@')[0]}`;
        }
    }
}

document.addEventListener('DOMContentLoaded', checkLoginStatus);
if (window.location.pathname.includes('admin-dashboard.html')) {
    setInterval(checkAdminAuth, 300000);
}

// Login dengan Google (user biasa)
function loginWithGoogle() {
    const randomEmail = 'user' + Math.floor(Math.random() * 1000) + '@gmail.com';
    localStorage.setItem('isLoggedIn', 'true');
    localStorage.setItem('currentUser', randomEmail);
    localStorage.setItem('username', randomEmail.split('@')[0]);
    window.location.href = 'index.php';
}

// Toggle visibility password
function togglePasswordVisibility(inputId, iconElement) {
    const passwordInput = document.getElementById(inputId);
    const icon = iconElement || document.querySelector(`#${inputId} + i`);
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Fungsi untuk registrasi
function register(e) {
    e.preventDefault();
    
    const name = document.getElementById('registerName').value;
    const email = document.getElementById('registerEmail').value;
    const password = document.getElementById('registerPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    // Validasi input
    if (!name || !email || !password || !confirmPassword) {
        alert('Semua field harus diisi!');
        return;
    }
    
    if (!validateEmail(email)) {
        alert('Email tidak valid!');
        return;
    }
    
    if (password.length < 6) {
        alert('Password harus minimal 6 karakter!');
        return;
    }
    
    if (password !== confirmPassword) {
        alert('Password tidak cocok!');
        return;
    }

    alert('Registrasi berhasil! Silakan login.');
    window.location.href = 'login.php';
}


// Fungsi untuk live chat
    function toggleChat() {
        const chatBox = document.getElementById('liveChatBox');
        if (chatBox.style.display === 'none' || chatBox.style.display === '') {
            chatBox.style.display = 'flex';
        } else {
            chatBox.style.display = 'none';
        }
    }