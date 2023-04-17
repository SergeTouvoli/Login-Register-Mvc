function setError(element, message) {
    element.textContent = message;
    element.classList.add('error');
}
  
function clearError(element) {
    element.textContent = '';
    element.classList.remove('error');
}
  
function validateEmail(email) {
    return new RegExp(/\S+@\S+\.\S+/).test(email);
}

function validatePseudo(pseudo) {
    return new RegExp(/^[a-zA-Z0-9]+$/).test(pseudo);
}

function validateRequired(value) {
    return value.trim() !== '';
}

function validateMatch(value1, value2) {
    return value1 === value2;
}



