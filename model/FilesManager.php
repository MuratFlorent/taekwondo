<?php

namespace taekwondo\model;

/**
 * Class FilesManager
 * class building a CRUD for all the entities related to the files (FileAdherent, FileAdmin and Category)
 */

class FilesManager extends Manager 
{
    /**
     * Add a new admin file
     */
    public function addAdminFile(FileAdmin $file){
        $req = $this->db->prepare('INSERT INTO admin_files(name_file, file_url,title_file) VALUES (?,?,?)');
        $req->execute(array($file->getName_file(),$file->getFile_url(),$file->getTitle_File()));
    }
    /**
     * Post all the inscriptions files online
     */
    public function listInscriptionFiles(){
        $req = $this->db->query('SELECT file_url, title_file FROM admin_files');
        $req->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, 
        'taekwondo\model\FileAdmin');
        return $req;
    }
    /**
     * insert a new signed adherent file
     */
    public function uploadAdherentFile(FileAdherent $file,$finalPath,$categorie){
        $req = $this->db->prepare('INSERT INTO p5_adherent_files(adherent_name,
        adherent_firstname,adherent_city,adherent_file_name,adherent_file_url,upload_date,category_id) 
                    VALUES (?,?,?,?,?,NOW(),?)');
        $req->execute(array($file->getAdherent_name(),$file->getAdherent_firstname(),$file->getAdherent_city(),
        $file->getAdherent_file_name(),$finalPath,$categorie));

    }
    /**
     * Select a category of file
     */
    public function chooseCategory(){
        $req = $this->db->query('SELECT * FROM p5_files_category');
        $req->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, 
        'taekwondo\model\Category');
        return $req;
    }
    /**
     * Select the signed inscriptions files according to there categories.
     */
    public function signedFiles($categoryFile){
        $req = $this->db->prepare("SELECT id,adherent_name,
        adherent_firstname,adherent_city,adherent_file_name,adherent_file_url, 
        DATE_FORMAT(upload_date,GET_FORMAT(DATE, 'EUR')) 
        AS sentDate ,category_id 
        FROM p5_adherent_files 
        WHERE category_id=?"); 
        $req->execute(array($categoryFile));
        $req->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, 
        'taekwondo\model\FileAdherent');
        $signedFiles=$req->fetchAll();
        return $signedFiles;
    }
    /**
     * Delete adherent files
     */
    public function deleteAdherentFile(FileAdherent $adherentFile){
        $req =$this->db->prepare('DELETE FROM p5_adherent_files WHERE id=?');
        $req->execute(array($adherentFile->getId()));
    }
    /**
     * Add a new Category
     */
    public function addCategory(Category $category){
        $req =$this->db->prepare('INSERT INTO p5_files_category(category_name)
        VALUES(?)');
        $req->execute(array($category->getCategory_name()));
    }
    /**
     * Delete a category
    */
    public function deleteCategory(Category $category){
        $req =$this->db->prepare('DELETE FROM p5_files_category WHERE id=?');
        $req->execute(array($category->getId()));
    }
}