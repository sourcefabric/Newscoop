/*
 * @(#)Starter.java
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
     * Starter is a workaround for a bug in some browsers. I some cases the 
     * filling with text and components of the textarea must be delayed 
     * or must be triggered by a user interaction. Otherwise, you get
     * null pointer exception.
     * It shows up a button, by clicking this, the textarea will be filled
     * with the default text.
     */


import com.sun.java.swing.*;
import java.awt.*;
import java.awt.event.*;

class Starter extends JPanel{
    Test parent;
    public Starter(Test p){
        super();
        parent=p;
        setPreferredSize(new Dimension(200,70));
        setBorder(BorderFactory.createEtchedBorder());    
        JButton b=new JButton("Load text");
        add(b,"CENTER");
        b.setBackground(Color.red);
        b.addActionListener(new ActionListener(){
            public void actionPerformed(ActionEvent e){
                parent.regen();
            }
            });
    }
}