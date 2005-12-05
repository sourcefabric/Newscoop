<?PHP

class Translation extends DatabaseObject {
	var $m_dbTableName = 'Translations';
	var $m_keyColumnNames = array('phrase_id', 'fk_language_id');
	var $m_keyIsAutoIncrement = false;
	var $m_columnNames = array('id', 
	                           'phrase_id', 
	                           'fk_language_id', 
	                           'translation_text');
	
	function Translation($p_languageId = null, $p_phraseId = null)
	{
		if (is_numeric($p_phraseId)) {
			$this->m_data['phrase_id'] = $p_phraseId;
		}
		if  (is_numeric($p_languageId)) {
			$this->m_data['fk_language_id'] = $p_languageId;
		}
		if ($this->keyValuesExist()) {
			$this->fetch();
		}
	} // constructor
	
	
	/**
	 * Create a translation of a phrase.  If the phrase ID is set in the
	 * constructor, we assume that the phrase already exists and we are
	 * just creating a translation, and not a new phrase.  
	 * 
	 * @param string $p_text
	 * 		Optional. The translation text.
	 * @return boolean
	 */
	function create($p_text = null)
	{
		global $Campsite;
		if (!isset($this->m_data['phrase_id'])) {
			$this->m_data['phrase_id'] = Translation::__GeneratePhraseId();
		}
		return parent::create(array("translation_text" => $p_text));
	} // fn create
	
	
	/**
	 * Delete the phrase and all of its translations.
	 * This can be called statically or as a member function.
	 * If called statically, you must give it an argument.
	 */
	function deletePhrase($p_phraseId = null)
	{
		global $Campsite;
		if (is_null($p_phraseId)) {
			$p_phraseId = $this->m_data['phrase_id'];
			$this->m_exists = false;
		}
		$sql = "DELETE FROM Translations WHERE phrase_id=$p_phraseId";
		$Campsite['db']->Execute($sql);
	} // fn deletePhrase
	
	
	/**
	 * Create a unique identifier for a phrase.
	 * @access private
	 */
	function __GeneratePhraseId() 
	{
	    global $Campsite;
		$queryStr = 'UPDATE AutoId SET translation_phrase_id=LAST_INSERT_ID(translation_phrase_id + 1)';
		$Campsite['db']->Execute($queryStr);
		if ($Campsite['db']->Affected_Rows() <= 0) {
			// If we were not able to get an ID.
			return 0;
		}
		return $Campsite['db']->Insert_ID();	    
	} // fn __generatePhraseId
			
		
	/**
	 * Get the phrase ID.
	 *
	 * @return int
	 */
	function getPhraseId()
	{
		return $this->getProperty('phrase_id');
	} // fn getPhraseId
	
	
	/**
	 * Get the language ID.
	 *
	 * @return int
	 */
	function getLanguageId()
	{
		return $this->getProperty('fk_language_id');
	} // fn getLanguageId
	
	
	/**
	 * Get the text.
	 *
	 * @return string
	 */
	function getText()
	{
		return $this->getProperty('translation_text');
	} // fn getText
	
	
	/**
	 * Set the translation text.
	 *
	 * @param string $p_value
	 * @return boolean
	 */
	function setText($p_value)
	{
		return $this->setProperty('translation_text', $p_value);
	} // fn setText
	
	
	/**
	 * A convenience function to just grab a translation.
	 *
	 * @param int $p_phraseId
	 * @param int $p_languageId
	 */
	function GetPhrase($p_languageId, $p_phraseId)
	{
		global $Campsite;
		$sql = "SELECT translation_text FROM Translations"
			   ." WHERE phrase_id=".$p_phraseId
			   ." AND fk_language_id=".$p_languageId;
		return $Campsite['db']->GetOne($sql);
	} // fn GetPhrase
	
	
	/**
	 * Enter description here...
	 *
	 * @param int $p_phraseId
	 * @param int $p_languageId
	 * @param string $p_text
	 */
	function SetPhrase($p_languageId, $p_phraseId, $p_text)
	{
		if (!is_numeric($p_languageId) || !is_numeric($p_phraseId) || !is_string($p_text)) {
			return false;
		}
		$translation =& new Translation($p_languageId, $p_phraseId);
		if ($translation->exists()) {
			return $translation->setText($p_text);
		} else {
			return $translation->create($p_text);
		}
	} // fn SetPhrase
	
	
	/**
	 * Return an array of phrases indexed by language ID.
	 *
	 * @param int $p_phraseId
	 * @param array $p_sqlOptions
	 * @return array
	 */
	function GetTranslations($p_phraseId, $p_sqlOptions = null)
	{
		global $Campsite;
		$phrases = array();
		if (!is_numeric($p_phraseId)) {
			return $phrases;
		}
		$sql = "SELECT fk_language_id, translation_text FROM Translations WHERE phrase_id=".$p_phraseId;
		$rows = $Campsite['db']->GetAll($sql);
		if (is_array($rows)) {
			foreach ($rows as $row) {
				$phrases[$row['fk_language_id']] = $row['translation_text'];
			}
		}
		return $phrases;
	} // fn GetTranslations
	
} // class Translation
?>