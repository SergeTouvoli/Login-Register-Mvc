
const cancelBtn = document.querySelector('#cancel');
if(cancelBtn) {
  cancelBtn.addEventListener('click', cancelAvatar);
}

const form = document.getElementById('updateAvatar');
form.addEventListener('submit', async function(event)  {
  event.preventDefault();

  const avatarInput = form.querySelector('input[name="avatar"]');

  const errorMessage = document.querySelector('span.error-message-avatar');
  if (!validateRequired(avatarInput.value)) {
    setError(errorMessage, "L'avatar est requis.");
    return;
  }
  clearError(errorMessage);

  Swal.fire({
    title: 'Êtes-vous sûr ?',
    text: "Vous êtes sur le point de modifier votre photo de profil.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    cancelButtonText: 'Non, annuler ',
    confirmButtonText: 'Oui, modifier  ',
    reverseButtons: true,
    forceSwal2: true
  }).then(async (result) => {
    if (result.isConfirmed) {
      const url = window.location.origin + '/LoginRegisterMvc/ajax/uploadAvatar';
      try {
        const response = await fetch(url, {
          method: 'POST',
          body: new FormData(form)
        });
        const data = await response.json();
        console.log(data)
        if (data.success) {
          Swal.fire({
            title: 'Succès',
            text: 'Votre photo de profil a été modifiée avec succès !',
            icon: 'success',
            showConfirmButton: false,
            forceSwal2: true,
            timer: 1500
          }).then(() => {
            window.location.href = window.location.origin + '/LoginRegisterMvc/profil';        
          });
        } else {
          
          console.log(data);
          console.log(data.errors)

          data.errors.forEach(error => {
            console.log(error)
            errorMessage.textContent = error.msg;
          });
          Swal.fire({
            title: 'Erreur',
            text: 'Une erreur est survenue lors de l\'enregistrement des modification.',
            icon: 'error',
            showConfirmButton: false,
            timer: 1500,
            forceSwal2: true
          })
        }
      } catch(error) {
        Swal.fire({
          title: 'Erreur',
          text: 'Une erreur est survenue lors de l\'enregistrement des modification.',
          icon: 'error',
          showConfirmButton: false,
          timer: 1500,
          forceSwal2: true
        })
      }
    }
  })
});

function previewImage(event) {
  const reader = new FileReader();
  reader.onload = function() {
    document.querySelector('#output_image').src = reader.result;
    document.querySelector('#cancel').style.display = 'block';
    document.querySelector('#validate').style.display = 'block';
  };
  reader.readAsDataURL(event.target.files[0]);
}

function cancelAvatar(){
  window.location.reload()
}