
document.addEventListener('DOMContentLoaded', function () {

    var passwordInput = document.getElementById('validationPassword');
    var progressBar = document.getElementById('progressbar');
    var feedbackValid = document.getElementById('feedbackin');
    var feedbackInvalid = document.getElementById('feedbackirn');
    var submitButton = document.getElementById('submitButton');
    submitButton.disabled = true;
    var strongpass =false;

    if (passwordInput && progressBar && feedbackValid && feedbackInvalid && submitButton) {
        function updatePasswordStrength() {
            var password = passwordInput.value;
            var progressBarWidth = 0;
            var hasUppercase = /[A-Z]/.test(password);
            var hasLowercase = /[a-z]/.test(password);
            var hasNumber = /\d/.test(password);
            var hasSpecialCharacter = /[!@#$%&*_?]/.test(password);

            if (password.length >= 8 && password.length <= 20) progressBarWidth += 25;
            if (hasUppercase) progressBarWidth += 25;
            if (hasLowercase) progressBarWidth += 25;
            if (hasNumber) progressBarWidth += 25;
            if (hasSpecialCharacter) progressBarWidth += 25;

            progressBar.style.width = progressBarWidth + '%';

            if (password.length >= 8 && password.length <= 20 && hasUppercase && hasLowercase && hasNumber && hasSpecialCharacter) {
                passwordInput.classList.remove('is-invalid');
                passwordInput.classList.add('is-valid');
                progressBar.classList.remove('bg-danger', 'bg-warning');
                progressBar.classList.add('bg-success');
                feedbackValid.style.display = 'block';
                feedbackInvalid.style.display = 'none';
                submitButton.disabled = false;
                strongpass = true;
            } else if (password.length > 0) {
                passwordInput.classList.remove('is-valid');
                passwordInput.classList.add('is-invalid');
                progressBar.classList.toggle('bg-danger', progressBarWidth < 50);
                progressBar.classList.toggle('bg-warning', progressBarWidth >= 50);
                feedbackValid.style.display = 'none';
                feedbackInvalid.style.display = 'block';
                submitButton.disabled = true;
                strongpass = false;
            } else {
                passwordInput.classList.remove('is-valid', 'is-invalid');
                progressBar.classList.remove('bg-success', 'bg-warning');
                progressBar.classList.add('bg-danger');
                feedbackValid.style.display = 'none';
                feedbackInvalid.style.display = 'none';
                submitButton.disabled = true;
                strongpass = false;
            }
        }

        passwordInput.addEventListener('input', updatePasswordStrength);
    }

    const form = document.getElementById('registrationForm');
    const showLoaderDiv = document.getElementById('showLoader');

    if (form && submitButton && showLoaderDiv) {
    

        form.addEventListener('input', function () {
            // Check if all required fields are valid
            if (form.checkValidity() && strongpass ) {
                submitButton.disabled = false;
            } else {
                submitButton.disabled = true;
            }
        });
        submitButton.addEventListener('click', function (event) {
            event.preventDefault(); // Verhindere das sofortige Absenden des Formulars

            // if(strongpass){
                showLoaderDiv.style.display = 'flex';
                window.scrollTo({ top: 0, behavior: 'smooth' });
                document.body.style.overflow = 'hidden';
    
                // Simuliere eine Verzögerung vor dem Absenden des Formulars
                setTimeout(function () {
                    showLoaderDiv.style.display = 'none';
                    document.body.style.overflow = '';
                    form.submit();
                  
                }, 3000); 
            // }
            // else {
                
            // }
       
        });
    }

    // E-Mail-Validierung
    const emailInput = document.getElementById('email');
    const emailHelpBlock = document.getElementById('emailHelpBlock');

    if (emailInput && emailHelpBlock) {
        emailInput.addEventListener('input', function () {
            const email = emailInput.value;
            const isValid = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(email);
            if (!isValid && email.length > 0) {
                emailInput.setCustomValidity('Please enter a valid email address.');
                emailHelpBlock.style.display = 'block';
                emailInput.classList.remove('is-valid');
                emailInput.classList.add('is-invalid');
            } else {
                emailInput.classList.remove('is-invalid');
                emailInput.classList.add('is-valid');
                emailInput.setCustomValidity('');
                emailHelpBlock.style.display = 'none';
            }
   
        });
    }

    // Telefonnummernvalidierung
    const phoneInput = document.getElementById('phone');

    if (phoneInput) {
        phoneInput.addEventListener('input', function () {
            let phone = phoneInput.value.replace(/[^\d+]/g, '');

            if (!phone.startsWith('+')) {
                phone = '+' + phone.substring(1);
            }

            phone = phone.substring(0, 13);
            phone = phone.replace(/\D/g, '')
                .replace(/^(\+\d{1,3})?(\d{1,3})?(\d{1,3})?(\d{1,4})?/, (match, g1, g2, g3, g4) => {
                    let result = g1 || '+';
                    if (g2) result += g2;
                    if (g3) result += '-' + g3;
                    if (g4) result += '-' + g4;
                    return result;
                });

            phoneInput.value = phone;

            const isValid = /^\+\d+([-\s\.]?[\d]+)*$/.test(phone) && phone.length <= 13;
            phoneInput.classList.toggle('is-valid', isValid);
            phoneInput.classList.toggle('is-invalid', !isValid);
        });

        phoneInput.addEventListener('keydown', function (event) {
            if (event.target.value.length >= 13 && event.key !== 'Backspace') {
                event.preventDefault();
            }
        });

        if (!phoneInput.value.startsWith('+')) {
            phoneInput.value = '+';
        }
    }
});
