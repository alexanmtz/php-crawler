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
		
		$this->crawler = new Crawler('<html><body>Hello!</body></html>', $this->header);
		
		$dom = $this->crawler->getDom();
		
		$this->assertEquals($dom->plaintext, 'Hello!');
		
		$this->assertInstanceOf('simple_html_dom', $dom);
		
	 }
	 
	 public function testLoadingExternalHTMLFile() {
	 	$this->crawler = new Crawler('hello.html', $this->header);
		
		$dom = $this->crawler->getDom();
		
		$this->assertEquals($dom->plaintext, 'Hello there!');
		
		$this->assertInstanceOf('simple_html_dom', $dom);
	 }
	 
	 public function testParsingHeader() {
	 	
		$header_content = $this->crawler->getHeader();
		
		$this->assertEquals($header_content, 'UID;CATEGORIES');
		
	 }
	 
	 public function testCSVOutput() {
	 	
	 	$content = $this->crawler->CSV();
		
		$this->assertEquals($content, "UID;CATEGORIES\n");
	 }
	 
	 public function testAddItem() {
	 	$this->crawler->addItem(array(
			'uid' => '1234',
			'categories' => 'cat1'			
		));
		
		$content = $this->crawler->CSV();
		$this->assertEquals($content, "UID;CATEGORIES\n1234;cat1");
		
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
		$this->assertEquals($content, "UID;CATEGORIES\n1234;cat1\n5678;cat2");
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
		$this->assertEquals($content, "UID;CATEGORIES\n1234;cat1\n5678;cat2\n9;cat3");
	 }
	  
     /*
	  * Extracting DOM
	  */
	  
	  public function testNode() {
	  	$this->crawler = new Crawler('<html><body><div class="products">item</div></body></html>', $this->header);
		
		$item = $this->crawler->query('products', 'div', 'class');
		
		$this->assertEquals($item[0], 'item');
		
	  }
	  public function testDomRuleProductItem() {
  		$this->header = array(
			
			'uid', 'name', 'categories'
		
		);
			
	  	$this->crawler = new Crawler('<html><body><div class="products"><div class="product"><item id="1" category="cat01">title</item></div><div class="product">product content</div></div></body></html>', $this->header);
		
		$item = $this->crawler->associate('div.product > item', array(
			'uid' => 'id',
			'name' => 'plaintext',
			'categories' => 'category',	
		));
		
		$this->assertEquals($this->crawler->CSV(), "UID;NAME;CATEGORIES\n1;title;cat01");
		
	  }
 	  public function testDomRuleProductItens() {
  		$this->header = array(
			
			'uid', 'name', 'categories'
		
		);
			
	  	$this->crawler = new Crawler('<html><body><div class="products"><div class="product"><item id="1" category="cat01">title</item></div><div class="product"><item id="2" category="cat02">title2</item></div></div></body></html>', $this->header);
		
		$item = $this->crawler->associate('div.product > item', array(
			'uid' => 'id',
			'name' => 'plaintext',
			'categories' => 'category',	
		));
		
		$this->assertEquals($this->crawler->CSV(), "UID;NAME;CATEGORIES\n1;title;cat01\n2;title2;cat02");
		
	  }

	  /*public function testGettingProductsFromRealPage() {
	  	$this->header = array(
			
			'uid', 'name', 'categories'
		
		);
			
	  	$this->crawler = new Crawler('products.html', $this->header);
		
		$this->crawler->limit = 1;
		
		$item = $this->crawler->associate('.productList--container .productList--item', array(
			'uid' => 'data-sku',
			'name' => array('.productList--item--name','plaintext')
		));
		
		$this->crawler->fill('categories', 'suplementos-alimentares');
		
		$this->assertEquals($this->crawler->CSV(), 'UID;NAME;CATEGORIES\n5001121081;BEA Óleo de Cártamo;suplementos-alimentares');
	  }
	  
	   public function testGettingMoreProductsFromRealPage() {
	  	$this->header = array(
			
			'uid', 'name', 'categories'
		
		);
			
	  	$this->crawler = new Crawler('products.html', $this->header);
		
		$this->crawler->limit = 3;
		
		$item = $this->crawler->associate('.productList--container .productList--item', array(
			'uid' => 'data-sku',
			'name' => array('.productList--item--name','plaintext')
		));
		
		$this->crawler->fill('categories', 'suplementos-alimentares');
		
		$result = 'UID;NAME;CATEGORIES\n5001121081;BEA Óleo de Cártamo;suplementos-alimentares\n';
		$result .= '2791041201;Óleo de Peixe (Validade: 30/11/2015), Consumo: 15 dias;suplementos-alimentares\n';
		$result .= '2071031161;SB Equilíbrio Abdominal, Goji Berry;suplementos-alimentares';
		
		$this->assertEquals($this->crawler->CSV(), $result);
	  }
	   
	  public function testGenerateCSVFile() {
	  	$this->header = array(
			
			'uid', 'name', 'categories'
		
		);
			
	  	$this->crawler = new Crawler('products.html', $this->header);
		
		$this->crawler->limit = 3;
		
		$item = $this->crawler->associate('.productList--container .productList--item', array(
			'uid' => 'data-sku',
			'name' => array('.productList--item--name','plaintext')
		));
		
		$this->crawler->fill('categories', 'suplementos-alimentares');
		
		$result = 'UID;NAME;CATEGORIES\n5001121081;BEA Óleo de Cártamo;suplementos-alimentares\n';
		$result .= '2791041201;Óleo de Peixe (Validade: 30/11/2015), Consumo: 15 dias;suplementos-alimentares\n';
		$result .= '2071031161;SB Equilíbrio Abdominal, Goji Berry;suplementos-alimentares';
		
		$filename = 'results.csv';
		$this->crawler->generateFile($filename);
		
		$this->assertEquals(is_file($filename), true);
		
		$handle = fopen ("results.csv","r");
		$data = fgetcsv($handle, 1000, ",");
		
		$this->assertEquals($data[0], 'UID;NAME;CATEGORIES\n5001121081;BEA Óleo de Cártamo;suplementos-alimentares\n2791041201;Óleo de Peixe (Validade: 30/11/2015)');
		
	  }*/

	  /*public function testFillOrder() {
	  	$this->header = array(
			
			'uid', 'categories', 'name' 
		
		);
			
	  	$this->crawler = new Crawler('products.html', $this->header);
		
		$this->crawler->limit = 3;
		
		$item = $this->crawler->associate('.productList--container .productList--item', array(
			'uid' => 'data-sku',
			'categories' => '',
			'name' => array('.productList--item--name','plaintext')
		));
		
		$filled = $this->crawler->fill('categories', 'suplementos-alimentares', 1);
		
		$result = 'UID;CATEGORIES;NAME\n5001121081;suplementos-alimentares;BEA Óleo de Cártamo\n';
		$result .= '2791041201;suplementos-alimentares;Óleo de Peixe (Validade: 30/11/2015), Consumo: 15 dias\n';
		$result .= '2071031161;suplementos-alimentares;SB Equilíbrio Abdominal, Goji Berry';
		
		//print_r($this->crawler->html->plaintext);
		
		$this->assertEquals($this->crawler->CSV(), $result);
		
	  } */

	  /*public function testCompleteProduct() {
	    $this->header = array(
			'UID', 'CATEGORIES', 'BRAND', 'FULL NAME', 'PRICE',
			'SHIPPING','IMAGE','RETAILER','DEEPLINK','DESCRIPTION',
			'GLOBAL_DESCRIPTION','VOUCHER'
		);
		$this->crawler = new Crawler('products.html', $this->header, '/suplementos-alimentares/');
		
		$this->crawler->limit = 1;
		
		$this->crawler->associate('.productList--container .productList--item', array(
			'UID' => 'data-sku',
			'CATEGORIES' => '',
			'BRAND' => array('span[itemprop="brand"]', 'plaintext'),
			'FULL NAME' => array('.productList--item--name','plaintext'),
			'PRICE' => array('.price', 'plaintext'),
			'SHIPPING' => '',
			'IMAGE' => array('.productList--item--image img', 'src'),
			'RETAILER' => array('span[itemprop="brand"]', 'plaintext'),
			'DEEPLINK' => array('.productList--item--link','href'),
			'DESCRIPTION' => array('.productList--item--description','plaintext'),
			'GLOBAL_DESCRIPTION' => array('.productList--item--description','plaintext'),
			'VOUCHER' => ''
		));
		
		$this->crawler->fill('CATEGORIES', 'suplementos-alimentares');
		
		
		$this->assertEquals($this->crawler->CSV(), 'UID;NAME;CATEGORIES\n5001121081;BEA Óleo de Cártamo;suplementos-alimentares');
	  }*/


}
?>