<?php

require_once('simple_html_dom.php');

class Crawler {
	
	public $url;
	
	function __construct($url, $header = array(), $path = '') {
		$this->url = $url;
		$this->path = $path;
		$this->header = $header;
		$this->headerArray = array();
		$this->item = array();
		$this->html = new simple_html_dom();
		$this->limit = 5;
		$this->separator = ';';
		$this->setHeader($header);
		
		if(file_exists($url) || filter_var($url, FILTER_VALIDATE_URL)) {
			$this->html = file_get_html($url);
		} else {
			$this->html = str_get_html($url);
		}
	}
	
	function getUrl() {
		return $this->url;
	}
	
	function getPath() {
		return $this->path;
	}
	
	function getDom() {
		
		return $this->html;
	}
	
	function query($value, $element, $attribute) {
		
		$item = array();
		
		$selector = $element.'['.$attribute.'='.'"'.$value.'"]';
		
		foreach($this->html->find($selector) as $element) {
			
			$item[] = $element->plaintext;
		}
		
		return $item;
	}
	
	function fill($key, $value) {
		$new = array();
			
		foreach($this->item as $item) {
			$new[] = array_merge( $item, array( $key => $value ) );
		}
		
		$this->item = $new;
		
		return $new;
	}
	
	function associate($root, $map) {
		
		$association = array();
		
		foreach($this->html->find($root) as $i => $item) {
			if($i == $this->limit) {
				break;
			}
			foreach($map as $key => $mapped) {
				if(is_array($mapped)) {
					$association[$key] = trim($this->html->find($root, $i)->find($mapped[0], 0)->{$mapped[1]});
					
				} else {
					$association[$key] = $this->html->find($root, $i)->{$mapped};
				}
			}
			$this->addItem($association);	
		}
		
		return $association;
	}
	
	function setHeader($header) {
		$sep = $this->separator;
		
		$this->header = strtoupper(implode($sep, $header));
		
		$this->headerArray = $header;
		
		return $this->header;
	}
	
	function getHeaderArray() {
		return $this->headerArray;
	}
	
	function getHeader() {
		return $this->header;
	}
	
	
	function CSV() {
		$header = $this->getHeader();
		$contents = array();
		
		foreach($this->item as $i) {
			$this->addItem($i);
			$contents[] = implode($this->separator, $i);
		}
		
		$content = implode("\n", $contents);
		return $header."\n".join("\n", $contents);
	}
	
	function generateFile($filename) {
		return file_put_contents($filename, $this->CSV());
	}
	
	function addItem($item) {
		$this->item[] = $item;
	}
}

/*function crawl_page($base_url, $path) {
	
	$html = file_get_html($base_url.$path);
	$content = array();
	
	foreach($html->find('div[class="product"]') as $element) {
		$src = 'data-src'; 
		$row = '';
		$row .= $element->id . ';'.'category'.';'.trim($element->find('span[itemprop="brand"]', 0)->plaintext);
		$row .= ';'.trim($element->find('div[itemprop="name"]', 0)->plaintext);
		$row .= ';'.substr(trim($element->find('span[itemprop="price"]', 0)->plaintext),2);
		$row .= ';'.'Shipping';
		$row .= ';'.$element->find('img',0)->$src;
		$row .= ';'.'Natue';
		$row .= ';'.$base_url.$element->find('a',0)->href;
		$row .= ';'.trim($element->find('div[itemprop="description"]', 0)->plaintext);
		$row .= ';'.trim($element->find('div[itemprop="description"]', 0)->plaintext);
		$row .= ';'.'0';
		$content['row'][] = $row;
	}
	
	$content['header'] = 'UID;CATEGORIES;BRAND;FULL NAME;PRICE;SHIPPING;IMAGE;RETAILER;DEEPLINK;DESCRIPTION;GLOBAL_DESCRIPTION;VOUCHER';
	//$content['row'] = '31;TV;Traders;32-inch Widescreen Full HD 1080p LED;748.49;10.99;http://www.awesem.com/compare/wp-content/uploads/compare/products/tv-150x100.jpg;Already Sheep;http://www.awesemthemes.com/;Lorem ipsum dolor sit amet, erant quodsi pro ex. Ei mel placerat similique. Nostrum philosophia at duo, oratio discere reprehendunt ne pro. Vix movet mundi probatus in. Nullam noster an per, mundi consequuntur ut pri, dicat soluta eum eu. Usu tale iracundia no, ex vim possim placerat recusabo. Mea alia etiam possit ex, te movet nihil dolorem quo. Homero legendos cu duo. Ius enim unum simul et. Eum ut legimus copiosae scripserit, ex unum dicam prodesset usu. Ne audire contentiones sea, pri adhuc vocibus ex. Ut qui movet cotidieque, justo dicant ex quo. Mei quem malis aeterno no. Id partem utamur sed, qui atomorum salutatus ea. Vel eripuit commune consequat no, te homero facilisi vis. Audire molestie adipisci eam ei. Veri putent probatus in has, duo cu utinam admodum vituperatoribus. Adipisci ocurreret eu eam. At sit eirmod delenit necessitatibus. Pri liber discere efficiendi ea. Gubergren percipitur eos an, est eu case adversarium delicatissimi, quis perpetua iracundia in duo. Eos primis tritani in. Dolore aperiri nec te, at duo posse nostrud legimus. Ut has detracto deseruisse intellegebat, ea mea novum platonem eloquentiam. Qui ne eius meis admodum, mei assum summo cu. ;;';
	
	file_put_contents(AW_ROOT_PATH.'/functions/views/admin/results.csv', $content['header']."\n".join("\n", $content['row']));
    
}
*/


?>