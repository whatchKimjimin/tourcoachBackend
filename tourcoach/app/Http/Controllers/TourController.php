<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tourdata as TourDataModel;
use App\LiveCoachList as LiveCoachListModel;
use App\TourWeather as TourWeatherModel;
use App\Locale as LocaleModel;
use App\Review as ReviewModel;
use App\ProductLike as ProductLikeModel;
use Illuminate\Support\Facades\DB;
use App\KakaoToken as KakaoaModel;

class TourController extends Controller
{
    // 메인 페이지
    public function index(Request $req){


        return view('tour.index');
    }

    // 실시간 여행지
    public function liveList(){
        return view('tour.live');
    }


    // 디비 날씨 체크하고 최신날짜 업데이트
    static function weatherCheck($id,$village,$city){

        // 5분전 날짜
        $date = date("Y-m-d H:i:s",strtotime("-5 minutes")) ;
        $weatherData = TourWeatherModel::where('tourId' , '=' , $id)->orderBy('date','desc')->first();
        // 결과값
        $result = null;

        if ( $weatherData['date'] < $date ){

            $locationData = LocaleModel::where('name' , 'LIKE' ,'%'.$village.'%')->orWhere('name' , 'LIKE' , '%'.$city.'%')->first();

            if( !$locationData ) return false;

            // 위도 경도
            $lat = $locationData['lat'];
            $lon = $locationData['lng'];

            // 날씨 데이터 가져옴
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => 'http://apis.skplanetx.com/weather/current/minutely?lon='.$lon.'&village=&cellAWS=&lat='.$lat.'&country=&city=&version=1',
                CURLOPT_HTTPHEADER => array(
                    'appKey: 5d4f31bc-6b5c-3c8d-9715-2672fb5f2e6a'
                )
            ));
            $resp = json_decode(curl_exec($curl));

            curl_close($curl);
//            print_r($resp);
            $updateWeather = $resp->weather->minutely[0]->temperature->tc;
            if($updateWeather == ""){
                $updateWeather = $weatherData['weather'];
            }
            $updateSky = $resp->weather->minutely[0]->sky->name;
            TourWeatherModel::create(array('tourId' => $id , 'weather' => $updateWeather , 'date' => date("Y-m-d H:i:s") , 'sky' => $updateSky));

