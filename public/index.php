<html>
  <head></head>
  <body>
    <form action="" method="POST">
      <input type="text"  size="150" value="<?php echo $_POST['incomingLine']; ?>" name="incomingLine"/>
      <button type="submit">Отправить</button>
      <br/>
      <?php
        require_once "../class/Line.php";
        require_once "../class/DataBase.php";

        if ($_POST && $_POST['incomingLine']) {
            $line = new Line();
            $line->value = $_POST['incomingLine'];
            $testValid  = $line->testValid();
            if ($testValid['result']) {

                $line->getAllGeneratedLines();
                echo "<h2>Сгенерированные строки</h2>";
                echo "<pre>";
                print_r($line->allGeneratedLines);
                echo "</pre>";

                $db = DataBase::getDB();
                $testValuesToUnique = DataBase::prepareData($line->allGeneratedLines, 1);
                $notUniqueValuesArray = $db->testToUnique($testValuesToUnique);
                $linesToInsert = array_diff($line->allGeneratedLines, $notUniqueValuesArray);
                echo "<h2>Строки готовые для вставку в базу</h2>";
                echo "<pre>";
                print_r($linesToInsert);
                echo "</pre>";
                $linesToInsert = DataBase::prepareData($linesToInsert, 2);
                if ($linesToInsert) {
                    $result = $db->query("INSERT INTO `texts` (`text`) VALUES $linesToInsert;");
                    if ($result) {
                        echo '<p>Данные успешно добавлены в таблицу.</p>';
                    } else {
                        echo '<p>Произошла ошибка';
                    }
                }
            } else {
                echo '<b style="color:red">' . $testValid['errorText'] . '</b>';
            }
        } else {
            echo "Введите строку";
        }
        ?>
    </form>
  </body>
</html>