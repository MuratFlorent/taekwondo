<?php

namespace taekwondo\controller;

use taekwondo\model\SliderManager;
use taekwondo\model\FilesManager;
use taekwondo\model\EventsManager;
use taekwondo\model\FileAdherent;


class FrontendController
{
    public $msg= "";
    private $sliderManager;
    private $filesManager;
    private $eventsManager;

    public function __construct(){
        $this->sliderManager = new SliderManager();
        $this->filesManager = new FilesManager();
        $this->eventsManager = new EventsManager();
    }

    /**
     * get all the images for the slider
     */
    public function slider(){
        $slides = $this->sliderManager->getAllSlides();

        require('view/homePageView.php');
    }
    /**
     * Show all the informations hours and documents
     */
    public function informations(){
        $inscriptionFiles=$this->filesManager->listInscriptionFiles();
        $categories=$this->filesManager->chooseCategory();
        require ('view/informationsView.php');
    }
    /**
     * Send the filled inscription files
     */
    public function sendInscriptionFile(){
        if(!empty($_FILES)):
            $temporaryPath= $_FILES['adherent_file']['tmp_name'];
            $fileName= $_FILES['adherent_file']['name'];
            $finalPath= 'public/adherent_files/'.$fileName;
            $fileExtension= strrchr($fileName, ".");
            $extensionAllowed= array('.pdf', '.PDF');
            $maxSize = 2000000;
            $size = ($_FILES['adherent_file']['size']);
            if(in_array($fileExtension,$extensionAllowed) && $size<$maxSize && $size!==0):
                $movePath= move_uploaded_file($temporaryPath,$finalPath);
                if($movePath):
                    $file= new FileAdherent(array(
                        'adherent_name'=>$_POST['name'],
                        'adherent_firstname'=>$_POST['firstname'],
                        'adherent_city'=>$_POST['city'],
                        'adherent_fileName'=>$_FILES['adherent_file']['name']
                    ));
                    $uploadedFile=$this->filesManager->uploadAdherentFile($file,$finalPath,$_POST['category']);
                    $this->msg =' le fichier a bien été chargé';;
                else :
                    $this->msg= 'Une erreur est survenue lors de l\'envoi du fichier';
                endif;
            elseif(in_array($fileExtension,$extensionAllowed) && $size>$maxSize || $size ==0):
                $this->msg='Le fichier ne doit pas faire plus de 2Mo';
            else:
                $this->msg= 'Seuls les fichiers PDF sont autorisés.';
            endif;
            header('Location:/p5/taekwondo/informations');
        endif;
        header('Location:/p5/taekwondo/informations');
    }
    /**
     * Load error page
     */
    public function error(){
        $this->msg= 'Accès refusé!';
        require('view/errorView.php');
    }
    public function events(){
        $count= $this->eventsManager->countEvent();
        $currentPage=$_GET['page'] ?? 1;
        var_dump($currentPage);
        if(!filter_var($currentPage, FILTER_VALIDATE_INT)){
            $this->msg='Numéro de page invalide';
            require('view/errorView.php');
        }
        if($currentPage <= 0) {
            $this->msg='Numéro de page invalide';
            require('view/errorView.php');
        }
        /*if($currentPage === '1'){
            header('Location: index.php?action=events');
        }*/
        $perPage= 3;
        $start =$perPage*($currentPage-1);
        $pages = ceil($count /$perPage);
        if ($currentPage > $pages){
            $this->msg='Cette page n\'existe pas';
            require('view/errorView.php');
        }
        $events = $this->eventsManager->getEvents($start,$perPage);
        require('view/eventsView.php');
    }
}