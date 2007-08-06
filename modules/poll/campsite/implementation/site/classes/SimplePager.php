<?php
class SimplePager {
    var $m_offsets;
    var $m_urls = array();
    var $m_selectedPageNumber = null;
    var $m_offset = null;
    var $m_renderedStr = null;
    var $m_numPages = 0;

    /**
     * SimplePager, unlike the PEAR Pager class, is a pager made to work
     * with template-like layouts. The constructor sets up the variables
     * you need to render the links, and you can render them
     * however you like.  There is a default render function for
     * reference.
     *
     * @param int $p_totalItems
     *         Total number of items.
     *
     * @param int $p_itemsPerPage
     *         Number of items to display per page.
     *
     * @param string $p_offsetVarName
     *         The name of the REQUEST variable which holds the order number
     *         of the first item on the selected page.
     *
     * @param string $p_baseUrl
     *         The url to which we attach the offset variable name.
     *
     * @param boolean $p_useSessions
     *         Set to TRUE if you want the offset item number to be stored in
     *         the session so that the user will return to their previous
     *         position in the pager when they leave the screen and come back
     *         to it.
     */
    function SimplePager($p_totalItems, $p_itemsPerPage, $p_offsetVarName, $p_baseUrl, $p_useSessions = true, $p_width = 10)
    {
        global $_REQUEST;

        $this->m_urls["links"] = array();
        if ($p_totalItems < 0) {
            $p_totalItems = 0;
        }
        if ($p_itemsPerPage < 1) {
            $p_itemsPerPage = 1;
        }

        // Get the current page number.
        if ($p_useSessions) {
            $this->m_offset = camp_session_get($p_offsetVarName, 0);
        } else {
            $this->m_offset = isset($_REQUEST[$p_offsetVarName]) ? $_REQUEST[$p_offsetVarName] : 0;
        }
        if ($this->m_offset < 0) {
            $this->m_offset = 0;
        } elseif ( ($this->m_offset) > $p_totalItems) {
            // If the offset is past the total number of items,
            // reset it.
            $this->m_offset = 0;
            if ($p_useSessions) {
                camp_session_set($p_offsetVarName, 0);
            }
        }

        // Only generate pager if there is more than one page of information.
        if ($p_totalItems > $p_itemsPerPage) {

            // Generate the offsets into the list.
            $remainder = $p_totalItems % $p_itemsPerPage;
            if ($remainder == 0) {
                $this->m_offsets = SimplePager::_range(0, $p_totalItems-1, $p_itemsPerPage);
            } else {
                $this->m_offsets = SimplePager::_range(0, $p_totalItems, $p_itemsPerPage);
            }

            $this->m_numPages = count($this->m_offsets);
            $this->m_selectedPageNumber = floor($this->m_offset/$p_itemsPerPage)+1;

            if ($p_width > $this->m_numPages) {
                $p_width = $this->m_numPages;
            }

            // Generate the numbered links
            if ($this->m_selectedPageNumber < ($p_width/2 + 1)) {
                $begin = 0;
                $end = $p_width;
            } else if ($this->m_selectedPageNumber > ($this->m_numPages - ($p_width/2))) {
                $begin = $this->m_numPages - $p_width;
                $end = $this->m_numPages;
            } else {
                $begin = $this->m_selectedPageNumber - ceil($p_width/2) - 1;
                $end = $this->m_selectedPageNumber + ceil($p_width/2);
            }
            for ($index = $begin; $index < $end; $index++) {
                $this->m_urls["links"][$index+1] = $p_baseUrl."$p_offsetVarName=".$this->m_offsets[$index];
            }

            // Generate special links.
            if ($this->m_selectedPageNumber > 1) {
                $this->m_urls["first"] = $p_baseUrl."$p_offsetVarName=".$this->m_offsets[0];
                   $this->m_urls["previous"] = $p_baseUrl."$p_offsetVarName=".$this->m_offsets[max(0, $this->m_selectedPageNumber-2)];
            } 
               if ($this->m_selectedPageNumber > 10) {
                $this->m_urls["previous_10_pages"] = $p_baseUrl."$p_offsetVarName=".$this->m_offsets[max(0, $this->m_selectedPageNumber-11)];
            }
            if ($this->m_selectedPageNumber > 100) {
                $this->m_urls["previous_100_pages"] = $p_baseUrl."$p_offsetVarName=".$this->m_offsets[max(0, $this->m_selectedPageNumber-101)];
            }
            if ( ($this->m_numPages > $this->m_selectedPageNumber)) {
                $this->m_urls["next"] = $p_baseUrl."$p_offsetVarName=".$this->m_offsets[min($this->m_numPages-1, $this->m_selectedPageNumber)];
            } 
            if ( ($this->m_numPages - $this->m_selectedPageNumber) > 9) {
                $this->m_urls["next_10_pages"] = $p_baseUrl."$p_offsetVarName=".$this->m_offsets[min($this->m_numPages-1, $this->m_selectedPageNumber+9)];
            }
            if ( ($this->m_numPages - $this->m_selectedPageNumber) > 99) {
                $this->m_urls["next_100_pages"] = $p_baseUrl."$p_offsetVarName=".$this->m_offsets[min($this->m_numPages-1, $this->m_selectedPageNumber+99)];
            }
            if ( ($this->m_numPages > $this->m_selectedPageNumber)) {
                $this->m_urls["last"] = $p_baseUrl."$p_offsetVarName=".$this->m_offsets[$this->m_numPages-1];
            } 
        }
    } // constructor


