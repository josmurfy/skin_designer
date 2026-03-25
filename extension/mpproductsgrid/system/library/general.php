<?php

// String

if (!function_exists('mp_strlen')) {
	function mp_strlen(string $string) {
		if (VERSION > '4.0.1.1') {
			return oc_strlen($string);
		} else {
			return \Opencart\System\Helper\Utf8\strlen($string);
		}
	}
}

if (!function_exists('mp_strpos')) {

	function mp_strpos(string $string, string $needle, int $offset = 0) {
		if (VERSION > '4.0.1.1') {
			return oc_strpos($string, $needle, $offset);
		} else {
			return \Opencart\System\Helper\Utf8\strpos($string, $needle, $offset);
		}

	}
}
if (!function_exists('mp_strrpos')) {


	function mp_strrpos(string $string, string $needle, int $offset = 0) {
		if (VERSION > '4.0.1.1') {
			return oc_strrpos($string, $needle, $offset);
		} else {
			return \Opencart\System\Helper\Utf8\strrpos($string, $needle, $offset);
		}
	}
}
if (!function_exists('mp_substr')) {


	function mp_substr(string $string, int $offset, ?int $length = null) {
		if (VERSION > '4.0.1.1') {
			return oc_substr($string, $offset, $length);
		} else {
			return \Opencart\System\Helper\Utf8\substr($string, $offset, $length);
		}
	}


}
if (!function_exists('mp_strtoupper')) {


	function mp_strtoupper(string $string) {
		if (VERSION > '4.0.1.1') {
			return oc_strtoupper($string);
		} else {
			return \Opencart\System\Helper\Utf8\strtoupper($string);
		}
	}


}
if (!function_exists('mp_strtolower')) {

	function mp_strtolower(string $string) {
		if (VERSION > '4.0.1.1') {
			return oc_strtolower($string);
		} else {
			return \Opencart\System\Helper\Utf8\strtolower($string);
		}
	}


}

// Other
if (!function_exists('mp_token')) {
	function mp_token(int $length = 32): string {
		if (VERSION > '4.0.1.1') {
			return oc_token($length);
		} else {
			return token($length);
		}
	}
}



