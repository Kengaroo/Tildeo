<?php
include_once 'connection.php';

class Project 
{
    const TABLE = 'project';
    const TABLE_MANAGER = 'manager';

    private $id;
    protected PDO $pdo;

    public function __construct() {
        $connection = new Connection();
        $this->pdo = $connection->getConnection();
    }

    /**
     * Assign new project to manager
     * 
     * 
     *@param $createdBy - id of user who created the project, or 0, if project was created by futur customer througth web-site
     *@return int|false $id - id of manager to which project was assigned or false on error adding project to DB
     */
    public function addProject(int $createdBy)
    {
        $assignedTo = $createdBy ? $createdBy : self::chooseManager();
        if ($assignedTo) {
            $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " (`created_by`, `assigned_to`) VALUES (:createdBy, :assignedTo)");
            $statement->bindValue('createdBy', $createdBy, \PDO::PARAM_INT);
            $statement->bindValue('assignedTo', $assignedTo, \PDO::PARAM_INT);
            $statement->execute();
            return $assignedTo;
        }
        return false;
    }

    private function chooseManager()
    {
        //array of available managers
        $managers = [];
        $query = 'SELECT id FROM `' . self::TABLE_MANAGER . '` WHERE available = 1 ORDER BY id ASC';
        $managers = $this->pdo->query($query)->fetchAll();

        if (!empty($managers)) {
            if (count($managers) > 1) {
                $query = 'SELECT `assigned_to` FROM `' . self::TABLE . '` WHERE `created_by` != `assigned_to` ORDER BY `date_creat` DESC LIMIT 1';
                $last = $this->pdo->query($query)->fetchAll();

                //if we already had a project added from web-site find his manager; otherwise choose first available
                if (isset($last[0])) {
                    $lastManager = $last[0]['assigned_to'];
                    foreach ($managers as $managerInfo) {
                        $manager = $managerInfo['id'];
                        if ($manager > $lastManager) {
                            return $manager;
                        }
                    }
                }
            }
            return array_shift($managers)['id'];
        } else {
            return false;
        }
    }
}
