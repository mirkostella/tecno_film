//################################ CONTROLLI ##################################
//ritorna l'array con l'email trovata altrimenti null
function controlloEmail(email){
    if(email=="user")
        return true;
    return new RegExp(/^(([^{}<>()\[\]\.,;:\s@\"\'\+#&\*$]+(\.[^{}<>()\[\]\.,;:\s@\"\'\+#&\*$]+)*))@(([^{}<>()[\]\.,;:\s@\"\'\+#&\*$]+\.)+(it|com|net|org))$/).exec(email);
}
//ritorna vero se la password é valida altrimenti falso (almeno 8 caratteri tra cui una cifra,una lettera minuscola e una lettera maiuscola)
function controlloPassword(password){
    if(password=="user")
        return true;
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
    if(stringa.length>=lung)
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
function controlloVuoto(elementoHTML,messaggio){
    eliminaMessaggiSuccessivi(elementoHTML);
    if(elementoHTML.value=="" || elementoHTML.value=="00:00"){
        mostraMessaggio(elementoHTML,messaggio);
        return false;
    }
    return true;
}
//elementoFile e' l'input di tipo file
function controlloImmagine(elementoFile,messaggio){
    var ok=true;
    var immagine=elementoFile.files[0];
    eliminaMessaggiSuccessivi(elementoFile);
    if(immagine){
        //messaggio errore formato immagine
        if(!controlloFormatoImmagine(immagine.name)){
            mostraMessaggio(elementoFile,"Formato immagine non valido! Il file deve avere uno dei seguenti formati: JPG, PNG, JPEG");
            ok=false;
        }
        //messaggio errore dimensione immagine
        if(!controlloDimensioneImmagine(immagine.size)){
            mostraMessaggio(elementoFile,"Il file è troppo grande, carica un file di dimensione minore di 5MB");
            ok=false;
        }
    }
    else{     
        mostraMessaggio(elementoFile,messaggio);
        ok=false;
    }  
    return ok;        

}
function controlloLunghezzaCampo(campo,lung=2){
    var ok=true;
    eliminaMessaggiSuccessivi(campo);
    if(!controlloLunghezzaStringa(campo.value,lung)){
        mostraMessaggio(campo,"Il campo deve essere lungo almeno "+lung+" caratteri");
        ok=false;
    }
    return ok;
}
//mostra il messaggio di errore se nessuna voce e' selezionata
function controlloCheckboxSelezionata(elementoCheck,nodeListCheck){
    var ok=true;
    eliminaMessaggiSuccessivi(elementoCheck);
    var generiSelezionati=new Array();
    for(var genere of nodeListCheck){
        if(genere.checked){
            generiSelezionati.push(genere);
        }
    }
    if(generiSelezionati.length==0){
        mostraMessaggio(elementoCheck,"Selezionare almeno un genere");   
        ok=false;
    }        
    return ok;
}
function controlloPrezzoAcquistoNoleggio(elementoAcquisto,elementoNoleggio){
    var ok=true;
    eliminaMessaggiSuccessivi(elementoAcquisto); 
    eliminaMessaggiSuccessivi(elementoNoleggio);
    var pAcquisto=elementoAcquisto.valueAsNumber;
    var pNoleggio=elementoNoleggio.valueAsNumber;
    if(pAcquisto<=0){
        mostraMessaggio(elementoAcquisto,"Inserire un valore maggiore di 0");
        ok=false;
    }
    if(pNoleggio<=0){
        mostraMessaggio(elementoNoleggio,"Inserire un valore maggiore di 0");
        ok=false;
    }
    if(pAcquisto>0 && pNoleggio>0){
        if(elementoNoleggio.valueAsNumber>=elementoAcquisto.valueAsNumber){
            mostraMessaggio(elementoNoleggio,"Il prezzo di noleggio deve essere minore del prezzo di acquisto");
            ok=false;
        }
        if(elementoAcquisto.valueAsNumber<=elementoNoleggio.valueAsNumber){
            mostraMessaggio(elementoAcquisto,"Il prezzo di acquisto deve essere maggiore del prezzo di noleggio");
            ok=false;
        }
    }
    return ok;
}

//################################ UTILITA' ##################################
//inserisce il pulsante dopo elemento
function aggiungiMostra(elemento,id){
    var vediP = document.createElement("input");
        vediP.type = "button";
        vediP.id=id;
        vediP.value = "Mostra password";
        vediP.classList = "btnVediPassword";
        elemento.insertAdjacentElement("afterend", vediP);
        return vediP;
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
            eliminaMessaggiSuccessivi(e.target);
            if(!controlloPresenzaErr(controlliLogin)){
                e.preventDefault();
                mostraMessaggio(e.target,"Le credenziali non sono corrette");
            }
        })
        var elePassword=document.getElementById("password");
        aggiungiMostra(elePassword,"mostraPassword").addEventListener("click",function(){mostraNascondiPassword(elePassword);});
    }
}


//################################ PAGINA DI REGISTRAZIONE ##################################
function init_registrazione(){
    if(document.getElementById("registrazione")){
        var controlliRegistrazione={};
        controlliRegistrazione['nome']=[[controlloLunghezzaStringa,"Il nome deve essere lungo almeno 2 caratteri ed essere composto solo da caratteri alfanumerici"]];
        controlliRegistrazione['cognome']=[[controlloLunghezzaStringa,"Il cognome deve essere lungo almeno 2 caratteri ed essere composto solo da caratteri alfanumerici"]];
        controlliRegistrazione['data']=[[controlloMaggiorenne,"L'età minima per poter registrarsi al sito è 18 anni."]];
        controlliRegistrazione['sesso']=[[controlloSesso,"Selezionare il sesso"]];
        controlliRegistrazione['email']=[[controlloEmail,"L'email inserita non é valida<br>Sono accettate email con domini .it .com .net .org"]];
        controlliRegistrazione['username']=[[controlloLunghezzaStringa,"L'username deve essere lungo almeno 2 caratteri"]];
        controlliRegistrazione['password']=[[controlloPassword,"La password deve essere lunga almeno 8 caratteri, contenere almeno una lettera maiuscola, una minuscola e un numero."]];

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

        aggiungiMostra(password,"mostraPassword").addEventListener("click",function(){mostraNascondiPassword(password);});

        aggiungiMostra(confermaPassword,"mostraPasswordConferma").addEventListener("click",function(){mostraNascondiPassword(confermaPassword);});

        aggiungiControlliFocusOut(controlliRegistrazione);
        //controllo immagine profilo (quando il valore dell'input file cambia)
        //array contenente il file selezionato come oggetto
        var immagineProfiloHTML=document.getElementById("immagineProfilo");
        var anteprimaImmagineHTML=document.getElementById("anteprima");
        immagineProfiloHTML.addEventListener("change",e=>{
            if(controlloImmagine(e.target,"Immagine profilo mancante"))
                anteprimaImmagine(immagineProfiloHTML,anteprimaImmagineHTML);
            else
                anteprimaImmagineHTML.src ="../img/img_componenti/profilo.jpg";
            }
        );
        var btn_invio=document.getElementById("invia");
        btn_invio.addEventListener("click",function(e){
            eliminaMessaggiSuccessivi(e.target);
            if(!controlloPresenzaErr(controlliRegistrazione) | !controlloImmagine(immagineProfiloHTML,"Immagine profilo mancante")){
                e.preventDefault();
                mostraMessaggio(e.target,"Errore nell'inserimento dei dati: sono presenti dei campi non validi");
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
                mostraMessaggio(e.target,"Le credenziali non sono corrette");
            }
        })
        
        var elePassword=document.getElementById("password");
        aggiungiMostra(elePassword,"mostraPassword").addEventListener("click",function(){mostraNascondiPassword(elePassword);});
    }
}    

//################################ PAGINA DI INSERIMENTO FILM AMMINISTRATORE ##################################
function init_amministratore_ins_film(){
    if(document.getElementById("formFilm")){  
        var titolo=document.getElementById("titolo");
        var copertina=document.getElementById("copertinaFilm");
        var alt=document.getElementById("altImg");
        var generiHTML=document.getElementById("generi");
        //ricavo la nodeList contenente i generi (attenzione nodeList e' una raccolta statica)
        var generi=document.getElementsByName("generi[]");
        var dataHTML=document.getElementById("dataUscita");
        var durataHTML=document.getElementById("durata");
        var pAcquisto=document.getElementById("prezzoAcquisto");
        var pNoleggio=document.getElementById("prezzoNoleggio");
        var trama=document.getElementById("trama");
        var btn_invio=document.getElementById("invio");
        //controlli
        titolo.addEventListener("focusout",e=>{controlloLunghezzaCampo(e.target)});
        copertina.addEventListener("change",e=>{
            controlloImmagine(e.target,"Immagine di copertina mancante");
        });
        alt.addEventListener("focusout",e=>{controlloLunghezzaCampo(e.target,15)});
        for(var genere of generi)
            genere.addEventListener("change",e=>controlloCheckboxSelezionata(generiHTML,generi));
        var dataAttuale=new Date();
        disabilitaDateSuccessive(dataHTML,dataAttuale);
        dataHTML.addEventListener("focusout",e=>{
            if(controlloVuoto(dataHTML,"Selezionare una data"))
                eliminaMessaggiSuccessivi(dataHTML);});
        durataHTML.addEventListener("focusout",e=>{
            if(controlloVuoto(durataHTML,"La durata deve essere maggiore di 00:00"))
                eliminaMessaggiSuccessivi(durataHTML);
        });
        pAcquisto.addEventListener("focusout",e=>{
            controlloPrezzoAcquistoNoleggio(pAcquisto,pNoleggio);
        });
        pNoleggio.addEventListener("focusout",e=>{
            controlloPrezzoAcquistoNoleggio(pAcquisto,pNoleggio);
        });
        trama.addEventListener("focusout",e=>{controlloLunghezzaCampo(e.target,50)});
        btn_invio.addEventListener("click",e=>{
            eliminaMessaggiSuccessivi(btn_invio);
            var ok=true;
            if(!controlloCheckboxSelezionata(generiHTML,generi) | !controlloLunghezzaCampo(titolo) | !controlloLunghezzaCampo(alt,15) | 
            !controlloPrezzoAcquistoNoleggio(pAcquisto,pNoleggio) | !controlloLunghezzaCampo(trama,50) | 
                !controlloImmagine(copertina,"Immagine di copertina mancante") | !controlloVuoto(dataHTML,"Selezionare una data") | !controlloVuoto(durataHTML,"La durata deve essere maggiore di 00:00"))      
                ok=false;
                
                if(!ok){
                e.preventDefault();
                mostraMessaggio(e.target,"Inserimento film fallito: sono presenti dei campi non validi");  
            }
           
        })
             
    }
}
function init_pagina_film(){
    if(document.getElementById("recensioni")){  
        var recensioneHTML=document.getElementById("testoRecensione");
        recensioneHTML.addEventListener("focusout",e=>{
            controlloLunghezzaCampo(e.target, 3);
            e.preventDefault();
        });
    }
}

function torna_su_init(){
    console.log(window.pageYOffset);
    console.log(window.screen.availWidth);
    var btn = document.getElementById("scrollBtn"); 

    if(window.pageYOffset > 50 && window.screen.availWidth <= 768) {
        btn.classList.remove("nascosto"); 
        console.log("rimosso nascosto");
    } else {
        if(!btn.classList.contains("nascosto")){
            btn.classList.add("nascosto"); 
            console.log("aggiunto nascosto");
        }
    }
}

//################################ AL CARICAMENTO DELLA PAGINA GENERICA ##################################
window.onload=function(){  
    //pagine utente 
    init_login();
    init_registrazione();
    init_pagina_film();
    //pagine amministratore
    init_amministratore_login();
    init_amministratore_ins_film();
    window.onscroll = torna_su_init;
}
