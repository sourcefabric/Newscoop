/*
 * @(#)ImageFrame.java
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
     * ImageFrame is a frame containing the controls necessary to set the
     * source, the align and the alt of an image.
     * The values are filled from the ImageControl which opened this frame.
     */
     
import com.sun.java.swing.*;
//import java.applet.*;
import java.awt.*;
import java.awt.event.*;
import com.sun.java.swing.event.*;
import java.net.*;
import java.util.*;

class ImageFrame extends JFrame/* implements Runnable*/{
    JTextField alt;
    JButton ok, cancel;
    Test parent;
    Container cp;
	ImageControl im;

    String urlVal;

    JComboBox image,align;
    GridBagLayout gbl;
    GridBagConstraints gbc;
    

    public ImageFrame(String s,Test par,Vector im){
        super(s);
        cp=getContentPane();
        parent=par;
        setVisible(false);
        setSize(400,230);

        gbl=new GridBagLayout();
        gbc=new GridBagConstraints();
        cp.setLayout(gbl);
        
        gbc.anchor=GridBagConstraints.NORTHWEST;
        gbc.gridwidth=GridBagConstraints.REMAINDER;
        gbc.insets=new Insets(0,10,10,10);
        gbc.anchor=GridBagConstraints.NORTH;
        gbc.fill=GridBagConstraints.HORIZONTAL;
        
        image=new JComboBox(im);

        Vector al=new Vector();
        al.addElement("align NONE");    
        //al.addElement("TOP");    
        //al.addElement("MIDDLE");    
        //al.addElement("BOTTOM");    
        al.addElement("RIGHT");    
        al.addElement("LEFT");    
        //al.addElement("ABSMIDDLE");    
        //al.addElement("ABSBOTTOM");    
        //al.addElement("TEXTTOP");    
        //al.addElement("BASELINE");    
        align=new JComboBox(al);
        align.setSelectedIndex(0);
        
        alt=new JTextField(20);
        ok=new JButton("Ok");
        cancel=new JButton("Cancel");

        addCompo(new JLabel("Image"),image);
        addCompo(new JLabel("Alignment"),align);
        addCompo(new JLabel("Alternative text"),alt);
        
        
        addCompo(ok,cancel);

        ok.addActionListener(new ActionListener(){
            public void actionPerformed(ActionEvent e){
				closeIt();
            }
            });
        cancel.addActionListener(new ActionListener(){
            public void actionPerformed(ActionEvent e){
                setVisible(false);
            }
            });
            
    }

	public void closeIt(){
		setVisible(false);
		int r=align.getSelectedIndex();
		if (r!=-1) im.align.setSelectedIndex(r);
		r=image.getSelectedIndex();
		if (r!=-1) im.combo.setSelectedIndex(r);
		im.alt=alt.getText();
	}

	public void open(ImageControl i){
		setVisible(true);
		im=i;
		int r=im.align.getSelectedIndex();
		if (r!=-1) align.setSelectedIndex(r);
		r=im.combo.getSelectedIndex();
		if (r!=-1) image.setSelectedIndex(r);
		if (im.alt==null) alt.setText(""); else
			alt.setText(im.alt);
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


    
}