            $result = array('weather' => $updateWeather , 'sky' => $updateSky);

        } else {

            $result = array('weather' => $weatherData['weather'] , 'sky' => $weatherData['sky']);
        }

        return $result;
    }

    // 여행지 자세히
    public function detail(Request $req , $no){
            $tourData = TourDataModel::where('id', '=', $no)->first();
            // 사용자 좋아요 버튼 유뮤
            $like = false;
            // 좋아요 개수 변수
            $likeCount = 0;
            // 후기 담는 변수
            $reviews = null;
            // 후기
            $reviewCount = 0;
            // 맞춤여행지 데이터 변수
            $userTourDatas = null;
            // 맞춤 여행지 쿼리
            $sql = "SELECT DISTINCT(B.id) ,A.name , B.name as realName, B.address , B.big_cate , B.middle_cate ".
                "FROM BestTour2016 AS A ".
                "RIGHT JOIN (SELECT * ".
                "FROM tourdatas as A ".
                "LEFT JOIN ".
                "(SELECT tourId , COUNT(*) as cnt from product_likes GROUP BY tourId) as B ".
                "ON A.id = B.tourId ".
                "ORDER BY cnt DESC) as B ".
                "ON A.location = B.address ".
                "WHERE B.small_cate = '".$tourData->small_cate."' ".
                "ORDER BY A.name DESC ".
                "LIMIT 0,5";


            if( isset($req->session()->get('loginData')->id) ) {
                // 현재 페이지에 좋아요 유무
                $getLike = ProductLikeModel::where([ ['userid', '=', $req->session()->get('loginData')->id] , ['tourId' , '=' , $no] ])->first();
                if( !$getLike ){
                    $like = true;
                }
            }

            // 좋아요 개수
            $likeCount = ProductLikeModel::where('tourId' , '=' , $no)->count();
            // 후기
            $reviews = ReviewModel::where('tourId' , '=' , $no)->orderBy('date' , 'desc')->limit(5)->get();
            // 후기 개수
            $reviewCount = ReviewModel::where('tourId' , '=' , $no)->orderBy('date' , 'desc')->count();
            // 맞춤여행지 데이터
            $userTourDatas = DB::select( DB::raw($sql) );
            // 실시간 날씨
            $weather = $this->weatherCheck($tourData->id,$tourData->vilage,$tourData->city);
//            dd($userTourDatas);
            return view('tour.detail',[
                'tourData' => $tourData,
                'weatherData' => $weather,
                'like' => $like,
                'likeCount' => $likeCount,
                'reviews' => $reviews,
                'reviewCount' => $reviewCount,
                'userTourDatas' => $userTourDatas
            ]);
    }

    // 여행지 추천
    public function propose(Request $req){
        $location = $req->input('location');

        $sql = "SELECT DISTINCT(B.id) ,A.name , B.name as realName, B.address , B.big_cate , B.middle_cate , B.small_cate as small_cate ".
            "FROM BestTour2016 AS A ".
            "RIGHT JOIN (SELECT * ".
            "FROM tourdatas as A ".
            "LEFT JOIN ".
            "(SELECT tourId , COUNT(*) as cnt from product_likes GROUP BY tourId) as B ".
            "ON A.id = B.tourId ".
            "ORDER BY cnt DESC) as B ".
            "ON A.location = B.address ".
            "WHERE B.area LIKE \"%$location%\" or B.city LIKE \"%$location%\" or B.village LIKE \"%$location%\" ".
            "ORDER BY A.name DESC ".
            "LIMIT 0,3";
        $tourData = DB::select( DB::raw($sql) );


        return view('tour.propose',['datas' => $tourData]);
    }

    // 여행지 추천 받아오기
     private function getTravelPropose($date , $location){
        // 여행지 추천 리스트 5개 받아오는 코드...
    }

    // 여행지 코치 결과값 ajax
    public function coachAjax(Request $req){
//        $date = $req->input('date');
        $location = $req->input('location');

        $returnData = null;

        // 이름 연관성 높은것을 찾는다.
        $tourDatas = TourDataModel::where('name', 'like', '%'.$location.'%')->first();

        if($tourDatas){
            // 유저 no
            $userId = isset( $req->session()->get('loginData')->id ) ? $req->session()->get('loginData')->id : null;

            $returnData = array('success' => 'true' , 'tourdata' => $tourDatas);
            $livecoacj= LiveCoachListModel::create(array('userId' => $userId , 'tourId' => $tourDatas->id, 'date' => date("Y-m-d H:i:s")));
        }else {
            $returnData = array('success' => 'false'  , 'msg' => '해당 여행지에대한 정보가 없습니다.');
        }

        return response()->json($returnData);

    }


    // 카테고리 , 검색 뷰
    public function cateSearch(Request $req){


        return view("tour.cateSearch");
    }


    // 좋아요
   public function productLike(Request $req){


            if( !isset($req->session()->get('loginData')->id) ){
                echo "false";
                exit;
            }

            $userId = $req->session()->get('loginData')->id;
            $tourId = $req->input('tourId');
//
            ProductLikeModel::create(array('userId' => $userId , 'tourId' => $tourId , 'date' => date("Y-m-d H:i:s")));

            echo "true";

   }

    // 후기
    public function letterWrite(Request $req , $tourId){

        if( !isset($req->session()->get('loginData')->id) ){
            return false;
        }
        $userName = $req->session()->get('loginData')->username;
       $userId = $req->session()->get('loginData')->id;
       $content = $req->input('content');


        ReviewModel::create(array('userName' => $userName,'userId' =>  $userId, 'content' => $content , 'date' => date("Y-m-d H:i:s") , 'tourId' => $tourId));

        return back();
   }

    // 카카오톡 보내기
   public function sendKakao(Request $req , $no){
       $tourData = TourDataModel::where('id' , '=' , $no)->first();
       $token = KakaoaModel::where('userId' , '=' , '6')->first();

       $weather =  TourController::weatherCheck($tourData->id,$tourData->village,$tourData->city);

       $title = $tourData->name." ".round($weather['weather'])."°C/ ".$weather['sky'];

       $location = is_null($tourData->address) ? $tourData->area." ".$tourData->city." ".$tourData->village : $tourData->address;

       $data = "template_object={
          \"object_type\": \"location\",
          \"content\": {
            \"title\": \"$title\",
            \"description\": \"$location\",
            \"image_url\": \"https://tourcoach.co.kr/img/favicon/favicon.png\",
            \"link\": {
              \"web_url\": \"https://tourcoach.co.kr/tour/detail/$tourData->id\",
              \"mobile_web_url\": \"https://tourcoach.co.kr/tour/detail/$tourData->id\",
              \"android_execution_params\": \"platform=android\",
              \"ios_execution_params\": \"platform=ios\"
            }
          },
          \"buttons\": [
            {
              \"title\": \"웹으로 보기\",
              \"link\": {
                \"web_url\": \"https://tourcoach.co.kr/tour/detail/$tourData->id\",
                \"mobile_web_url\": \"https://tourcoach.co.kr/tour/detail/$tourData->id\"
              }
            }
          ],
          \"address\": \"$location\",
          \"address_title\": \"$tourData->name\"
        }";


       $curl = curl_init();
       // Set some options - we are passing in a useragent too here
       curl_setopt_array($curl, array(
           CURLOPT_RETURNTRANSFER => 1,
           CURLOPT_URL => 'https://kapi.kakao.com/v2/api/talk/memo/default/send',
           CURLOPT_USERAGENT => 'Codular Sample cURL Request',
           CURLOPT_HTTPHEADER => array(
               'Authorization: Bearer '.$token->accessToken,
           ),
           CURLOPT_POSTFIELDS => $data
//            CURLOPT_POSTFIELDS => $data
       ));
       // Send the request & save response to $resp
       $resp = json_decode(curl_exec($curl));
       // Close request to clear up some resources
       curl_close($curl);
       print_r($resp);
       // 카카오톡 인증코드 재인증
       if( isset($resp->code) ){

           $data2 = 'grant_type=refresh_token&client_id=5ec50e2b770cb96c54982616ede557ad&refresh_token='.$token->refreshToken;
           $curl = curl_init();
           // Set some options - we are passing in a useragent too here
           curl_setopt_array($curl, array(
               CURLOPT_RETURNTRANSFER => 1,
               CURLOPT_URL => 'https://kauth.kakao.com/oauth/token',
               CURLOPT_USERAGENT => 'Codular Sample cURL Request',
               CURLOPT_HTTPHEADER => array(
                   'Content-Type: application/x-www-form-urlencoded'
               ),
               CURLOPT_POSTFIELDS => $data2
//            CURLOPT_POSTFIELDS => $data
           ));
           // Send the request & save response to $resp
           $resp = json_decode(curl_exec($curl));
           // Close request to clear up some resources
           curl_close($curl);
           $token->accessToken = $resp->access_token;
           $token->save();

           // 다시 추천하기 메시지 전송
           $curl = curl_init();
           // Set some options - we are passing in a useragent too here
           curl_setopt_array($curl, array(
               CURLOPT_RETURNTRANSFER => 1,
               CURLOPT_URL => 'https://kapi.kakao.com/v2/api/talk/memo/default/send',
               CURLOPT_USERAGENT => 'Codular Sample cURL Request',
               CURLOPT_HTTPHEADER => array(
                   'Authorization: Bearer '.$resp->access_token,
               ),
               CURLOPT_POSTFIELDS => $data
//            CURLOPT_POSTFIELDS => $data
           ));
           // Send the request & save response to $resp

           // Close request to clear up some resources
           curl_close($curl);

       }

   }

   public function getReview(Request $req){
       $start = $req->input('start');
       $end = $req->input('end');
       $tourId = $req->input('tourId');


       $reviewDatas = ReviewModel::where('tourId' , '=' , $tourId)->orderBy('date','desc')->offset($start)->limit($end)->get();

       return response()->json($reviewDatas);

   }

}
