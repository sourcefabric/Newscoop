/*
 * @(#)LinkCombo.java
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
     * LinkCombo is used by the InternalLinkFrame
     */



import com.sun.java.swing.*;
import java.awt.event.*;
import java.awt.*;
import java.util.*;

class LinkCombo extends JPanel{
    Vector id;
    Vector name;
    JComboBox combo;
    String value=null;
    int level;
    boolean valid;
    String ID=null;
    LinkCombo upper;
    InternalLinkFrame pp;
    //Color invcolor=new Color(255,0,0);
    //Color valcolor;
    
    public LinkCombo(int l,String id,InternalLinkFrame p){
        super();
        combo=new JComboBox();
        add(combo);
        pp=p;
        level=l;
        ID=id;
        upper=null;
      //  valcolor=getBackground();
      //  setBackground(invcolor);
        
        combo.addActionListener(new ActionListener(){
            public void actionPerformed(ActionEvent e){
                int i=combo.getSelectedIndex();
                setVal(i);
            }
            });
    }
    
    public void setValid(boolean v){
        //valid=v;
        if ((!v)&&(combo.getItemCount()>=1)) combo.setSelectedIndex(0);
        combo.setEnabled(v);
        //if (v==true) setBackground(valcolor); else setBackground(invcolor);
    }
    
    public void setUpper(LinkCombo u){
        upper=u;
    }
    
    public void setValues(String s){
        
        if (pp.err) return;
        //System.out.println("setvalues enter");
        id=new Vector();
        name=new Vector();
        if (combo.getItemCount()!=0)
        {
        remove(combo);
        combo=null;
        combo=new JComboBox();
        add(combo);
        setValid(true);
        setVisible(true);
        combo.setVisible(true);
        revalidate();
        combo.addActionListener(new ActionListener(){
            public void actionPerformed(ActionEvent e){
                int d=combo.getSelectedIndex();
                setVal(d);
            }
            });

        }
        //setValid(true);
        id.addElement("ID?");
        name.addElement("?");
        //System.out.println("szia1"+getItemCount());
            //System.out.println("aaa");
        combo.addItem("?");
            //System.out.println("aaa"+s);
        int i=0;
        while(i<s.length())
        {
            StringBuffer sb =new StringBuffer();
            while(s.charAt(i)!='\n') 
            {
                sb.append(s.charAt(i));
                i++;
            }
            id.addElement(sb.toString());
            i++;
            sb =new StringBuffer();
            while(s.charAt(i)!='\n') 
            {
                sb.append(s.charAt(i));
                i++;
            }
            name.addElement(sb.toString());
            combo.addItem(sb.toString());
            i++;
        }
        combo.setSelectedIndex(0);
        combo.setEnabled(true);
        //System.out.println(s);
        //System.out.println("setvalues ex ti");

    }
    
    private void setVal(int i){
        if ((i==0)&&(level<4)) 
            for (int g=level+1;g<=4;g++) 
            {
                pp.links[g].setValid(false);
                pp.links[g].valid=false;
            }
        value=(String)id.elementAt(i);
        //System.out.println("en "+level+" validom"+valid);
        valid=(i!=0);
        if ((i!=0)&&(level!=4))
                    pp.refresh(level+1);
        //System.out.println("en "+level+" validom"+valid);
        pp.status.setText(pp.stat());
   }
} 
