<?PHP
require_once('Localizer.php');

$localizerLanguage =& new LocalizerLanguage('locals', 'xx');
$localizerLanguage->setMode('xml');
echo "Add some strings...<br>";
$localizerLanguage->addString("foo", "foo");
$localizerLanguage->addString("bar", "bar");
$localizerLanguage->addString("high", "high");
$localizerLanguage->addString("low", "low");
$localizerLanguage->dumpToHtml();

echo "Add a string in between...<br>";
$localizerLanguage->addString("test", "test", 1);
$localizerLanguage->dumpToHtml();

echo "Update the value of 'test'...<br>";
$success = $localizerLanguage->updateString("test", "test", "***");
if (!$success) {
    echo "ERROR UPDATING VALUE<br>";
}
$localizerLanguage->dumpToHtml();

echo "Update the key for 'test'...<br>";
$success = $localizerLanguage->updateString("test", "test_new");
if (!$success) {
    echo "ERROR UPDATING KEY<br>";
}
$localizerLanguage->dumpToHtml();

echo "Update the key and value for 'test'...<br>";
$success = $localizerLanguage->updateString("test_new", "boo", "ghost");
if (!$success) {
    echo "ERROR UPDATING STRING<br>";
}
$localizerLanguage->dumpToHtml();

echo "Move string forward (0 to 4)...<br>";
$localizerLanguage->moveString(0, 4);
$localizerLanguage->dumpToHtml();

echo "Move string foo to position 1...<br>";
$localizerLanguage->moveString('foo', 1);
$localizerLanguage->dumpToHtml();

echo "Delete foo..<br>";
$localizerLanguage->deleteString("foo");
$localizerLanguage->dumpToHtml();

echo "Delete position 2...<br>";
$localizerLanguage->deleteStringAtPosition(2);
$localizerLanguage->dumpToHtml();

echo "Save to xml...<br>";
$xml = $localizerLanguage->saveFile('xml');
echo "<pre>".htmlspecialchars($xml)."</pre>";

echo "Save as GS...<br>";
$gs = $localizerLanguage->saveFile('gs');
echo "<pre>".htmlspecialchars($gs)."</pre>";

echo "Load XML...<br>";
$xmlLang =& new LocalizerLanguage('locals', 'xx');
$result = $xmlLang->loadFile('xml');
if (!$result) {
    echo "Error!  Could not load XML file.<br>";
}
else {
    echo "Success!<br>";
}

echo "<br>Load GS...<br>";
$gsLang =& new LocalizerLanguage('locals', 'xx');
$result = $gsLang->loadFile('gs');
if (!$result) {
    echo "Error!  Could not load GS file.<br>";
}
else {
    echo "Success!<br>";
}

echo "<br>Testing for equality...<br>";
if (!$gsLang->equal($xmlLang)) {
    echo "Error! Not Equal<br>";
    echo "GS: <br>";
    $gsLang->dumpToHtml();
    echo "XML: <br>";
    $xmlLang->dumpToHtml();
}
else {
    echo "Success! They are equal<br>";    
}

echo "Testing ability to get languages in the base directory...<br>";
$languages = Localizer::GetLanguages();
echo "<pre>";
print_r($languages);
echo "</pre>";

?>