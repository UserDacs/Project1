<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use DB;
use Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function printExcel($rows,$title)
    {
        header('Content-type: application/excel');
        $filename = $title.'.xls';
        header('Content-Disposition: attachment; filename='.$filename);

            $data = '<html xmlns:x="urn:schemas-microsoft-com:office:excel">
            <head>
                <!--[if gte mso 9]>
                <xml>
                    <x:ExcelWorkbook>
                        <x:ExcelWorksheets>
                            <x:ExcelWorksheet>
                                <x:Name>Sheet 1</x:Name>
                                <x:WorksheetOptions>
                                    <x:Print>
                                        <x:ValidPrinterInfo/>
                                    </x:Print>
                                </x:WorksheetOptions>
                            </x:ExcelWorksheet>
                        </x:ExcelWorksheets>
                    </x:ExcelWorkbook>
                </xml>
                <![endif]-->
            </head>

            <body>
            <table>';
            $data .= '<tr style="border-bottom:2px solid;">
            <td>NAME</td>
            <td>DATE</td>
            <td>TIME IN</td>
            <td>TIME OUT</td>
            </tr>';
            foreach($rows as $row){
            $data .= '<tr>
                    <td>'.$row->name.'</td>
                    <td>'.$row->date_created.'</td>
                    <td>'.$row->timein.'</td>
                    <td>'.$row->timeout.'</td>
                    </tr>';
            }
            $data .= '</table>
            </body></html>';

            echo $data;
 
            
    }
    public function positions()
    {
        $data = DB::table('emp_positions');
       
        if(isset($_GET['name_search'])){
            if($_GET['name_search'] != ''){
                $data = $data->where('emp_title','like','%'.$_GET['name_search'].'%');
            }
        }

        $data = $data->paginate(10);
        
        $rows['data'] = $data;
        $blinds = array();
        return view('settings.position')->with('data',$rows);
            
    }
    public function userLogin()
    {
            
    }
    public function qrScan()
    {
        return view('qrscan');
    }
    public function Scanner(){

        return view('qrscan');
    }
    public function myProfile(){

        $data = DB::table('users')->where('id',Auth::user()->id)->first();
        

        $rows['info'] = $data;

        return view('users.profile')->with('data',$rows);
    }
    public function myAttendance(){
        $rows['blinds'] = array();

        $data = DB::table('timein')
        ->leftJoin('users', 'timein.userId', '=', 'users.id')
        ->where('users.id',Auth::user()->id)
        ->orderby('timein.user_id','desc');
        if(isset($_GET['date_range'])){
            $dates = explode('-',$_GET['date_range']);
            $date_from = explode('/',$dates[0]);
            $date_from = str_replace(' ','',$date_from[2]).'-'.str_replace(' ','',$date_from[0]).'-'.$date_from[1];
            $date_to = explode('/',$dates[1]);
            $date_to = $date_to[2].'-'.str_replace(' ','',$date_to[0]).'-'.$date_to[1];

            $data = $data->whereBetween('date_created', [$date_from.' 00:00:00', $date_to.' 23:59:59']);
        }
        if(isset($_GET['name_search'])){
            if($_GET['name_search'] != ''){
                $data = $data->where('users.name','like','%'.$_GET['name_search'].'%');
            }
        }
      
                if(isset($_GET['name_search'])){
                             $data = $data->get();

                }else{
                            $data = $data->paginate(10);

                }


        
        $rows['data'] = $data;
        return view('myAtt')->with('data',$rows);
    }
    public function update_ranks(Request $req){

        $data = $req->input();
            unset($data['_token']);
            $users = DB::table('ranks')->where('rank_id', $data['rank_id'])->update($data);
            $ret['status'] = 'success';
            $ret['mesg'] = 'Rank Successfully updated!';
        return $ret;

    }
    public function pointsGenerator(){
          $data['data'] = DB::table('ranks')
         ->leftJoin('users', 'users.id', '=', 'ranks.user_id')
        ->where('user_access','regular')
        ->whereNotNull('total_points')
        ->orderby('total_points','desc')
        ->paginate(20);

        return view('points')->with('data',$data);
    }

    public function checkSeck(){
        $rows['blinds'] = DB::table('blinds')
                         ->orderby('blind_id','desc')
                         ->get();

        $data = DB::table('timein')
        ->leftJoin('users', 'timein.userId', '=', 'users.id')
        ->orderby('timein.user_id','desc');
        if(isset($_GET['date_range'])){
            $dates = explode('-',$_GET['date_range']);
            $date_from = explode('/',$dates[0]);
            $date_from = str_replace(' ','',$date_from[2]).'-'.str_replace(' ','',$date_from[0]).'-'.$date_from[1];
            $date_to = explode('/',$dates[1]);
            $date_to = $date_to[2].'-'.str_replace(' ','',$date_to[0]).'-'.$date_to[1];

            $data = $data->whereBetween('date_created', [$date_from.' 00:00:00', $date_to.' 23:59:59']);
        }
        if(isset($_GET['name_search'])){
            if($_GET['name_search'] != ''){
                $data = $data->where('users.name','like','%'.$_GET['name_search'].'%');
            }
        }
        if(isset($_GET['blinds'])){
            if($_GET['blinds'] != ''){
                $data = $data->where('timein.blinds','like','%'.$_GET['blinds'].'%');
            }
        }
        $data = $data->get();
        
        $rows['data'] = $data;
        return view('timein2')->with('data',$rows);

    }
    public function userslists()
    {
        $data = DB::table('users')
        ->where('user_access','regular')
        ->orderby('sit_status','desc');
        if(isset($_GET['name_search'])){
            if($_GET['name_search'] != ''){
                $data = $data->where('users.name','like','%'.$_GET['name_search'].'%');
            }
        }

        $data = $data->paginate(10);
        
        $rows['data'] = $data;
        $blinds = array();
        $rows['blinds'] = $blinds;
        return view('users')->with('data',$rows);
    }

    public function index()
    {
        $data = DB::table('users')
        ->where('user_access','regular')
        ->orderby('id','desc');
        if(isset($_GET['name_search'])){
            if($_GET['name_search'] != ''){
                $data = $data->where('users.name','like','%'.$_GET['name_search'].'%');
            }
        }

        $data = $data->paginate(10);
        
        $rows['data'] = $data;
        $blinds = array();
        return view('home')->with('data',$rows);
    }
    
    public function employees()
    {
        $data = DB::table('users')
        ->where('user_access','regular')
        ->where('user_head', Auth::user()->id)

        ->orderby('id','desc');
        if(isset($_GET['name_search'])){
            if($_GET['name_search'] != ''){
                $data = $data->where('users.name','like','%'.$_GET['name_search'].'%');
            }
        }

        $data = $data->paginate(10);
        
        $rows['data'] = $data;
        $blinds = array();
        return view('employees')->with('data',$rows);
    }
    public function register()
    {
        $data = DB::table('users')
        ->where('user_access','regular')
        ->orderby('id','desc')->paginate(10);
        
        $rows['data'] = $data;
        $blinds = DB::table('blinds')
        ->orderby('blind_id','desc')->get();
        $rows['blinds'] = $blinds;
        return view('registration')->with('data',$rows);
    }
    public function getBlinds(){
        $blinds = DB::table('blinds')
        ->select('blinds.blind_id as value')
        ->addSelect('blinds.blind_type as text')
        ->orderby('blind_id','desc')->get();
        $rows['blinds'] = $blinds;

        return $blinds;
        
    }
    public function addBlinds(Request $request){

        $ifExist = DB::table('blinds')->where('blind_type',$request->blinds)->count();
        $error = 0;
        $checkinput = $request->input();
    
        if($ifExist > 0){
            $error++;
            $return['status'] = 'failed';
            $return['mesg'] = 'Blind already exist!';
        }
        
    
        if($error == 0){
            $return['status'] = 'success';
            $return['mesg'] = 'Successfully Added';
            unset($checkinput['_token']);
            $checkinput['date_created'] = date('Y-m-d H:i:s');
    
    
             DB::table('blinds')->insert($checkinput);
    
        }
        return $return;
    
    
    }
    public function blinds()
    {
        $data = DB::table('blinds')
        ->orderby('blind_id','desc')->paginate(10);
        
        $rows['data'] = $data;
        return view('blinds')->with('data',$rows);
    }
     public function loyaltyCard($id)
    {
             $data = DB::table('users')->where('id',$id)->first();


        $rows['info'] = $data;
  
                
        return view('print.landscape')->with('data',$rows);
    }
    public function activeTable()
    {
        $data = DB::table('active_table')
        ->orderby('table_id','desc')->get();
        
        $rows['data'] = $data;
        return view('active_table')->with('data',$rows);
    }
    public function updateBlinds(Request $req){
    	$data = $req->input();
    	unset($data['_token']);
    	$users = DB::table('blinds')->where('blind_id', $data['blind_id'])->update($data);
    	$ret['status'] = 'success';
    	$ret['mesg'] = 'Blinds Successfully updated!';
    	return $ret;
    }
    public function attendance(){
        $rows['blinds'] = array();

        $data = DB::table('timein')
        ->leftJoin('users', 'timein.userId', '=', 'users.id')
        ->where('users.user_head', Auth::user()->id)
        ->orderby('timein.user_id','desc');
        if(isset($_GET['date_range'])){
            $dates = explode('-',$_GET['date_range']);
            $date_from = explode('/',$dates[0]);
            $date_from = str_replace(' ','',$date_from[2]).'-'.str_replace(' ','',$date_from[0]).'-'.$date_from[1];
            $date_to = explode('/',$dates[1]);
            $date_to = $date_to[2].'-'.str_replace(' ','',$date_to[0]).'-'.$date_to[1];

            $data = $data->whereBetween('date_created', [$date_from.' 00:00:00', $date_to.' 23:59:59']);
        }
        if(isset($_GET['name_search'])){
            if($_GET['name_search'] != ''){
                $data = $data->where('users.name','like','%'.$_GET['name_search'].'%');
            }
        }
      
                if(isset($_GET['name_search'])){
                             $data = $data->get();

                }else{
                            $data = $data->paginate(10);

                }


        
        $rows['data'] = $data;
        return view('attendance')->with('data',$rows);
    }
    public function adminuser()
    {
        $data = DB::table('users')
        ->orderby('id','desc')
        ->where('user_access','admin')
        ->orWhere('user_access','manager')
        ->paginate(10);
        
        $rows['data'] = $data;
        return view('admin')->with('data',$rows);
    }
    public function timein()
    {
        $rows['blinds'] = array();

        $data = DB::table('timein')
        ->leftJoin('users', 'timein.userId', '=', 'users.id')
        ->orderby('timein.user_id','desc');
        if(isset($_GET['date_range'])){
            $dates = explode('-',$_GET['date_range']);
            $date_from = explode('/',$dates[0]);
            $date_from = str_replace(' ','',$date_from[2]).'-'.str_replace(' ','',$date_from[0]).'-'.$date_from[1];
            $date_to = explode('/',$dates[1]);
            $date_to = $date_to[2].'-'.str_replace(' ','',$date_to[0]).'-'.$date_to[1];

            $data = $data->whereBetween('date_created', [$date_from.' 00:00:00', $date_to.' 23:59:59']);
        }
        if(isset($_GET['name_search'])){
            if($_GET['name_search'] != ''){
                $data = $data->where('users.name','like','%'.$_GET['name_search'].'%');
            }
        }
      
                if(isset($_GET['name_search'])){
                             $data = $data->get();

                }else{
                            $data = $data->paginate(10);

                }


        
        $rows['data'] = $data;
        return view('timein')->with('data',$rows);
    }


    public function timein_mobile_display()
    {
        $data = DB::table('timein')
        ->join('users', 'timein.userId', '=', 'users.id')
        ->orderby('timein.user_id','desc')
        ->get();

        

        $yourJson = trim(json_encode($data), '[]');

       
        return view('attend');
    }



    public function exportExcel()
    {
      

        $rows['blinds'] = array();
        $data = DB::table('timein')
        ->leftJoin('users', 'timein.userId', '=', 'users.id')
        ->orderby('timein.user_id','desc');
        if(isset($_GET['date_range'])){
            $dates = explode('-',$_GET['date_range']);
            $date_from = explode('/',$dates[0]);
            $date_from = str_replace(' ','',$date_from[2]).'-'.str_replace(' ','',$date_from[0]).'-'.$date_from[1];
            $date_to = explode('/',$dates[1]);
            $date_to = $date_to[2].'-'.str_replace(' ','',$date_to[0]).'-'.$date_to[1];

            $data = $data->whereBetween('date_created', [$date_from.' 00:00:00', $date_to.' 23:59:59']);
        }
        if(isset($_GET['name_search'])){
            if($_GET['name_search'] != ''){
                $data = $data->where('users.name','like','%'.$_GET['name_search'].'%');
            }
        }
      
        $data = $data->get();


        
        $rows['data'] = $data;
        $this->printExcel($data,'attendance-'.date('Y-m-d H:i:s'));

    }
       public function timeinFilter(Request $req)
    {
        $data = DB::table('timein')
        ->leftJoin('users', 'timein.userId', '=', 'users.id')
        ->orderby('timein.user_id','desc')
        ->paginate(10);
        
        $rows['data'] = $data;
        return $req;
    }
}
