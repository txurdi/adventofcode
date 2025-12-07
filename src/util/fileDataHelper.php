<?php

namespace App\util;

class fileDataHelper
{
    public const DATA_FORMAT_STRING = 1;
    public const DATA_FORMAT_LINES = 1;
    public const DATA_FORMAT_COLS = 2;
    public const DATA_FORMAT_CHARS = 3;
    /**
     * Return the content of a file as a string
     * @param string $filePath
     * @return string
     */
    public static function fileToString(string $filePath): string
    {
        return file_get_contents($filePath);
    }

    /**
     * Return the content of a file as an array of lines (each line is a string)
     * @param string $filePath
     * @param int|null $maxLine
     * @return array
     */
    public static function fileToArrayByLine(string $filePath, ?int $maxLine=0): array
    {
        $result = [];
        $arc = fopen($filePath,"r");
        $numLine = 1;
        while(! feof($arc))  {
            if ($maxLine && ($numLine > $maxLine)) break;
            $result[$numLine] = fgets($arc);
            $numLine++;
        }
        fclose($arc);
        return $result;
    }

    /**
     * Return the content of a file as an array of line and columns (each line is an array)
     * @param string $filePath
     * @param int|null $maxLine
     * @return array
     */
    public static function fileToArrayByLineAndCol(string $filePath, ?int $maxLine=0): array
    {
        $result = [];
        $arc = fopen($filePath,"r");
        $numLine = 1;
        while(! feof($arc))  {
            if ($maxLine && ($numLine > $maxLine)) break;
            $linea = fgets($arc);
            //$keywords = preg_split("/[\s,]+/", "hypertext language, programming");
            //$result[$numLine] = explode(' ',(trim($linea)));
            $result[$numLine] = preg_split("/[\s,]+/", (trim($linea)));
            $numLine++;
        }
        fclose($arc);
        return $result;
    }

    /**
     * Return the content of a file as an array of line and columns (each line is an array of chars)
     * @param string $filePath
     * @param int|null $maxLine
     * @return array
     */
    public static function fileToArrayByLineAndChar(string $filePath, ?int $maxLine=0): array
    {
        $result = [];
        $arc = fopen($filePath,"r");
        $numLine = 0;
        while(! feof($arc))  {
            if ($maxLine && ($numLine > $maxLine)) break;
            $linea = fgets($arc);
            $lineaArray = str_split(trim($linea));
            $result[$numLine] = $lineaArray;
            $numLine++;
        }
        fclose($arc);
        return $result;
    }
}
