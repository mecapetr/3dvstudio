<?php
class Seo_Sitemap_Library_SitemapFunctions
{
	private $modifyDate;
	private $changefreq;
	private $domain;
	private $path = "Public/Xml/Sitemaps";

	public function generate($domain){
		
		 $langs = new Models_Language_DB_LanguageMutation();
		 $allLangs = $langs->getAllItems("enabled = 1","priority");
		 
		 $sitemap = new Seo_Sitemap_Models_Sitemap();
		 $smData = $sitemap->getOneRow("seoID = 1");
		 
		 $this->changefreq = $smData->changefreq;
		 $this->modifyDate = date("Y-m-d");
		 $this->domain     = $domain;
		 
		 		 
		 $article  = new Content_Models_Article();
		 $link     = new Content_Models_Link();
		 $linkLang = new Content_Models_LinkLang();
		 
		 $i = 1;
		 foreach($allLangs as $l){
		 	
		 	$this->xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>');	
		 	$this->setUrl('http://'.$this->domain);
		 	
		 	$suffix = "";
		 	if($i != 1)$suffix = "_".$l->suffix;
		 	
		 	
		 	$links = $link->getLinksToSitemap($l->suffix);
		 	foreach($links as $li){

		 		$linkContent = $linkLang->getLinkdata("LL.linkID = '$li->linkID' AND LL.lang = '$l->suffix'");
		 		$lLink = $this->implodeUrlLink($linkContent,$link,$l->suffix);
		 		 
		 		$this->setUrl('http://'.$this->domain."/".$l->suffix.$lLink);
		 	}
		 	
		 	
		 	$articles = $article->getArticlesToSitemap($l->suffix);
		 	foreach($articles as $art){
		 		$linkContent = $linkLang->getLinkdata("LL.linkID = '$art->linkID' AND LL.lang = '$l->suffix'");
		 		$artLink = $this->implodeUrlLink($linkContent,$link,$l->suffix);
		 		
		 		$this->setUrl('http://'.$this->domain."/".$l->suffix.$artLink."/".$art->articleID."-".$art->niceTitle);
		 	}
		 	
		 	
		 	
		 	file_put_contents("./".$this->path."/sitemap".$suffix.".xml",$this->xml->asXML());
		 	
		 	$i++;
		 }
		
	}
	
	public function getAllXmls(){
		
		$dir = scandir("./".$this->path);
		if(!empty($dir)){
			
			foreach($dir as $key => $d){
				if($d == "." || $d == "..")unset($dir[$key]);
			}
			
			return $dir;
			
		}
		
	}
	
	public function getPath(){
		return $this->path;
	}
	
	private function implodeUrlLink($it,$link,$lang){
		 
		$this->urlLink = array();
		 
		$this->urlLink[] = $it->niceTitle;
		 
		$this->setUrlLink($it->parentID,$link,$lang);  //vytvori cestu k dane podkategorii pro strankovani
		 
		$link = "/".implode("/",$this->urlLink);
		 
		return $link;
	}
	 
	private function setUrlLink($parentID,$link,$lang){
		 
		if($parentID){
			 			 
			$linkLang = new Content_Models_LinkLang();
			$parentLink = $linkLang->getLinkdata("LL.linkID = '$parentID' AND LL.lang = '$lang'");
			 
			array_unshift($this->urlLink, $parentLink->niceTitle);
			 
			$this->setUrlLink($parentLink->parentID,$link,$lang);
		}
		 
	}
	
	private function setUrl($urlData){
		
		$url = $this->xml->addChild("url");
		
		$url->addChild('loc',$urlData);
		$url->addChild('lastmod',$this->modifyDate);
		$url->addChild('changefreq',$this->changefreq);
		$url->addChild('priority','0.9');
		
	}

}

?>