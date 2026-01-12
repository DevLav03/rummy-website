<?php

namespace App\Domain\Rummy_Website\Settings\Repository;

use PDO;

final class SettingsRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //Get all data
    public function getLogoSettings(): array
    {   
        $statement = $this->conn->prepare("SELECT * FROM ws_settings ");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows;
    }

    //Update
    public function updateLogoSettings(array $data, $logo_img): int
    {
        
        if (empty($logo_img)){
            //print_r('no logo'); exit;
            $statement =  $this->conn->prepare("UPDATE ws_settings SET footer=?, banner_title=?");
            $statement->execute(array($data['footer'], $data['banner_title'] ));
        }else{
            //print_r('full data'); exit;
            $statement =  $this->conn->prepare("UPDATE ws_settings SET logo_image=?, footer=?, banner_title=?");
            $statement->execute(array($logo_img, $data['footer'], $data['banner_title'] ));
        }
        
        $count = $statement->rowCount();
        $statement->closeCursor();
        return $count;

    }
   
 

}

