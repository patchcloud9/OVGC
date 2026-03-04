// reCAPTCHA v3: reads site key from form data-key attribute, executes on submit.
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('contact-form');
    var key = form ? form.dataset.key : '';
    if (!form || !key) return;
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        grecaptcha.ready(function () {
            grecaptcha.execute(key, {action: 'contact'}).then(function (token) {
                document.getElementById('recaptcha_token').value = token;
                form.submit();
            });
        });
    });
});
