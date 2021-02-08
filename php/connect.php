<?php 

namespace db;

class connect {

	protected $host = 'host';
	protected $port = 'port';
	public    $dbname = 'dbname';
	protected $user = 'user';
	protected $password = 'password';

	public function setdbname($a){
		$this->dbname = $a;
	}

	public function getdbname(){
		return $this->dbname;
	}

	public function open (){

		$conn = mysqli_connect($this->host,$this->user,$this->password,$this->dbname);

		if (!$conn) {
    		die("Connection failed: " . mysqli_connect_error());
		}

		return $conn;
	}

}

 ?>