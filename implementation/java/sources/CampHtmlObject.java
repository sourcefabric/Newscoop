/*
 * @(#)CampHtmlObject.java
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
     * CampHtmlObject is an ancerstor object for all objects
     * found in HTML document
     */
     
import javax.swing.text.*;
import javax.swing.*;
import java.awt.*;
import java.awt.event.*;
import java.util.*;

public class CampHtmlObject{

    protected static JTextPane textPane;
    protected static Campfire parent;
    protected Vector objList=new Vector();
    protected int objIndex=0;
    
    public CampHtmlObject () {
    }

    public void init(Campfire p){
        parent=p;
        textPane=parent.textPane;
        objIndex=0;
        objList=new Vector();
    }

	protected String cutString(String s,int start,String toCut){
	    StringBuffer sb=new StringBuffer();
	    int length=toCut.length();
	    sb.append(s.substring(0,start));
	    sb.append(s.substring(start+length));
	    return new String(sb);
	}

	protected int firstTag(String s,String tag,int si){
	    int idx=-1;
	    String upp=s.toUpperCase();
	    idx=upp.indexOf(tag,si);
	    return idx;
	}

	protected boolean createIsValid(){
	    int selStart=0;
	    int selEnd=0;
        int t=0;
        
        selStart=textPane.getSelectionStart();
        selEnd=textPane.getSelectionEnd();

        if (selStart==selEnd){
            showInfo(CampResources.get("Info.PleaseSelectSomeText"));
            return false;
        }else{
            StyledDocument doc;
            
            doc= textPane.getStyledDocument();
            if (selStart>selEnd){
                t=selEnd;
                selEnd=selStart;
                selStart=t;
            }
            
            for (int i=selStart; i<selEnd;i++){
                Element elem=doc.getCharacterElement(i);
                AttributeSet set=elem.getAttributes();
    		    Enumeration names = set.getAttributeNames();
    		    while(names.hasMoreElements()) {
    		        Object nextName = names.nextElement();
  		            if (nextName.toString().equalsIgnoreCase("component")){
                        showInfo(CampResources.get("Info.YouCanNotMixTwoDifferentElements"));
                        return false;
                    }
                }
            }
            return true;
        }
        
	}

 	protected int charPosition(String s,int v){
	    //System.out.print("eza a v"+v);
	    boolean isTag=false;
	    int ret=0;
	    for(int i=0;i<v;i++)
	    {
	    if (s.charAt(i)=='<') isTag=true;
	        else 
	        {
	            if (s.charAt(i)=='>')
	            {
	                if (isTag==true) isTag=false;
	            }
	            else 
	                if (!isTag) ret++;
	        }
	   //System.out.println(""+s.charAt(i)+" "+ret);
	    }
	   // System.out.println("ez a vissza "+ret);
	    return ret;
	}
	
    protected void insertComponentTo(Component c){
    	int car=textPane.getCaretPosition();
    	try{
    		textPane.insertComponent(c);
    	}
    	catch(Exception e){
    		System.out.println("at insert "+e);
    		}
    
    	}

    public void reset(){
        objList=new Vector();
        objIndex=0;
    }


	protected void showInfo(String s){
	   parent.showInfo( s);
	}

	protected void showError(String s){
	   parent.showError( s);
	}

	protected void showStatus(String s){
	   parent.showStatus( s);
	}

   
}