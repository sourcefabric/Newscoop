/*
 * @(#)CampToolbarIcon.java
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
     * CampToolbarIcon. The icons of the toolbar are stored outside of the jar,
     * so they can be easily changed. But it is time-consuming to get 16 small images,
     * so why don't put all the images into one big image (big.gif).
     * This class maps the image names with "gif" extensions into portions
     * of the big.gif image.
     */



import javax.swing.*;
import java.awt.*;
import java.net.URL;

class CampToolbarIcon extends ImageIcon{

    Image im;
    URL imgURL;

    public CampToolbarIcon(String s,Campfire parent){
        super();

        try{        
            imgURL = Campfire.class.getResource("icons/" + s); 
        }
        catch(Exception e){}

        im=parent.getImage(imgURL);
        super.setImage(im);
        
    }
    
}