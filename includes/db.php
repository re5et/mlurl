<?php

class db{
	
	var $link, $affected_rows, $insert_id;
		
	var $db_host = false;
	var $db_user = false;
	var $db_pass = false;
	var $db_name = false;
	var $prefix  = false;

	function __construct($options)
	{
		if(is_array($options))
		{
			foreach(array('db_host', 'db_user', 'db_pass', 'db_name', 'prefix') as $var)
			{
				if(array_key_exists($var, $options))
				{
					$this->{$var} = $options[$var];
				}
			}
		}
		$this->link = @mysql_connect($this->db_host, $this->db_user, $this->db_pass);
		$select = @mysql_select_db($this->db_name, $this->link);
		if(!$select)
		{
			$this->link = false;
		}
	}

	function query($query, $report = false)
	{
		$result = mysql_query($query, $this->link);
		$this->affected_rows = mysql_affected_rows($this->link);
		$this->insert_id = mysql_insert_id($this->link);

		if ( ! is_resource($result) )
		{
			if($result == false && $report == true)
			{
				echo mysql_error();
			}
			return $result;
		}
		else
		{
			$numRows = mysql_num_rows($result);
			if($numRows == 0)
			{
				return false;
			}
			$numFields = mysql_num_fields($result);

			for ( $i = 0; $i < $numFields; $i++ )
			{
				$resultFields[] = mysql_field_name($result, $i);
			}

			for ( $i = 0; $i < $numRows; $i++ )
			{
				$resultValues = mysql_fetch_row($result);
				$results[] = array_combine($resultFields, $resultValues);
			}
						
			return $results;
		}
	}
	
	function row($query)
	{
		
		$results = $this->query($query);
		
		if(!$results)
		{
			die('here');
			return false;
		}
			
		if(is_array($results) && is_array($results[0]))
		{
			return $results[0];
		}
		else
		{
			return false;
		}
		
	}
	
	function value($query)
	{
		
		$results = $this->query($query);
		
		if(!$results)
		{
			return false;
		}
		
		if(is_array($results) && is_array($results[0]))
		{
			$result = array_shift(array_values($results[0]));
			return $result;
		}
		else
		{
			return false;
		}
		
	}

	function escape($string)
	{
		return mysql_real_escape_string($string, $this->link);
	}
	
}

?>