/*
 * @(#)FontObject.java
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
import java.net.URL;

public final class FontObject extends CampHtmlObject {

    FontColorFrame colorChooser;
    URL imagepath=null;
    
    public FontObject () {
    }

    public void init(Campfire p, URL imgpath){
        init(p);
        colorChooser=null;
        imagepath= imgpath;
    }
    
	public String parseHtml(String s){
	    String ret=new String(s);
	    int end;
	    int color,size,last;
	    int mode;//1 color 2 size
	    if ((end=(ret.toUpperCase()).indexOf("</FONT>"))!=-1)
	    {
	        color=ret.toUpperCase().lastIndexOf("<FONT COLOR",end);
	        size=ret.toUpperCase().lastIndexOf("<FONT SIZE",end);
	        if (color==-1)
	        {
	            if (size==-1)
	            {
	                System.out.println("</FONT> without known opener at position "+charPosition(ret,end));
        	        int er=ret.toUpperCase().lastIndexOf("<FONT",end);
        	        if (er!=-1)
        	        {
        	        ret=cutString(ret,end,"</FONT>");
        	        int fin=ret.toUpperCase().indexOf(">",er);
    	            ret=cutString(ret,er,ret.substring(er,fin));
    	            }
	                mode=-1;
	                last=-1;
	            }
	            else
	            {
	                last=size;
	                mode=2;
	            }
	        }
	        else
	        {
	            if (size==-1)
	            {
	                last=color;
	                mode=1;
	            }
	            else
	            {
	                if (size<color) {last=color;mode=1;}
	                    else {last=size;mode=2;}
	            }
	        }
	        //size
	        if (mode==2)
	        {
	            //textPane.setCaretPosition(charPosition(ret,last));
	            textPane.setSelectionStart(charPosition(ret,last));
	            textPane.setSelectionEnd(charPosition(ret,end));
	            FontSizeStyleAction a=new FontSizeStyleAction("",textPane,ret.substring(ret.indexOf("=",last)+1,ret.indexOf("=",last)+2));
	            a.actionPerformed(new ActionEvent(textPane,1,""));
    	        ret=cutString(ret,end,"</FONT>");
	            ret=cutString(ret,last,"<FONT SIZE=1>");
	        }
	        if (mode==1)
	        {
	            //textPane.setCaretPosition(charPosition(ret,last));
	            textPane.setSelectionStart(charPosition(ret,last));
	            textPane.setSelectionEnd(charPosition(ret,end));
	            FontColorStyleAction a=new FontColorStyleAction("",textPane,null,ret.substring(ret.indexOf("=",last)+1,ret.indexOf("=",last)+8));
	            a.actionPerformed(new ActionEvent(textPane,1,""));
    	        ret=cutString(ret,end,"</FONT>");
	            ret=cutString(ret,last,"<FONT COLOR=#ffffff>");
	        }
	    }
	    
	    return ret;
	}

    public void setColor(){
        
        openDialog();
        colorChooser.setTP(textPane);
        colorChooser.nonef=false;
        colorChooser.setVisible(true);
    }
    
    public void clear(){
        FontColorStyleAction a=new FontColorStyleAction("NULLOL",textPane, null,"");
        a.actionPerformed(new ActionEvent(textPane,1,""));
    }

    private URL buildURL(String a)
    {
        URL Im=null;
            try{
                Im=new URL(imagepath,a);
            }
            catch(Exception e){}
        return Im;    
    }

    private void openDialog(){
        if (colorChooser==null){
            colorChooser=new FontColorFrame(parent, CampResources.get("FontColorFrame.Title"), buildURL(CampConstants.COLOR_CHOOSE_IMAGE));
        }
    }


}