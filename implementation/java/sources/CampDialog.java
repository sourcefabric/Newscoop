/*
 * @(#)CampDialog.java
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
     * CampDialog : is the ancestor for other dialogs in the package
     */



import javax.swing.*;
import javax.swing.event.*;
import java.awt.*;
import java.awt.event.*;
import java.util.*;

class CampDialog extends JDialog{
    
    protected Container cp;
    
    protected JPanel contentPanel = new JPanel();
    protected JPanel displayPanel = new JPanel();
    protected JPanel buttonPanel = new JPanel();
    protected CampSCLayout contentLayout = new CampSCLayout(2, CampSCLayout.FILL, CampSCLayout.FILL, 3);
    protected CampLayout displayLayout;
    protected CampSRLayout buttonLayout;
    
    protected static JButton ok, cancel;
    protected static Campfire parent;
    protected int rowNo, colNo;
    
    protected CampDialog(Campfire p, String title){
        super (p.getParentFrame(), title, true);
        parent=p;
        initDialog( title);
    }

    protected CampDialog(Campfire p, String title, int rowNo){
        super (p.getParentFrame(), title, true);
        parent=p;
        displayLayout =new CampLayout(rowNo, 1, CampLayout.CENTER, CampLayout.FILL, 10, 10);
        buttonLayout= new CampSRLayout(1, CampSRLayout.CENTER, CampSRLayout.FILL, 10);

        initDialog( title);
    }

    protected CampDialog(Campfire p, String title, int w, int h){
        super (p.getParentFrame(), title, true);
        parent=p;
        rowNo= w;
        colNo= h;

        if (CampResources.isRightToLeft())
            displayLayout =new CampLayout(rowNo, colNo, CampLayout.RIGHT, CampLayout.CENTER, 10, 10);
        else
            displayLayout =new CampLayout(rowNo, colNo, CampLayout.LEFT, CampLayout.CENTER, 10, 10);
        buttonLayout= new CampSRLayout(2, CampSRLayout.FILL, CampSRLayout.CENTER, 10);

        initDialog( title);
    }
    
    protected CampDialog(Campfire p, String title, int w, int h, int c, int r){
        super (p.getParentFrame(), title, true);
        parent=p;
        //setSize( w, h);
        rowNo= w;
        colNo= h;
        displayLayout =new CampLayout(rowNo, colNo, c, r, 10, 10);
        buttonLayout= new CampSRLayout(2, CampSRLayout.FILL, CampSRLayout.CENTER, 10);

        initDialog( title);
    }


    private void initDialog(String title){
        ok=new JButton(CampResources.get("CampFrame.OkButton"));
        cancel=new JButton(CampResources.get("CampFrame.CancelButton"));
        
        contentLayout.setMargins(0, 0, 10, 0);
        contentPanel.setLayout(contentLayout);

        displayLayout.setMargins(15, 15, 15, 15);
        displayPanel.setLayout(displayLayout);

        buttonLayout.setMargins(0, 25, 0, 25);
        buttonPanel.setLayout(buttonLayout);
    }

    private void centerFrame() {
         Dimension sdim = Toolkit.getDefaultToolkit().getScreenSize();
         int fw = getSize().width;
         int fh = getSize().height;
         int fx = (sdim.width-fw)/2;
         int fy = (sdim.height-fh)/2;
             
         this.setBounds(fx, fy, fw, fh);
    }        
    
    protected void addCompo(JComponent o1,JComponent o2){
        if (CampResources.isRightToLeft()){
            displayPanel.add(o2);
            displayPanel.add(o1);
        }else{
            displayPanel.add(o1);
            displayPanel.add(o2);
        }
    }


    protected void addCompo(JComponent o2){
        displayPanel.add(o2);
    }

    protected void addCompo(Component o2){
        displayPanel.add(o2);
    }


    protected void addButtons(JComponent o1,JComponent o2){
        if (CampResources.isRightToLeft()){
            buttonPanel.add(o2);
            buttonPanel.add(o1);
        }else{
            buttonPanel.add(o1);
            buttonPanel.add(o2);
        }
    }

    protected void addButton(JComponent o1){
        buttonPanel.add(o1);
    }

    protected void finishDialog(){
        //displayPanel.setPreferredSize(displayLayout.preferredLayoutSize(displayPanel));
        for (int i=0; i<colNo; i++){
            if(CampResources.isRightToLeft()){
                if (i!=1) displayLayout.setColumnScale(i, (double)(displayLayout.getColMaxSize(displayPanel, i))/(displayLayout.getColMaxSize(displayPanel, 1)+30));
            }else{
                if(i!=0) displayLayout.setColumnScale(i, (double)(displayLayout.getColMaxSize(displayPanel, i))/(displayLayout.getColMaxSize(displayPanel, 0)+30));
            }
        }
        contentPanel.add(displayPanel);
        contentPanel.add(buttonPanel);
        contentLayout.setScale(1, (double)buttonLayout.preferredLayoutSize(buttonPanel).height/displayLayout.preferredLayoutSize(displayPanel).height);
        setContentPane(contentPanel);
        if (CampResources.isRightToLeft()) applyResourceBundle(CampResources.getBundle());
        pack();
        centerFrame();
    }


    protected void showStatus(String s){
        parent.showStatus(s);
    }

    protected void showError(String s){
        JOptionPane op=new JOptionPane();
        op.showMessageDialog(this,s,CampResources.get("Error.Title"),JOptionPane.ERROR_MESSAGE);
    }

    protected void showInfo(String s){
        JOptionPane op=new JOptionPane();
        op.showMessageDialog(this,s,CampResources.get("Info.Title"),JOptionPane.INFORMATION_MESSAGE);
    }

}