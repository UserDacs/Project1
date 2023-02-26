<?php
require 'lib/controller.php';
require 'models/payrollModel.php';
class payrollController extends Controller{
    private $controller;
	function __construct()
	{
		$this->controller = new Controller();
	}

    public function index()
    {
        $range = isset($_GET['range'])?$_GET['range'] : '';
        $model = new payrollModel();
        $data = [
            'range'=>$range
        ];
        $em = $model->all($data);

        
    
        return $this->controller->view()->render('payroll.php',$em);
    }

    public function store()
    {
        $data = array(
            'title' => $_POST['title'],
            'rate' => $_POST['rate']
         
        );
    
        $model = new payrollModel();
		$result = $model->save($data);

        return $this->controller->view()->route('payroll');

    }

    public function edit()
    {
        $id = $_POST['id'];
        $model = new payrollModel();
        $getId = $model->getId($id);
        echo json_encode($getId);
    }

    public function update()
    {
        $data = array(
            'id' => $_POST['id'],
            'title' => $_POST['title'],
            'rate'=> $_POST['rate']
        );
        $model = new payrollModel();
		$result = $model->update($data);

        return $this->controller->view()->route('payroll');
    }

    public function destroy()
    {
        $id = $_POST['id'];
      
        $model = new payrollModel();
		$result = $model->delete($id);

        return $this->controller->view()->route('payroll');
    }

}


?>