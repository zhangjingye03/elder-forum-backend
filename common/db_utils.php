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
				return "`" . implode("`, `", $what) . "`";
			else
				throw new \Exception("Unsupported type.", 1);
		}

		function select($what) {
			$this->s .= "SELECT " . $this->strize($what) . " ";
			return $this;
		}

		function update($what) {
			if (!is_string($what))
				throw new \Exception("Unsupported type.", 1);
			$this->s .= "UPDATE {$what} ";
		}

		function insertInto($table, $col, $arg, $type = null) {
			$this->arg = $arg; $this->type = $type;
			$this->s .= "INSERT INTO {$table} (" . $this->strize($col) . ") VALUES (";
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

		function createTable($name) {
			$this->s .= "CREATE TABLE {$name} ";
		}

		function dropTable($which) {
			$this->s .= "DROP TABLE `{$which}` ";
		}

		function deleteFrom($which) {
			$this->s .= "DELETE FROM `{$which}` ";
		}

		function from($what) {
			$this->s .= "FROM {$what} ";
			return $this;
		}

		function set($what, $arg = null, $type = null) {
			$this->s .= "SET {$what} ";
			$this->arg = $arg;
			$this->type = $type;
			return $this;
		}

		function where($what, $arg = null, $type = null) {
			$this->s .= "WHERE {$what} ";
			$this->arg = $arg;
			$this->type = $type;
			return $this;
		}

		function like($what) {
			$this->s .= "LIKE {$what} ";
		}

		function orderBy($what) {
			$this->s .= "ORDER BY " . $this->strize($what) . " ";
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

		function limit($arg1, $arg2 = null) {
			$this->s .= "LIMIT {$arg1} ";
			if ($arg2 != null) $this->s .= ", {$arg2} ";
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
				$this->dbo->bindParam(1, $this->arg, ($this->type == null) ? PDO::PARAM_STR : $this->type);
			}
			$this->dbo->execute();
			$this->s = "";
			if ($this->dbo->errorCode() != 0)
				throw new \Exception(var_dump($this->dbo->errorInfo()), 1);
			return $this;
		}

		function debug() {
			return $this->s;
		}

		function rowCount() {
			if ($this->dbo == null)
				throw new \Exception("The SQL statement hasn't been executed yet.", 1);
			return $this->dbo->rowCount();
		}

		function fetch() {
			if ($this->dbo == null)
				throw new \Exception("The SQL statement hasn't been executed yet.", 1);
			return $this->dbo->fetch(PDO::FETCH_ASSOC);
		}

		function fetchAll() {
			if ($this->dbo == null)
				throw new \Exception("The SQL statement hasn't been executed yet.", 1);
			return $this->dbo->fetchAll(PDO::FETCH_ASSOC);
		}

		function lastInsertId() {
			if ($this->dbo == null)
				throw new \Exception("The SQL statement hasn't been executed yet.", 1);
			return $this->dbo->lastInsertId() - 0;
		}
	}

	function calc_limit_offset($count, $page) {
		if (!is_integer($count) || !is_integer($page)) return 0;
		return ($page - 1) * $count
	}
?>
