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
     * It has a series of ComboBoxes, the content of these are retreived using a CGI,
     * and passing the already set values for a given depth 
     * (Language->Publication->Isuue->Section)
     */

import javax.swing.*;
import javax.swing.event.*;
import java.awt.*;
import java.awt.event.*;
import java.util.*;
import java.net.*;
import java.io.*;

class InternalLinkFrame extends CampDialog{
    
    boolean err=false;
    private JComboBox target;
    //private JButton rest;
    LinkCombo links[];
    private JTextField frame;
    private String list[];
    private final static String listofIds[]={"IdLanguage","IdPublication","NrIssue","NrSection","NrArticle"};
    private InternalLinkProperties intProps;
    private boolean bIsNew=false;

    
    public InternalLinkFrame(Campfire p, String title){
        //super(p, title, 400, 400);
        super(p, title, 7, 2);

        list=new String[5];
        list[0]= CampResources.get("InternalLinkFrame.Language");
        list[1]= CampResources.get("InternalLinkFrame.Publication");
        list[2]= CampResources.get("InternalLinkFrame.Issue");
        list[3]= CampResources.get("InternalLinkFrame.Section");
        list[4]= CampResources.get("InternalLinkFrame.Article");
        
        links=new LinkCombo[5];

        for (int i=0;i<5;i++)
        {
            links[i]=new LinkCombo(i,listofIds[i],this);
            addCompo(new JLabel(list[i]),links[i]);
            links[i].setValid(false);
            if (i>0) links[i].setUpper(links[i-1]);
        }
        target=new JComboBox();
        target.addItem(CampResources.get("InternalLinkFrame.OpenIn.Default"));
        target.addItem(CampResources.get("InternalLinkFrame.OpenIn.NewWindow"));
        target.addItem(CampResources.get("InternalLinkFrame.OpenIn.FullWindow"));
        target.addItem(CampResources.get("InternalLinkFrame.OpenIn.FrameNamed"));
        if (CampResources.isRightToLeft())((JLabel)target.getRenderer()).setHorizontalAlignment(SwingConstants.RIGHT);
        
        frame=new JTextField(15);
        addCompo(new JLabel(CampResources.get("InternalLinkFrame.OpenIn")),target);
        addCompo(new JLabel(CampResources.get("InternalLinkFrame.FrameName")),frame);
        frame.setEditable(false);
        //rest=new JButton("Reread");
        //rest.setPreferredSize(new Dimension(80,26));
        //rest.setMaximumSize(new Dimension(80,26));
        //addCompo(new JLabel("Reread"),rest);
        addButtons(ok,cancel);
        finishDialog();
        
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
        //rest.addActionListener(new ActionListener(){
        //    public void actionPerformed(ActionEvent e){
        //        restart();
        //    }
        //    });
        
    }

    

	private void okClicked(){

        boolean ok=true;

        int tarIndex=target.getSelectedIndex();
        String myframe= new String(frame.getText());

        myframe= myframe.trim();

        for (int i=0;i<5;i++)
            if (!links[i].valid) ok=false;
        if (tarIndex==3 && myframe.length()<1) ok=false;
        if (!ok){
             showInfo(CampResources.get("Info.YouMustInitializeAllFields"));
             if (!links[0].valid)
                links[0].requestFocus();
             else if (!links[1].valid)
                links[1].requestFocus();
             else if (!links[2].valid)
                links[2].requestFocus();
             else if (!links[3].valid)
                links[3].requestFocus();
             else if (!links[4].valid)
                links[4].requestFocus();
             else
                frame.requestFocus();
        }
        else {
            
            intProps.languageId= new Integer(links[0].value).intValue();
            intProps.publicationId= new Integer(links[1].value).intValue();
            intProps.issueId=new Integer(links[2].value).intValue();
            intProps.sectionId= new Integer(links[3].value).intValue();
            intProps.articleId= new Integer(links[4].value).intValue();
            if (tarIndex==3){
                intProps.target=frame.getText();
            }else if (tarIndex==1){
                intProps.target="_blank";
            }else if (tarIndex==2){
                intProps.target="_top";
            }else {
                intProps.target="";
            }
        		
    		if (bIsNew) {
                CampBroker.getInternalLink().createPresentation(intProps);
            }else{
                CampBroker.getInternalLink().save(intProps);
        	}
    		setVisible(false);
            
        }


	}

