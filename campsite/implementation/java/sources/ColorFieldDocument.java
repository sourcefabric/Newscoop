/*
 * @(#)ColorFieldDocument.java
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
     * ColorFieldDocument is the Document class for the R,G,B JTextFields from the
     * FontColorFrame frame. It checks if the resulted value (after an insert or a remove)
     * is a valid decimal color code (0-255), and updates the sample JPanel from the 
     * FontColorFrame frame.
     */

import javax.swing.text.*;
import javax.swing.*;

class ColorFieldDocument extends PlainDocument{
    private JTextComponent tC;
    FontColorFrame parent;
    int col;
    
    public ColorFieldDocument(JTextComponent tc,FontColorFrame p,int c){
        tC=tc;
        parent=p;
        col=c;
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
        if ((c<0)||(c>255)) tC.setText(temp);
            else{
                switch (col)
                {
                    case 1:parent.ared=c;break;
                    case 2:parent.agreen=c;break;
                    case 3:parent.ablue=c;break;
                }
                parent.updaterKeyb();
            }
    }
    /*
    protected void removeUpdate(AbstractDocument.DefaultDocumentEvent chng){
        int c=-1;
        try{
            if (tC.getText().equals("")) c=0;
                else c=new Integer(tC.getText()).intValue();
        }
        catch(Exception e){
            c=-1;
        }
        if (!((c<0)||(c>255)))
            {
                slider.setValue(c);
                slider.revalidate();
            }
    }
    */
    public void remove(int x, int y) throws BadLocationException{
        super.remove(x,y);
        int c=-1;
        String temp=tC.getText();
        try{
            if (tC.getText().equals("")) {c=0;}
                else c=new Integer(tC.getText()).intValue();
        }
        catch(Exception e){
            tC.setText(temp);
        }
        if ((c<0)||(c>255)) tC.setText(temp);
            else{
                switch (col)
                {
                    case 1:parent.ared=c;break;
                    case 2:parent.agreen=c;break;
                    case 3:parent.ablue=c;break;
                }
                parent.updaterKeyb();
            }
    }
}