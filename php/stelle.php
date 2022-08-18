<?php
     function creaStelle($numeroStelle){
        $stelle="";
        for($i=$numeroStelle;$i>0;$i--){
            $stelle=$stelle.'<img src = "../img/img_componenti/stella.png" class="stella" alt="icona di una stella piena" aria-hidden="true">';
        }
        $stelleVuote=5-$numeroStelle;
        for($i=$stelleVuote;$i>0;$i--){
            $stelle=$stelle.'<img src = "../img/img_componenti/stella_vuota.png" class="stella" alt="icona di una stella vuota" aria-hidden="true">';
        }
        $stelle=$stelle."<span class=\"aiuti\">$numeroStelle stelle</span>";
        return $stelle;
    }
?>