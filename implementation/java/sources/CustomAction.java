/*
 * @(#)CustomAction.java
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
     * CustomAction extends StyledEditorKit.StyledTextAction, and is a class for the actions 
     * bounded to menu points, or to the buttons on the toolbar.
     * It will either set some CharacterAttributes/ParagraphAttributes,
     * or it will execute a method from the main class.
     */

import com.sun.java.swing.*;
import com.sun.java.swing.text.*;
import java.awt.event.*;
import java.awt.*;

class CustomAction extends StyledEditorKit.StyledTextAction{
//    final static int FORM=0;
    //final static int PREVIEW=1;
    final static int DUMP=2;
    final static int IMAGE=3;
    final static int NEW=4;
    final static int SPACE=5;
    final static int UPLOAD=6;
    final static int CLIP=7;
    final static int COLOR=8;
    final static int CLEAR=9;
    final static int SETHTML=10;
    final static int BOLD=11;
    final static int ITALIC=12;
    final static int UNDERLINE=13;
    final static int CENTER=14;
    final static int RIGHT=15;
    final static int TITLE=16;
    final static int EXTLINK=17;
    final static int WORD=18;
    final static int INTLINK=19;
    final static int RE=20;
    //final static int NEW=5;
    int source;
    Test parent;

    public CustomAction(String action, int src,Test p){
        super(action);
        source=src;
        parent=p;
    }
    
    public void actionPerformed(ActionEvent e){
        
        /*if(source==FORM)
            parent.iform.setVisible(true);*/
/*        if(source==PREVIEW)
            parent.preview();*/
        if(source==DUMP)
            parent.dump();
        if(source==IMAGE)
            parent.insertImage(true);
        if(source==NEW)
            parent.newFile(true);
        if(source==SPACE)
            parent.insertSpace();
        if(source==EXTLINK)
            parent.insertLink(LinkControl.EXT,null,"",true);
        if(source==INTLINK)
            parent.insertLink(LinkControl.INT,null,"",true);
        if(source==UPLOAD)
            parent.upload();
        if(source==CLIP)
            parent.clip();
        if(source==COLOR)
            parent.fontColor();
        if(source==CLEAR)
            parent.fontClear();
        if(source==SETHTML)
            parent.setHtml();
        if(source==TITLE)
            parent.setTitle();
        if(source==WORD)
            parent.openWord();
        if(source==RE)
            parent.regen();
        if (source==BOLD)
        {
            AttributeSet attr=parent.textPane.getCharacterAttributes();
            MutableAttributeSet ats=new SimpleAttributeSet();
            StyleConstants.setBold(ats,true);
            setCharacterAttributes(parent.textPane,ats,false);
            //System.out.println("kezd"+parent.textPane.getSelectionStart());
        }
        if (source==ITALIC)
        {
            AttributeSet attr=parent.textPane.getCharacterAttributes();
            MutableAttributeSet ats=new SimpleAttributeSet();
            StyleConstants.setItalic(ats,true);
            setCharacterAttributes(parent.textPane,ats,false);
            //false=append, not owerwrite
        }
        if (source==UNDERLINE)
        {
            AttributeSet attr=parent.textPane.getCharacterAttributes();
            MutableAttributeSet ats=new SimpleAttributeSet();
            StyleConstants.setUnderline(ats,true);
            setCharacterAttributes(parent.textPane,ats,false);
        }
        if (source==CENTER)
        {
            AttributeSet attr=parent.textPane.getCharacterAttributes();
            MutableAttributeSet ats=new SimpleAttributeSet();
            StyleConstants.setAlignment(ats,StyleConstants.ALIGN_CENTER);
            setParagraphAttributes(parent.textPane,ats,false);
        }
        if (source==RIGHT)
        {
            AttributeSet attr=parent.textPane.getCharacterAttributes();
            MutableAttributeSet ats=new SimpleAttributeSet();
            StyleConstants.setAlignment(ats,StyleConstants.ALIGN_RIGHT);
            setParagraphAttributes(parent.textPane,ats,false);
        }
    }
}