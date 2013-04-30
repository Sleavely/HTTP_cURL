<?php

/**
 * cURL wrapper based on HTTP-methods. It's built as I
 * go along and need more stuff, so.. you know, be nice.
 *
 * @author Joakim Hedlund <contact@joakimhedlund.com>
 * @uses PHP 5.1.3
 */
class HTTP_cURL {
	
	private $instance;
	public $options = array();
	public $last_request = NULL;
	
	public function __construct($url = NULL){
		$this->instance = curl_init($url);
		$this->setDefaults();
	}
	
	//Main functions
	
	public function GET(){
		return $this->execute();
	}
	
	/**
	 * Perform a HTTP POST request to the target URL
	 *
	 * @param array $postdata
	 * 		Key-val postdata
	 * @param bool $multipart
	 * 		Whether to use multipart/form-data or not.
	 * 		Defaults to application/x-www-form-urlencoded
	 * @return mixed
	 * 		curl_exec()
	 */
	public function POST($postdata = array(), $multipart = FALSE){
		
		//Normally we want to submit as application/x-www-form-urlencoded
		//so lets make sure that cURL doesnt autoconvert our array
		if($multipart !== TRUE){
			$old_postdata = $postdata;
			$postdata = array();
			foreach($old_postdata as $key => $val){
				$postdata[] = urlencode($key).'='.urlencode($val);
			}
			$postdata = implode('&', $postdata);
		}
		
		$this->options[CURLOPT_POST] = TRUE;
		$this->options[CURLOPT_POSTFIELDS] = $postdata;
		
		return $this->execute();
	}
	
	public function PUT(){}
	
	public function DELETE(){
		$this->options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
		
		return $this->execute();
	}
	
	private function execute(){
		foreach($this->options as $option => $value){
			curl_setopt($this->instance, $option, $value);
		}
		$result = curl_exec($this->instance);
		
		$this->last_request = array(
			'errno' => curl_errno($this->instance),
			'error' => curl_error($this->instance),
			'exec' => $result,
			'info' => curl_getinfo($this->instance)
		);
		
		return $result;
	}
	
	//Auxiliary functions
	
	/**
	 * Set some options that I like.
	 */
	private function setDefaults(){
		$this->options = array(
			CURLINFO_HEADER_OUT => TRUE, //TRUE to track the handle's request string.
			CURLOPT_RETURNTRANSFER => TRUE, //TRUE to return the transfer as a string instead of outputting it out directly.
			CURLOPT_TIMEOUT => 10 //.. seconds before we give up
		);
	}
	
	/**
	 * Yo dawg, I put a URL in your cURL so you can set it while you sit.
	 */
	public function setUrl($url){
		$this->options[CURLOPT_URL] = $url;
	}
	
	//Magic methods below; BEWARE!
	
	/**
	 * Allows you to set cURL options like so:
	 * $http_curl_instance->{CURLOPT_TIMEOUT} = 1337;
	 */
	public function __set($name, $value){
		$this->options[$name] = $value;
	}
	public function __get($name){
		return (isset($this->options[$name]) ? $this->options[$name] : null);
	}
	
	
	public function __isset($name){
		return isset($this->options[$name]);
	}
	public function __unset($name){
		if(isset($this->options[$name])) unset($this->options[$name]);
	}
	
	public function __destruct(){
		// From what I can tell, there is rarely any point in closing
		// the cURL session immediately, so we might as well let the
		// class instance's lifetime determine when it's time to die.
		curl_close($this->instance);
	}
	
}
