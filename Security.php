<?php

class App_Security
{

    /**
     * Get string and return a hash
     * @param string $string
     * @return string
     */
    public static function stringToPasswordHash($string)
    {
        if (strlen($string) != 40) {
            $string = sha1($string);
        }
        $salt = self::getPasswordSalt();
        $hash = sha1($salt . $string . $salt);
        return $hash;
    }

    /**
     * Get password salt
     * @return string
     */
    public static function getPasswordSalt()
    {
        return Zend_Registry::get('securitySalt');
    }

    /**
     * Generate a new Token
     * @param string $tokenString string to modify token
     * @return string
     */
    public static function getNewToken($tokenString = '')
    {
        $tokenString .= self::getPasswordSalt();
        return sha1($tokenString . time() . $tokenString);
    }

    /**
     * Get confirmation for a token
     * @param string $stroken
     * @return string
     */
    public static function getTokenConfirmation($token)
    {
        return sha1(self::getPasswordSalt() . $token . 'confirmation');
    }

}

