<?php
    class gestione_img{
        
        public function uploadImg($directory, $input_name){

            $dir_upload = "../img/".$directory;
            $uploaded_file = $dir_upload . basename($_FILES[$input_name]["name"]);
            $upload_ok = true;
            $error ='';
            $imgFileType = strtolower(pathinfo($uploaded_file, PATHINFO_EXTENSION));

            //check file size
            if($_FILES[$input_name]["size"] > 4000000){
                $error = $error."<div class=\"msg_box error_box\">Il file è troppo grande, carica un file di dimensione minore di 4MB.</div>";
                $upload_ok = false;
            }

            //check formato file
            if($imgFileType != "jpg"  && $imgFileType != "png" && $imgFileType!= "jpeg"){
                $error = $error."<div class=\"msg_box error_box\">Il file deve avere una delle seguenti estensioni: JPG, PNG, JPEG.</div>";
                $upload_ok = false;
            }

            if($upload_ok){
                $temp = explode(".", $_FILES[$input_name]["name"]);
                $newfilename = round(microtime(true)) . "." . end($temp);
                $uploaded_file=$dir_upload.$newfilename;
                if(!move_uploaded_file($_FILES[$input_name]["tmp_name"], $uploaded_file)){
                    $error="<div class=\"msg_box error_box\">C'è stato un errore nel caricamento del file.</div>";
                }
            }

            $upload['error']=$error;
            $upload['path']=$uploaded_file;
            return $upload;
        }
        
        public function deleteImg($path){
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }
?>