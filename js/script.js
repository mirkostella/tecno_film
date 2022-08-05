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
function controlloLunghezzaStringa(stringa){
    if(stringa.length>2)
        return true;
    else
        return false;
}
//ritorna true se la stringa é composta da solo lettere
function controlloSoloLettere(stringa){
    //rimuove gli spazi all'inizio e alla fine della stringa
    stringa=stringa.trim();
    return RegExp(stringa,/^[a-zA-Z]/).test();
}
function controlloMaggiorenne(data){
    var dataAttuale=new Date();
    var dataNascita=new Date(data);
    var eta=new Date(dataAttuale-dataNascita);
    return eta.getFullYear()>=1970+18;
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
function controlloFormatoImmagine(){

}
function controlloVuoto(valore){
    if(valore=="")
        return false;
    return true;
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
function invioForm(e,controlli){
    if(!controlloPresenzaErr(controlli)){
        e.preventDefault();
        eliminaMessaggiSuccessivi(e.target);
        mostraMessaggio(e.target,"Sono presenti dei campi non validi");
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
        console.log(controlliLogin);
        controlliLogin['email']=[[controlloEmail,"L'email inserita non é valida<br>Sono accettate email con domini .it .com .net .org"]];
        controlliLogin['password']=[[controlloPassword,"La password inserita non é valida"]];
        //per ogni campo aggiungo il controllo che viene fatto quando perde il focus (l'id del campo é la chiave nell'array controlli)
        aggiungiControlliFocusOut(controlliLogin);
        //controllo che quando viene premuto il pulsante di invio non ci siano errori.. se ci sono errori non invio il form
        var btn_invio=document.getElementById("invia");
        btn_invio.addEventListener("click",e => {
            invioForm(e,controlliLogin);
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
        controlliRegistrazione['nome']=[[controlloLunghezzaStringa,"Il nome deve essere lungo almeno 3 caratteri"]];
        controlliRegistrazione['cognome']=[[controlloLunghezzaStringa,"Il cognome deve essere lungo almeno 3 caratteri"]];
        controlliRegistrazione['data']=[[controlloMaggiorenne,"Bisogna avere almeno 18 anni per registrarsi"],[controlloVuoto,"Selezionare una data"]];
        controlliRegistrazione['sesso']=[[controlloSesso,"Selezionare il sesso"]];
        controlliRegistrazione['email']=[[controlloEmail,"L'email inserita non é valida<br>Sono accettate email con domini .it .com .net .org"]];
        controlliRegistrazione['username']=[[controlloLunghezzaStringa,"L'username deve essere lungo almeno 3 caratteri"]];
        controlliRegistrazione['password']=[[controlloPassword,"La password inserita non é valida"]];

        controlliRegistrazione['immagineProfilo']=[[controlloDimensioneImmagine,"La dimensione dell'immagine deve essere inferiore a "],
                                                  [controlloFormatoImmagine,"Formato immagine non valido.. I formati accettati sono "]];

        //controlli a piú parametri
        var password=document.getElementById('password');
        var confermaPassword=document.getElementById('confPassword');
        confermaPassword.addEventListener('focusout',e=>{
            //se le password non coincidono
            if(!controlloCorrispondenzaPassword(password.value,confermaPassword.value)){
                eliminaMessaggiSuccessivi(e.target);
                mostraMessaggio(e.target,"Le password non coincidono");
            }
            else
                eliminaMessaggiSuccessivi(e.target);

        });
        password.addEventListener('focusout',e=>{
            //se le password non coincidono
            if(!controlloCorrispondenzaPassword(password.value,confermaPassword.value)){
                eliminaMessaggiSuccessivi(confermaPassword);
                mostraMessaggio(confermaPassword,"Le password non coincidono");
            }
            else
                eliminaMessaggiSuccessivi(confermaPassword);
        });
        var btn_mostra=document.getElementById("mostraPassword");
        btn_mostra.addEventListener("click",function(){mostraNascondiPassword(password);});
        var btn_mostra_conferma=document.getElementById("mostraPasswordConferma");
        btn_mostra_conferma.addEventListener("click",function(){mostraNascondiPassword(confermaPassword);});



        aggiungiControlliFocusOut(controlliRegistrazione);
        var btn_invio=document.getElementById("invia");
        btn_invio.addEventListener("click",function(e){
            invioForm(e,controlliRegistrazione);
        })
    }
}

window.onload=function(){   
    init_login();
    init_registrazione();

}