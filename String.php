<?php

/**
 * Util string mainpulation
 *
 * @author marcelo
 */
class App_String
{

    private static $_lowerUpper = array(
        'ç' => 'Ç',
        'à' => 'À', 'á' => 'Á', 'ã' => 'Â', 'ã' => 'Ã', 'â' => 'Â',
        'è' => 'è', 'é' => 'É', 'ê' => 'Ê', 'ë' => 'Ë',
        'ì' => 'Ì', 'í' => 'Í', 'î' => 'Î', 'ï' => 'Ï',
        'ò' => 'Ò', 'ó' => 'Ó', 'ô' => 'Ô', 'ö' => 'Ö',
        'ù' => 'Ù', 'ú' => 'Ú', 'û' => 'Û',
    );
    private static $_accents = array(
        'c' => array('ç'),
        'a' => array('à', 'á', 'ã', 'ã', 'â'),
        'e' => array('è', 'é', 'ê', 'ë'),
        'i' => array('ì', 'í', 'î', 'ï'),
        'o' => array('ò', 'ó', 'ô', 'ö'),
        'u' => array('ù', 'ú', 'û', 'ü'),
    );

    /**
     * Array to url string
     *
     * Each element of an array is a url part (separeted with a slash)
     * Calling this function and passing as parameter
     * array('One Nice', 'Example') will return '/one-nice/example'
     *
     * @param array $strings
     * @return string
     */
    public static function arrayToUrl(array $strings)
    {
        foreach ($strings as $key => $string) {
            $strings[$key] = self::toUrl($string);
        }
        $string = '/' . implode('/', $strings);
        return $string;
    }

    /**
     * Convert to the url format of a string
     * @param string $string
     * @return string
     */
    public static function toUrl($string, $spaceReplacement = '-')
    {
        $string = self::removeAccents($string);
        $string = self::spacesTo($string, $spaceReplacement);
        $string = self::toLowerCase($string, $spaceReplacement);
        return $string;
    }

    /**
     * remove accents from a string
     * @param string $string
     * @return string
     */
    public static function removeAccents($string)
    {
        foreach (self::$_accents as $char => $accents) {
            $pattern = '(' . implode('|', $accents) . ')';

            $upperPattern = self::toUpperCase($pattern);
            $upperChar = strtoupper($char);

            $string = preg_replace($pattern, $char, $string);
            $string = preg_replace($upperPattern, $upperChar, $string);
        }
        return $string;
    }

    /**
     * To lower case
     * @param string $string
     * @return string
     */
    public static function toLowerCase($string)
    {
        $string = strtolower($string);
        $upper = array_values(self::$_lowerUpper);
        $lower = array_keys(self::$_lowerUpper);
        $string = str_replace($upper, $lower, $string);
        return $string;
    }

    /**
     * To upper case
     * @param string $string
     * @return string
     */
    public static function toUpperCase($string)
    {
        $string = strtoupper($string);
        $upper = array_values(self::$_lowerUpper);
        $lower = array_keys(self::$_lowerUpper);
        $string = str_replace($lower, $upper, $string);
        return $string;
    }

    /**
     * To lower case
     * @param string $string
     * @return string
     */
    public static function spacesTo($string, $spaceReplacement = '-')
    {
        return preg_replace("/\s+/", $spaceReplacement, $string);
    }

}