/*
 * @(#)FontColorStyleAction.java
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
     * FontColorStyleAction is triggered when the user clicks the ok button of the
     * FontColorFrame frame. If there is a selected text, its color will be changed.
     * It is also triggered when all the attributes of a text must be cleared.
     * In this case, it will set the color to null.
     */



import javax.swing.text.*;
import javax.swing.*;
import java.awt.event.*;
import java.awt.*;

class FontColorStyleAction extends StyledEditorKit.StyledTextAction{
    JTextPane text;
    Color color;
    boolean nullcol;
    String defaultColor;

    public FontColorStyleAction(String action,JTextPane tp,Color ch,String cols){
        super(action);
        defaultColor=new String(cols);
        if (action.equals("NULLOL")) nullcol=true; else nullcol=false;
        text=tp;
        color=ch;
    }
    
    public void actionPerformed(ActionEvent e){
        if (text!=null){
            if (nullcol) color=null;
            if (!defaultColor.equals(""))
                color=new ColorConverter(defaultColor).getColor();
            if (color!=null)
            {
            AttributeSet attr=text.getCharacterAttributes();
            MutableAttributeSet ats=new SimpleAttributeSet();
            StyleConstants.setForeground(ats,color);
            setCharacterAttributes(text,ats,false);
            }
            else 
            {
            MutableAttributeSet t=new SimpleAttributeSet();
            setCharacterAttributes(text,t,true);
            }
        }
    }
}