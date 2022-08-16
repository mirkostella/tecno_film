<?php
    class GestioneImg{
        public $erroriImg;
        public $file_path=null;
        public $dimensioneFile;
        

        public function __construct(){
            $this->erroriImg=array(
                'errDim'=>'',
                'errFormato'=>'',
                'errCaricamentoFile'=>''
            );
        }

        public function getErroreDimensione(){
            return $this->erroriImg['errDim'];
        }
        public function getErroreFormato(){
            return $this->erroriImg['errFormato'];
        }
        public function getErroreCaricamento(){
            return $this->erroriImg['errCaricamentoFile'];
        }
        
        //se va a buon fine ritorna il path dell'immagine altrimenti false e in $erroriImg sono presenti le cause di fallimento
        public function caricaImmagine($directory, $input_name){

            $dir_upload = "../img/".$directory;
            $uploaded_file = $dir_upload . basename($_FILES[$input_name]["name"]);
            $imgFileType = strtolower(pathinfo($uploaded_file, PATHINFO_EXTENSION));
            $this->dimensioneFile=round($_FILES[$input_name]["size"]/(pow(2,20)),2);
            //check file size
            if($_FILES[$input_name]["size"] > 4000000)
                $this->erroriImg['errDim']="<div class=\"error_box\">Il file è troppo grande, carica un file di dimensione minore di 4MB. (dimensione del file: '".$this->dimensioneFile."'MB)</div>";
            
            //check formato file
            if($imgFileType != "jpg"  && $imgFileType != "png" && $imgFileType!= "jpeg")
                $this->erroriImg['errFormato']="<div class=\"error_box\">Il file deve avere una delle seguenti estensioni: JPG, PNG, JPEG.</div>";

            if($this->erroriImg['errDim']=='' && $this->erroriImg['errFormato']==''){
                $temp = explode(".", $_FILES[$input_name]["name"]);
                $newfilename = round(microtime(true)) . "." . end($temp);
                $uploaded_file=$dir_upload.$newfilename;
                if(move_uploaded_file($_FILES[$input_name]["tmp_name"], $uploaded_file)){
                    $this->file_path=$uploaded_file;
                    return $this->file_path;
                } 
                else
                    $this->erroriImg['errCaricamentoFile']="<div class=\"error_box\">C'è stato un errore nel caricamento del file.</div>";
            }   
            return false;
        }
        
        public function deleteImg($path){
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }
?>
