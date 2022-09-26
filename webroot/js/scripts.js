var nav = window.Event ? true : false; //objeto en el que se definen los eventos del objeto window

function validar_num(evento){
    /* 	Las teclas tienen los siguientes códigos:
            * Enter = 13
            * '0' a '9' = 48 a 57
    */
    var key = nav ? evento.which : evento.keyCode; //cargamos en key el valor del código del caracter ingresado por teclado
    return (key <= 13 || (key >= 48 && key <= 57)); //devuelve el valor verdadero si la tecla presionada es un numero o un enter y falso si no lo es
}
function validar_text(evento){
    /* 	Las teclas tienen los siguientes códigos:
            * Espacio = 8
            * Enter = 13
            * '0' a '9' = 48 a 57
    */
    var key = nav ? evento.which : evento.keyCode; //cargamos en key el valor del código del caracter ingresado por teclado
    return (key <= 13 || key <= 32 || key>=192 || (key >= 65 && key <= 90) || (key >= 97 && key <= 122)); //devuelve el valor verdadero si la tecla presionada es una letra o un enter y falso si no lo es
}
function validar_text_num(evento){
    /* 	Las teclas tienen los siguientes códigos:
            * Espacio = 8
            * Enter = 13
            * '0' a '9' = 48 a 57
    */
    var key = nav ? evento.which : evento.keyCode; //cargamos en key el valor del código del caracter ingresado por teclado
    return (key <= 13 || key <= 32 || key>=192 || (key >= 65 && key <= 90) || (key >= 48 && key <= 57) || (key >= 97 && key <= 122)); //devuelve el valor verdadero si la tecla presionada es una letra o un enter y falso si no lo es
}