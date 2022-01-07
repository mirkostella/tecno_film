<?php 

class Connessione{
    const HOST="localhost";
    const USERNAME="root";
    const PASSWORD="";
    const NOME_DATABASE="tec_web";

    public $conn=null;

    //apre la connessione restituendo true se é andata a buon fine altrimenti false
    public function apriConnessione(){
        $this->conn = new mysqli(Connessione::HOST,Connessione::USERNAME,Connessione::PASSWORD,Connessione::NOME_DATABASE);
        if(!$this->conn->connect_errno)
            return true;
        else
            return false;
    }
    //esegue la stringa di query che viene passata e restituisce un array associativo se ha prodotto un risultato altrimenti false
    //NB la connessine deve essere aperta
    public function interrogaDB($stringaQuery){

        $risultatoQuery=$this->conn->query($stringaQuery);

        $ris=array();

        if($risultatoQuery && ($risultatoQuery->num_rows>0)){
            while($row=$risultatoQuery->fetch_array(MYSQLI_ASSOC)){
                array_push($ris,$row);
            }
            
            $risultatoQuery->close();
            return $ris;
        }
        else
            return false;
    }
        
    //chiude la connessione     
    public function chiudiConnessione(){
        if($this->conn)
        $this->conn->close();
    }
    //esegue la stringa di query che viene passata
    public function eseguiQuery($query){
        $this->conn->query($query);
    }
}
?>