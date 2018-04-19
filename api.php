<?php 
   require_once("Rest.inc.php");
   require_once('vendor/autoload.php');
   
   use Hubstaff\Hubstaff;
   
   use Hubstaff\Authentication;
   
   use Curl\Curl;
   
   //Get instance of Hubstaff
   class API extends REST {
   	
   		public $data = "";   		
   		const DB_SERVER = "localhost";
   		const DB_USER = "root";
   		const DB_PASSWORD = "";
   		const DB = "test";
   		
   		private $db = NULL;   		
   				
   		// Authenticate hubstaff with credentials you have //define variable as public
   		const appToken = 'M0OH_1KDgaC21-SkhxHC3h5xYboav2L8swViUrFOYc4';
   		const email = 'bhavna@cueserve.com';
   		const password = 'bhaVn@13';
   		const authToken = '_sCwhM7WO0cibomV_s228aJB421KLx_xdO3S4aq8SuU';		
   		
   	
   		public function __construct(){
   			parent::__construct();				// Init parent contructor
   			$this->dbConnect();	
   			session_start();
   							// Initiate Database connection
   		}
   		
   		/*
   		 *  Database connection 
   		*/
   		private function dbConnect(){
   
   			$this->db = mysqli_connect(self::DB_SERVER,self::DB_USER,self::DB_PASSWORD,self::DB);
   			if (mysqli_connect_errno())
   			  {
   			  echo "Failed to connect to MySQL: " . mysqli_connect_error();
   			  }
   		}
   		
   
   		private function authenticate(){ /* Post method */
   			
   			$toke=$_POST['App-Token'];
   			$email=$_POST['email'];
   			$password=$_POST['password'];
   
   			$this->url='https://api.hubstaff.com/v1/auth';
   			$curl = new Curl();
   	        $curl->setHeader('App-Token', $toke);
   	        $curl->post($this->url, array(
   	            'email' => $email,
   	            'password' => $password,
   	        ));
   	        if ($curl->error) {
   	            
   	            if($curl->error_code=='429'){
   	            	echo $res = 'You can`t make any more requests ';
   	            }else {echo $res = 'Unauthenticated user'; }die;
   	            
   	        }
   	        else {
   	            $response = json_decode($curl->response);
   	        }
   
   	        $curl->close();
   
   	        $res= $response->user; 
   	        
   	        $array = json_decode(json_encode($res), True);
   	        $_SESSION["userid"] = $response->user->id;
   	        $_SESSION["username"] = $response->user->name;
   	        $_SESSION["last_activity"] = $response->user->last_activity;
   	        $_SESSION["auth_token"] = $response->user->auth_token;
   
   	        $_SESSION["app_token"] = $_POST['App-Token'];
   	        $_SESSION["email"] = $_POST['email'];
   	        $_SESSION["password"] = $_POST['password'];
   
   			$this->response($this->json($array), 200);
   			$this->response('',204);
   				
   		}
   
   		private function getPerticularUserProject(){
   
   			$userId = $_SESSION["userid"];
   			$auth_token = $_SESSION["auth_token"];
   			$app_token = $_SESSION["app_token"];
   			$email = $_SESSION["email"];
   			$password = $_SESSION["password"];
   
   			$hubstaff = Hubstaff::getInstance();
   			$hubstaff->authenticate($app_token, $email, $password, $auth_token);
   	   
   			   $curl = new Curl();
   			   $curl->setHeader('App-Token', $app_token);
   			   $curl->setHeader('Auth-Token', $auth_token);
   			       
   			   $url='https://api.hubstaff.com/v1/users/'.$userId.'/projects';
   
   			   $curl->get($url, array(
   			      'offset' => '0'
   			   ));
   			   if ($curl->error) {
   			      echo 'errorCode' . $curl->error_code;
   			      die();
   			   }
   			   else {
   			      $response = json_decode($curl->response);
   			   }
   			   $array = json_decode(json_encode($response), True);
   
   			   $curl->close();
   			   $this->response($this->json($array), 200);
   				$this->response('',204);	
   
   		}
   
   		private function getAllOrgs(){
   
   		
            $auth_token = $_SESSION["auth_token"];
            $app_token = $_SESSION["app_token"];
            $email = $_SESSION["email"];
            $password = $_SESSION["password"];
   
   			$hubstaff = Hubstaff::getInstance();
   			$hubstaff->authenticate($app_token, $email, $password, $auth_token);
   	
   				$result=$hubstaff->getRepository('organization')->getAllOrgs();
   				
   				$this->response($this->json($result), 200);
   				$this->response('',204);				
   			}
   
   		private function getPerticularTask()
   		{
   
   			$auth_token = $_SESSION["auth_token"];
   			$app_token = $_SESSION["app_token"];
   			$email = $_SESSION["email"];
   			$password = $_SESSION["password"];
   
   			$hubstaff = Hubstaff::getInstance();
   			$hubstaff->authenticate($app_token, $email, $password, $auth_token);
   	   
   			$Atoken=$_SESSION["auth_token"];
   			$toke=$_SESSION["app_token"];
   			 $startdt=$_POST['start_date'];
   			$enddt=$_POST['end_date'];
   			$organizationsid=$_POST['organizationid'];
   			$projectsid=$_POST['projects'];
   			$users=$_SESSION["userid"];
   			$show_tasks=$_POST['show_tasks'];
   			$show_notes=$_POST['show_notes'];
   			$show_activity=$_POST['show_activity'];
   			$include_archived=$_POST['include_archived'];
   			
   			$curl = new Curl();
   			        $curl->setHeader('App-Token', $app_token);
   			        $curl->setHeader('Auth-Token', $auth_token);
   			        $this->url='https://api.hubstaff.com/v1/users/';
   			        $url='https://api.hubstaff.com/v1/custom/by_date/my?start_date='.$startdt.'&end_date='.$enddt.'&organizations='.$organizationsid.'&projects='.$projectsid.'&users='.$users.'&show_tasks='.$show_tasks.'&show_notes='.$show_notes.'&show_activity='.$show_activity.'&include_archived='.$include_archived;
   			       
   			        $curl->get($url, array());
   			         if ($curl->error) {
   			            echo 'errorCode' . $curl->error_code;
   			            die();
   			        }
   			        else {
   			            $response = json_decode($curl->response);
   			        }
   			        
   			        $curl->close();
   			$array = json_decode(json_encode($response), True);
   	        
   			$this->response($this->json($array), 200);
   			$this->response('',204);
   
   		}
   
   
   		public function getPerticularTaskTime(){
   			$auth_token = $_SESSION["auth_token"];
   			$app_token = $_SESSION["app_token"];
   			$email = $_SESSION["email"];
   			$password = $_SESSION["password"];
    
   			$Atoken=$_SESSION["auth_token"];
   			$toke=$_SESSION["app_token"];
   			 $startdt=$_POST['start_date'];
   			$enddt=$_POST['end_date'];
   			$organizationsid=$_POST['organizationid'];
   			$projectsid=$_POST['projects'];
   			$users=$_SESSION['userid'];
   			$show_tasks=$_POST['show_tasks'];
   			$show_notes=$_POST['show_notes'];
   			$show_activity=$_POST['show_activity'];
   			$include_archived=$_POST['include_archived'];
   			$tot='0';
   			 
   			$curl = new Curl();
   			        $curl->setHeader('App-Token', $app_token);
   			        $curl->setHeader('Auth-Token', $auth_token);
   			        $this->url='https://api.hubstaff.com/v1/users/';
   			        $url='https://api.hubstaff.com/v1/custom/by_date/my?start_date='.$startdt.'&end_date='.$enddt.'&organizations='.$organizationsid.'&projects='.$projectsid.'&users='.$users.'&show_tasks='.$show_tasks.'&show_notes='.$show_notes.'&show_activity='.$show_activity.'&include_archived='.$include_archived;
   			       
   			        $curl->get($url, array(
   			            
   			        ));
   			         if ($curl->error) {
   			            echo 'errorCode' . $curl->error_code;
   			            die();
   			        }
   			        else {
   			            $response = json_decode($curl->response);
   			        }
   			        
   			        $curl->close();
   
   			        $organizations=$response->organizations;
   			        foreach ($organizations as $org) {
   			        	$dates= $org->dates;
   			        	foreach($dates as $com){
   			        		 $user=$com->users;
   			        		 foreach($user as $us){
   			        		 	$proj=$us->projects;
   			        		 	 foreach($proj as $task){
   			        		 	 	$tasks=$task->tasks;
   			        		 	 		foreach($tasks as $tsk){
   			        		 	 			$sec=$tsk->duration;
   			        		 	 			$ech=$sec/60;
   			        		 	 			$tot=$ech+$tot;
   			        		 	 			$tot=round($tot);  
   
   			        		 	 	}
   			        		 	 }
   			        		 }
   
   			        	}
   
   			        }
   			        $respo = date('H:i', mktime(0,$tot));
   
   			$response=array('Total Time'=>$respo);
   			$this->response($this->json($response), 200);
   			$this->response('',204);
   			
   		}
   		public function getTaskTime(){
   
   			$auth_token = $_SESSION["auth_token"];
   			$app_token = $_SESSION["app_token"];
   			$email = $_SESSION["email"];
   			$password = $_SESSION["password"];
   
   			$Atoken=$_SESSION["auth_token"];
   			$toke=$_SESSION["app_token"];
   			 $startdt=$_POST['start_date'];
   			$enddt=$_POST['end_date'];
   			$organizationsid=$_POST['organizationid'];
   			$projectsid=$_POST['projects'];
   			$users=$_SESSION['userid'];
   			$show_tasks=$_POST['show_tasks'];
   			$show_notes=$_POST['show_notes'];
   			$show_activity=$_POST['show_activity'];
   			$include_archived=$_POST['include_archived'];
   			$tot='0';
   			
   			$curl = new Curl();
   			        $curl->setHeader('App-Token', $app_token);
   			        $curl->setHeader('Auth-Token', $auth_token);
   			        $url='https://api.hubstaff.com/v1/custom/by_date/my?start_date='.$startdt.'&end_date='.$enddt.'&organizations='.$organizationsid.'&projects='.$projectsid.'&users='.$users.'&show_tasks='.$show_tasks.'&show_notes='.$show_notes.'&show_activity='.$show_activity.'&include_archived='.$include_archived;
   			       
   			        $curl->get($url, array());
   			         if ($curl->error) {
   			            echo 'errorCode' . $curl->error_code;
   			            die();
   			        }
   			        else {
   			            $response = json_decode($curl->response);
   			        }
   			        
   			        $curl->close();
   			        $ar=array();
   
   			        $organizations=$response->organizations;
   			        foreach ($organizations as $org) {
   			        	$dates= $org->dates;
   			        	foreach($dates as $com){
   			        		 $user=$com->users;
   			        		 foreach($user as $us){
   			        		 	$proj=$us->projects;
   			        		 	 foreach($proj as $task){
   			        		 	 	$tasks=$task->tasks;
   			        		 	 		foreach($tasks as $tsk){
   			        		 	 			$sec=$tsk->duration;
   			        		 	 			$ech=$sec/60;
   			        		 	 			$tot=$ech+$tot;
   			        		 	 			$tot=round($tot);
   			        		 	 			$deu=date('H:i', mktime(0,$ech));
   										$a[]=array(
                                                 'taskid'=>$tsk->id,
                                                  'summary'=>$tsk->summary,
                                                  'remote_id'=>$tsk->remote_id,
                                                  'remote_alternate_id'=>$tsk->remote_alternate_id,
                                                  'duration'=>$deu, 
   											);
   			        		 	 	}
   			        		 	 }
   			        		 }
   
   			        	}
   
   			        }
   			        $array = json_decode(json_encode($a), True);
   			        $arr['task']=$array;
   			        $arr['taskTime']=date('H:i', mktime(0,$tot));;
   					$this->response($this->json($arr), 200);
   					$this->response('',204);
   			    
   		}
   
   
   
   		public function getTaskTimeForSingle(){
   
   			$auth_token = $_SESSION["auth_token"];
   			$app_token = $_SESSION["app_token"];
   			$email = $_SESSION["email"];
   			$password = $_SESSION["password"];
   
   			$Atoken=$_SESSION["auth_token"];
   			$toke=$_SESSION["app_token"];
   			 $startdt=$_POST['start_date'];
   			$enddt=$_POST['end_date'];
   			$organizationsid=$_POST['organizations'];
   			$projectsid=$_POST['projects'];
   			$users=$_SESSION['userid'];
   			$show_tasks=$_POST['show_tasks'];
   			$show_notes=$_POST['show_notes'];
   			$show_activity=$_POST['show_activity'];
   			$include_archived=$_POST['include_archived'];
   			$taskid=$_POST['taskid'];
   			$tot='0';
   			
   			$curl = new Curl();
   			        $curl->setHeader('App-Token', $app_token);
   			        $curl->setHeader('Auth-Token', $auth_token);
   			        $url='https://api.hubstaff.com/v1/custom/by_date/my?start_date='.$startdt.'&end_date='.$enddt.'&organizations='.$organizationsid.'&projects='.$projectsid.'&users='.$users.'&show_tasks='.$show_tasks.'&show_notes='.$show_notes.'&show_activity='.$show_activity.'&include_archived='.$include_archived;
   			      
   			        $curl->get($url, array());
   			         if ($curl->error) {
   			            echo 'errorCode' . $curl->error_code;
   			            die();
   			        }
   			        else {
   			            $response = json_decode($curl->response);
   			        }
   			        
   			        $curl->close();
   			        $ar=array();
   
   			        $organizations=$response->organizations;
   			        foreach ($organizations as $org) {
   			        	$dates= $org->dates;
   			        	foreach($dates as $com){
   			        		 $user=$com->users;
   			        		 foreach($user as $us){
   			        		 	$proj=$us->projects;
   			        		 	 foreach($proj as $task){
   			        		 	 	
   			        		 	 	 $tasks=$task->tasks;
   			        		 	 	foreach($tasks as $signtask){
   			        		 	 		$sh=$signtask->id;
   			        		 	 	if($sh==$taskid){
   			        		 	 		$sec=$signtask->duration;
   			        		 	 			$ech=$sec/60;
   			        		 	 			$tot=$ech+$tot;
   			        		 	 			$tot=round($tot);
   			        		 	 			$deu=date('H:i', mktime(0,$ech));
   										$a[]=array(
                                                 'taskid'=>$signtask->id,
                                                  'summary'=>$signtask->summary,
                                                  'remote_id'=>$signtask->remote_id,
                                                  'remote_alternate_id'=>$signtask->remote_alternate_id,
                                                  'duration'=>$deu, 
   											);
   			        		 	 	}
   			        		 	 	
   			        		 	 }
   
   			        		 	 }
   			        		 }
   
   			        	}
   
   			        }
   			           $array = json_decode(json_encode($a), True);
   	        
   			$this->response($this->json($array), 200);
   			$this->response('',204);
   			
   		}
   
   
   		private function getAllUsers(){
   				
   			$auth_token = $_SESSION["auth_token"];
   			$app_token = $_SESSION["app_token"];
   			$email = $_SESSION["email"];
   			$password = $_SESSION["password"];
   
   			$hubstaff = Hubstaff::getInstance();
   			$hubstaff->authenticate($app_token, $email, $password, $auth_token);
   	
   			$result = $hubstaff->getRepository('user')->getAllUsers();
   			$this->response($this->json($result), 200);
   			$this->response('',204);				
   		}
   		
   		private function getAllProjects(){
   			
   			$auth_token = $_SESSION["auth_token"];
   			$app_token = $_SESSION["app_token"];
   			$email = $_SESSION["email"];
   			$password = $_SESSION["password"];
   
   			$hubstaff = Hubstaff::getInstance();
   			$hubstaff->authenticate($app_token, $email, $password, $auth_token);
   			$result = $hubstaff->getRepository('project')->getAllProjects();
   			$this->response($this->json($result), 200);
   			$this->response('',204);				
   		}
   			
   		private function getProjectDetail(){
   			
   			$auth_token = $_SESSION["auth_token"];
   			$app_token = $_SESSION["app_token"];
   			$email = $_SESSION["email"];
   			$password = $_SESSION["password"];
   			$projectid = $_POST["projectid"];
   
   			$hubstaff = Hubstaff::getInstance();
   			$hubstaff->authenticate($app_token, $email, $password, $auth_token);
   	
   			
   			$curl = new Curl();
   	        $curl->setHeader('App-Token', $app_token);
   	        $curl->setHeader('Auth-Token', $auth_token);
   			       
   	        $url='https://api.hubstaff.com/v1/projects/'.$projectid;
   
   	        $curl->get($url, array());
   			    if ($curl->error) {
   			        echo 'errorCode' . $curl->error_code;
   			        die();
   			    }
   		        else 
   		        {
   			        $response = json_decode($curl->response);
   			    }
   			    $array = json_decode(json_encode($response), True);
   
   			    $curl->close();
   		        $this->response($this->json($array), 200);
   				$this->response('',204);			
   		}
   
   		
   		public function processApi(){
   			$func = strtolower(trim(str_replace("/","",$_REQUEST['rquest'])));
   			if((int)method_exists($this,$func) > 0)
   				$this->$func();
   			else
   				$this->response('',404);				// If the method not exist with in this class, response would be "Page not found".
   		}
   		private function json($data){
   			if(is_array($data)){
   				return json_encode($data);
   			}
   		}
   	}
   	
   	
   	$api = new API;
   	$api->processApi();
   ?>