<?php
class ModelExtensionSmsoValidator extends Model
{

	public static function isEmpty($str)
    {
    	return (empty($str) ? false : true);
    }
 	public static function isPhoneNumber($number)
    {
        if(empty($number))
          return false;
        $result = !empty($number) && preg_match('/^[+0-9. ()-]*$/', $number);
        return $result;
    }
    public static function isEmail($email)
    {
        if(empty($email))
          return false;
        $result =  !empty($email) && preg_match('/^[a-z\p{L}0-9!#$%&\'*+\/=?^`{}|~_-]+[.a-z\p{L}0-9!#$%&\'*+\/=?^`{}|~_-]*@[a-z\p{L}0-9]+(?:[.]?[_a-z\p{L}0-9-])*\.[a-z\p{L}0-9]+$/ui', $email);
        return $result;
    }
    public static function isPasswd($passwd, $size = 5)
    {
        if(empty($passwd))
          return false;
        if(empty($passwd))
          return false;
        $result = !empty($passwd) && (strlen($passwd) >= $size && strlen($passwd) < 255);         
        return $result;
    }
    public static function isName($name)
    {
        if(empty($name))
          return false;
        $result = !empty($name) && preg_match('/^[^0-9!<>,;?=+()@#"°{}_$%:¤|]*$/u', stripslashes($name));
        return $result;
    }
    public static function isDate($date)
    {
        if(empty($date))
          return false;
        if (empty($date) || !preg_match('/^([0-9]{4})-((?:0?[0-9])|(?:1[0-2]))-((?:0?[0-9])|(?:[1-2][0-9])|(?:3[01]))( [0-9]{2}:[0-9]{2}:[0-9]{2})?$/', $date, $matches)) {
            return false;
        }
        $result = checkdate((int)$matches[2], (int)$matches[3], (int)$matches[1]);
        return $result;
    }
    public static function isUnsignedInt($value)
    {
        if(empty($value))
          return false;
        return ((string)(int)$value === (string)$value && $value < 4294967296 && $value >= 0);
    }
    public static function isInt($value)
    {
        if(empty($value))
          return false;
        if (!is_scalar($value)) {
            return false;
        }
        return ((string)(int)$value === (string)$value || $value === false);
    }
}