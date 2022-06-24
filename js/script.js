
//ritorna l'array con l'email trovata altrimenti null
function controlloEmail(email){
    return new RegExp(/^(([^{}<>()\[\]\.,;:\s@\"\'\+#&\*$]+(\.[^{}<>()\[\]\.,;:\s@\"\'\+#&\*$]+)*))@(([^{}<>()[\]\.,;:\s@\"\'\+#&\*$]+\.)+(it|com|net|org))$/).exec(email);
}
//ritorna vero se la password é valida altrimenti falso
function controlloPassword(password){
    return new RegExp(/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{8,}$/).test(password);
}


 
window.onload=function(){   
    alert("ciao");
}