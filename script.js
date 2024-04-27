function validateForm() {
    var date1 = document.getElementById('date1').value;
    var date2 = document.getElementById('date2').value;
    if (date1 == "" || date2 == "") {
        alert("Favor de llenar ambos campos en formato 'dd-mm-aaaa'.");
        return false;
    }

    if (date1 === date2) {
        alert('The two dates cannot be the same.');
        return false;
    }
}

function resetForm() {
    document.getElementById('date1').value = '';
    document.getElementById('date2').value = '';
    document.querySelectorAll('.result-row').forEach(function(element) {
        element.innerHTML = '';
    });
}


window.onload = function() {
    document.getElementById('date1').focus();
};