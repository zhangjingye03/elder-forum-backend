<?php
	class SQLStatement {
		var $s;
		var $arg;
		var $type;
		var $dbo;
		var $db;
		function __construct() {
			require_once('config/database.php');
			try {
				$this->db = new PDO($_dsn, $_username, $_password);
			} catch (PDOException $ex) {
				die("PDO initializing failed. Reason: " . $ex->getMessage());
			}
			$this->s = "";
		}
		function select($what) {
			$this->s .= "SELECT ";
			if (is_string($what))
				$this->s .= $what . " ";
			else if (is_array($what)) {
				$this->s .= implode(", ", $s);
			} else
				throw new \Exception("Unsupported select type.", 1);
				return $this;
		}
		function from($what) {
			$this->s .= "FROM {$what} ";
			return $this;
		}
		function where($what, $arg, $type) {
			$this->s .= "WHERE {$what} ";
			$this->arg = $arg;
			$this->type = $type;
			return $this;
		}


		function execute() {
			$this->dbo = $this->db->prepare($this->s);
			for ($i = 0; $i < sizeof($this->arg); $i++) {
				$this->dbo->bindParam($i + 1, $this->arg[$i], $this->type[$i]);
			}
			$this->dbo->execute();
			if ($this->dbo->errorCode() != 0)
				throw new \Exception(var_dump($this->dbo->errorInfo()), 1);
		}
		function rowCount() {
			if ($this->dbo == null)
				throw new \Exception("The SQL statement hasn't been executed yet.", 1);
			return $this->dbo->rowCount();
		}
		function fetchAll() {
			if ($this->dbo == null)
				throw new \Exception("The SQL statement hasn't been executed yet.", 1);
			return $this->dbo->fetchAll();
		}
	}

?>
