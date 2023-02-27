<?php
require 'lib/controller.php';
require 'models/payrollModel.php';
require 'models/deductionModel.php';
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

    public function print()
    {
        $range = $_POST['date_range'];
        $ex = explode(' - ', $range);
        $from = date('Y-m-d', strtotime($ex[0]));
        $to = date('Y-m-d', strtotime($ex[1]));


        $from_title = date('M d, Y', strtotime($ex[0]));
        $to_title = date('M d, Y', strtotime($ex[1]));

        $data = array(
            'from' => $from,
            'to' => $to
        );
    
        $model = new payrollModel();
		$result = $model->payroll($data);

        $modelde = new deductionModel();
        $em = $modelde->all();

        return $this->controller->view()->render('print_payroll.php',array('result' =>$result,'dec'=>$em , 'from_title'=>$from_title, 'to_title'=>$to_title));

    }

    public function sample()
    {

     return $this->controller->view()->render('sample_print.php');
    }

    public function payslip()
    {
        $id = $_POST['id'];
        $model = new payrollModel();
        $getId = $model->getId($id);
        echo json_encode($getId);
    }

   

}


?>