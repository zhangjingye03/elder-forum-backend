<?php
/**
 * SQLStatement类 /common/db_utils.php
 * ===========================================================
 * 一个优雅的SQL语句拼接类，让代码变得整洁雅观。
 *
 * 使用说明：
 * 先 $q = new SQLStatement; 构造对象
 * 然后进行各种操作，比如
 * $q->select("*")
 *   ->from("user")
 *   ->where("username = ?", $username, PDO::PARAM_STR)
 *   ->orderBy("username")
 *   ->desc()
 *   ->execute();
 * 然后用$q->fetchAll()获取返回的结果，$q->rowCount()获取返回行数
 * ===========================================================
 */
	class SQLStatement {
		var $s, $arg, $type, $dbo, $db;
		function __construct($default = '') {
			require_once('config/database.php');
			try {
				$this->db = new PDO($_dsn, $_username, $_password);
			} catch (PDOException $ex) {
				die("PDO initializing failed. Reason: " . $ex->getMessage());
			}
			$this->s = $default;
		}

		function strize($what) {
			if (is_string($what))
				return $what;
			else if (is_array($what))
				return implode(", ", $what);
			else
				throw new \Exception("Unsupported type.", 1);
		}

		function select($what) {
			$this->s .= "SELECT " . strize($what) . " ";
			return $this;
		}

		function update($what) {
			if (!is_string($what))
				throw new \Exception("Unsupported type.", 1);
			$this->s .= "UPDATE {$what} ";
		}

		function insertInto($table, $col, $arg, $type = null) {
			$this->arg = $arg; $this->type = $type;
			$this->s .= "INSERT INTO {$table} (" . strize($col) . ") VALUES (";
			if (is_array($arg)) {
				for ($i = 0; $i < sizeof($arg); $i++) {
					$this->s .= "?, ";
				}
				$this->s = substr($this->s, 0, strlen($this->s) - 2);
			} else if (is_string($arg))
				$this->s .= "?";
			else
				throw new \Exception("Unsupported type.", 1);
			$this->s .= ")";
		}

		function from($what) {
			$this->s .= "FROM {$what} ";
			return $this;
		}

		function set($what, $arg, $type = null) {
			$this->s .= "SET {$what} ";
			$this->arg = $arg;
			$this->type = $type;
			return $this;
		}

		function where($what, $arg, $type = null) {
			$this->s .= "WHERE {$what} ";
			$this->arg = $arg;
			$this->type = $type;
			return $this;
		}

		function orderBy($what) {
			$this->s .= "ORDER BY " . strize($what) . " ";
			return $this;
		}

		function asc() {
			$this->s .= "ASC ";
			return $this;
		}

		function desc() {
			$this->s .= "DESC ";
			return $this;
		}

		function execute() {
			$this->dbo = $this->db->prepare($this->s . ";");
			if (is_array($this->arg)) {
				for ($i = 0; $i < sizeof($this->arg); $i++) {
					if ($this->type == null)
						$this->dbo->bindParam($i + 1, $this->arg[$i], PDO::PARAM_STR);
					else
						$this->dbo->bindParam($i + 1, $this->arg[$i], $this->type[$i]);
				}
			} else if (is_string($this->arg)) {
				$this->dbo->bindParam(1, $this->arg, $this->type);
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
