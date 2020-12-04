<?php
class Line
{
    public $value;
    public $allGeneratedLines = [];

    /**
     * Функция формирует массив ответа с ошибкой
     *
     * @param sting $sting - строка с текстом ошибки
     *
     * @return string
     */
    private function _badResult($sting)
    {
        return [
            "result" => false,
            "errorText" => $sting
        ];
    }

    /**
     * Функция проверяет на валидность введенной строки
     *
     * @return array
     */
    public function testValid()
    {
        $string = $this->value;
        $symbolStack = [];
        for ($i = 0; $i < strlen($string); $i++) {
                $char = $string[$i];

            if (in_array($char, ["<",">",":"])) {
                if ($char ==">") {
                    if (empty($symbolStack)) {
                        return self::_badResult("Лишний символ >");
                    }
                    $topSymbolInStack = array_pop($symbolStack);
                    if ($topSymbolInStack != "<") {
                        return self::_badResult("Лишний символ <");
                    }
                } elseif ($char =="<") {
                    array_push($symbolStack, "<");
                } elseif (($char ==":")) {
                    if (empty($symbolStack)) {
                        return self::_badResult(": не в скобках");
                    }
                    $beforeChar = $string[ $i-1 ];
                    $afterChar = $string [ $i+1 ];
                    if ($beforeChar ==":" or $afterChar ==":") {
                        if ($beforeChar ==":" and $afterChar ==":") {
                            return self::_badResult("Более чем двойная :");
                        }
                    } else {
                        return self::_badResult("Одинарная :");
                    }
                }
            }
        }
        if (count($symbolStack) == 0) {
            return [
              "result" => true,
              "errorText" => ""
            ];
        } else {
            return self::_badResult("Лишние скобки");
        }
    }

    /**
     * Функция запускает генерацию массива строк
     *
     * @return null
     */
    public function getAllGeneratedLines()
    {
        self::_getLines($this->value);
        $this->allGeneratedLines = array_unique($this->allGeneratedLines);
    }

    /**
     * Функция формирования массива строк
     *
     * @param sting $lineToParse - строка шаблоном
     *
     * @return null
     */
    private function _getLines($lineToParse)
    {
        $temp_lines = array();
        if (preg_match("~\<([^<>]+)\>~siU", $lineToParse, $matches)) {
            $currentValues = explode("::", $matches[1]);
            foreach ($currentValues as $var) {
                $temp_lines[] = str_replace('<' . $matches[1] . '>', $var, $lineToParse);
            }
            foreach ($temp_lines as $currentStr) {
                if (preg_match("~\<([^<>]+)\>~siU", $currentStr, $matches)) {
                    self::_getLines($currentStr);
                } else {
                    array_push($this->allGeneratedLines, $currentStr);
                }
            }

        } else {
            array_push($this->allGeneratedLines, $lineToParse);
        }
    }


}

?>