const form = document.getElementById('login-form');
form.addEventListener('submit', async function(event)  {
    event.preventDefault();

    const emailInput = this.querySelector('input[name="email"]');
    const passwordInput = this.querySelector('input[name="password"]');
    const errorMessages = this.querySelectorAll('span.error-message');

    if(!validateRequired(emailInput.value)) {
        setError(errorMessages[0], "Veuillez entrer une adresse email.");
        return;
    }
    clearError(errorMessages[0]);

    if(!validateEmail(emailInput.value)) {
        setError(errorMessages[0], "Veuillez entrer une adresse email valide.");
        return;
    }
    clearError(errorMessages[0]);

    if(!validateRequired(passwordInput.value)) {
        setError(errorMessages[1], "Veuillez entrer un mot de passe.");
        return;
    }
    clearError(errorMessages[1]);

    if(passwordInput.value.length < 8 || passwordInput.value.length > 20) {
        setError(errorMessages[1], "Le mot de passe doit contenir entre 8 et 20 caractères.");
        return;
    }
    clearError(errorMessages[1]);

   const url = window.location.origin + '/LoginRegisterMvc/ajax/login';
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
                text: 'Connexion réussie.',
                icon: 'success',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = window.location.origin + '/LoginRegisterMvc/profil';
            });
        } else {
            data.errors.forEach(error => {
                const errorMessage = this.querySelector(`span.error-message[data-error="${error.param}"]`);
                errorMessage.textContent = error.msg;
            });
            Swal.fire({
                title: 'Erreur',
                text: 'Une erreur est survenue lors de la connexion.',
                icon: 'error',
                showConfirmButton: false,
                timer: 1500,
            })
        }
    } catch (error) {
        Swal.fire({
            title: 'Erreur',
            text: 'Une erreur est survenue lors de la connexion.',
            icon: 'error',
            showConfirmButton: false,
            timer: 1500,
        })
    }
});
