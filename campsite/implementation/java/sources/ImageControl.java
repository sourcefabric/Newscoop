/*
 * @(#)ImageControl.java
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
     * ImageControl is the Panel included in the textarea, representing an image.
     * It has some components that are not visible, their content is transfered 
     * into the corresponding fields of the ImageFrame, when this is opened.
     * The toString method is used when generating the html.
     */

import javax.swing.*;
import java.awt.*;
import java.awt.event.*;
import java.util.*;

class ImageControl extends JPanel{
    
    JLabel labl;
    private ImageProperties myProperties= new ImageProperties();
        
    public ImageControl(CampToolbarIcon ico){
        setPreferredSize(new Dimension(40,40));
        setMaximumSize(new Dimension(40,40));
        setBorder(BorderFactory.createEtchedBorder());    
        this.setCursor(Cursor.getPredefinedCursor(Cursor.DEFAULT_CURSOR));
        addIcon(ico);
    }
    
    private void addIcon(CampToolbarIcon ico){
        labl=new JLabel("");
        labl.setIcon(ico);
        add(labl,"CENTER");
        labl.setEnabled(true);
        labl.addMouseListener(new MouseListener(){
            public void mouseClicked(MouseEvent e){
                if (e.getClickCount()==2 && SwingUtilities.isLeftMouseButton(e))
                    editProperties();
            }
            public void mousePressed(MouseEvent e){}
            public void mouseReleased(MouseEvent e){}
            public void mouseEntered(MouseEvent e){}
            public void mouseExited(MouseEvent e){}
        }
        );
        
    }

	private void editProperties(){
		CampBroker.getImage().edit(this);
	}

    public void setProperties(ImageProperties props){
        myProperties=props;
    }

    public ImageProperties getProperties(){
        return myProperties;
    }


    public String toString(){
        String ali=new String();
		String alti, subi;
		if ((myProperties.altText==null)||(myProperties.altText.equals(""))) alti=""; else alti=" ALT=\""+myProperties.altText+"\"";
		if ((myProperties.subTitle==null)||(myProperties.subTitle.equals(""))) subi=""; else subi=" SUB=\""+myProperties.subTitle+"\"";
        if (myProperties.alignWay.length()!=0) ali=" ALIGN="+ myProperties.alignWay;
        return "<!** Image " + myProperties.imageName+ali+alti+subi+">";
    }
    
    
}