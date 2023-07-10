<?php

declare(strict_types=1);

class Utils
{


	public static function typeOf($var): string
	{
		switch (gettype($var)) {
			case "array":
				return "array";
			case "integer":
				return "int";
			case "double":
				return "float";
			case "NULL":
				return "null";
			case "boolean":
				return "bool";
			case "object":
				return "object";
			default:
				return "string";
		}
	}


	public static function randToken($bytes = 32): string
	{
		return bin2hex(random_bytes($bytes));
	}

	public static function unsetNullArray(array $arr)
	{
		foreach ($arr as $key => $value) if (Str::isEmptyStr($value)) unset($arr[$key]); // Remove Empty values
		return $arr;
	}

	// Empty String and Array support 
	public static function isNull($var): bool
	{
		if (empty($var) || is_null($var)) return true;
		return false;
	}
}