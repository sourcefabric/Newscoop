<?php
class SimplePager {
	var $m_offsets;
	var $m_urls;
	var $m_selectedPageNumber;
	var $m_offset;
	var $m_renderedStr = null;
	
	/**
	 * SimplePager, unlike the PEAR Pager class, is a pager made to work with template-like layouts.
	 * The constructor sets up the variables you need to render the links, and you can render them
	 * however you like.  There is a default render function for reference.
	 *
	 * @param int $p_totalItems
	 * @param int $p_itemsPerPage
	 * @param string $p_offsetVarName
	 * @param string $p_baseUrl
	 */
	function SimplePager($p_totalItems, $p_itemsPerPage, $p_offsetVarName, $p_baseUrl) 
	{
		$this->m_urls["first"] = $p_baseUrl;
		$this->m_urls["previous"] = $p_baseUrl;
		$this->m_urls["next"] = $p_baseUrl;
		$this->m_urls["last"] = $p_baseUrl;
		$this->m_urls["links"] = array();
		
		if ($p_totalItems > $p_itemsPerPage) {
			$this->m_offset = isset($_REQUEST[$p_offsetVarName]) ? $_REQUEST[$p_offsetVarName] : 0;
			if ($this->m_offset < 0) {
				$this->m_offset = 0;
			}
			$remainder = $p_totalItems % $p_itemsPerPage;
			if ($remainder == 0) {
				$this->m_offsets = SimplePager::_range(0, $p_totalItems-1, $p_itemsPerPage);
			}
			else {
				$this->m_offsets = SimplePager::_range(0, $p_totalItems, $p_itemsPerPage);
			}
			$numPages = count($this->m_offsets);
			$this->m_selectedPageNumber = $numPages;
			$this->m_urls = array();
			for ($index = 0; $index < count($this->m_offsets); $index++) {
				$this->m_urls["links"][$index+1] = $p_baseUrl."$p_offsetVarName=".$this->m_offsets[$index];
				if (($this->m_selectedPageNumber == $numPages) && ($this->m_offsets[$index] > $this->m_offset)) {
					$this->m_selectedPageNumber = $index;
				}
			}
			$this->m_urls["first"] = $p_baseUrl."$p_offsetVarName=".$this->m_offsets[0];
			$this->m_urls["previous"] = $p_baseUrl."$p_offsetVarName=".$this->m_offsets[max(0, $this->m_selectedPageNumber-2)];
			$this->m_urls["next"] = $p_baseUrl."$p_offsetVarName=".$this->m_offsets[min($numPages-1, $this->m_selectedPageNumber)];
			$this->m_urls["last"] = $p_baseUrl."$p_offsetVarName=".$this->m_offsets[$numPages-1];	
		}
	} // constructor

	
	/**
	 * Default way to render the links.  Feel free to come up with your own way.
	 */
	function render()
	{
		if ($this->m_renderedStr !== null) {
			return $this->m_renderedStr;
		}
		$this->m_renderedStr = "";
		if (count($this->m_urls["links"]) > 1) {
			$this->m_renderedStr .= "<a href=\"".$this->m_urls["first"]."\">".getGS("First")."</a> | ";
			$this->m_renderedStr .= "<a href=\"".$this->m_urls["previous"]."\">".getGS("Previous")."</a> | ";
			foreach ($this->m_urls["links"] as $number => $url) {
				if ($number == $this->m_selectedPageNumber) {
					$this->m_renderedStr .= "$number | ";
				} else {
					$this->m_renderedStr .= "<a href=\"".$url."\">$number</a> | ";	
				}
			}
			$this->m_renderedStr .= "<a href=\"".$this->m_urls["next"]."\">".getGS("Next")."</a> | ";
			$this->m_renderedStr .= "<a href=\"".$this->m_urls["last"]."\">".getGS("Last")."</a>";
		}
		return $this->m_renderedStr;
	} // fn render

	
	function _range($num1, $num2, $step=1)
	{
		$temp = array();
	   	for($i = $num1; $i <= $num2; $i += $step) {
	    	$temp[] = $i;
	   	}
	   	return $temp;
	} // fn _range

} // class SimplePager

?>