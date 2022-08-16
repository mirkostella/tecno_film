<?php
     function creaStelle($numeroStelle){
        $stelle="";
        for($i=$numeroStelle;$i>0;$i--){
            $stelle=$stelle.'<img src = "../img/img_componenti/stella.png" class="stella">';
        }
        $stelleVuote=5-$numeroStelle;
        for($i=$stelleVuote;$i>0;$i--){
            $stelle=$stelle.'<img src = "../img/img_componenti/stella_vuota.png" class="stella">';
        }
        return $stelle;
    }
?>