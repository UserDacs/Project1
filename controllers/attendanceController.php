<?php
require 'lib/controller.php';
require 'models/attendanceModel.php';
class attendanceController extends Controller{
    private $controller;
	function __construct()
	{
		$this->controller = new Controller();
	}

    public function index()
    {
        if(!isset($_SESSION['admin']) || trim($_SESSION['admin']) == ''){
			
			header('location: /login');
		}
        $model = new attendanceModel();
        $em = $model->all();
        return $this->controller->view()->render('attendance.php',$em);
    }

    public function store()
    {
        $employee = $_POST['employee'];
		$date = $_POST['date'];
		$time_in = $_POST['time_in'];
		$time_out = $_POST['time_out'];

        $model = new attendanceModel();
		$result = $model->save(array('employee' => $employee,'date' => $date,'time_in'=> $time_in, 'time_out'=> $time_out));

        return $this->controller->view()->route('attendance');

    }

    public function edit()
    {
        $id = $_POST['id'];
        $model = new attendanceModel();
        $getId = $model->getId($id);

        echo json_encode($getId);
    }

    public function update()
    {
        $id = $_POST['id'];
		$date = $_POST['edit_date'];
		$time_in = $_POST['edit_time_in'];
		$time_out = $_POST['edit_time_out'];

      
        $model = new attendanceModel();
		$result = $model->update(array('id' => $id,'edit_date' => $date,'edit_time_in'=> $time_in, 'edit_time_out'=> $time_out));

        return $this->controller->view()->route('attendance');
    }

    public function destroy()
    {
        $id = $_POST['id'];
	
      
        $model = new attendanceModel();
		$result = $model->delete($id);

        return $this->controller->view()->route('attendance');
    }

}


?>