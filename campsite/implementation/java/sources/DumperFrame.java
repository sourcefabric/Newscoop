/*
 * @(#)DumperFrame.java
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
     * DuperFrame is a workaround for a security issue on Netscape browsers.
     * An unsigned Swing applet can not access the clipboard.
     * With this AWT frame, you set, get, or insert text into the main text editor.
     * The button on the toolbar for this frame will be present only if there is
     * a parameter named "clip" (the passed value can be even an empty string).
     * If the parameter is missing, the Copy, Cut and Paste buttons will appear instead.
     * This frame is also used for debug purposes, when the "debug" parameter
     * is set. It will show up on the toolbar an additional button.
     * By clicking that button, the generated content(that will be sent to the server)
     * will appear in the textarea of this frame.
     */

import java.awt.*;
import java.awt.event.*;
import javax.swing.*;

class DumperFrame extends Frame{
    
    TextArea t;
    private JScrollPane scrollPane;
    Panel p=new Panel();
    Button close=new Button("Close");
    Button set=new Button("Set Text");
    Button get=new Button("Get Text");
    Button clear=new Button("Clear");
    Button insert=new Button("Insert");
    Campfire parent;
    
    public DumperFrame(Campfire par){
        super("Clipboard operations");
        parent=par;
        setSize(400,300);
        t=new TextArea();
        addWindowListener(new WindowListener(){
            public void windowOpened(WindowEvent e){}
            public void windowClosing(WindowEvent e){
                //System.out.println("c");
                setVisible(false);
                }
            public void windowClosed(WindowEvent e){}
            public void windowIconified(WindowEvent e){}
            public void windowDeiconified(WindowEvent e){}
            public void windowActivated(WindowEvent e){}
            public void windowDeactivated(WindowEvent e){}
            });
        add("Center",t);
        //p.setSize(150,50);
        p.add(insert);
        p.add(clear);
        p.add(get);
        p.add(set);
        p.add(close);
        add("South",p);
        close.addActionListener(new ActionListener(){
            public void actionPerformed(ActionEvent e){
                setVisible(false);
            }
            });
        set.addActionListener(new ActionListener(){
            public void actionPerformed(ActionEvent e){
                parent.newFile(false);
                parent.textPane.setText(t.getText());
            }
            });
        get.addActionListener(new ActionListener(){
            public void actionPerformed(ActionEvent e){
                t.setText(parent.textPane.getText());
            }
            });
        clear.addActionListener(new ActionListener(){
            public void actionPerformed(ActionEvent e){
                t.setText("");
            }
            });
        insert.addActionListener(new ActionListener(){
            public void actionPerformed(ActionEvent e){
                parent.insertT(t.getText());
            }
            });
        
    }
    
        public void setText(String s){
            t.setText(s);
        }
        
    
}