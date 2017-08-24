<?php

namespace Root;
use \PDO;

class Db
{
    private $pdo;
    
    private $sQuery;
    
    private $settings;
    
    private $bConnected = false;
    
    private $parameters = array();
    
    /**
     *  1. Get settings
     *	2. Connect to database.
     */
    public function __construct()
    {
        #Get settings
        if (file_exists(__DIR__ . "/../../config/database.ini")) {
            $this->settings = parse_ini_file("./config/database.ini", true);
        }
        else die('not find config.php');
        #Connect to database
        $this->Connect();
    }
    
    /**
     *	This method makes connection to the database.
     */
    private function Connect()
    {
        $dsn = 'mysql:dbname=' . $this->settings['mysql']['database']['database'] . ';host=' . $this->settings['mysql']['database']['host'] . '';
        
        try {
            $this->pdo = new PDO($dsn, $this->settings['mysql']['database']['user'], $this->settings['mysql']['database']['password'], array(
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
            ));
            
            # Get any exceptions on Fatal error. 
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            # Disable emulation of prepared statements, use REAL prepared statements instead.
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            
            # Connection succeeded, set the boolean to true.
            $this->bConnected = true;
        }
        catch (PDOException $e) {
            # Echo Exception log
            $this->ExceptionLog($e->getMessage());
            die('DB connect fail');
        }
    }
    /*
     *   Close the DB connection
     */
    public function CloseConnection()
    {
        $this->pdo = null;
    }
    
    /**
     *	1. If not connected, connect to the database.
     *	2. Prepare Query.
     *	3. Parameterize Query.
     *	4. Execute Query.	
     *	5. On exception : Echo Exception log + SQL query.
     *	6. Reset the Parameters.
     */
    private function Init($query, $parameters = "")
    {
        # Connect to database
        if (!$this->bConnected) {
            $this->Connect();
        }
        try {
            # Prepare query
            $this->sQuery = $this->pdo->prepare($query);
            
            # Add parameters to the parameter array	
            $this->bindMore($parameters);
            
            # Bind parameters
            if (!empty($this->parameters)) {
                foreach ($this->parameters as $param => $value) {
                    
                    $type = PDO::PARAM_STR;
                    switch ($value[1]) {
                        case is_int($value[1]):
                            $type = PDO::PARAM_INT;
                            break;
                        case is_bool($value[1]):
                            $type = PDO::PARAM_BOOL;
                            break;
                        case is_null($value[1]):
                            $type = PDO::PARAM_NULL;
                            break;
                    }
                    // Add type when binding the values to the column
                    $this->sQuery->bindValue($value[0], $value[1], $type);
                }
            }
            
            # Execute SQL 
            $this->sQuery->execute();
        }
        catch (PDOException $e) {
            # Write into log and display Exception
            $this->ExceptionLog($e->getMessage(), $query);
            die('Sql query error');
        }
        
        # Reset the parameters
        $this->parameters = array();
    }
    
    /**
     *	@void
     *	
     *	Add more parameters to the parameter array
     *	@param array $parray
     */
    public function bindMore($parray)
    {
        if (empty($this->parameters) && is_array($parray)) {
            $columns = array_keys($parray);
            foreach ($columns as $i => &$column) {
                $this->parameters[$i] = [":" . $column , $parray[$column]];
            }
        }
    }
    /**
     * 
     *  @param  string $query
     *	@param  array  $params
     *	@param  int    $fetchmode
     *	@return mixed
     */
    public function query($query, $params = null, $fetchmode = PDO::FETCH_ASSOC)
    {
        $query = trim(str_replace("\r", " ", $query));
        
        $this->Init($query, $params);
        
        $rawStatement = explode(" ", preg_replace("/\s+|\t+|\n+/", " ", $query));
        
        # Which SQL statement is used 
        $statement = strtolower($rawStatement[0]);
        
        if ($statement === 'select' || $statement === 'show') {
            return $this->sQuery->fetchAll($fetchmode);
        } elseif ($statement === 'insert' || $statement === 'update' || $statement === 'delete') {
            return $this->sQuery->rowCount();
        } else {
            return NULL;
        }
    }
    
    /**
     *  Returns the last inserted id.
     *  @return string
     */
    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }
    public function rowCount()
    {
        return $this->sQuery->rowCount();
    }
    
    /**
     *	Returns an array which represents a column from the result set 
     *
     *	@param  string $query
     *	@param  array  $params
     *	@return array
     */
    public function column($query, $params = null)
    {
        $this->Init($query, $params);
        $Columns = $this->sQuery->fetchAll(PDO::FETCH_NUM);
        
        $column = null;
        
        foreach ($Columns as $cells) {
            $column[] = $cells[0];
        }
        
        return $column;
        
    }
    /**
     *
     *  @param  string $query
     *  @param  array  $params
     *  @param  int    $fetchmode
     *  @return array
     */
    public function row($query, $params = null, $fetchmode = PDO::FETCH_ASSOC)
    {
        $this->Init($query, $params);
        $result = $this->sQuery->fetch($fetchmode);
        $this->sQuery->closeCursor(); // Frees up the connection to the server so that other SQL statements may be issued,
        return $result;
    }
    /**	
     * Writes the log and returns the exception
     *
     * @param  string $message
     * @param  string $sql
     * @return 
     */
    private function ExceptionLog($message, $sql = "")
    {
        echo "Raw SQL : " . $sql . "<br />";
        echo $message;
        
        return;
    }

    public function retorna_pdo_instancia(){
        return $this->pdo;
    }
}
?>