<?php


class Instapress_Core_Zemanta
{
	private $_requestUrl = "http://api.zemanta.com/services/rest/0.0/";
	private $_format = "json";
	private $_apiKey = "apikey";
	private $_articlesArray = array();
	private $_markupArray = array();
	private	$_imagesArray = array();
	private	$_keywordsArray = array();

	function __construct()
	{}

	//get suggestion for given content
	//$zemantaMethod : 'suggest', 'suggest_markup'
	public function processContent($content, $zemantaMethod = 'suggest')
	{
		$args = array(
				'method'=> "zemanta.$zemantaMethod",
				'api_key'=> $this->_apiKey,
				'text'=> $content,
				'format'=> $this->_format
				);

		$data = "";				
		foreach($args as $key=>$value)
		{
			$data .= ($data != "")?"&f":"";
			$data .= urlencode($key)."=".urlencode($value);
		}

		$params = array('http' => array(
										'method' => 'POST',
										'Content-type'=> 'application/x-www-form-urlencoded',
										'Content-length' =>strlen( $data ),
										'content' => $data
										)
						);

		$context = stream_context_create($params);
		$fp = @fopen($this->_requestUrl, 'r', false, $context);

		if ($fp)
		{
			$response = @stream_get_contents($fp);
			if ($response === false)
			{
				throw new Exception( "Problem reading data from " . $this->_requestUrl . "\n" . $php_errormsg);
			}
			fclose($fp);
		}
		else
		{
			throw new Exception( "Problem reading data from " . $this->_requestUrl . "\n" . $php_errormsg);
		}
	
		$responseArray = json_decode($response);
		
		if("suggest_markup" == $zemantaMethod)
		{
			$this->_markupArray = $responseArray->markup;
		}
		else
		{
			$this->_articlesArray = $responseArray->articles;
			$this->_markupArray = $responseArray->markup;
			$this->_imagesArray = $responseArray->images;
			$this->_keywordsArray = $responseArray->keywords;
		}		
	}

	public function suggestArticles()
	{
		$articles = array();
		foreach($this->_articlesArray as $key=>$value)
		{
			$articles[$key]['title'] = $value->title;
			$articles[$key]['url'] = $value->url;
			$articles[$key]['post_datetime'] = $value->published_datetime;
		}
		return $articles;
	}

	public function suggestKeywords()
	{
		//print_r($this->_keywordsArray);
		$keywords = array();
		foreach($this->_keywordsArray as $key=>$value)
		{
			$keywords[$key]['name'] = $value->name;
			$keywords[$key]['source'] = $value->scheme;
		}
		return $keywords;	
	}

	public function suggestImages()
	{
		//print_r($this->_imagesArray);
		$images = array();
		foreach($this->_imagesArray as $key=>$value)
		{
			$images[$key]['url_l'] = $value->url_l;
			$images[$key]['url_l_w'] = $value->url_l_w;
			$images[$key]['url_l_h'] = $value->url_l_h;
			$images[$key]['url_m'] = $value->url_m;
			$images[$key]['url_m_w'] = $value->url_m_w;
			$images[$key]['url_m_h'] = $value->url_m_h;
			$images[$key]['url_s'] = $value->url_s;
			$images[$key]['url_s_w'] = $value->url_s_w;
			$images[$key]['url_s_h'] = $value->url_s_h;
			$images[$key]['description'] = $value->description;			
		}
		return $images;
	}	
	
	public function suggestMarkupText()
	{
		//print_r($this->_markupArray);
		return $this->_markupArray->text;
	}
	
	public function suggestMarkupLinks()
	{
		//print_r($this->_markupArray);
		$markupLinks = array();

		foreach($this->_markupArray->links as $key=>$value)
		{
			$markupLinks[$key]['word'] = $value->anchor;
			
			foreach($value->target as $key1=>$value1)
			{
				$markupLinks[$key][$key1]['title'] = $value1->title;
				$markupLinks[$key][$key1]['url'] = $value1->url;
				$markupLinks[$key][$key1]['type'] = $value1->type;
			}
		
		}
		return $markupLinks;
	}
}
?>
