<?php
class Log {
	private $handle;

	public function __construct($filename) {
		$this->handle = fopen(DIR_LOGS . $filename, 'a');
	}


	public function debug2($output1, $output2 = '!"£$%^&*') {
		$file = DIR_LOGS . 'debug.txt';
		
		$handle = fopen($file, 'a+'); 
		
		if(is_array($output1)) {
			fwrite($handle, date('Y-m-d H:i:s') . ' - ' . $this->var_debug($output1) . "\n");
		} else {
			fwrite($handle, date('Y-m-d H:i:s') . ' - ' . $this->var_debug($output1) . "\n");
		}
		
		if($output2 != '!"£$%^&*') {
			$this->debug2($output2);
		}
			
		fclose($handle); 
	}
	
	public function debug($output1, $output2 = '!"£$%^&*') {
		$file = DIR_LOGS . 'debug.txt';
		
		$handle = fopen($file, 'a+'); 
		
		if(is_array($output1)) {
			fwrite($handle, date('Y-m-d H:i:s') . ' - ' . print_r($output1, true) . "\n");
		} else {
			if($output1 === false) {
				$output1 = '(boolean FALSE)';
			} elseif($output1 === true) {
				$output1 = '(boolean TRUE)';
			} elseif($output1 === null) {
				$output1 = '(NULL)';
			} elseif($output1 === '') {
				$output1 = '(\'\')';
			}
			fwrite($handle, date('Y-m-d H:i:s') . ' - ' . $output1 . "\n");
		}
		
		if($output2 != '!"£$%^&*') {
			$this->debug($output2);
		}
			
		fclose($handle); 
	}
	
	public function resetDebug() {
		if(file_exists(DIR_LOGS . 'debug.txt')) {
			unlink(DIR_LOGS . 'debug.txt');
		}
	}

	public function displayErrors() {
		error_reporting(E_ALL); 
		ini_set('display_errors', 1);
	}
	

// This function has been adapted from the script provided by Maurits van der Schee at
// http://www.leaseweblabs.com/2013/10/smart-alternative-phps-var_dump-function/

	public function var_debug($variable,$strlen=100,$width=25,$depth=10,$i=0,&$objects = array()) {
	  $search = array("\0", "\a", "\b", "\f", "\n", "\r", "\t", "\v");
	  $replace = array('\0', '\a', '\b', '\f', '\n', '\r', '\t', '\v');
	 
	  $string = '';
	 
	  switch(gettype($variable)) {
		case 'boolean':      $string.= $variable?'true':'false'; break;
		case 'integer':      $string.= $variable;                break;
		case 'double':       $string.= $variable;                break;
		case 'resource':     $string.= '[resource]';             break;
		case 'NULL':         $string.= "null";                   break;
		case 'unknown type': $string.= '???';                    break;
		case 'string':
		  $len = strlen($variable);
		  $variable = str_replace($search,$replace,substr($variable,0,$strlen),$count);
		  $variable = substr($variable,0,$strlen);
		  if ($len<$strlen) $string.= '"'.$variable.'"';
		  else $string.= 'string('.$len.'): "'.$variable.'"...';
		  break;
		case 'array':
		  $len = count($variable);
		  if ($i==$depth) $string.= 'array('.$len.') {...}';
		  elseif(!$len) $string.= 'array(0) {}';
		  else {
			$keys = array_keys($variable);
			$spaces = str_repeat(' ',$i*2);
			$string.= "array($len)\n".$spaces.'{';
			$count=0;
			foreach($keys as $key) {
			  if ($count==$width) {
				$string.= "\n".$spaces."  ...";
				break;
			  }
			  $string.= "\n".$spaces."  [$key] => ";
			  $string.= $this->var_debug($variable[$key],$strlen,$width,$depth,$i+1,$objects);
			  $count++;
			}
			$string.="\n".$spaces.'}';
		  }
		  break;
		case 'object':
		  $id = array_search($variable,$objects,true);
		  if ($id!==false)
			$string.=get_class($variable).'#'.($id+1).' {...}';
		  else if($i==$depth)
			$string.=get_class($variable).' {...}';
		  else {
			$id = array_push($objects,$variable);
			$array = (array)$variable;
			$spaces = str_repeat(' ',$i*2);
			$string.= get_class($variable)."#$id\n".$spaces.'{';
			$properties = array_keys($array);
			foreach($properties as $property) {
			  $name = str_replace("\0",':',trim($property));
			  $string.= "\n".$spaces."  [$name] => ";
			  $string.= $this->var_debug($array[$property],$strlen,$width,$depth,$i+1,$objects);
			}
			$string.= "\n".$spaces.'}';
		  }
		  break;
	  }
	 	 
	  return $string;
	}	
		
	public function write($message) {
		fwrite($this->handle, date('Y-m-d G:i:s') . ' - ' . print_r($message, true) . "\n");
	}

	public function __destruct() {
		fclose($this->handle);
	}
}