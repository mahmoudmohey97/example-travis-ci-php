<?php
class Company
{
    private $conn;
    private $table_name = "company";


    public $EMAIL;
    public $PASSWORD;
    public $NAME;
    public $NUMEMPLOYEES;
    public function __construct($db)
    {
        $this-> conn = $db;

    }

    function getUserId($email)
    {
        $var = $this->conn->query("SELECT * FROM company WHERE EMAIL='{$email}'")->fetchAll();
        return $var[0]["USERID"];
    }

    function update($email)
    {
        if($this->NAME != null and $this->NUMEMPLOYEES != null and $this->emailExists($this->EMAIL))
        {
            $query = "UPDATE company
                  SET
                    NUMEMPLOYEES = :NUMEMPLOYEES,
                    NAME = :NAME 
                    WHERE company.EMAIL = '{$email}'";



            $stmt = $this->conn->prepare($query);

            $this->NUMEMPLOYEES = htmlspecialchars(strip_tags($this->NUMEMPLOYEES));
            $this->NAME = htmlspecialchars(strip_tags($this->NAME));

            $stmt->bindParam(':NAME', $this->NAME);
            $stmt->bindParam(':NUMEMPLOYEES', $this->NUMEMPLOYEES);

            if ($stmt->execute())
            {
                //echo($stmt->rowCount());
                return true;
            }
            return false;
        }

        else
        {
            return false;
        }
    }

    function readById($id)
    {
        $query = "select company.NAME , company.EMAIL, company.NUMEMPLOYEES , GROUP_CONCAT(INTERESTS) as interest_name 
                    FROM interest , company WHERE ID IN 
                    ( SELECT interest.ID FROM interest WHERE interest.COM_USERID = '{$id}' ) GROUP BY applicant.USERNAME";
        //$query = "select FNAME, LNAME, AGE , EMAIL , USERNAME from applicant";
        $stmt = $this->conn->query($query);
        return $stmt;
    }

    function read()
    {

        $query = "select company.NAME , company.EMAIL, company.NUMEMPLOYEES , GROUP_CONCAT(INTERESTS) as interest_name 
                    FROM interest , company WHERE ID IN 
                    ( SELECT interest.ID FROM interest WHERE interest.COM_USERID = company.USERID) GROUP BY company.NAME";
        $stmt = $this->conn->query($query);
        return $stmt;
    }

    function create()
    {
        $query = "INSERT INTO " . $this->table_name . "
            SET
                EMAIL = :EMAIL,
                NAME = :NAME,
                NUMEMPLOYEES = :NUMEMPLOYEES,
                PASSWORD = :PASSWORD";

        $stmt = $this-> conn-> prepare($query);

        $this->EMAIL=htmlspecialchars(strip_tags($this->EMAIL));
        $this->NAME =htmlspecialchars(strip_tags($this->NAME));
        $this->NUMEMPLOYEES=htmlspecialchars(strip_tags($this->NUMEMPLOYEES));
        $this->PASSWORD=htmlspecialchars(strip_tags($this->PASSWORD));


        $stmt-> bindParam(':NAME' , $this-> NAME);
        $stmt-> bindParam(':EMAIL' , $this-> EMAIL);
        $stmt->bindParam(':PASSWORD', $this->PASSWORD);
        $stmt->bindParam(':NUMEMPLOYEES', $this->NUMEMPLOYEES);

        if($stmt->execute()){
            return true;
        }

        return false;
    }

    function emailExists()
    {
        // query to check if email exists
        $query = "SELECT USERID, PASSWORD
            FROM " . $this->table_name . "
            WHERE EMAIL = ?
            LIMIT 0,1";

        // prepare the query
        $stmt = $this->conn->prepare( $query );

        // sanitize
        $this->EMAIL=htmlspecialchars(strip_tags($this->EMAIL));

        // bind given email value
        $stmt->bindParam(1, $this->EMAIL);
        $stmt->execute();
        $num = $stmt->rowCount();

        if($num > 0)
        {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->USERID = $row['USERID'];
            $this->PASSWORD = $row['PASSWORD'];
            return true;
        }
        return false;
    }


}
