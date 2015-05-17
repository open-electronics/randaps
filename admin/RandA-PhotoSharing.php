<?php

	require_once "common/db_class.php";
	
	new RandAPS($_POST);
	
	class RandAPS {
	
		protected $db;
		protected $parameters;
	
		function __construct ($params) {
		
			$this->db = new DataB();
			$this->db->OpenDb();
			
			$this->parameters = $params;
			
			$this->{$this->parameters["Command"]}();
		
		}
	
		protected function login() {
		
			$pass = "";
		
			$sql = "SELECT Password FROM users WHERE User = '".$this->parameters["User"]."'";
			$result = $this->db->QueryDb($sql);
			while($row = $this->db->FetchArray($result)) {
				$pass =  $row['Password'];
			}
			
			if($pass == md5($this->parameters["Password"])) {
				@session_start();
				$_SESSION['User'] = $this->parameters["User"];
				echo 0;
			} else {
				echo 1;
			}
		
		}
	
		protected function logout() {
		
			@session_start();
			$_SESSION['User'] = "";
		
		}
		
		protected function saveSettings() {
		
			foreach($this->parameters["Settings"] as $key=>$value) {
				$value = stripslashes($value);
				$value = str_replace("'", "''", $value);
				$sql = "UPDATE settings SET Value = '".$value."' WHERE Tag = '".$key."'";
				$result = $this->db->QueryDb($sql);
			}
		
		}
		
		protected function loadSettings() {
		
			$response = array();
		
			$sql = "SELECT Tag, Value FROM settings";
			$result = $this->db->QueryDb($sql);
			while($row = $this->db->FetchArray($result)) {
				$response[$row['Tag']] = trim($row['Value']);
			}

			echo json_encode($response);
		
		}
		
		protected function updatePhotos() {
		
			$table = "
			<table class='striped'>
				<thead>
					<tr>
						<th data-field='DateTime'>Date/Time</th>
						<th data-field='Social'>Social</th>
						<th data-field='eMail'>eMail</th>
					</tr>
				</thead>
				<tbody>
			";
		
			$sql = "SELECT File, DateTime, Social, eMail FROM photos ORDER BY DateTime DESC";
			$result = $this->db->QueryDb($sql);
			while($row = $this->db->FetchArray($result)) {
				$table .= "
					<tr>
						<td>".(trim($row['File']) != "" ? "<a href = '../photos/".$row['File']."' target = '_blank'>".$row['DateTime']."</a>" : $row['DateTime'])."</td>
						<td>".($row['Social'] == 1 ? "<i class='mdi-navigation-check'></i>" : "<i class='mdi-navigation-close'></i>")."</td>
						<td>".$row['eMail']."</td>
					</tr>
				";
			}

			$table .= "
				</tbody>
			</table>
			";
			
			echo $table;
		
		}
		
		protected function deleteAll() {

			$sql = "DELETE FROM photos";
			$result = $this->db->QueryDb($sql);

			$file = popen("sudo rm /var/www/randaps/photos/*", "r");
			pclose($file);
		
		}
		
		protected function addTheme() {

			$sql = "INSERT INTO themes (Value, Description) VALUES ('".trim($this->parameters["Value"])."', '".trim($this->parameters["Description"])."')";
			$result = $this->db->QueryDb($sql);
		
		}
		
		protected function deleteTheme() {

			$sql = "DELETE FROM themes WHERE Value = '".trim($this->parameters["Value"])."'";
			$result = $this->db->QueryDb($sql);
		
		}
		
		protected function loadThemes() {
		
			$response = array();
		
			$sql = "SELECT Value, Description FROM themes";
			$result = $this->db->QueryDb($sql);
			while($row = $this->db->FetchArray($result)) {
				$response[] = array(
				'Value' => $row['Value'],
				'Description' => trim($row['Description'])
				);
			}

			echo json_encode($response);
		
		}

	}

?>