/*
 * @(#)WordFrame.java
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
     * WordFrame : is the frame in which you can choose the keyword for the selected
     * text. Has a textfield with code-complition , and a JList with the existing keywords.
     * the selected text will become a link.
     */



import com.sun.java.swing.*;
import com.sun.java.swing.event.*;
import java.awt.*;
import java.awt.event.*;
import java.util.*;

class WordFrame extends JFrame{
    
    JPanel panel=new JPanel();
    //JLabel title;
    //JSeparator sep=new JSeparator();
    Container cp;
    JButton ok,cancel;
    JTextField name;
    
    GridBagLayout gbl;
    GridBagConstraints gbc;
    JList list;
    Vector wordvect;
    Test parent;
    String pwords[];
    //KeyDocumentListener dl;
    boolean working=false;
    
    //Vector fields=new Vector();
    //Vector labels=new Vector();
    //Vector ids=new Vector();
    
    //String allcombo;
    //String alltext;
    
    public WordFrame(String titles,String[] words,int w,int h,Test p){
        super(titles);
        parent=p;
        pwords=words;
        //System.out.println(cl);
        //close=new JButton("Close");
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
        
        name=new JTextField(15);
        ok=new JButton("OK");
        addCompo(name,ok);

        wordvect=new Vector();
        for (int i=0;i<words.length;i++)
            wordvect.addElement(words[i]);
        
        list=new JList(wordvect);
        cancel=new JButton("Cancel");
        JScrollPane scrollPane = new JScrollPane(list);
        addCompo(scrollPane,cancel);
        finishForm();
    }
 /*   
    public void addList(String title,String[] v){
        JLabel label=new JLabel(title);
        JList list=new JList(v);
        //list.setPreferredSize(new Dimension(getSize().width,500));
        JScrollPane scrollPane = new JScrollPane(list);
        addCompo(label,scrollPane);
    }
   */ 
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
        list.addListSelectionListener(new ListSelectionListener(){
            public void valueChanged(ListSelectionEvent e){
                int i=list.getMinSelectionIndex();
                list.getSelectionModel().setSelectionInterval(i,i);
                name.setText((String)(list.getSelectedValue()));
            }
            });    
        ok.addActionListener(new ActionListener(){
            public void actionPerformed(ActionEvent e){
                finish();
            }
            });
        //name.setDocument(new KeyDocument(name,this));
        //dl=new KeyDocumentListener(this);
        name.setDocument(new KeyDocument(this,name));
        name.addActionListener(new ActionListener(){
            public void actionPerformed(ActionEvent e){
                System.out.println(e);
                finish();
            }
            });
            
            
    }
    
    private void hideIt(){
        this.setVisible(false);
    }
    
    public void open(){
        setVisible(true);
        name.requestFocus();
    }
    
    public void modi(){
        //name.getDocument().removeDocumentListener(dl);
        if (!working)
        {
            working=true;
        System.out.print("d");
        name.setText("gasa");
        working=false;
        }
        
        //name.getDocument().addDocumentListener(dl);
    }
    
    private void finish(){
                int id=wordvect.indexOf(name.getText());
                if (id!=-1)
                {
                    parent.setWord(id);
                    hideIt();
                }
    }
    
}