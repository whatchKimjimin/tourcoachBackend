<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tourdata as TourDataModel;
use App\Http\Controllers\TourController as TourController;

class TestController extends Controller
{
    private $token = "rlawlals";

    public function index(Request $req){

//        //dump($req);
//        $token = $req->input("hub_verify_token");
//
//        $hub = $req->input("hub_challenge");
//
////        ob_start();
////        var_dump($hub);
////        $result = ob_get_clean();
////        \File::put(storage_path()."/text.txt", $result);
//
//        return response($hub, 200);
        return view('test.index');
    }

    public function send(){
        $answer = $_GET['id'];
        $type = $_GET['type'];
        echo "{\"msg\":\"$answer\",\"type\":\"$type\"}";
    }

    public function test(){

//        test$to      = 'kimppangs@gmail.com';
//        $subject = '테스트';
//        $message = rand();
//        $headers = 'From: 투어코치 <no-reply@tourcoach.co.kr>' . "\r\n" .
//            'Reply-To: no-reply@tourcoach.co.kr' . "\r\n" .
//            'X-Mailer: PHP/' . phpversion();
//
//        mail($to, $subject, $message, $headers);
        // 여행지명
        $name = $_GET['location'];
        $tourData = TourDataModel::where('name' , 'LIKE' , '%'.$name.'%')->first();
        $weather =  TourController::weatherCheck($tourData->id,$tourData->vilage,$tourData->city);

        $date = $_GET['date'];
        $msg = $date."에 ".$name."에 날씨는 ".$weather['weather']."도이며 하늘은 ".$weather['sky']."입니다 현재 위치로부터 ".$name."까지 예상 소요시간은 자가용기준 1시간입니다. ";
        echo "{\"id\":\"223\",\"name\":\"$name\",\"weather\":\"23.3\",\"msg\":\"$msg\"}";
    }

    public function t(){

        $data = shell_exec('echo 7월19일에 강원도로 놀러갈건데 코치좀해줘 | mecab');
//        $data = shell_exec("python ko.py '" . "안녕하세요 양영디지털고등학교" . "'");
        $data = trim($data);
        $data = explode("\n",$data);
        print_r($data);

//        $cate = null;
//
//        for($a = 0 ; $a < count($data) ; $a++){
//            $data[$a] = explode(",",$data[$a]);
//            $data[$a][0] = explode("\t",$data[$a][0]);
//
//
//            if($data[$a][0][0] == "추천"){
//                $cate = "추천";
//            }else if($data[$a][0][0] == "코치"){
//                $cate = "코치";
//            }
//        }

//        echo $cate;


//        dump($data);

    }
    private function chu($data){

    }

    private function coach($data){

    }
    private function date($data){
        
    }
}
