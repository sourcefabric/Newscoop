/*
 * @(#)CampResources.java
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
     * CampResources manages all resource related things in Campfire
     */

import javax.swing.*;
import java.util.*;
//import java.util.ResourceBundle;
//import java.util.Locale;

public final class CampResources {

    private static ResourceBundle resources;
    private static boolean bRightToLeft;
    
    public static void init( String lang){
            try {
                resources = PropertyResourceBundle.getBundle("resources.Campfire", new Locale(lang, lang.toUpperCase()));
                if (resources.getString("Orientation").equals("RIGHT_TO_LEFT")) bRightToLeft=true;
                
            } catch (MissingResourceException mre2) {
                System.err.println("Campfire's properties not found");
            }
    }
    
    public static ResourceBundle getBundle( ){
        return resources;
    }

    public static boolean isRightToLeft( ){
        return bRightToLeft;
    }

    public static String get( String value){
    	String str;
    	try {
//            try{    
//        	   str = new String( resources.getString(value).getBytes("UTF-16"));
//    	   }catch(Exception e){
//        	   str = new String("Not translated");
//    	   }
       	   str = resources.getString(value);
    	} catch (MissingResourceException mre) {
    	   str = new String("Not translated");
    	}
    	return str;
	}

}