/*
 * @(#)CampBroker.java
 *
 * Copyright (c) 2000,2001 Media Development Loan Fund
 *
 * CAMPSITE is a Unicode-enabled multilingual web content                     
 * management system for news publications.                                   
 * CAMPFIRE is a Unicode-enabled java-based near WYSIWYG text editor.         
 * Copyright (C)2000,2001  Media Development Loan Fund                        
 * contact: contact@campware.org - http://www.campware.org                    
 * Campware encourages further development. Please let us know.               
 *                                                                            
 * This program is free software; you can redistribute it and/or              
 * modify it under the terms of the GNU General Public License                
 * as published by the Free Software Foundation; either version 2             
 * of the License, or (at your option) any later version.                     
 *                                                                            
 * This program is distributed in the hope that it will be useful,            
 * but WITHOUT ANY WARRANTY; without even the implied warranty of             
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the               
 * GNU General Public License for more details.                               
 *                                                                            
 * You should have received a copy of the GNU General Public License          
 * along with this program; if not, write to the Free Software                
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


    /**
     * CampBroker contains all components used in Campfire
     */
import javax.swing.*;


public final class CampBroker
{
    

	/** Use to manage images. */
	public static ImageObject getImage()
	{
		return imageObject;
	}

	/** Use to manage fonts. */
	public static FontObject getFont()
	{
		return fontObject;
	}

	/** Use to manage external links. */
	public static ExternalLinkObject getExternalLink()
	{
		return extLinkObject;
	}

	/** Use to manage internal links. */
	public static InternalLinkObject getInternalLink()
	{
		return intLinkObject;
	}

	/** Use to manage audio links. */
//	public static AudioLinkObject getAudioLink()
//	{
//		return audLinkObject;
//	}

	/** Use to manage video links. */
//	public static VideoLinkObject getVideoLink()
//	{
//		return vidLinkObject;
//	}

	/** Use to manage keywords. */
	public static KeywordObject getKeyword()
	{
		return keywordObject;
	}

	/** Use to manage subheads. */
	public static SubheadObject getSubhead()
	{
		return subheadObject;
	}


// Attributes:
	private static ImageObject imageObject = null;
	private static FontObject fontObject = null;
	private static KeywordObject keywordObject = null;
	private static ExternalLinkObject extLinkObject = null;
	private static InternalLinkObject intLinkObject = null;
//	private static AudioLinkObject audLinkObject = null;
//	private static VideoLinkObject vidLinkObject = null;
	private static SubheadObject subheadObject = null;

	static
	{
		imageObject = new ImageObject();
		fontObject = new FontObject();
		keywordObject = new KeywordObject();
		extLinkObject = new ExternalLinkObject();
		intLinkObject = new InternalLinkObject();
//		audLinkObject = new AudioLinkObject();
//		vidLinkObject = new VideoLinkObject();
		subheadObject = new SubheadObject();
	}
}
