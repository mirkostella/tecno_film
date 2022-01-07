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
	$struttura->aggiungiMenu($pagina,"","");
	
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
	
	$error='';
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
			$error=$error."<div class=\"msg_error_box\">Errore di connessione al database</div>";
			$no_error=false;
		}

		//controlli input

		if(!check_nome($nome)){
			$error=$error."<div class=\"msg_error_box\">Il nome deve essere lungo almeno 2 caratteri ed essere composto solo da caratteri alfabetici.</div>";
			$no_error=false;
		}
		if(!check_nome($cognome)){
			$error=$error."<div class=\"msg_error_box\">Il nome deve essere lungo almeno 2 caratteri ed essere composto solo da caratteri alfabetici.</div>";
			$no_error=false;
		}


		$date = new DateTime($data_nascita);
		$now = new DateTime();
		$delta = $now->diff($date);
		if($delta->y<18){
			$error=$error."<div class=\"msg_error_box\">L'età minima per poter utilizzare questo sito è 18 anni.</div>";
			$no_error=false;
		}

		if(!check_email($email)){
			$error=$error."<div class=\"msg_error_box\">L'email inserita non è valida.</div>";
			$no_error=false;
		}

		$query_email="SELECT * FROM utente WHERE BINARY email='".$email."'"; //CASE SENSITIVE??
		$query_user ="SELECT * FROM utente WHERE BINARY username='".$username."'"; //CASE SENSITIVE??

		if($connessione->interrogaDB($query_email)){
			$error=$error."<div class=\"msg_error_box\">Questa email è già in uso";
			$no_error=false;
		}

		if($connessione->interrogaDB($query_user)){
			$error=$error."<div class=\"msg_error_box\">Questo username è già in uso. Scegline un altro";
			$no_error=false;
		}

		if($psw!=$conf_psw){
			$error=$error."<div class=\"msg_error_box\">Password e Conferma Password non coincidono.</div>";
			$no_error=false;
		}
		if(!check_password($psw)){
			$error=$error."<div class=\"msg_error_box\">La password deve essere lunga almeno 8 caratteri, contenere almeno una lettera maiuscola, una minuscola e un numero.</div>";
			$no_error=false;
		}

		//se gli input vanno bene, carico l'immagine profilo

		if($no_error){
			$gestisci_img = new gestione_img();

			if(isset($_FILES['immagineProfilo']) && is_uploaded_file($_FILES['immagineProfilo']['tmp_name'])){
				$upload_result=$gestisci_img->uploadImg("Utenti/", "immagineProfilo");
				if($upload_result['error']==''){
					$file_path=$upload_result['path'];
				}
				else{
					$error=$error.$upload_result['error'];
					$no_error=false;
				}
			}

			if(($file_path !== "../img/Utenti/") && ($upload_result['error'] =='')){
				$insert_Foto="INSERT INTO foto_utente(ID, path, descrizione, segnalazioni) VALUES (NULL, '$file_path', NULL, NULL)";
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
				$error=$error."<div class=\"msg_error_box\">Errore nell'inserimento dei dati</div>";
				$connessione->chiudiConnessione();	
			}
			else{
				$connessione->chiudiConnessione();
				header('location: login.php');
			}		
		}

	}
	$pagina=str_replace('%error%', $error, $pagina);
	echo $pagina;
	
?>

