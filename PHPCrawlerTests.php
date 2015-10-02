<?php

require_once "crawler.php";

class CrawlerTest extends PHPUnit_Framework_TestCase
{
    public function testInitClass()
    {
        
		$crawler = new Crawler('http://foo.test');
		
        $this->assertEquals($crawler->getUrl(), 'http://foo.test');
    }
}
?>