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
     
import javax.swing.*;
//import java.applet.*;
import java.awt.*;
import java.awt.event.*;
import javax.swing.event.*;
import java.net.*;
import java.util.*;

class ImageFrame extends CampDialog{
    private JTextField alt, sub;
	private ImageControl imgControl;
    private String urlVal;
    private JComboBox image,align;
    private ImageProperties imgProps;
    private boolean bIsNew=false;
    private Vector imgPseudos= new Vector();

    public ImageFrame(Campfire p,String title,Vector im, Vector imps){
        //super(p, title, 400, 280);
        super(p, title, 4, 2);

        image=new JComboBox(im);
        imgPseudos= imps;
        Vector al=new Vector();
        al.addElement(CampResources.get("ImageFrame.Alignment.None"));    
        al.addElement(CampResources.get("ImageFrame.Alignment.Right"));    
        al.addElement(CampResources.get("ImageFrame.Alignment.Left"));    
        align=new JComboBox(al);

        if (CampResources.isRightToLeft())((JLabel)image.getRenderer()).setHorizontalAlignment(SwingConstants.RIGHT);
        if (CampResources.isRightToLeft())((JLabel)align.getRenderer()).setHorizontalAlignment(SwingConstants.RIGHT);
        align.setSelectedIndex(0);
        
        alt=new JTextField(20);
        sub=new JTextField(20);
        //alt.setPreferredSize(new Dimension(180,20));
        //sub.setPreferredSize(new Dimension(180,20));
        
        
        addCompo(new JLabel(CampResources.get("ImageFrame.Image")),image);
        addCompo(new JLabel(CampResources.get("ImageFrame.Alignment")),align);
        addCompo(new JLabel(CampResources.get("ImageFrame.AlternativeText")),alt);
        addCompo(new JLabel(CampResources.get("ImageFrame.Caption")),sub);
        
        
        addButtons(ok,cancel);
        finishDialog();
        
        cancel.addActionListener(new ActionListener(){
            public void actionPerformed(ActionEvent e){
				cancelClicked();
            }
            });

        ok.addActionListener(new ActionListener(){
            public void actionPerformed(ActionEvent e){
				okClicked();
            }
            });
        
        if (image.getItemCount()<1) ok.setEnabled(false);
        
          
    }


	private void okClicked(){
		setVisible(false);
		//dispose();

		imgProps.imageName=(String)imgPseudos.elementAt(image.getSelectedIndex());
		
		if (align.getSelectedIndex()==0)
		  imgProps.alignWay="NONE";
		else if (align.getSelectedIndex()==1)
		  imgProps.alignWay="RIGHT";
		else if (align.getSelectedIndex()==2)
		  imgProps.alignWay="LEFT";
		else
		  imgProps.alignWay="";
		  
		imgProps.altText=alt.getText();
		imgProps.subTitle=sub.getText();
    		
		if (bIsNew) {
            CampBroker.getImage().createPresentation(imgProps);
        }else{
    	   imgControl.setProperties(imgProps);
    	}
	}

	private void cancelClicked(){
		setVisible(false);
        //dispose();
	}

	public void open(ImageControl i){
		imgControl=i;
		
	    bIsNew= false;
		imgProps= imgControl.getProperties();
		
		image.setSelectedIndex(imgPseudos.indexOf(imgProps.imageName));
		if (imgProps.alignWay.equals("NONE"))
		  align.setSelectedIndex(0);
		else if (imgProps.alignWay.equals("RIGHT"))
		  align.setSelectedIndex(1);
		if (imgProps.alignWay.equals("LEFT"))
		  align.setSelectedIndex(2);
		//align.setSelectedItem(imgProps.alignWay);
        alt.setText(imgProps.altText);
        sub.setText(imgProps.subTitle);
        
        this.setVisible(true);
		image.requestFocus();
	}

	public void open(ImageProperties props){
	    int r=0;
	    
	    imgProps=props;
	    bIsNew= true;
	    
        this.setVisible(true);
		image.requestFocus();
	}

	public void reset(){
	    int r=0;
	    
		if (image.getItemCount()>0) image.setSelectedIndex(r);
		if (align.getItemCount()>0) align.setSelectedIndex(r);
    	alt.setText("");
    	sub.setText("");
	}

    
}