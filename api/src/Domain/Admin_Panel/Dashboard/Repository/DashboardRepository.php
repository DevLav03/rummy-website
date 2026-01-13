<?php

namespace App\Domain\Admin_Panel\Dashboard\Repository;

use PDO;

final class DashboardRepository
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    //get all data
    public function getAdminCount(): array
    {   
        $statement = $this->conn->prepare("SELECT COUNT(id) AS total FROM admin");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows;
    }

    
    //get career data
    public function getCareerCount(): array
    {   
        $statement = $this->conn->prepare("SELECT COUNT(id) AS total FROM form_career");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows;
    }
 

    //get project data
    public function getProjectCount(): array
    {   
        $statement = $this->conn->prepare("SELECT COUNT(id) AS total FROM form_project");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		$statement->closeCursor();
        return $rows;
    }

    //get contact data
    public function getContactCount(): array
    {   
        $statement = $this->conn->prepare("SELECT COUNT(id) AS total FROM form_contact");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $rows;
    }
 
    //get contact data
    public function getJobPostCount(): array
    {   
        $statement = $this->conn->prepare("SELECT COUNT(id) AS total FROM post_job");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $rows;
    }


    //get career year data
    public function getCareerYear()
    {   
        $statement = $this->conn->prepare("SELECT COUNT(id) AS total, YEAR(created_at) AS filter FROM `form_career` GROUP BY YEAR(created_at)");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $rows;
    }

    
    //get career month data
    public function getCareerMonth()
    {   
        $statement = $this->conn->prepare("SELECT COUNT(id) AS total, concat(MONTHNAME(created_at),'''',DATE_FORMAT(created_at, '%y')) AS filter FROM `form_career` where created_at > now() - INTERVAL 12 month GROUP BY MONTHNAME(created_at)");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $rows;
    }
}
