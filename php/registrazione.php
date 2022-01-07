<?php

	require_once('sessione.php');
	require_once('connessione.php');
	require_once('struttura.php');
	require_once('reg_check.php');
	require_once('upload_img.php');

	$pagina=file_get_contents("../html/registrazione.html");
	$struttura=new Struttura();
	$struttura->aggiungiHeader($pagina);
	$struttura->aggiungiAccount($pagina);
	$inAttivo=file_get_contents("../componenti/menu.html");
	$attivo=file_get_contents("../componenti/menu.html");
    $struttura->aggiungiMenu($pagina,$inAttivo,$attivo);
	$nome='';
	$cognome='';
	$data_nascita='';
	$sesso='';
	$email='';
	$username='';
	$psw='';
	$conf_psw='';
	$id_foto=NULL;
	$file_path='';
	$no_error=true;
	$upload_result=NULL;
	
	if(isset($_POST['invia'])){
		if(isset($_POST['nome'])){
			$nome=test_input($_POST['nome']);
		}
		if(isset($_POST['cognome'])){
			$cognome=test_input($_POST['cognome']);
		}
		if(isset($_POST['data'])){
			$data_nascita=test_input($_POST['data']);
		}
		if(isset($_POST['sesso'])){
			$sesso=test_input($_POST['sesso']);
		}
		if(isset($_POST['email'])){
			$email=test_input($_POST['email']);
		}
		if(isset($_POST['username'])){
			$username=test_input($_POST['username']);
		}
		if(isset($_POST['password'])){
			$psw=test_input($_POST['password']);
		}
		if(isset($_POST['confPassword'])){
			$conf_psw=test_input($_POST['confPassword']);
		}	
	
		$connessione=new Connessione();
		if(!$connessione->apriConnessione()){
			$pagina=str_replace('%error_conn%', "<div class=\"msg_box error_box\">Errore di connessione al database</div>", $pagina);
			$no_error=false;
		}
		else
			$pagina=str_replace('%error_conn%', '', $pagina);
		//controlli input

		if(!check_nome($nome)){
			$pagina=str_replace('%error_nome%', "<div class=\"msg_box error_box\">Il nome deve essere lungo almeno 2 caratteri ed essere composto solo da caratteri alfabetici.</div>", $pagina);
			$no_error=false;
		}
		else
			$pagina=str_replace('%error_nome%', '', $pagina);

	
		if(!check_nome($cognome)){
			$pagina=str_replace('%error_cognome%', "<div class=\"msg_box error_box\">Il cognome deve essere lungo almeno 2 caratteri ed essere composto solo da caratteri alfabetici.</div>", $pagina);
			$no_error=false;
		}
		else
			$pagina=str_replace('%error_cognome%', '', $pagina);	
		$date = new DateTime($data_nascita);
		$now = new DateTime();
		$delta = $now->diff($date);
		if($delta->y<18){
			$pagina=str_replace('%error_data%', "<div class=\"msg_box error_box\">L'età minima per poter utilizzare questo sito è 18 anni.</div>", $pagina);
			$no_error=false;
		}
		else
			$pagina=str_replace('%error_data%', '', $pagina);

		if(!check_email($email)){
			$pagina=str_replace('%error_email%', "<div class=\"msg_box error_box\">L'email inserita non è valida.</div>", $pagina);
			$no_error=false;
		}
		else
			$pagina=str_replace('%error_email%', '', $pagina);
		$query_email="SELECT * FROM utente WHERE BINARY email='".$email."'"; //CASE SENSITIVE??
		$query_user ="SELECT * FROM utente WHERE BINARY username='".$username."'"; //CASE SENSITIVE??

		if($connessione->interrogaDB($query_email)){
			$pagina=str_replace('%error_email_usata%', "<div class=\"msg_box error_box\">L'email inserita è già in uso.</div>", $pagina);
			$no_error=false;
		}
		else
			$pagina=str_replace('%error_email_usata%', '', $pagina);


		if($connessione->interrogaDB($query_user)){
			$pagina=str_replace('%error_username%', "<div class=\"msg_box error_box\">Questo username è già in uso. Scegline un altro</div>", $pagina);
			$no_error=false;
		}
		else
			$pagina=str_replace('%error_username%', '', $pagina);


		if($psw!=$conf_psw){
			$pagina=str_replace('%error_confPwd%', "<div class=\"msg_box error_box\">Password e Conferma Password non coincidono.</div>", $pagina);
			$no_error=false;
		}
		else
			$pagina=str_replace('%error_confPwd%', '', $pagina);

		if(!check_password($psw)){
			$pagina=str_replace('%error_pwd%', "<div class=\"msg_box error_box\">La password deve essere lunga almeno 8 caratteri, contenere almeno una lettera maiuscola, una minuscola e un numero.</div>", $pagina);
			$no_error=false;
		}
		else
			$pagina=str_replace('%error_pwd%', '', $pagina);
		//se gli input vanno bene, carico l'immagine profilo
		if($no_error){
			$gestisci_img = new gestione_img();

			if(isset($_FILES['immagineProfilo']) && is_uploaded_file($_FILES['immagineProfilo']['tmp_name'])){
				$upload_result=$gestisci_img->uploadImg("Utenti/", "immagineProfilo");
				$pagina = str_replace('%error_foto%', $upload_result['error'], $pagina);
				if($upload_result['error']==''){
					$file_path=$upload_result['path'];
				}
				else{
					$no_error=false;
				}
			}

			if(($file_path !== "../img/Utenti/") && ($upload_result['error'] =='')){
				$insert_Foto="INSERT INTO foto_utente(ID, path, descrizione) VALUES (NULL, '$file_path', NULL)";
				$connessione->eseguiQuery($insert_Foto);
				$check_insert="SELECT * FROM foto_utente WHERE path='".$file_path."'";
				$query_result=$connessione->conn->query($check_insert);
				$row=$query_result->fetch_array(MYSQLI_ASSOC);
				$id_foto=$row['ID'];
			}
		}

		//se l'immagine va bene, inserisco tutto nel db

		if($no_error){
			if($id_foto != NULL)
			{
				$insert_Utente_Foto="INSERT INTO utente(username, password, email, nome, cognome, data_nascita, sesso, ID_foto) VALUES (\"$username\", \"$psw\", \"$email\", \"$nome\", \"$cognome\", \"$data_nascita\", \"$sesso\", \"$id_foto\")";
				$connessione->eseguiQuery($insert_Utente_Foto);
			}
			else{
				$insert_Utente="INSERT INTO utente(username, password, email, nome, cognome, data_nascita, sesso) VALUES (\"$username\", \"$psw\", \"$email\", \"$nome\", \"$cognome\", \"$data_nascita\", \"$sesso\")";
				$connessione->eseguiQuery($insert_Utente);
			}

			//controllo che l'inserimento sia andato a buon fine 
			$query_check="SELECT * FROM utente WHERE email = '".$email."'";

			if(!$connessione->interrogaDB($query_check)){
				$pagina = str_replace('%error_reg%', "<div class=\"msg_box error_box\">Errore nell'inserimento dei dati</div>", $pagina);
				$connessione->chiudiConnessione();	
			}
			else{
				$pagina = str_replace('%msg_reg%', "<div class=\"msg_box success_box\">Registrazione avvenuta! Verrai indirizzato al login</div>", $pagina);
				$connessione->chiudiConnessione();
				header("refresh: 10; url= login.php");
			}		
		}

	}
	$pagina=str_replace('%error_conn%', '', $pagina);
	$pagina=str_replace('%error_nome%', '', $pagina);
	$pagina=str_replace('%error_cognome%', '', $pagina);
	$pagina=str_replace('%error_data%', '', $pagina);
	$pagina=str_replace('%error_email%', '', $pagina);
	$pagina=str_replace('%error_email_usata%', '', $pagina);
	$pagina=str_replace('%error_username%', '', $pagina);
	$pagina=str_replace('%error_pwd%', '', $pagina);
	$pagina=str_replace('%error_confPwd%', '', $pagina);
	$pagina=str_replace('%error_foto%', '', $pagina);
	$pagina=str_replace('%error_reg%', '', $pagina);
	$pagina=str_replace('%msg_reg%', '', $pagina);
	echo $pagina;
?>

