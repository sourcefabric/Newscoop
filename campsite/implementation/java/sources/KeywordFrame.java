/*
 * @(#)KeywordFrame.java
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
     * KeywordFrame : is the frame in which you can choose the keyword for the selected
     * text. Has a textfield with code-complition , and a JList with the existing keywords.
     * the selected text will become a link.
     */



import javax.swing.*;
import javax.swing.event.*;
import java.awt.*;
import java.awt.event.*;
import java.util.*;

class KeywordFrame extends CampDialog{
    
    private JComboBox keyword;
    private Vector wordvect;
    private String pwords[];
    private KeywordProperties keyProps;
    private boolean bIsNew=false;
   
    
    public KeywordFrame(Campfire p, String titles,String[] words){
        //super(p, titles, 400, 160);
        super(p, titles, 1, 2);
        pwords=words;
        

        wordvect=new Vector();
        for (int i=0;i<words.length;i++)
            wordvect.addElement(words[i]);
        
        keyword=new JComboBox(wordvect);
        if (CampResources.isRightToLeft())((JLabel)keyword.getRenderer()).setHorizontalAlignment(SwingConstants.RIGHT);
        addCompo(new JLabel(CampResources.get("KeywordFrame.Keyword")),keyword);
        addButtons(ok,cancel);
        finishDialog();
        
        cancel.addActionListener(new ActionListener(){
            public void actionPerformed(ActionEvent e){
                cancelClicked();
            }
            });
        ok.addActionListener(new ActionListener(){
            public void actionPerformed(ActionEvent e){
                okClicked();
            }
            });

        if (keyword.getItemCount()<1) ok.setEnabled(false);
        

    }
 
 
	private void okClicked(){
		setVisible(false);
		
        keyProps.className=(String)keyword.getSelectedItem();
    		
		if (bIsNew) {
            CampBroker.getKeyword().createPresentation(keyProps);
        }else{
       	    CampBroker.getKeyword().save(keyProps);
    	}
	}

	private void cancelClicked(){
		setVisible(false);
	}
    
   
	public void open(KeywordProperties props, boolean b){
	    int r=0;
	    
	    keyProps=props;
	    bIsNew= b;

	    if (!bIsNew ){
    		keyword.setSelectedItem(keyProps.className);
	    }
	    
        this.setVisible(true);
		keyword.requestFocus();
	}

    
	public void reset(){
	    int r=0;
		if (keyword.getItemCount()>0) keyword.setSelectedIndex(r);
	}

   
    
}