/*
 * @(#)UrlChooser.java
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
     * UrlChooser frame for external links 
     */


import com.sun.java.swing.*;
//import java.applet.*;
import java.awt.*;
import java.awt.event.*;
import com.sun.java.swing.event.*;
import java.net.*;

class UrlChooser extends JFrame/* implements Runnable*/{
    JTextField url,frame;
    JButton ok, cancel;
    Test parent;
    Container cp;
    String urlVal;
    LinkControl which;
    JComboBox target;
    GridBagLayout gbl;
    GridBagConstraints gbc;
    

    public UrlChooser(String s,Test par){
        super(s);
        cp=getContentPane();
        parent=par;
        setVisible(false);
        
        
        
        
        setSize(400,230);
//        cp.setLayout(new FlowLayout());

        gbl=new GridBagLayout();
        gbc=new GridBagConstraints();
        cp.setLayout(gbl);
        
        gbc.anchor=GridBagConstraints.NORTHWEST;
        gbc.gridwidth=GridBagConstraints.REMAINDER;
        gbc.insets=new Insets(0,10,10,10);
        gbc.anchor=GridBagConstraints.NORTH;
        gbc.fill=GridBagConstraints.HORIZONTAL;
        
        target=new JComboBox();
        target.addItem("default");
        target.addItem("new window");
        target.addItem("full window");
        target.addItem("frame named");
        
        
        
        url=new JTextField(20);
        ok=new JButton("Ok");
        cancel=new JButton("Cancel");
        addCompo(new JLabel("URL"),url);
        frame=new JTextField(15);
        frame.setEditable(false);
        //target.setEditable(true);
        addCompo(new JLabel("Open in"),target);
        addCompo(new JLabel("Frame name"),frame);
        
        
        addCompo(ok,cancel);
        //cp.add(url,"NORTH");
        //cp.add(ok,"WEST");
        //cp.add(cancel,"EAST");

        ok.addActionListener(new ActionListener(){
            public void actionPerformed(ActionEvent e){
                setVisible(false);
                which.setUrl(url.getText(),true);
                String si=(String)(target.getSelectedItem());
                
                which.setFrame(frame.getText());
                which.pair.setFrame(frame.getText());

                which.setTarget(si);
                which.pair.setTarget(si);

                
            }
            });
        cancel.addActionListener(new ActionListener(){
            public void actionPerformed(ActionEvent e){
                setVisible(false);
            }
            });

        target.addItemListener(new ItemListener(){
            public void itemStateChanged(ItemEvent ev){
                if (target.getSelectedIndex()==3) 
                    {
                        frame.setEditable(true); 
                        frame.requestFocus();
                        }
                        else frame.setEditable(false);
            }
            });
            
            
    }
    private void addCompo(JComponent o1,JComponent o2){
        JPanel holder=new JPanel();
        holder.add(o2);
        holder.setLayout(new FlowLayout(FlowLayout.LEFT));
        gbc.anchor=GridBagConstraints.WEST;
        gbc.gridwidth=1;
        gbc.insets=new Insets(3,10,3,10);
        cp.add(o1,gbc);
        cp.add(Box.createHorizontalStrut(10));
        gbc.gridwidth=GridBagConstraints.REMAINDER;
        cp.add(holder,gbc);
        //fields.addElement(o2);
        //labels.addElement(o1);
    }
    
    public void  changeUrl(LinkControl w){
        setVisible(true);
        which=w;
        url.setText(which.getUrl());
        url.requestFocus();
	String t=which.target;
	frame.setEditable(false);
	if ((t==null)||(t.equals(""))||(t.toUpperCase().equals("UNSPECIFIED"))) {target.setSelectedItem("default");return;}
	if (t.equals("_top")) {target.setSelectedItem("full window");return;}
	if (t.equals("_blank")) {target.setSelectedItem("new window");return;}
	frame.setEditable(true);
	frame.setText(t);
	target.setSelectedItem("frame named");
    }
    
    
}