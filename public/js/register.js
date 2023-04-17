const form = document.getElementById('register-form');
form.addEventListener('submit', async function(event)  {
    event.preventDefault();

    const pseudoInput = this.querySelector('input[name="pseudo"]');
    const emailInput = this.querySelector('input[name="email"]');
    const passwordInput = this.querySelector('input[name="password"]');
    const confirmPasswordInput = this.querySelector('input[name="confirm_password"]');
    const errorMessages = this.querySelectorAll('span.error-message');

    if (!validateRequired(pseudoInput.value)) {
        setError(errorMessages[0], "Veuillez entrer un pseudo.");
        return;
    }
    clearError(errorMessages[0]);
    if(pseudoInput.value.length < 3 || pseudoInput.value.length > 20) {
        setError(errorMessages[0], "Le pseudo doit contenir entre 3 et 20 caractères.");
        return;
    }
    clearError(errorMessages[0]);

    if (!validatePseudo(pseudoInput.value)) {
        setError(errorMessages[0], "Le pseudo doit être alphanumérique.");
        return;
    }
    clearError(errorMessages[0]);

    if (!validateRequired(emailInput.value)) {
        setError(errorMessages[1], "Veuillez entrer un email.");
        return;
    }
    clearError(errorMessages[1]);

    if (!validateEmail(emailInput.value)) {
        setError(errorMessages[1], "Veuillez entrer un email valide.");
        return;
    }
    clearError(errorMessages[1]);

    if (!validateRequired(passwordInput.value)) {
        setError(errorMessages[2], "Veuillez entrer un mot de passe.");
        return;
    }
    clearError(errorMessages[2]);

    if (passwordInput.value.length < 8 || passwordInput.value.length > 20) {
        setError(errorMessages[2], "Le mot de passe doit contenir entre 8 et 20 caractères.");
        return;
    }
    clearError(errorMessages[2]);

    if (!validateRequired(confirmPasswordInput.value)) {
        setError(errorMessages[3], "Veuillez confirmer votre mot de passe.");
        return;
    }
    clearError(errorMessages[3]);

    if (!validateMatch(passwordInput.value, confirmPasswordInput.value)) {
        setError(errorMessages[3], "Les mots de passe ne correspondent pas.");
        return;
    }
    clearError(errorMessages[3]);

    const url = window.location.origin + '/LoginRegisterMvc/ajax/register';
    try {
        const response = await fetch(url, {
            method: 'POST',
            body: new FormData(this)
        });
        const data = await response.json();
        console.log(data)
        if (data.success) {
            Swal.fire({
                title: 'Succès',
                text: 'Inscription réussie.',
                icon: 'success',
                showConfirmButton: false,
                forceSwal2: true,
                timer: 1500
            }).then(() => {
                window.location.href = window.location.origin + '/LoginRegisterMvc/connexion';
            });
        } else {
            data.errors.forEach(error => {
                const errorMessage = this.querySelector(`span.error-message[data-error="${error.param}"]`);
                errorMessage.textContent = error.msg;
            });
            Swal.fire({
                title: 'Erreur',
                text: 'Une erreur est survenue lors de l\'inscription.',
                icon: 'error',
                showConfirmButton: false,
                forceSwal2: true,
                timer: 1500,
            })
        }
    } catch(error) {
        console.error(error);
        Swal.fire({
            title: 'Erreur',
            text: 'Une erreur est survenue lors de l\'inscription.',
            icon: 'error',
            showConfirmButton: false,
            forceSwal2: true,
            timer: 1500,
        })
    }
});




   