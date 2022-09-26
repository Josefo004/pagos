function validar_checks() {
    var checks = $('input[type=checkbox]');
    var band = false;
    for (i=0; i<checks.length; i++) {
        if (checks[i].checked) {
            band = true;
            break;
        }
    }
    if (!band) {
        alert('Debe seleccionar al menos un registro');
        return false;
    } else {
        return true;
    }
}

function marcar_checks(frm) {
    var checks = $('input[type=checkbox]');
    for (i=0; i<checks.length; i++) {
        checks[i].checked = checks[0].checked;
    }
}

var win = null;
function ventana(pagina,w, h, p){
    LeftPosition = (screen.width) ? (screen.width-w)/2 : 0;
    TopPosition = (screen.height) ? (screen.height-h)/2 : 0;
    estilo ='height='+h+',width='+w+',top='+TopPosition+',left='+LeftPosition+',menubar=no,scrollbars=yes,toolbar=no,location=no,directories=no,resizable=no';
    win = window.open(pagina,'ventana',estilo);
    if (p == true) {
        win.print();
    }
}

/**
 * Función para acceder a una URL seleccionando una opción
 */
function action (url, name, id, message) {
    if (!id) {
        alert('Debe seleccionar un registro');
    } else {
        if ((name == 'eliminar') || (name == 'borrar_obs') || (name == 'eliminar_estado')) {
            if (confirm(message)) {
                $('#frm_borrar').attr('action', url + '/' + name + '/' + id);
                $('#frm_borrar').submit();
            }
        } else if (name == 'imprimir') {
            ventana (url + '/' + name + '/' + id, 800, 600, true);
        } else {
            window.location = url + '/' + name + '/' + id;
        }
    }
} 

var tr_anterior = null;
var id_registro = null;
var id_obs = null;

/**
 * Función para seleccionar un registro
 */
function seleccionar(tr, id){
    if (tr_anterior){
       $(tr_anterior).removeClass('seleccionado');
    }
    $(tr).addClass('seleccionado');
    tr_anterior = tr;
    id_registro = id;
}

/**
 * Función para seleccionar un registro
 */
function seleccionar2(tr, id, obs){
    if (tr_anterior){
       $(tr_anterior).removeClass('seleccionado');
    }
    $(tr).addClass('seleccionado');
    tr_anterior = tr;
    id_registro = id;
    id_obs = obs;
}
