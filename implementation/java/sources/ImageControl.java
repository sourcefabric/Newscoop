/*
 * @(#)ImageControl.java
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
     * ImageControl is the Panel included in the textarea, representing an image.
     * It has some components that are not visible, their content is transfered 
     * into the corresponding fields of the ImageFrame, when this is opened.
     * The toString method is used when generating the html.
     */

import com.sun.java.swing.*;
import java.awt.*;
import java.awt.event.*;
import java.util.*;

class ImageControl extends JPanel{
    
    JButton butt;
    JComboBox combo;
    JComboBox align;
    Vector pseu;
    Test parent;
	String alt;
    
    public ImageControl(Test p){
        setPreferredSize(new Dimension(50,50));
        setBorder(BorderFactory.createEtchedBorder());    
        parent=p;
    }
    
    public void addIcon(String s){
        butt=new JButton("");
        butt.setIcon(new TolImageIcon("image.gif",parent.bigim,parent));
        butt.setMargin(new Insets(1,1,1,1));
        add(butt,"CENTER");
        butt.setEnabled(true);
		butt.addActionListener(new ActionListener(){
			public void actionPerformed(ActionEvent ev){
			openFrame();
			}
		});
    }

	public void openFrame(){
		parent.openImageFrame(this);
	}
/*    
    public void addRemover(String s){
        butt=new JButton("s");
        butt.setIcon(new ImageIcon(s));
        butt.setMargin(new Insets(1,1,1,1));
        add(butt,"NORTHEAST");
        //butt.setEnabled(false);
        butt.addActionListener(new ActionListener(){
            public void actionPerformed(ActionEvent e){
                vala();
            }
            });
    }
    
    public void vala(){
        //setVisible(false);
        this.dispose();
    }
  */  
    public void addCombo(Vector v,Vector ps){
        Vector al=new Vector();
        pseu=ps;
        combo=new JComboBox(v);
      /*  add(combo,"CENTER");
        combo.addItemListener(new ItemListener(){
            public void itemStateChanged(ItemEvent a){
                butt.setEnabled(true);
            }
            });
         */
   
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
/*        add(align,"SOUTH");*/

        align.setSelectedIndex(0);
    }

    

    
    public String toString(){
        String ali=new String();
		String alti;
		if ((alt==null)||(alt.equals(""))) alti=""; else alti=" ALT=\""+alt+"\"";
        if (align.getSelectedIndex()!=0) ali=" ALIGN="+(String)align.getSelectedItem();
        if (combo.getSelectedIndex()==-1) return "?";
        else return (String)pseu.elementAt(combo.getSelectedIndex())+ali+alti;
    }
    
    public void setAlign(String s){
        int i=s.toUpperCase().indexOf("ALIGN=");
        if (i==-1) return;
		int altidx=s.toUpperCase().indexOf("ALT=");
		int toidx=s.length();
		if (altidx!=-1) toidx=altidx-1;
		String a=s.substring(i+6,toidx).toUpperCase();
		
        align.setSelectedItem(a);
        //System.out.println("szai"+a);
    }

    public void setAlt(String s){
        int i=s.toUpperCase().indexOf("ALT=");
        if (i==-1) return;
        String a=s.substring(i+5,s.indexOf("\"",i+5));
		alt=a;
    }
    
    public void setImage(Test p,String s){
        //System.out.println(s);
        
        if (s.equals("?")) return;
        int sp=s.indexOf(" ");
        if (sp==-1) sp=s.length();
        int ind=p.vectorOfImagePseudos.indexOf(s.substring(0,sp));
        if (ind!=-1) combo.setSelectedIndex(ind);
        
    }
}