/*
 * @(#)TolImageIcon.java
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
     * TolImageIcon. The icons of the toolbar are stored outside of the jar,
     * so they can be easily changed. But it is time-consuming to get 16 small images,
     * so why don't put all the images into one big image (big.gif).
     * This class maps the image names with "gif" extensions into portions
     * of the big.gif image.
     */



import com.sun.java.swing.*;
import java.awt.*;

class TolImageIcon extends ImageIcon{
    public TolImageIcon(String s,Image image,Test parent){
        super();
        if (s.equals("bold.gif")) {r=0;c=0;}
        if (s.equals("italic.gif")) {r=0;c=1;}
        if (s.equals("underline.gif")) {r=0;c=2;}
        if (s.equals("color.gif")) {r=0;c=3;}
        if (s.equals("intlink.gif")) {r=0;c=4;}

        if (s.equals("left.gif")) {r=1;c=0;}
        if (s.equals("center.gif")) {r=1;c=1;}
        if (s.equals("right.gif")) {r=1;c=2;}
        if (s.equals("selall.gif")) {r=1;c=3;}
        if (s.equals("redo.gif")) {r=1;c=4;w=1;}
        
        if (s.equals("cut.gif")) {r=2;c=0;}
        if (s.equals("copy.gif")) {r=2;c=1;}
        if (s.equals("paste.gif")) {r=2;c=2;}
        if (s.equals("new.gif")) {r=2;c=3;}
        if (s.equals("undo.gif")) {r=2;c=4;w=1;}

        if (s.equals("clip.gif")) {r=3;c=0;}
        if (s.equals("clearall.gif")) {r=3;c=1;}
        if (s.equals("image.gif")) {r=3;c=2;}
        if (s.equals("upload.gif")) {r=3;c=3;}
        if (s.equals("title.gif")) {r=3;c=4;}

        if (s.equals("re.gif")) {r=4;c=0;}
        if (s.equals("model.gif")) {r=4;c=1;}
        if (s.equals("html.gif")) {r=4;c=2;}
        if (s.equals("link.gif")) {r=4;c=3;}
        if (s.equals("keyword.gif")) {r=4;c=4;}

        int wi,he;
        int mx=25;
        if (w==2) wi=25; else wi=15;
        if (h==2) he=25; else he=15;
        im=parent.createImage(wi,he);
        im.getGraphics().drawImage(image,0,0,wi,he,c*mx,r*mx,c*mx+wi,r*mx+he,Color.blue,null);
        super.setImage(im);
    }
    
    Image im;
    int r=0,c=0,w=2,h=2;
}