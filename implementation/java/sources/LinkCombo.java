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



import javax.swing.*;
import java.awt.event.*;
import java.awt.*;
import java.util.*;

class LinkCombo extends JPanel{
    Vector id;
    private Vector name;
    JComboBox combo;
    String value=null;
    private int level;
    boolean valid;
    private String ID=null;
    private LinkCombo upper;
    private InternalLinkFrame pp;
    private CampSRLayout comboLayout = new CampSRLayout(1, CampSRLayout.FILL, CampSRLayout.CENTER, 0);    
    
    public LinkCombo(int l,String id,InternalLinkFrame p){
        super();
        this.setLayout(comboLayout);
        combo=new JComboBox();
        add(combo);
        if (CampResources.isRightToLeft())((JLabel)combo.getRenderer()).setHorizontalAlignment(SwingConstants.RIGHT);
        
        pp=p;
        level=l;
        ID=id;
        upper=null;
        
        combo.addActionListener(new ActionListener(){
            public void actionPerformed(ActionEvent e){
                int i=combo.getSelectedIndex();
                setVal(i);
            }
            });
    }
    
    public void setValid(boolean v){
        if ((!v)&&(combo.getItemCount()>=1)) combo.setSelectedIndex(0);
        combo.setEnabled(v);
    }
    
    public void setUpper(LinkCombo u){
        upper=u;
    }
    
    public void setValues(String s){
        
        if (pp.err) return;
        id=new Vector();
        name=new Vector();
        if (combo.getItemCount()!=0)
        {
        remove(combo);
        combo=null;
        combo=new JComboBox();
        add(combo);
        //combo.setPreferredSize(new Dimension(180,20));
        //combo.setMaximumSize(new Dimension(180,20));
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
        id.addElement("ID?");
        name.addElement("?");
        combo.addItem("?");

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

    }
    
    private void setVal(int i){
        if ((i==0)&&(level<4)) 
            for (int g=level+1;g<=4;g++) 
            {
                pp.links[g].setValid(false);
                pp.links[g].valid=false;
            }
        value=(String)id.elementAt(i);
        valid=(i!=0);
        if ((i!=0)&&(level!=4))
                    pp.refresh(level+1);
   }
} 
