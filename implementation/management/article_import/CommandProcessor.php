<?
require_once('DocBookParser.php');
require_once('Article.php');

// Command processor
if ($_REQUEST["form_name"] == "upload_article_form") {
	upload_article_handler($_REQUEST, $_SESSION, $_FILES);
}

function upload_article_handler(&$request, &$session, &$files) {
	
	// Check input
	if (!isset($request["Pub"]) 
		|| (!isset($request["Issue"]))
		|| (!isset($request["Section"]))
		|| (!isset($request["Language"]))
		|| (!isset($request["sLanguage"]))
		|| (!isset($request["Article"]))
		|| (!isset($files["filename"]))) {
		echo "Input Error: Missing input";
		return;
	}
	$publication = $request["Pub"];
	$issue = $request["Issue"];
	$section = $request["Section"];
	$language = $request["Language"];
	$sLanguage = $request["sLanguage"];
	$articleNumber = $request["Article"];
	
	// Unzip the sxw file to get the content.
	$zip = zip_open($files["filename"]["tmp_name"]);
	if ($zip) {
		$xml = null;
		while ($zip_entry = zip_read($zip)) {
			if (zip_entry_name($zip_entry) == "content.xml") {
		       	if (zip_entry_open($zip, $zip_entry, "r")) {
		           	$xml = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
			        zip_entry_close($zip_entry);
		       	}
			}
		}
		zip_close($zip);
		
		if (!is_null($xml)) {
			// Write the XML to a file because the XSLT functions
			// require it to be in a file in order to be processed.
			$tmpXmlFilename = tempnam("/tmp", "ArticleImportXml");
			$tmpXmlFile = fopen($tmpXmlFilename, "w");
			fwrite($tmpXmlFile, $xml);
			fclose($tmpXmlFile);
			
			// Transform the OpenOffice document to DocBook format.
			$xsltProcessor = xslt_create();
			$docbookXml = xslt_process($xsltProcessor, 
									   $tmpXmlFilename, 
									   "sxwToDocbook.xsl");
			unlink($tmpXmlFilename);
			
			// Parse the docbook to get the data.
			$docBookParser =& new DocBookParser();
			$docBookParser->parseString($docbookXml, true);
			
			$article =& new Article($publication, $issue, $section, $articleNumber, $language);
			$article->setTitle($docBookParser->getTitle());
			$article->setIntro($docBookParser->getIntro());
			$article->setBody($docBookParser->getBody());
			
			// Go back to the "Edit Article" page.
			header("Location: /$ADMIN/pub/issues/sections/articles/edit.php?Pub=".$publication."&Issue=".$issue."&Section=".$section."&Article=".$articleNumber."&Language=".$language."&sLanguage=".$sLanguage);
		} // if (!is_null($xml))
	} // if ($zip)
	
	// Some sort of error occurred - show the upload page again.
	include("index.php");
} // fn upload_article_handler

?>