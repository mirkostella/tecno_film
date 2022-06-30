//CONTROLLI
//ritorna l'array con l'email trovata altrimenti null
function controlloEmail(email){
    return new RegExp(/^(([^{}<>()\[\]\.,;:\s@\"\'\+#&\*$]+(\.[^{}<>()\[\]\.,;:\s@\"\'\+#&\*$]+)*))@(([^{}<>()[\]\.,;:\s@\"\'\+#&\*$]+\.)+(it|com|net|org))$/).exec(email);
}
//ritorna vero se la password é valida altrimenti falso (almeno 8 caratteri tra cui una cifra,una lettera minuscola e una lettera maiuscola)
function controlloPassword(password){
    return new RegExp(/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{8,}$/).test(password);
}

function controlloPresenzaErr(controlli){
    var ok=true;
    for(controllo in controlli){
        var elemento=document.getElementById(controllo);
        eliminaMessaggiSuccessivi(elemento);
        var controlloCorrente = controlli[controllo]; 
        for(var i=0;i<controlloCorrente.length;i++){
            var esito=false;
            esito=controlloCorrente[i][0](elemento.value);
            if(!esito){
                ok=false;
                mostraMessaggio(elemento,controlloCorrente[i][1]);
            }
        }
    }
    return ok;
}
function controlloLunghezzaStringa(lunghezza,stringa){
    if(stringa.length>lunghezza)
        return false;
    else
        return true;
}
//ritorna true se la stringa é composta da solo lettere
function controlloSoloLettere(stringa){
    //rimuove gli spazi all'inizio e alla fine della stringa
    stringa=stringa.trim();
    return RegExp(stringa,/^[a-zA-Z]/).test();
}
function controlloDataSup(dataConfronto,dataDaConfrontare){
    if(dataConfronto>dataDaConfrontare)
        return false;
    else
        return true;
}
function controlloSesso(sesso){
    if(sesso=="M" | sesso=="F")
        return true;
    else
        return false;
}
function controlloCorrispondenzaPassword(password1,password2){
    if(password1==password2)
        return true;
    else
        return false;
}
function controlloDimensioneImmagine(){

}
function mostraNascondiPassword(elemento){
    if(elemento.type=="password")
        elemento.type="text";
    else
        elemento.type="password";
}
function creaDivMessaggio(messaggio){
    var nuovoDiv=document.createElement("div");
    nuovoDiv.classList.add("error_box");
    nuovoDiv.innerHTML=messaggio;
    return nuovoDiv;
}
function mostraMessaggio(elemento,messaggio){
    var nuovoDiv=creaDivMessaggio(messaggio); 
    elemento.after(nuovoDiv);
}
function eliminaMessaggiSuccessivi(elemento){
    var stop=false;
        while(!stop){
            var successivo=elemento.nextElementSibling;
            if(successivo && successivo.classList.contains("error_box"))
                successivo.remove(); 
            else
                stop=true;
        }
}
//campi é un array avente come chiavi gli id dei campi del form a cui aggiungere i controlli 
//e come valori i controlli da aggiungere e messaggi di errore
// campi: campo[id]->[controllo,messaggio]..[controllo,messaggio]
function aggiungiControlliFocusOut(campi){
    for(campo in campi){
        var elemento=document.getElementById(campo);
        elemento.addEventListener("focusout",function(e){
       
        //rimuovo i messaggi esistenti
        eliminaMessaggiSuccessivi(e.target);

        var campoEvento=campi[e.target.id];
        for(var i = 0; i < campoEvento.length; i++) {
            if(!campoEvento[i][0](e.target.value))
                //viene passato il target per inserire il div dopo il campo
                mostraMessaggio(e.target,campoEvento[i][1]); 
        }      
    });
    }
}

//funzione che controlla la presenza di errori e nel caso ci fossero errori stampa i relativi messaggi senza inviare il form
//restituisce true nel caso in cui la form sia stata inviata altrimenti false
function invioForm(elementoInvio,controlli){
    if(!controlloPresenzaErr(controlli)){
        e.preventDefault();
        eliminaMessaggiSuccessivi(elementoInvio);
        mostraMessaggio(elementoInvio,"Sono presenti dei campi non validi");
        return false;
    }
    else
        return true;

}

//PAGINA DI LOGIN UTENTE
function init_login(){
    //istruzioni da eseguire solo se mi trovo nella pagina di login
    if(document.getElementById("login")){
        var controlliLogin={};   
        controlliLogin['email']=[[controlloEmail,"L'email inserita non é valida<br>Sono accettate email con domini .it .com .net .org"]];
        controlliLogin['password']=[[controlloPassword,"La password inserita non é valida"]];
        //per ogni campo aggiungo il controllo che viene fatto quando perde il focus (l'id del campo é la chiave nell'array controlli)
        aggiungiControlliFocusOut(controlliLogin);
        //controllo che quando viene premuto il pulsante di invio non ci siano errori.. se ci sono errori non invio il form
        var btn_invio=document.getElementById("invia");
        btn_invio.addEventListener("click",function(e){
            invioForm(e.target,controlliLogin);
        })
        var elePassword=document.getElementById("password");
        var btn_mostra=document.getElementById("mostraPassword");
        btn_mostra.addEventListener("click",function(){mostraNascondiPassword(elePassword);});
    }
}

//PAGINA DI REGISTRAZIONE
function init_registrazione(){
    if(document.getElementById("registrazione")){
        var controlliRegistrazione={};
        controlliRegistrazione['nome']=[[]];
        controlliRegistrazione['cognome']=
        controlliRegistrazione['data']=
        controlliRegistrazione['sesso']=
        controlliRegistrazione['email']=
        controlliRegistrazione['username']=
        controlliRegistrazione['password']=
        controlliRegistrazione['confPassword']=
        controlliRegistrazione['immagineProfilo']=
        aggiungiControlliFocusOut(controlliRegistrazione);
        var btn_invio=document.getElementById("invia");
        btn_invio.addEventListener("click",function(e){
            invioForm(e.target,controlliRegistrazione);
        })
    }
}

window.onload=function(){   
    init_login();
    init_registrazione();

}