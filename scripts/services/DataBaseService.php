<?php
    class DataBaseService
    {
        private $conn = null;

        public function startConnection() {//метод открытия подключения к БД
            $servername = "127.0.0.1";
            $database = "forum";
            $username = "root";
            $password = "";

            $this->conn = mysqli_connect($servername, $username, $password, $database);

            return $this->conn == null;
        }

        public function findAll($tableName){//выборка всех записей из таблицы
            if (!$this->conn)
                return null;

            $sql = "SELECT * FROM `$tableName`";

            $result = mysqli_query($this->conn, $sql);

            $response = [];
            while ($row = mysqli_fetch_array($result)) {
                $response[] = $row;
            }

            return $response;
        }

        public function findAllByParam($tableName, $name, $value){//выборка всех записей из таблицы по параметру
            if (!$this->conn)
                return null;

            $sql = "SELECT * FROM `$tableName` WHERE `$tableName`.$name = '$value'";

            $result = mysqli_query($this->conn, $sql);

            if ($result == null) {
                return [];
            }

            $response = [];
            while ($row = mysqli_fetch_array($result)) {
                $response[] = $row;
            }

            return $response;
        }

        public function selectById($tableName, $id){//выбрать запись по id
            if (!$this->conn)
                return null;

            $sql = "SELECT * FROM `$tableName` WHERE `$tableName`.id = '$id'";

            $result = mysqli_query($this->conn, $sql);

            return mysqli_fetch_array($result);
        }

        public function selectByParam($tableName, $name, $value){//выбрать запись по параметру
            if (!$this->conn)
                return null;

            $sql = "SELECT * FROM `$tableName` WHERE `$tableName`.$name = '$value'";
            $result = mysqli_query($this->conn, $sql);

            if ($result == null) {
                return null;
            }

            return mysqli_fetch_array($result);
        }

        public function insert($tableName, $values){//вставить записи в таблицу
            if (!$this->conn)
                return null;

            $keys = "";
            $vals = "";

            $sql = "INSERT INTO `$tableName`";
            foreach ($values as $key => $value){
                $keys .= "`$key`, ";
                $vals .= "'$value', ";
            }

            $vals = substr($vals, 0, strlen($vals)-2);
            $keys = substr($keys, 0, strlen($keys)-2);

            $sql .= "(`id`, $keys) VALUES (null, $vals);";

            $result = mysqli_query($this->conn, $sql);

            return ($result);
        }

        public function delete($tableName, $id){//удалить запись из таблицы
            if (!$this->conn)
                return null;

            $sql = "DELETE FROM `$tableName` WHERE `$tableName`.`id` = '$id'";

            $result = mysqli_query($this->conn, $sql);

            return ($result);
        }

        public function update($tableName, $values, $findParams = null){//вставить записи в таблицу
            if (!$this->conn)
                return null;

            $sql = "UPDATE `$tableName` SET ";
            foreach ($values as $key => $value){
                $sql = $sql . "`$key` = '$value', ";
            }

            $sql = substr($sql, 0, strlen($sql)-2);

            if ($findParams != null) {
                $sql = $sql . " WHERE ";

                foreach ($findParams as $key => $value){
                    $sql = $sql . "`$key` = '$value', ";
                }

                $sql = substr($sql, 0, strlen($sql)-2);
            }

            $result = mysqli_query($this->conn, $sql);

            return ($result);
        }

        function closeConnection(){//закрыть подключение
            mysqli_close($this->conn);
        }
}
?>
