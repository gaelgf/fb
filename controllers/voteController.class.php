<?php


class voteController{
    public function indexAction( $args )
    {
        $view = new view();

        if (!isset($_SESSION['facebook_access_token'])) {
            header("Location: ".BASE_URL);
        } else {

            // Recuperation de la valeur du participant
            if( !isset($_SESSION["id_participant"]) || empty($_SESSION["id_participant"])){
                header("Location: ".BASE_URL);
            }
            else{
                $idParticipant = $_SESSION["id_participant"];

                $photosNotVotedYet = self::getPhotosNotVotedYetByParticipantId($idParticipant);

                if(count($photosNotVotedYet) == 0){
                    header("Location: ".BASE_URL."vote/classement");
                }
                else{
                    $view->setView("indexVote");

                    // Verification des valeurs de la campagne en cours
                    $arrayCampagne = self::getCampagneArrayAttributes();
                    $arrayCritere = critere::load();
                    $idPhotoAffichee = rand(0,count($photosNotVotedYet)-1);
                    $view->assign("photo", $photosNotVotedYet[$idPhotoAffichee]);
                    $view->assign("base_url", BASE_URL);
                    $view->assign("array_campagne", $arrayCampagne);
                    $view->assign("criteres", $arrayCritere);
                    $view->assign("participant", $idParticipant);
                }
            }
        }
    }

    public function voteparticipantAction(){

        $criteres = critere::load();

        if ( $this->verifyPostVote($criteres) ) {

            foreach($criteres as $critere){
                $id_critere = $critere->getId();
                $vote = new vote(NULL,$_POST['id_photo'],$id_critere,date("Y-m-d"),$_POST["critere_".$id_critere],$_POST['id_participant']);
                $vote->save();
            }

            header("Location: ".BASE_URL."vote");
        } else {
            header("Location: ".BASE_URL."vote");
        }
    }

    public function classementAction( $args ){
        $view = new view();
        $view->setView("classementVote");

    }






    public function verifyPostVote($criteres){

        $res = true;

        if( !$this->verifyPostCriteres($criteres)){
            $res = false;
        }
        if( !isset($_POST['id_photo']) || empty($_POST['id_photo'])){
            $res = false;
        }
        if( !isset($_POST['id_participant']) || empty($_POST['id_participant'])){
            $res = false;
        }


        return $res;
    }









    public function verifyPostCriteres( $arrayCritere ){
        $res = true;
        foreach($arrayCritere as $critere){
            $id = $critere->getId();
            if( !isset($_POST['critere_'.$id]) || empty($_POST['critere_'.$id]) ){
                $res = false;
            }
        }

        return $res;
    }











    public function getPhotosNotVotedYetByParticipantId($participantId) {
        $IdsPhotosAlreadyVotedByCurrentParticipant = vote::loadIdsPhotosFromVotesWhereParticipantIdVoted($participantId);
        if( isset($_SESSION["campagne_in_session"]) && $_SESSION["campagne_in_session"] == "OK" && isset($_SESSION["campagne_id"]) ){
            $campagneId = $_SESSION["campagne_id"];
        } else {
            $campagne = campagne::loadCurrent();
            $campagneId = $campagne->getId();
        }
        $AllPhotos = photo::loadByCampagneId($campagneId);
        
        $photosNotVotedYet = [];
        foreach ($AllPhotos as $photo) {
            $isAlreadyVoted = false;
            foreach ($IdsPhotosAlreadyVotedByCurrentParticipant as $IdPhotoAlreadyVoted) {
                if($IdPhotoAlreadyVoted === $photo->getId()) {
                    $isAlreadyVoted = true;
                    break;
                }
            }
            if($isAlreadyVoted === false) {
                $photosNotVotedYet []= $photo;
            }
        }
        return $photosNotVotedYet;
    }



    public function getCampagneArrayAttributes(){

        $campagneArray = [];

        if( !isset($_SESSION["campagne_in_session"]) || $_SESSION["campagne_in_session"] == "OK"  ){
            $campagne = campagne::loadCurrent();
            $_SESSION["campagne_id"] = $campagne->getId();
            $_SESSION["campagne_logo_entreprise"] = $campagne->getLogoEntreprise();
            $_SESSION["campagne_nom_campagne"] = $campagne->getNomCampagne();
            $_SESSION["campagne_date_debut"] = $campagne->getDateDebut();
            $_SESSION["campagne_date_fin"] = $campagne->getDateFin();
            $_SESSION["campagne_couleur"] = $campagne->getCouleur();
            $_SESSION["campagne_text_accueil"] = $campagne->getTextAccueil();
            $_SESSION["campagne_text_felicitations"] = $campagne->getTextFelicitations();
            $_SESSION["campagne_is_active"] = $campagne->getIsActive();
            $_SESSION["campagne_nom_lot"] = $campagne->getNomLot();
            $_SESSION["campagne_description_lot"] = $campagne->getDescriptionLot();
            $_SESSION["campagne_image_lot"] = $campagne->getImageLot();
            $_SESSION["campagne_photo_accueil_one"] = $campagne->getPhotoAccueilOne();
            $_SESSION["campagne_photo_accueil_two"] = $campagne->getPhotoAccueilTwo();
            $_SESSION["campagne_photo_accueil_three"] = $campagne->getPhotoAccueilThree();
            $_SESSION["campagne_icone_coeur"] = $campagne->getIconeCoeur();
            $_SESSION["campagne_icone_principale"] = $campagne->getIconePrincipale();
            $_SESSION["campagne_in_session"] = "OK";
        }

        $campagneArray["id"] = $_SESSION["campagne_id"];
        $campagneArray["logo_entreprise"] = $_SESSION["campagne_logo_entreprise"];
        $campagneArray["nom_campagne"] = $_SESSION["campagne_nom_campagne"];
        $campagneArray["date_debut"] = $_SESSION["campagne_date_debut"];
        $campagneArray["date_fin"] = $_SESSION["campagne_date_fin"];
        $campagneArray["couleur"] = $_SESSION["campagne_couleur"];
        $campagneArray['text_accueil'] = $_SESSION["campagne_text_accueil"];
        $campagneArray['text_felicitations'] = $_SESSION["campagne_text_felicitations"];
        $campagneArray['is_active'] = $_SESSION["campagne_is_active"];
        $campagneArray['nom_lot'] = $_SESSION["campagne_nom_lot"];
        $campagneArray['description_lot'] = $_SESSION["campagne_description_lot"];
        $campagneArray['image_lot'] = $_SESSION["campagne_image_lot"];
        $campagneArray['photo_accueil_one'] = $_SESSION["campagne_photo_accueil_one"];
        $campagneArray['photo_accueil_two'] = $_SESSION["campagne_photo_accueil_two"];
        $campagneArray['photo_accueil_three'] = $_SESSION["campagne_photo_accueil_three"];
        $campagneArray['icone_coeur'] = $_SESSION["campagne_icone_coeur"];
        $campagneArray['icone_principale'] = $_SESSION["campagne_icone_principale"];

        return $campagneArray;
    }
}