	private void cancelClicked(){
		setVisible(false);
	}
   
	public void open(InternalLinkProperties props, boolean b){
	    int r=0;
	    
	    intProps=props;
	    bIsNew= b;
	    
	    if (!bIsNew){
        	if ((intProps.target==null)||(intProps.target.equals(""))||(intProps.target.toUpperCase().equals("UNSPECIFIED"))) {
        	   target.setSelectedIndex(0);
        	}else if (intProps.target.equals("_blank")) {
        	   target.setSelectedIndex(1);
        	}else if (intProps.target.equals("_top")) {
        	   target.setSelectedIndex(2);
        	}else{
            	frame.setEditable(true);
        		frame.setText(intProps.target);
            	target.setSelectedIndex(3);
            }
    		setCombos();
	    }else{
            links[0].setValues(contact(0));
	    }
	    
        this.setVisible(true);
		links[0].requestFocus();
	}
    
	public void reset(){
	    int r=0;
        for(int i=0;i<5;i++){
    		//links[i].combo.setSelectedIndex(r);
        }
		target.setSelectedIndex(r);
		frame.setText("");
       	frame.setEditable(false);
	}


//    public void open(InternalLinkControl w){
//        intControl=w;
//        setVisible(true);
//        if (!links[0].valid) {links[0].setValues(contact(0));}
//        else 
//            if (w.ids[4]!=null) setCombos(w);
        
        
//    }

    private void setCombos(){
        for(int i=0;i<5;i++)
        {
        links[i].setValues(contact(i));
        int idx=0;
        if (i==0)
            idx=links[i].id.indexOf(new Integer(intProps.languageId).toString());
        else if (i==1)
            idx=links[i].id.indexOf(new Integer(intProps.publicationId).toString());
        else if (i==2)
            idx=links[i].id.indexOf(new Integer(intProps.issueId).toString());
        else if (i==3)
            idx=links[i].id.indexOf(new Integer(intProps.sectionId).toString());
        else if (i==4)
            idx=links[i].id.indexOf(new Integer(intProps.articleId).toString());
        if (idx==-1) idx=0;
        links[i].combo.setSelectedIndex(idx);
        }
        
    }
    
    public void refresh(int i){
        for (int g=i;g<5;g++)
        {
            if (links[g].combo.getItemCount()!=0) links[g].combo.setSelectedIndex(0);
            links[g].setValid(false);
        }
        links[i].setValues(contact(i));
    }
    
    private String contact(int l){
        err=false;
        showStatus(CampResources.get("Status.Connecting"));
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
            //URL u=new URL("http://netfinity-4.mdlf.org:80/priv/pub/issues/sections/articles/list.xql"+"?"+sb.toString());
            URL u=new URL(parent.linkscript+"?"+sb.toString());
            URLConnection uc=u.openConnection();
            //DataInputStream dis=new DataInputStream(uc.getInputStream());
            BufferedReader dis= new BufferedReader(new InputStreamReader(uc.getInputStream(), "UTF-8"));
 
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
            showError(e.toString());
            err=true;
        }
        if (!err) {
                showStatus(CampResources.get("Status.Ready"));
            }
        return page;
    }
    
    private void canExit(){
        boolean ok=true;
        for (int i=0;i<5;i++)
            if (!links[i].valid) ok=false;
        if (!ok) showInfo(CampResources.get("Info.YouMustInitializeAllFields"));
        else
        {
        for (int i=0;i<5;i++)
        {
//            which.ids[i]=links[i].value;
//            which.pair.ids[i]=links[i].value;
//            which.gotIds(true);
        }
        setVisible(false);
        String si=(String)(target.getSelectedItem());
//                which.setFrame(frame.getText());
//                which.pair.setFrame(frame.getText());
//        which.setTarget(si);
//        which.pair.setTarget(si);
            
        }
    }

    public String stat(){
        StringBuffer sb=new StringBuffer();
        for (int i=0;i<5;i++)
            if (!links[i].valid) sb.append("_"); else sb.append("X");
            return sb.toString();
    }
    
//    public void restart(){
//        links[0].setValues(contact(0));
//    }


}