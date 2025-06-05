function switchTab(tab) {
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.form-container').forEach(f => f.classList.remove('active'));

    if (tab === 'login') {
        document.querySelector('.tab:nth-child(2)').classList.add('active');
        document.getElementById('login-form').classList.add('active');
        document.getElementById('tab-indicator').style.left = '0';
    } else {
        document.querySelector('.tab:nth-child(3)').classList.add('active');
        document.getElementById('signup-form').classList.add('active');
        document.getElementById('tab-indicator').style.left = '50%';
    }
}