    /**
     * Default way to render the links.  Feel free to come up with your own way.
     * @return string
     */
    function render()
    {
        if ($this->m_renderedStr !== null) {
            return $this->m_renderedStr;
        }
        $this->m_renderedStr = "";
        if (count($this->m_urls["links"]) > 1) {
            if (isset($this->m_urls["first"])) {
                $this->m_renderedStr .= "<a href=\"".$this->m_urls["first"]."\">".getGS("First")."</a> | ";
            }
            if (isset($this->m_urls["previous_100_pages"])) {
                $this->m_renderedStr .= "<a href=\"".$this->m_urls["previous_100_pages"]."\">".getGS("Previous")." 100</a> | ";
            }
            if (isset($this->m_urls["previous_10_pages"])) {
                $this->m_renderedStr .= "<a href=\"".$this->m_urls["previous_10_pages"]."\">".getGS("Previous")." 10</a> | ";
            }
            if (isset($this->m_urls["previous"])) {
                $this->m_renderedStr .= "<a href=\"".$this->m_urls["previous"]."\">".getGS("Previous")."</a> | ";
            }
            foreach ($this->m_urls["links"] as $number => $url) {
                if ($number == $this->m_selectedPageNumber) {
                    $this->m_renderedStr .= "$number | ";
                } else {
                    $this->m_renderedStr .= "<a href=\"".$url."\">$number</a> | ";
                }
            }
            if (isset($this->m_urls["next"])) {
                $this->m_renderedStr .= "<a href=\"".$this->m_urls["next"]."\">".getGS("Next")."</a> | ";
            }
            if (isset($this->m_urls["next_10_pages"])) {
                $this->m_renderedStr .= "<a href=\"".$this->m_urls["next_10_pages"]."\">".getGS("Next")." 10</a> | ";
            }
            if (isset($this->m_urls["next_100_pages"])) {
                $this->m_renderedStr .= "<a href=\"".$this->m_urls["next_100_pages"]."\">".getGS("Next")." 100</a> | ";
            }
            if (isset($this->m_urls["last"])) {
                $this->m_renderedStr .= "<a href=\"".$this->m_urls["last"]."\">".getGS("Last")."</a>";
            }
        }
        return $this->m_renderedStr;
    } // fn render


    /**
     * Create an array of integers starting at $p_num1
     * and ending at $p_num2 (inclusive), going up by a
     * value of $p_step each time.
     *
     * @param int $p_num1
     * @param int $p_num2
     * @param int $p_step
     * @return array
     */
    function _range($p_num1, $p_num2, $p_step = 1)
    {
        $temp = array();
           for($i = $p_num1; $i <= $p_num2; $i += $p_step) {
            $temp[] = $i;
           }
           return $temp;
    } // fn _range

} // class SimplePager

?>