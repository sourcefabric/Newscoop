/*
 * @(#)KeyDocument.java
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
     * KeyDocument is a document bounded to a JTextField of the WordFrame.
     * It implements a kind of "code-complition".
     */


import com.sun.java.swing.text.*;
import com.sun.java.swing.*; 
class KeyDocument extends PlainDocument{
    WordFrame par=null;
    JTextComponent tc;
    boolean working=false;
        public KeyDocument(WordFrame p,JTextComponent t){
            par=p;
            tc=t;
        }
        
    public void insertString(int offset,String s, AttributeSet as) throws BadLocationException{
        super.insertString(offset,s,as);
        if (!working) modi();
    }
    /*
    public void remove(int x, int y) throws BadLocationException{
        super.remove(x,y);
        if (!working) modi();
    } 
    */
 
 private void modi(){
    working=true;
    String s=tc.getText();
    int i=0;
    boolean cont=true;
    while ((i<par.pwords.length)&&(cont))
    {
        if (par.pwords[i].length()>=s.length())
            if (par.pwords[i].substring(0,s.length()).equals(s))
                {
                    int c=tc.getCaretPosition();
                    tc.setText(par.pwords[i]);
                    tc.setCaretPosition(c);
                    tc.setSelectionEnd(c);
                    tc.setSelectionStart(tc.getText().length());
                    cont=false;
                }
        i++;
    }
    working=false;
        
     }
}