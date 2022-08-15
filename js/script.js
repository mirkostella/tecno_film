//################################ CONTROLLI ##################################
//ritorna l'array con l'email trovata altrimenti null
function controlloEmail(email){
    return new RegExp(/^(([^{}<>()\[\]\.,;:\s@\"\'\+#&\*$]+(\.[^{}<>()\[\]\.,;:\s@\"\'\+#&\*$]+)*))@(([^{}<>()[\]\.,;:\s@\"\'\+#&\*$]+\.)+(it|com|net|org))$/).exec(email);
}
//ritorna vero se la password é valida altrimenti falso (almeno 8 caratteri tra cui una cifra,una lettera minuscola e una lettera maiuscola)
function controlloPassword(password){
    return new RegExp(/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{8,}$/).test(password);
}
function controlloEmailAdmin(email){
    if(email=="admin")
        return true;
    return new RegExp(/^(([^{}<>()\[\]\.,;:\s@\"\'\+#&\*$]+(\.[^{}<>()\[\]\.,;:\s@\"\'\+#&\*$]+)*))@(([^{}<>()[\]\.,;:\s@\"\'\+#&\*$]+\.)+(it|com|net|org))$/).exec(email);
}
function controlloPasswordAdmin(password){
    if(password=="admin")
        return true;
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
function controlloLunghezzaStringa(stringa,lung=2){
    if(stringa.length>lung)
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

//dim viene passata in MB
function controlloDimensioneImmagine(dim){
    var dimMB=1048576*5;
    if(dim>dimMB)
        return false;
    return true;
}
function controlloFormatoImmagine(nome){
    var estensioniSupp=["png","jpg","jpeg"]
    var estensione=nome.split(".").pop();
    if(!estensioniSupp.includes(estensione))
        return false;
    return true;  
}
function controlloVuoto(valore){
    if(valore=="")
        return false;
    return true;
}
//elementoFile e' l'input di tipo file
function controlloImmagineProfilo(elementoFile){
    var ok=true;
    var immagineProfilo=elementoFile.files[0];
    console.log(immagineProfilo);
    console.log(elementoFile);
    eliminaMessaggiSuccessivi(elementoFile);
    if(immagineProfilo){
        //messaggio errore formato immagine
        if(!controlloFormatoImmagine(immagineProfilo.name)){
            mostraMessaggio(elementoFile,"Formato immagine non valido! I formati accettati sono png,jpeg,jpg");
            ok=false;
        }
        //messaggio errore dimensione immagine
        if(!controlloDimensioneImmagine(immagineProfilo.size)){
            mostraMessaggio(elementoFile,"Dimensione immagine non valida!La dimensione deve essere inferiore a 5MB");
            ok=false;
        }
    }
    else{     
        mostraMessaggio(elementoFile,"Immagine profilo mancante");
        ok=false;
    }  
    return ok;        

}
function controlloLunghezzaCampo(campo,lung=2){
    eliminaMessaggiSuccessivi(campo);
    if(!controlloLunghezzaStringa(campo.value,lung)){
        mostraMessaggio(campo,"Il campo deve essere lungo almeno "+lung+" caratteri");
    }
}
//mostra il messaggio di errore se nessuna voce e' selezionata
//elemento contiene l'elemento che precede il messaggio di errore,nodeListCheck contiene le checkbox da controllare
function controlloCheckboxSelezionata(elemento,nodeListCheck){
    var ok=true;
    var generiSelezionati=new Array();
    for(var genere of nodeListCheck){
        if(genere.checked){
            generiSelezionati.push(genere);
        }
    }
    console.log(generiSelezionati);
    if(generiSelezionati.length==0){
        mostraMessaggio(elemento,"Selezionare almeno un genere");   
        ok=false;
    }        
    return ok;
    }


//################################ UTILITA' ##################################
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
function anteprimaImmagine(sourceElement, previewElement) {
    if(sourceElement.files && sourceElement.files[0]) {
        var reader = new FileReader(); 
        reader.onload = function(ee) {
            previewElement.src = ee.target.result;
        }
        reader.readAsDataURL(sourceElement.files[0]);
    }
}
function disabilitaDateSuccessive(campo,data){
    //formato classe max (anno-mese-giorno)
    var giorno=data.getDate();
    var mese=data.getMonth()+1;
    var anno=data.getFullYear();
    if(giorno<10)
        giorno="0"+giorno;
    if(mese<10)
        mese="0"+mese;
    var dataStringa=anno+"-"+mese+"-"+giorno;
    campo.max=dataStringa;
}
//################################ PAGINA DI LOGIN UTENTE ##################################
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
        btn_invio.addEventListener("click",e => {
            if(!controlloPresenzaErr(controlliLogin))
                e.preventDefault();
                mostraMessaggio(e.target,"Sono presenti dei campi non validi");
        })
        var elePassword=document.getElementById("password");
        var btn_mostra=document.getElementById("mostraPassword");
        btn_mostra.addEventListener("click",function(){mostraNascondiPassword(elePassword);});
    }
}


//################################ PAGINA DI REGISTRAZIONE ##################################
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
        //controllo immagine profilo (quando il valore dell'input file cambia)
        //array contenente il file selezionato come oggetto
        var immagineProfiloHTML=document.getElementById("immagineProfilo");
        var anteprimaImmagineHTML=document.getElementById("anteprima");
        immagineProfiloHTML.addEventListener("change",e=>{
            if(controlloImmagineProfilo(e.target))
                anteprimaImmagine(immagineProfiloHTML,anteprimaImmagineHTML);
            else
                anteprimaImmagineHTML.src ="../img/img_componenti/profilo.jpg";
            }
        );
        var btn_invio=document.getElementById("invia");
        btn_invio.addEventListener("click",function(e){
            eliminaMessaggiSuccessivi(e.target);
            if(!controlloPresenzaErr(controlliRegistrazione) || !controlloImmagineProfilo(immagineProfiloHTML)){
                e.preventDefault();
                mostraMessaggio(e.target,"Sono presenti dei campi non validi");
            }
        })
    }
}
//################################ PAGINA DI LOGIN AMMINISTRATORE ##################################
function init_amministratore_login(){
    if(document.getElementById("loginAdmin")){
        var controlliAmministratoreLogin={};   
        controlliAmministratoreLogin['email']=[[controlloEmailAdmin,"L'email inserita non é valida<br>Sono accettate email con domini .it .com .net .org"]];
        controlliAmministratoreLogin['password']=[[controlloPasswordAdmin,"La password inserita non é valida"]];
        //per ogni campo aggiungo il controllo che viene fatto quando perde il focus (l'id del campo é la chiave nell'array controlli)
        aggiungiControlliFocusOut(controlliAmministratoreLogin);
        //controllo che quando viene premuto il pulsante di invio non ci siano errori.. se ci sono errori non invio il form
        var btn_invio=document.getElementById("invia");
        btn_invio.addEventListener("click",e => {
            eliminaMessaggiSuccessivi(e.target);
            if(!controlloPresenzaErr(controlliAmministratoreLogin)){
                e.preventDefault();
                mostraMessaggio(e.target,"Sono presenti dei campi non validi");
            }
        })
    }
}    

//################################ PAGINA DI INSERIMENTO FILM AMMINISTRATORE ##################################
function init_amministratore_ins_film(){
    if(document.getElementById("formFilm")){  
        var titolo=document.getElementById("titolo");
        var alt=document.getElementById("altImg");
        var generiHTML=document.getElementById("generi");
        //ricavo la nodeList contenente i generi (attenzione nodeList e' una raccolta statica)
        var generi=document.getElementsByName("generi[]");
        console.log(generi);
        var dataHTML=document.getElementById("dataUscita");
        var pAcquisto=document.getElementById("prezzoAcquisto");
        var pNoleggio=document.getElementById("prezzoNoleggio");
        var trama=document.getElementById("trama");
        var btn_invio=document.getElementById("invio");
        pAcquisto.value=0;
        pNoleggio.value=0;
        //controlli
        titolo.addEventListener("focusout",e=>{controlloLunghezzaCampo(e.target)});
        alt.addEventListener("focusout",e=>{controlloLunghezzaCampo(e.target,15)});
        var dataAttuale=new Date();
        disabilitaDateSuccessive(dataHTML,dataAttuale);
        pAcquisto.addEventListener("focusout",e=>{
            eliminaMessaggiSuccessivi(e.target);
            eliminaMessaggiSuccessivi(pNoleggio);
            if(e.target.value<=pNoleggio.value){
                mostraMessaggio(e.target,"Il prezzo di acquisto non puo' essere minore o uguale del prezzo di noleggio");
            }
        });
        pNoleggio.addEventListener("focusout",e=>{
            eliminaMessaggiSuccessivi(e.target); 
            eliminaMessaggiSuccessivi(pAcquisto);
            if(e.target.value>=pAcquisto.value){
                mostraMessaggio(e.target,"Il prezzo di noleggio non puo' essere maggiore o uguale del prezzo di acquisto");
            }
        });
        trama.addEventListener("focusout",e=>{controlloLunghezzaCampo(e.target,50)});
        btn_invio.addEventListener("click",e=>{
            var ok=true;
            eliminaMessaggiSuccessivi(generiHTML);
            eliminaMessaggiSuccessivi(btn_invio);
            if(!controlloCheckboxSelezionata(generiHTML,generi))
                ok=false;

            if(!ok){
                e.preventDefault();
                mostraMessaggio(e.target,"Sono presenti dei campi non validi");  
            }
           
        })
             
    }
}

//################################ AL CARICAMENTO DELLA PAGINA GENERICA ##################################
window.onload=function(){   
    init_login();
    init_registrazione();
    init_amministratore_login();
    init_amministratore_ins_film();
}