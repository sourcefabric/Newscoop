/*
 * @(#)SpaceFieldDocument.java
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
     * SpaceFieldDocument is the document used by SpaceControl, and 
     * as a consequence, it is not used in the actual release.
     */


import com.sun.java.swing.text.*;
import com.sun.java.swing.*;

class SpaceFieldDocument extends PlainDocument{
    private JTextComponent tC;
    
    public SpaceFieldDocument(JTextComponent tc){
        tC=tc;
    }
    
    public void insertString(int offset,String s, AttributeSet as) throws BadLocationException{
        int c=-1;
        String temp=tC.getText();
        super.insertString(offset,s,as);
        try{
            if (tC.getText().equals("")) c=0;
                else c=new Integer(tC.getText()).intValue();
        }
        catch(Exception e){
            tC.setText(temp);
        }
        if ((c<0)||(c>100)) tC.setText(temp);
    }
}