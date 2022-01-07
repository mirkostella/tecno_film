<?php
     function creaStelle($numeroStelle){
        $stelle="";
        for($i=$numeroStelle;$i>0;$i--){
            $stelle=$stelle."<span class=\"stella\"></span>";
        }
        $stelleVuote=5-$numeroStelle;
        for($i=$stelleVuote;$i>0;$i--){
            $stelle=$stelle."<span class=\"stella vuota\"></span>";
        }
        return $stelle;
    }
?>