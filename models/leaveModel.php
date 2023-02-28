<?php
class leaveModel {
    private $conn;

    public function __construct(){
        $db = new database();
        $this->conn = $db->connection();
    }

    public function all()
    {
        

        $arr = array();
        $sql = "SELECT *, `leave`.id as lid,CONCAT(`employees`.`lastname`,', ',`employees`.`firstname`) as fullname FROM `leave` INNER JOIN employees ON employees.id = `leave`.emp_id";
        $query = $this->conn->query($sql);
        while($row = $query->fetch_assoc()){
            $arr[] = $row;
        }
        $this->conn->close();
        return $arr;
    }

    public function save($param = array())
    {

        $from = $param['from'];
        $to = $param['to'];
		$emp_id = $param['emp_id'];
        $desctiption = $param['desctiption'];
		$type = $param['type'];

		$sql = "INSERT INTO `leave`(`type`, `from_date`, `to_date`, `emp_id`, `status`, `description`) VALUES ('$type','$from','$to','$emp_id','0','$desctiption')";
		if($this->conn->query($sql)){
			$_SESSION['success'] = 'leave added successfully';
		}
		else{
			$_SESSION['error'] = $this->conn->error;
		}
    }

    public function getId($id)
    {
       
		$sql = "SELECT * FROM leave WHERE id = '$id'";
		$query = $this->conn->query($sql);
		$row = $query->fetch_assoc();

    
		return ($row);
    }
    public function update($param = array())
    {
        $id = $param['id'];
		$title = $param['title'];
		$rate = $param['rate'];

		$sql = "UPDATE leave SET description = '$title', rate = '$rate' WHERE id = '$id'";
		if($this->conn->query($sql)){
			$_SESSION['success'] = 'leave updated successfully';
		}
		else{
			$_SESSION['error'] = $this->conn->error;
		}
    }

    public function delete($id)
    {
		$sql = "DELETE FROM leave WHERE id = '$id'";
		if($this->conn->query($sql)){
			$_SESSION['success'] = 'leave deleted successfully';
		}
		else{
			$_SESSION['error'] = $this->conn->error;
		}
    }

}

?>