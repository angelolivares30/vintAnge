document.getElementById('submitBtn').addEventListener('click', function(event) {
    var creditCardNumber = document.getElementById('creditCardNumber').value;
    var cvv = document.getElementById('cvv').value;

    // Regular expressions for validation
    var creditCardPattern = /^\d{16}$/;
    var cvvPattern = /^\d{3}$/;

    // Validate credit card number and CVV
    if (!creditCardPattern.test(creditCardNumber)) {
        alert('El número de tarjeta de crédito debe ser de 16 dígitos.');
        event.preventDefault(); // Prevent form submission
        return;
    }

    if (!cvvPattern.test(cvv)) {
        alert('El CVV debe ser de 3 dígitos.');
        event.preventDefault(); // Prevent form submission
        return;
    }
});