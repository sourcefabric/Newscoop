/*
 * @(#)InternalLinkObject.java
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
     * InternalLinkObject is a object containing all methods concerning internal
     * links found in HTML document
     */
     
import javax.swing.text.*;
import javax.swing.*;
import java.awt.*;
import java.awt.event.*;
import java.util.*;

public final class InternalLinkObject extends CampHtmlObject {

	private InternalLinkFrame ilframe;

    public InternalLinkObject () {

    }

    public void init( Campfire p){
        super.init(p);
        ilframe=null;
    }

	public String parseHtml(String s){
	    String my=new String(s);
	    StringBuffer t;
	    int fbeg, sbeg, fend, send;
	    InternalLinkProperties intProps;

	    if ((fbeg=firstTag(my,"<!** LINK INTERNAL",0))!=-1){
	        t=new StringBuffer();
	        intProps= new InternalLinkProperties();
	        
	        fend=my.indexOf(">",fbeg);
	        sbeg=my.indexOf("<!** EndLink",fend);
            send=my.indexOf(">",sbeg);
	        
	        t.append(my.substring(0,fbeg));
	        //t.append(":");
	        t.append(my.substring(fend+1, sbeg));
	        //t.append(":");
	        t.append(my.substring(send+1));
	        

            //find target
	        int tar=my.indexOf("TARGET \"",fbeg);
	        String targets="";
	        if ((tar!=-1)&&(tar<fend)){
	            targets=my.substring(tar+(new String("TARGET \"")).length(),fend-1);
	        }
	        
            // find article path
	        String props="";
	        if ((tar!=-1)&&(tar<fend)){
	            props=my.substring(fbeg+19,tar-1);
            }else {
	            props=my.substring(fbeg+19,fend);
	        }

            //parent.debug(props);
            intProps= parseProperties(props);
            
    	    intProps.target=targets;
    	    intProps.selStart=fbeg;
    	    intProps.selEnd=fbeg+(sbeg-fend)-1;
            textPane.setSelectionStart(intProps.selStart);
            textPane.setSelectionEnd(intProps.selEnd);
            
            createPresentation(intProps);
	        my=t.toString();
	    }
	    return my;
	}
	
    private InternalLinkProperties parseProperties( String s){
        InternalLinkProperties intProps= new InternalLinkProperties();
        int i, idend;
        Integer idValue;
        String alignWay, altText, imageName;
        String toParse;
        
        toParse=s.toUpperCase();
        
        // here we find language id
        i=toParse.indexOf("IDLANGUAGE=");
        if (i==-1){
    		intProps.languageId=0;
    	}else{
            idend= toParse.indexOf("&", i);
            if (idend==-1){
        		intProps.languageId=0;
        	}else{
                idValue= new Integer(toParse.substring(i+11,idend));
                intProps.languageId=idValue.intValue();
            }
    	}
		
        
        // here we find publication id
        i=toParse.indexOf("IDPUBLICATION=");
        if (i==-1){
    		intProps.publicationId=0;
    	}else{
            idend= toParse.indexOf("&", i);
            if (idend==-1){
        		intProps.publicationId=0;
        	}else{
                idValue= new Integer(toParse.substring(i+14,idend));
                intProps.publicationId=idValue.intValue();
            }
    	}
		
        // here we find issue id
        i=toParse.indexOf("NRISSUE=");
        if (i==-1){
    		intProps.issueId=0;
    	}else{
            idend= toParse.indexOf("&", i);
            if (idend==-1){
        		intProps.issueId=0;
        	}else{
                idValue= new Integer(toParse.substring(i+8,idend));
                intProps.issueId=idValue.intValue();
            }
    	}
		
        // here we find section id
        i=toParse.indexOf("NRSECTION=");
        if (i==-1){
    		intProps.sectionId=0;
    	}else{
            idend= toParse.indexOf("&", i);
            if (idend==-1){
        		intProps.sectionId=0;
        	}else{
                idValue= new Integer(toParse.substring(i+10,idend));
                intProps.sectionId=idValue.intValue();
            }
    	}
		
        // here we find article id
        i=toParse.indexOf("NRARTICLE=");
        if (i==-1){
    		intProps.articleId=0;
    	}else{
            idend= toParse.length();
            idValue= new Integer(toParse.substring(i+10,idend));
            intProps.articleId=idValue.intValue();
    	}

        return intProps;

    }


    public void create(){
	    InternalLinkProperties intProps= new InternalLinkProperties();

        intProps.selStart=textPane.getSelectionStart();
        intProps.selEnd=textPane.getSelectionEnd();

        if (createIsValid()) {
    	    openDialog();
            ilframe.open(intProps, true);
        }
        
        
        
    }
	
	public void edit(Integer i){
	   InternalLinkProperties myProps= new InternalLinkProperties();;
	   
	   myProps= (InternalLinkProperties)objList.get( i.intValue());

        openDialog();
        ilframe.open(myProps, false);
	}

	public void save(InternalLinkProperties props){
        int myIndex= props.objIndex;
        
        objList.set( myIndex, props);

	}

   public void createPresentation(InternalLinkProperties props){

        int ss=props.selStart;
        int se=props.selEnd;
    
        if (ss>se){
            int swap=se;
            se=ss;
            ss=se;
        }
    
    
        props.objIndex= objIndex;
        objList.addElement(props);
        parent.htmleditorkit.createPresentation("InternalLink", new Integer(objIndex));
        objIndex++;

   }

    private void openDialog(){
        if (ilframe==null){
            ilframe=new InternalLinkFrame(parent, CampResources.get("InternalLinkFrame.Title"));
            //ilframe.links[0].setValues(ilframe.contact(0));
            //ilframe.links[0].valid=true;
        }else{
            ilframe.reset();
        }
    }
    
    public String getFirstTag( Integer i){
       String sTag= new String();
       String sInt= new String();
	   InternalLinkProperties myProps= new InternalLinkProperties();;
	   
	   myProps= (InternalLinkProperties)objList.get( i.intValue());

	   sInt= "IdLanguage=" + new Integer(myProps.languageId).toString();
	   sInt= sInt + "&IdPublication=" + new Integer(myProps.publicationId).toString();
	   sInt= sInt + "&NrIssue=" + new Integer(myProps.issueId).toString();
	   sInt= sInt + "&NrSection=" + new Integer(myProps.sectionId).toString();
	   sInt= sInt + "&NrArticle=" + new Integer(myProps.articleId).toString();

	   sTag= "<!** Link internal " + sInt;
	   if (myProps.target.length()> 0) sTag= sTag + " TARGET \"" + myProps.target + "\"";
	   sTag= sTag + ">";
        
       return sTag;
        
    }
    

}