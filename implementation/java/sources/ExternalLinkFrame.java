/*
 * @(#)ExternalLinkFrame.java
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
     * ExternalLinkFrame frame for external links 
     */


import javax.swing.*;
import java.awt.*;
import java.awt.event.*;
import javax.swing.event.*;
import java.net.*;
import java.lang.*;

class ExternalLinkFrame extends CampDialog{
    private JTextField url,frame;
    private String urlVal;
    private JComboBox target;
    private ExternalLinkProperties extProps;
    private boolean bIsNew=false;

    public ExternalLinkFrame(Campfire p, String title){
        //super(p, title, 400, 230);
        super(p, title, 3, 2);

        target=new JComboBox();
        target.addItem(CampResources.get("ExternalLinkFrame.OpenIn.Default"));
        target.addItem(CampResources.get("ExternalLinkFrame.OpenIn.NewWindow"));
        target.addItem(CampResources.get("ExternalLinkFrame.OpenIn.FullWindow"));
        target.addItem(CampResources.get("ExternalLinkFrame.OpenIn.FrameNamed"));
        
        if (CampResources.isRightToLeft())((JLabel)target.getRenderer()).setHorizontalAlignment(SwingConstants.RIGHT);

        url=new JTextField(20);
        frame=new JTextField(15);
        frame.setEditable(false);
        addCompo(new JLabel(CampResources.get("ExternalLinkFrame.URL")),url);
        addCompo(new JLabel(CampResources.get("ExternalLinkFrame.OpenIn")),target);
        addCompo(new JLabel(CampResources.get("ExternalLinkFrame.FrameName")),frame);
        
        
        addButtons(ok,cancel);
        finishDialog();

        ok.addActionListener(new ActionListener(){
            public void actionPerformed(ActionEvent e){
                okClicked();
                
            }
            });
        cancel.addActionListener(new ActionListener(){
            public void actionPerformed(ActionEvent e){
                cancelClicked();
            }
            });

        target.addItemListener(new ItemListener(){
            public void itemStateChanged(ItemEvent ev){
                if (target.getSelectedIndex()==3){
                    frame.setEditable(true); 
                    frame.requestFocus();
                }else {
                    frame.setText("");
                    frame.setEditable(false);
                }
            }
            });
    }

	private void okClicked(){

        int tarIndex=target.getSelectedIndex();
        String myurl= new String(url.getText());
        String myframe= new String(frame.getText());

        myurl= myurl.trim();
        myframe= myframe.trim();
        
        if (myurl.length()<1 || (tarIndex==3 && myframe.length()<1)){
            showInfo(CampResources.get("Info.YouMustInitializeAllFields"));
            if(myurl.length()<1)
                url.requestFocus();
            else
                frame.requestFocus();
            }
        else{
    		setVisible(false);
            extProps.url=myurl;
            
            if (tarIndex==3){
                extProps.target=myframe;
            }else if (tarIndex==1){
                extProps.target="_blank";
            }else if (tarIndex==2){
                extProps.target="_top";
            }else {
                extProps.target="";
            }
        		
    		if (bIsNew) {
                CampBroker.getExternalLink().createPresentation(extProps);
            }else{
        	   CampBroker.getExternalLink().save(extProps);
        	}
        }
	}

	private void cancelClicked(){
		setVisible(false);
	}
    
	public void open(ExternalLinkProperties props, boolean b){
	    int r=0;
	    
	    
	    extProps=props;
	    bIsNew= b;
	    
	    if (!bIsNew ){
    		url.setText(extProps.url);
    
        	if ((extProps.target==null)||(extProps.target.equals(""))||(extProps.target.toUpperCase().equals("UNSPECIFIED"))) {
        	   target.setSelectedIndex(0);
        	}else if (extProps.target.equals("_blank")) {
        	   target.setSelectedIndex(1);
        	}else if (extProps.target.equals("_top")) {
        	   target.setSelectedIndex(2);
        	}else{
            	frame.setEditable(true);
        		frame.setText(extProps.target);
            	target.setSelectedIndex(3);
            }
	   }
	       
        this.setVisible(true);
		url.requestFocus();
	}

    
	public void reset(){
	    int r=0;
		url.setText("");
		target.setSelectedIndex(r);
		frame.setText("");
       	frame.setEditable(false);
	}


    
}