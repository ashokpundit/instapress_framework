<?php


define('COPYSCAPE_USERNAME', 'username');
define('COPYSCAPE_API_KEY', 'password');
define('COPYSCAPE_API_URL', 'http://www.copyscape.com/api/');
define('ENCODING','UTF-8');
define('OPERATION','csearch');

/*
 * Created on Feb 15, 2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class Instapress_Core_CopyScape
{
	private $_querywords = 0;
	private $_matchedResultsCount = 0;
	private $_matchedResults = array();
	
	public function __construct($text_to_search)
	{
		$params=array();
		$params['e']=ENCODING;
		
		$this->copyscapeApiCall( $params, array(2 => array ('result' => 'array')), $text_to_search);
	}
	
	public function getResultCount()
	{
		return $this->_matchedResultsCount;
	}
	
	public function getResults() {
		if( isset( $this->_matchedResults[ 'index' ] ) ) {
			$this->_matchedResults = array( $this->_matchedResults );
		}

		for($i=0;$i<$this->_matchedResultsCount;$i++)
		{
			$this->_matchedResults[$i]['domain'] = Helper::getUrlSourceDomain( $this->_matchedResults[$i]['url'] );
			$this->_matchedResults[$i]['percentage'] = round((($this->_matchedResults[$i]['minwordsmatched'])/ $this->_querywords)*100);
			$this->_matchedResults[$i]['shortsnippet'] = substr($this->_matchedResults[$i]['textsnippet'], 0, 150 );
			
			if($this->_matchedResults[$i]['percentage'] > 50)
				$this->_matchedResults[$i]['color'] = 'red';
			else if($this->_matchedResults[$i]['percentage'] > 20)
				$this->_matchedResults[$i]['color'] = 'yellow';
			else
				$this->_matchedResults[$i]['color'] = 'green';			
		}		
		return $this->_matchedResults;
	}	

	private function copyscapeApiCall($params=array(), $xmlspec=null, $postdata=null)
	{
		$url=COPYSCAPE_API_URL.'?u='.urlencode(COPYSCAPE_USERNAME).
			'&k='.urlencode(COPYSCAPE_API_KEY).'&o='.urlencode(OPERATION);

		foreach ($params as $name => $value)
		$url.='&'.urlencode($name).'='.urlencode($value);

		$curl=curl_init();

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_TIMEOUT, 60);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, isset($postdata));

		if (isset($postdata))
			curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);

		$response=curl_exec($curl);
		curl_close($curl);		
		
		if (strlen($response))
		{
			$copyscapeResponse = Helper::xml2array($response);
			
			if(is_array($copyscapeResponse))
			{
				$this->_querywords = $copyscapeResponse['response']['querywords'];
				$this->_matchedResultsCount = $copyscapeResponse['response']['count'];
				$this->_matchedResults = $copyscapeResponse['response']['result'];
			}
		}
	}
}
?>
