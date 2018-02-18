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
		var $s, $arg, $argc, $type, $dbo, $db, $debug;
		function __construct($default = '') {
			require_once('config/database.php');
			try {
				global $_dsn, $_username, $_password, $_debug;
				$this->debug = $_debug;
				$this->db = new PDO($_dsn, $_username, $_password);
			} catch (PDOException $ex) {
				die("PDO initializing failed. Reason: " . $ex->getMessage());
			}
			$this->initialize();
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

		function selectCount() {
			$this->s .= "SELECT COUNT(*) ";
			return $this;
		}

		function update($what) {
			if (!is_string($what))
				throw new \Exception("Unsupported type.", 1);
			$this->s .= "UPDATE {$what} ";
			return $this;
		}

		function insertInto($table, $col, $arg, $type = null) {
			$this->bind($arg, $type);
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

			return $this;
		}

		function createTable($name) {
			$this->s .= "CREATE TABLE {$name} ";
			return $this;
		}

		function dropTable($which) {
			$this->s .= "DROP TABLE `{$which}` ";
			return $this;
		}

		function deleteFrom($which) {
			$this->s .= "DELETE FROM `{$which}` ";
			return $this;
		}

		function from($what) {
			$this->s .= "FROM {$what} ";
			return $this;
		}

		function set($what, $arg = null, $type = null) {
			$this->s .= "SET {$what} ";
			$this->bind($arg, $type);
			return $this;
		}

		function where($what, $arg = null, $type = null) {
			$this->s .= "WHERE {$what} ";
			$this->bind($arg, $type);
			return $this;
		}

		function and($what, $arg = null, $type = null) {
			$this->s .= "AND ";
			$this->bind($arg, $type);
			return $this;
		}

		function or($what, $arg = null, $type = null) {
			$this->s .= "OR ";
			$this->bind($arg, $type);
			return $this;
		}

		function like($what) {
			$this->s .= "LIKE {$what} ";
			return $this;
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
			return $this;
		}

		private function bind($arg, $type) {
			if (is_array($arg)) {
				foreach ($arg as $k => $v) {
					$this->arg[$this->argc] = $v;
					if ($type == null)
						$this->type[$this->argc] = PDO::PARAM_STR;
					else
						$this->type[$this->argc] = $type[$k];
					$this->argc++;
				}
			} else if (is_string($arg)) {
				$this->arg[$this->argc] = $arg;
				$this->type[$this->argc++] = ($type == null) ? PDO::PARAM_STR : $type;
			} else if (is_integer($arg)) {
				$this->arg[$this->argc] = $arg;
				$this->type[$this->argc++] = PDO::PARAM_INT;
			}
		}

		function execute() {
			if ($this->debug) {
				ob_start();
				echo("Begin to execute {$this->s} with args: \n");
				var_dump($this->arg);
				$res = ob_get_clean();
				file_put_contents(__DIR__ . "debug.txt", $res, FILE_APPEND);
			}
			$this->dbo = $this->db->prepare($this->s . ";");
			for ($i = 0; $i < sizeof($this->arg); $i++)
				$this->dbo->bindParam($i + 1, $this->arg[$i], $this->type[$i]);
			$this->dbo->execute();

			$this->initialize();

			if ($this->dbo->errorCode() != 0)
				throw new \Exception("Database error: " . $this->dbo->errorInfo()[2]);
			return $this;
		}

		function initialize() {
			$this->s = "";
			$this->argc = 0;
			$this->arg = [];
			$this->type = [];
		}

		function debug() {
			return $this->s;
		}

		function rowCount() {
			if ($this->dbo == null)
				throw new \Exception("The SQL statement hasn't been executed yet.", 1);
			return $this->dbo->rowCount();
		}

		function fetchCount() {
			if ($this->dbo == null)
				throw new \Exception("The SQL statement hasn't been executed yet.", 1);
			return $this->dbo->fetch()["COUNT(*)"];
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
			return $this->db->lastInsertId() - 0;
		}
	}

	function calc_limit_offset($count, $page) {
		return (intval($page) - 1) * intval($count);
	}
?>
