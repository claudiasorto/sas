<?php

################################################
# CLASE Y LIBRERIA PARA ACCESO A BASE DE DATOS #
#  02/2011 Miguel Romero mikeromero21@gmail.com  #
################################################
	
class DBX{
	var $dbhost='localhost';
	/*var $dbuser = 'root';
	var $dbpass = '';
	var $dbname =  'sistemaskycom050918';*/
	var $dbuser='mostechnologies';
	var $dbpass='mostechnologies';
	var $dbname='sistemaskycom050918';
		
	var $numrows;
	var $columnsInfo;
	var $insertID;
	var $affectedRows;
	var $dblink;
	var $dbsel;
	var $Error;
	var $errordesc;
	var $UserDat;
	var $sesST;

	function DBX(){ // Constructor
	   $sesST = session_id();
       if ($sesST == '') session_start();
	  		
		$this->dblink=mysqli_connect($this->dbhost,$this->dbuser,$this->dbpass,$this->dbname) or $this->eDie();
		//$this->dbsel=mysqli_select_db($this->dbname,$this->dblink) or $this->eDie();
		mysqli_query ($this->dblink,"SET NAMES 'utf8'");
		mysqli_query ($this->dblink,"SET CHARACTER SET 'utf8'");
		
	}

	function eDie(){ //Mensaje de error si falla conexi�n a la base de datos
		$this->Error=mysqli_error($this->dblink);
		die($this->Error);
	}

	function close(){ //Cierra la conexci�n 
		mysqli_close($this->dblink);
		session_destroy();
	}

	function selSql($query){ //Ejecuta la consulta(select) enviada y devuelve un array de resultados
		//$rs=mysqli_query($query,$this->dblink) or $this->eDie();
		$rs=mysqli_query($this->dblink,$query) or $this->eDie();
		$this->numrows=mysqli_num_rows($rs);
		$aRows=array();
		while($rows=mysqli_fetch_assoc($rs)){
			$aRows[]=$rows;
		}
		
		/* INVENTORY*/
	 	$this->columnsInfo=mysqli_fetch_field($rs);	
		 
		 /*
		    $fields = mysql_fetch_fields('table');
			foreach ($fields as $key => $field) {
        	echo $field->name . ' ' . $field->definition . "\n";
			}
			 
		 */
		
		
		return $aRows;
	}

	function insSql($query){ //Ejecuta la consulta(insert) para ingreso de datos
		$rs=mysqli_query($this->dblink,$query) or $this->eDie();
		$this->insertID=mysqli_insert_id($this->dblink);
	}

	function updSql($query){ //Ejecuta la consulta(update) 
		$rs=mysqli_query($this->dblink,$query) or $this->eDie();
		$this->affectedRows=mysqli_affected_rows($this->dblink);
	}
	
	function rmvChars($txt){
	  $texto = str_replace('á','&aacute;',$txt);
	  $texto = str_replace('é','&eacute;',$texto);
	  $texto = str_replace('í','&iacute;',$texto);
	  $texto = str_replace('ó','&oacute;',$texto);
	  $texto = str_replace('ú','&uacute;',$texto);
	  
	  $texto = str_replace('ñ','&ntilde;',$texto);
	  $texto = str_replace('Ñ','&Ntilde;',$texto);
	  $texto = str_replace("\n","<br>",$texto);
	  $texto = str_replace('�','&quot;',$texto);
	  $texto = str_replace('�','&quot;',$texto);	  
	  $texto = str_replace('�','-',$texto);
	  
	  return $texto;
	}

	
}
?>