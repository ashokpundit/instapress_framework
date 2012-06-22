<?php

	class Instapress_Core_Digg{
		private $_url;
		private $_infoColumns = array(  'Diggs', 'Total Comments' , 'User Name');
		private $_urlInfo = array();
	 	public function __construct($url = null){
	 		$this -> _url = $url;
	 	}

		 public function get_digg_count($url=NULL){
			ini_set('user_agent', 'CHANGE ME TO NAME YOUR APPLICATION'); # Change this! 
			$this->_url = $url !== null ? $url : $this->_url;
			$apicall = 'http://services.digg.com/stories/';
			$apicall .= '?link='.urlencode($url);
			$apicall .= '&appkey='.urlencode('http://www.metalinjection.net/');
			$apicall .= '&type=xml';
		
			$result = (array) simplexml_load_file($apicall);
			
			if(!empty($result['story'])){
			   	$result = (array) $result['story'];
			    $this->_urlInfo[ 'Diggs' ] = $result['@attributes']['diggs'] ;
					/* $this->_urlInfo[ 'Total Comments' ] = $result['@attributes']['comments'] ;
					$userName = '';
					foreach( $result['user']->attributes() as $a => $b) {
						if( $a == 'name' ) {
							$userName = $b;
							break;
						}
				   }
			   
				$this->_urlInfo[ 'User Name' ] = $userName ; */ 
			    return $this ->_urlInfo['Diggs'];
			  } else {
			    return 0;
		   }
      }
	    	
	    	public function getUrlInfo( $infoColumn ) {
				if( in_array( $infoColumn, $this->_infoColumns ) ) {
					return $this->_urlInfo[ $infoColumn ];
				}
				else
				return 0;
				
	   }
  }

	/* try {
	$diggObj = new Instapress_Core_Digg();
	$diggObj -> get_digg_count('http://www.trutv.com/video/tsg-presents/emergency-phone-sex-line.html?link=truTVshlk');
	
	echo '<p>Diggs : ' . $diggObj->getUrlInfo( 'Diggs' ) . '</p>'.'</ br>';
	echo '<p>Total Comments : ' . $diggObj->getUrlInfo( 'Total Comments' ) . '</p>'.'</ br>';
	echo '<p>Username : ' . $diggObj->getUrlInfo( 'User Name' ) . '</p>';
	}
	
	catch ( Exception $ex ){
		echo $ex -> getMessage();
	} */
	

?>