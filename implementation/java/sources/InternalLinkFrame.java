/*
 * @(#)InternalLinkFrame.java
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
     * InternalLinkFrame is the frame for setting the attributes of a link,
     * which has as the target another article from the system.
     * I has a series of ComboBoxes, the content of these are retreived using a CGI,
     * and passing the already set values for a given depth 
     * (Language->Publication->Isuue->Section)
     */

import com.sun.java.swing.*;
import com.sun.java.swing.event.*;
import java.awt.*;
import java.awt.event.*;
import java.util.*;
import java.net.*;
import java.io.*;

class InternalLinkFrame extends JFrame{
    
    boolean err=false;
    LinkControl which;
    JPanel panel=new JPanel();
    JComboBox target;
    //JLabel title;
    //JSeparator sep=new JSeparator();
    Container cp;
    JButton ok,cancel,rest;
    
    GridBagLayout gbl;
    GridBagConstraints gbc;
    Test parent;
    LinkCombo links[];
    JTextField frame;
    final static String list[]={"Language","Publication","Issue","Section","Article"};
    final static String listofIds[]={"IdLanguage","IdPublication","NrIssue","NrSection","NrArticle"};
    JTextField status;

    
    public InternalLinkFrame(String titles,int w,int h,Test p){
        super(titles);
        parent=p;
        cp=getContentPane();
        cp.add(new JScrollPane(panel));
        setSize(w,h);
        gbl=new GridBagLayout();
        gbc=new GridBagConstraints();
        panel.setLayout(gbl);
        
        gbc.anchor=GridBagConstraints.NORTHWEST;
        gbc.gridwidth=GridBagConstraints.REMAINDER;
        gbc.insets=new Insets(0,10,10,10);
        gbc.anchor=GridBagConstraints.NORTH;
        gbc.fill=GridBagConstraints.HORIZONTAL;
        links=new LinkCombo[5];

        for (int i=0;i<5;i++)
        {
            links[i]=new LinkCombo(i,listofIds[i],this);
            addCompo(new JLabel(list[i]),links[i]);
            links[i].setValid(false);
            if (i>0) links[i].setUpper(links[i-1]);
        }
        //addCompo(new JLabel("Language"),links[0]);
        target=new JComboBox();
        target.addItem("default");
        target.addItem("new window");
        target.addItem("full window");
        target.addItem("frame named");
        
        frame=new JTextField(15);
        //target.setEditable(true);
        addCompo(new JLabel("Open in"),target);
        addCompo(new JLabel("Frame name"),frame);
        frame.setEditable(false);
        status=new JTextField(15);
        addCompo(new JLabel("Status"),status);
        status.setEnabled(false);
        rest=new JButton("Reread");
        addCompo(new JLabel("Reread"),rest);
        cancel=new JButton("Cancel");
        ok=new JButton("OK");
        addCompo(ok,cancel);
        
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

        finishForm();
    }

    private void addCompo(JComponent o1,JComponent o2){
        JPanel holder=new JPanel();
        holder.add(o2);
        holder.setLayout(new FlowLayout(FlowLayout.LEFT));
        gbc.anchor=GridBagConstraints.WEST;
        gbc.gridwidth=1;
        gbc.insets=new Insets(3,10,3,10);
        panel.add(o1,gbc);
        panel.add(Box.createHorizontalStrut(10));
        gbc.gridwidth=GridBagConstraints.REMAINDER;
        panel.add(holder,gbc);
        //fields.addElement(o2);
        //labels.addElement(o1);
    }
    
    
    public void finishForm(){
        gbc.anchor=GridBagConstraints.SOUTH;
        gbc.fill=GridBagConstraints.HORIZONTAL;
//        panel.add(new JSeparator(),gbc);
//        panel.add(close);
        cancel.addActionListener(new ActionListener(){
            public void actionPerformed(ActionEvent e){
                hideIt();
            }
            });

        ok.addActionListener(new ActionListener(){
            public void actionPerformed(ActionEvent e){
                //refresh(1);
                canExit();
            }
            });
        rest.addActionListener(new ActionListener(){
            public void actionPerformed(ActionEvent e){
                restart();
            }
            });
    }
    
    private void hideIt(){
        this.setVisible(false);
    }
    
    public void open(LinkControl w){
        which=w;
        setVisible(true);
        //if (links[0].valid)setCombos(w);
        //if (links[0].valid) printCombos(w);
        if (!links[0].valid) {links[0].setValues(contact(0));}
        else 
            if (w.ids[4]!=null) setCombos(w);
        
        
    }
    /*
    public void printCombos(LinkControl w){
        for (int i=0;i<5;i++)
        {
            System.out.println(w.idV[i]);
            System.out.println(w.nameV[i]);
        }
    }*/
    public void setCombos(LinkControl w){
        //parent.showInfo("Connecting ");
        for(int i=0;i<5;i++)
        {
        links[i].setValues(contact(i));
        int idx=links[i].id.indexOf(w.ids[i]);
        if (idx==-1) idx=0;
        links[i].combo.setSelectedIndex(idx);
        }
        //parent.showInfo("");
            //System.out.println(idx);
    }
    
    public void refresh(int i){
        for (int g=i;g<5;g++)
        {
            if (links[g].combo.getItemCount()!=0) links[g].combo.setSelectedIndex(0);
            links[g].setValid(false);
        }
        //System.out.println("refresh"+i);
        links[i].setValues(contact(i));
    }
    
    public String contact(int l){
        //System.out.println("contact enter"+l);
        err=false;
        status.setText("Connecting ...");
        //parent.showInfo("Connecting ...");
        status.revalidate();
        StringBuffer sb=new StringBuffer();
        for(int i=0;i<l;i++)
        {
            if (i>0) sb.append("&");
            sb.append(listofIds[i]);
            sb.append("=");
            sb.append(links[i].value);
        }
        String page="";
        try{
            URL u=new URL(parent.linkscript+"?"+sb.toString());
            //System.out.println(u);
            URLConnection uc=u.openConnection();
            DataInputStream dis=new DataInputStream(uc.getInputStream());
            //System.out.println("getContent"+o);
            String s="";
            while (s!=null)
            {
                s=dis.readLine();
                if (s!=null) page+=(s+'\n');
            }
        }
        catch (Exception e)
        {
            System.out.println(e);
            status.setText(e.toString());
            err=true;
        }
        if (!err) {
                status.setText("Ready");
                //parent.showInfo("");
            }
        //System.out.println("contact exit"+l);
                //System.out.println(page);
        return page;
    }
    
    private void canExit(){
        boolean ok=true;
        for (int i=0;i<5;i++)
            if (!links[i].valid) ok=false;
        if (!ok) status.setText("You must initialize all fields !");
        else
        {
        for (int i=0;i<5;i++)
        {
            which.ids[i]=links[i].value;
            which.pair.ids[i]=links[i].value;
            //System.out.println(links[i].value);
            which.gotIds(true);
        }
        setVisible(false);
        String si=(String)(target.getSelectedItem());
            //System.out.println("st"+si);
                which.setFrame(frame.getText());
                which.pair.setFrame(frame.getText());
        which.setTarget(si);
        which.pair.setTarget(si);
            
        }
    }
    public String stat(){
        StringBuffer sb=new StringBuffer();
        for (int i=0;i<5;i++)
            if (!links[i].valid) sb.append("_"); else sb.append("X");
            return sb.toString();
    }
    
    public void restart(){
        links[0].setValues(contact(0));
    }
}