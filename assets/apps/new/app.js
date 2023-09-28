import "../../styles/new/app.scss";

// Seleciona o elemento de entrada pelo ID
var cpfInput = document.getElementById('pessoas_cpf');

cpfInput.addEventListener('input', function (event) {
    let value = event.target.value.replace(/\D/g, ''); // Remove todos os não dígitos
    if (value.length > 3) {
        value = value.substring(0, 3) + '.' + value.substring(3);
    }
    if (value.length > 7) {
        value = value.substring(0, 7) + '.' + value.substring(7);
    }
    if (value.length > 11) {
        value = value.substring(0, 11) + '-' + value.substring(11);
    }
    event.target.value = value;
});

