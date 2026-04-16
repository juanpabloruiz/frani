function buscar() {
    var input = document.getElementById('buscar');

    if (!input) {
        return;
    }

    var pattern = input.value;
    var solicitud = new XMLHttpRequest();
    var data = new FormData();
    var url = 'buscar.php';
    data.append("busqueda", pattern);
    solicitud.open('POST', url, true);
    solicitud.send(data);
    solicitud.addEventListener('load', function(e) {
        var cajadatos = document.getElementById('datos');
        cajadatos.innerHTML = e.target.responseText;
    }, false);
}
window.addEventListener('load', function() {
    var input = document.getElementById('buscar');

    if (input) {
        input.addEventListener('input', buscar, false);
    }
}, false);
