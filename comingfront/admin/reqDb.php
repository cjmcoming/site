<?php
	class dbconfig {
		private static $dsn;

		/**
		* @return   返回pdo dsn配置
		*/
		public static function getdsn() {
			global $data;
			if (!isset(self::$dsn)) {
				self::$dsn = $data['dbms'] . ':host=' . $data['server'] . ';port=' . $data['port'] . ';dbname=' . $data['dbname'] . ';charset=' . $data['charset'];
			}
			return self::$dsn;
		}

		public static function getusername() {
			global $data;
			return $data['user'];
		}

		public static function getpassword() {
			global $data;
			return $data['password'];
		}
	}

	/**
	* 数据库操作工具类
	*/
	class dbtemplate {

		/**
		* 返回多行记录
		* @param  $sql
		* @param  $parameters
		* @return  记录数据
		*/
		public function queryrows($sql, $parameters = null) {
			return $this->exequery($sql, $parameters);
		}

		/**
		* 返回为单条记录
		* @param  $sql
		* @param  $parameters
		* @return
		*/
		public function queryrow($sql, $parameters = null) {
			$rs = $this->exequery($sql, $parameters);
			if (count($rs) > 0) {
				return $rs[0];
			} else {
				return array();
			} 
		} 

		/**
		* 查询单字段，返回整数
		* @param  $sql
		* @param  $parameters
		* @return
		*/
		public function queryforint($sql, $parameters = null) {
			$rs = $this->exequery($sql, $parameters);
			if (count($rs) > 0) {
				return intval($rs[0][0]);
			} else {
				return null;
			}
		}

		/**
		* 查询单字段，返回浮点数(float)
		* @param  $sql
		* @param  $parameters
		* @return
		*/
		public function queryforfloat($sql, $parameters = null) {
			$rs = $this->exequery($sql, $parameters);
			if (count($rs) > 0) {
				return floatval($rs[0][0]);
			} else {
				return null;
			}
		}

		/**
		* 查询单字段，返回浮点数(double)
		* @param  $sql
		* @param  $parameters
		* @return
		*/
		public function queryfordouble($sql, $parameters = null) {
			$rs = $this->exequery($sql, $parameters);
				if (count($rs) > 0) {
				return doubleval($rs[0][0]);
			} else {
				return null;
			}
		}

		/**
		* 查询单字段，返回对象，实际类型有数据库决定
		* @param  $sql
		* @param  $parameters
		* @return
		*/
		public function queryforobject($sql, $parameters = null) {
			$rs = $this->exequery($sql, $parameters);
			if (count($rs) > 0) {
				return $rs[0][0];
			} else {
				return null;
			}
		}

		/**
		* 执行一条更新语句.insert / update / delete
		* @param  $sql
		* @param  $parameters
		* @return  影响行数
		*/
		public function update($sql, $parameters = null) {
			return $this->exeupdate($sql, $parameters);
		}

		public function insert($sql, $parameters = null) {
			$conn = $this->getconnection();
			$stmt = $conn->prepare($sql);
			$stmt->execute($parameters);
			$affectedrows = $stmt->rowcount();
			$newId = $conn->lastInsertId();
			$stmt = null;
			$conn = null;
			return array("affectedrows" => $affectedrows, "newId" => $newId);
		}

		private function getconnection() {
			$conn = new pdo(dbconfig::getdsn(), dbconfig::getusername(), dbconfig::getpassword());
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return $conn;
		}

		private function exequery($sql, $parameters = null) {
			$conn = $this->getconnection();
			$stmt = $conn->prepare($sql);
			$stmt->execute($parameters);
			$rs = $stmt->fetchall();
			$stmt = null;
			$conn = null;
			return $rs;
		}

		private function exeupdate($sql, $parameters = null) {
			$conn = $this->getconnection();
			$stmt = $conn->prepare($sql);
			$stmt->execute($parameters);
			$affectedrows = $stmt->rowcount();
			$stmt = null;
			$conn = null;
			return $affectedrows;
		}
	}
?>