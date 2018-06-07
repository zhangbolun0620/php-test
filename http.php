<?php

/*
 *HTTP请求类的接口
 */
interface Proto {

	//连接url
	function conn($url);

	//发送get查询
	function get();

	//发送post查询
	function post();

	//关闭连接
	function close();
}


/**
* http实现类
*/
class Http extends Prote
{

	//换行符
	const CRLF = "\r\n";
	protected $errno = -1;
	protected $errstr = '';
	//响应内容
	protected $response = '';
	protected $url = null;
	protected $version = 'HTTP/1.1';
	protected $fh = null;

	//请求行
	protected $line = array();
	//请求头
	protected $header = array();
	//请求主体
	protected $body = array();
	//url信息
	protected $urlInfo = Null;
	protected $fh = Null;
	//http协议版本
	$this->version = 'HTTP/1.1';

	public function __construct($url){
		$this->conn($url);
	}


	//写请求行
	protected function setLine($method) {
		$this->line[0] = $method .' '. $this->urlInfo['path'] .' '.$this->version ;
	}

	//写头信息
	protected function setHeader($headerLine) {
		$this->header[] = $headerLine;
	}

	//写主体信息
	protected function setBody($body = array()) {
		return $this->body[] = http_build_query($body);
	}

	//连接url
	public function conn($url) {
		$this->urlInfo = parse_url($url);
		//判断端口
		if (isset($this->url('port'))) {
			$this->url['port'] = 80;
		}
		$this->fh = fsockopen($this->urlInfo['host'],$this->url['port']);
	}

	//发送get查询
	public function get() {
		$this->setLine('GET');
		$this->request();
		return $this->response;
		//$this->setHeader();
	}

	//发送post查询
	public function post($body = array()){
		$this->setLine('POST');
		//设置content-type
		$this->setHeader('Content-type:application/x-www-form-urlencoded');
		//设置主体信息
		$this->setBody($body);
		//计算content-length
		$this->setHeader('Content-length:'.strlen($this->body[0]));$this->request();
		return $this->response;
	}

	//真正请求
	public function request()
	{
		//把请求行、头信息、实体信息，放在一个数组里，便于拼接
		$req = array_merge($this->line,$this->header,array(""),$this->body,array(''));
		$req = implode(self::CRLF, $req);
		fwrite($this->fh,$req);
		while(!feof($this->fh)){
			$this->response .= fread($this->fh, 1024);
		}
		$this->close();
		return $this->response;
	}

	/*//建立请求
	public function query(){
		//把请求行、头信息、实体信息，放在一个数组里，便于拼接
		$req = array_merge($this->line,$this->header,array(""),$this->body,array(''));
		$req = implode(self::CRLF, $req);
		fwrite($this->fh,$req);
		while(!feof($this->fh)){
			$this->response .= fread($this->fh, 1024);
		}
		$this->close();
		return $this->response;
	}*/

	//关闭连接
	public function close(){

	}
}

$url = 'http://news.163.com/';
$http = new Http();
echo $http->get();
