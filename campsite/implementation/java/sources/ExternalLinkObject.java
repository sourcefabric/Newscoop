/*
 * @(#)ExternalLinkObject.java
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
     * ExternalLinkObject is a object containing all methods concerning external
     * links found in HTML document
     */
     
import javax.swing.text.*;
import javax.swing.*;
import java.awt.*;
import java.awt.event.*;
import java.util.*;

public final class ExternalLinkObject extends CampHtmlObject {

    private ExternalLinkFrame urler;

    public ExternalLinkObject () {

    }

    public void init( Campfire p){
        super.init(p);
        urler=null;
    }

	public String parseHtml(String s){
	    String my=new String(s);
	    StringBuffer t;
	    int fbeg, sbeg, fend, send;
	    ExternalLinkProperties extProps;
	    
	    if ((fbeg=firstTag(my,"<!** LINK EXTERNAL",0))!=-1)
	    {
	        t=new StringBuffer();
	        extProps= new ExternalLinkProperties();
	        
	        fend=my.indexOf(">",fbeg);
	        sbeg=my.indexOf("<!** EndLink",fend);
            send=my.indexOf(">",sbeg);
	        
	        t.append(my.substring(0,fbeg));
	        //t.append(":");
	        t.append(my.substring(fend+1, sbeg));
	        //t.append(":");
	        t.append(my.substring(send+1));
	        
	        // find url
	        int quo=my.indexOf("\"",fbeg);
	        String url=my.substring(quo+1,my.indexOf("\"",quo+1));

            //find target
	        int tar=my.indexOf("TARGET \"",fbeg);
	        String targets="";
	        if ((tar!=-1)&&(tar<fend)){
	            targets=my.substring(tar+(new String("TARGET \"")).length(),fend-1);
	        }
	        
    	    extProps.url=url;
    	    extProps.target=targets;
    	    extProps.selStart=fbeg;
    	    extProps.selEnd=fbeg+(sbeg-fend)-1;
            textPane.setSelectionStart(extProps.selStart);
            textPane.setSelectionEnd(extProps.selEnd);
            createPresentation(extProps);
	        my=t.toString();
	    }
	    return my;
	}

    public void create(){
	    ExternalLinkProperties extProps= new ExternalLinkProperties();

        extProps.selStart=textPane.getSelectionStart();
        extProps.selEnd=textPane.getSelectionEnd();

        if (createIsValid()){
    	    openDialog();
            urler.open(extProps, true);
        }
    }

	public void edit(Integer i){
	   ExternalLinkProperties myProps= new ExternalLinkProperties();;
	   
	   myProps= (ExternalLinkProperties)objList.get( i.intValue());
	   openDialog();
       urler.open(myProps, false);
	}

	public void save(ExternalLinkProperties props){
        int myIndex= props.objIndex;
        
        objList.set( myIndex, props);

	}

   public void createPresentation(ExternalLinkProperties props){

        int ss=props.selStart;
        int se=props.selEnd;
        
        if (ss>se){
            int swap=se;
            se=ss;
            ss=se;
        }
        props.objIndex= objIndex;
        objList.addElement(props);
        parent.htmleditorkit.createPresentation("ExternalLink", new Integer(objIndex));
        objIndex++;

   }

    private void openDialog(){
        if (urler==null){
            urler=new ExternalLinkFrame(parent, CampResources.get("ExternalLinkFrame.Title"));
        }else{
            urler.reset();
        }
    }

    public String getFirstTag( Integer i){
       String sTag= new String();
	   ExternalLinkProperties myProps= new ExternalLinkProperties();;
	   
	   myProps= (ExternalLinkProperties)objList.get( i.intValue());
	   sTag= "<!** Link external \"" + myProps.url + "\"";
	   if (myProps.target.length()> 0) sTag= sTag + " TARGET \"" + myProps.target + "\"";
	   sTag= sTag + ">";
        
       return sTag;
        
    }

}