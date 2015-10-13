<?php

require_once "crawler.php";

class CrawlerTest extends PHPUnit_Framework_TestCase
{
	
	public function setUp() {
		$this->header = array(
			
			'uid', 'Categories'
		
		);
		
		$this->crawler = new Crawler('<html><body>Hello!</body></html>', $this->header);
	}
	
    public function testInitClass()
    {
        $this->assertEquals($this->crawler->getUrl(), '<html><body>Hello!</body></html>');
    }
	public function testPathEmptyParameter() {
		$this->assertEquals($this->crawler->getPath(), '');		
	}
	public function testPathWithParameter() {
		$this->crawler = new Crawler('<html><body>Hello!</body></html>', $this->header, '/fooproduct');
		
		$this->assertEquals($this->crawler->getPath(), '/fooproduct');		
	}
	
	/*
	 * testing parser
	 */
	 
	 public function testLoadingDomObject() {
		
		$dom = $this->crawler->getDom();
		
		//$this->assertEquals($dom, '<html><body>Hello!</body></html>');
		
		$this->assertInstanceOf('simple_html_dom', $dom);
		
	 }
	 
	 public function testParsingHeader() {
	 	
		$header_content = $this->crawler->getHeader();
		
		$this->assertEquals($header_content, 'UID;CATEGORIES');
		
	 }
	 
	 public function testCSVOutput() {
	 	
	 	$content = $this->crawler->CSV();
		
		$this->assertEquals($content, 'UID;CATEGORIES\n');
	 }
	 
	 public function testAddItem() {
	 	$this->crawler->addItem(array(
			'uid' => '1234',
			'categories' => 'cat1'			
		));
		
		$content = $this->crawler->CSV();
		$this->assertEquals($content, 'UID;CATEGORIES\n1234;cat1');
		
	 }
	 
	 public function testAddTwoItens() {
	 	$this->crawler->addItem(array(
			'uid' => '1234',
			'categories' => 'cat1'			
		));	
			
	 	$this->crawler->addItem(array(
			'uid' => '5678',
			'categories' => 'cat2'			
		));
		
		$content = $this->crawler->CSV();
		var_dump($content);
		$this->assertEquals($content, 'UID;CATEGORIES\n1234;cat1\n5678;cat2');
	 }
	  public function testAddMultipleItens() {
	 	$this->crawler->addItem(array(
			'uid' => '1234',
			'categories' => 'cat1'			
		));	
			
	 	$this->crawler->addItem(array(
			'uid' => '5678',
			'categories' => 'cat2'			
		));
		
		$this->crawler->addItem(array(
			'uid' => '9',
			'categories' => 'cat3'			
		));
		
		$content = $this->crawler->CSV();
		$this->assertEquals($content, 'UID;CATEGORIES\n1234;cat1\n5678;cat2\n9;cat3');
	 }
}
?>