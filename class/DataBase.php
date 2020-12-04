<?php
class DataBase
{
    private static $_db = null;

    /**
     * Конструкт создает подключение к базе
     */
    private function __construct()
    {
        $this->mysqli = new mysqli("localhost", "root", "", "test_base");
    }

    /**
     * Функция формирует строку для дальнейшей вставки
     *
     * @param array  $array - массив с данным и для формирования строки
     * @param string $type  - нужна для переключения
     *
     * @return string
     */
    public function prepareData($array, $type)
    {
        $newArray = array();
        foreach ($array as $item) {
            if ($type == 1 ) {
                $newArray[] = "'" . addslashes($item) . "'";
            } else {
                $newArray[] = "('" . addslashes($item) . "')";
            }
        }
        return implode(',', $newArray);
    }

    /**
     * Получение экземпляра класса
     */
    public static function getDB()
    {
        if (self::$_db == null) {
            self::$_db = new DataBase();
        }
        return self::$_db;
    }


    /**
     * Получение неуникальных значений строк
     *
     * @param string $data - список всех значений для проверки на уникальность
     *
     * @return array
     */
    public function testToUnique($data)
    {
        $success = $this->mysqli->query("SELECT `text` FROM `texts` WHERE `text` IN ($data);");
        $resultArray = [];
        if ($success) {
            while ($obj = $success->fetch_object()) {
                $resultArray[] = $obj->text;
            }
            return $resultArray;
        } else {
            return false;
        }
    }

    /**
     * Запрос к базе
     *
     * @param string $query - текст запроса
     *
     * @return boolean
     */
    public function query($query)
    {
        $success = $this->mysqli->query($query);
        if ($success) {
            return true;
        }
        return false;
    }

    /**
     * Закрываем базу
     */
    public function __destruct()
    {
        if ($this->mysqli) {
            $this->mysqli->close();
        }
    }
}
?>