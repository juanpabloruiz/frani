function buscar() {
    var pattern = document.getElementById('buscar').value;
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
    document.getElementById('buscar').addEventListener('input', buscar, false);
}, false);