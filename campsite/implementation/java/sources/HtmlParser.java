/*
 * @(#)HtmlParser.java
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
     * HtmlParser parses the content from the html(transfered as a parameter) 
     * and regenerates the content of the textarea. It is not tolerant to errors.
     * If the debu parameter is present, it will appear a debug-tool button:
     * the Regenerate HTML . 
     * By pressing this button, the applet will generate the HTML, and will regenerate
     * the content. By this, you can check the correctness of the content without
     * uploading to the server, and restarting the applet.
     */



import javax.swing.text.*;
import javax.swing.*;
import java.awt.event.*;
import java.awt.Font;
import java.io.*;
import java.util.*;

class HtmlParser{
    
    private Campfire parent;
    private JTextPane textPane;
  	private String fromHtml=new String();
    private StringBuffer sixb=new StringBuffer();
    private StringBuffer tsb=new StringBuffer();
    private String sixChar,tabChar;
    private String urlString;

    
    public HtmlParser(JTextPane t,Campfire p,String s){
        parent=p;
        textPane=t;
	    sixb.append((char)6);
	    tsb.append((char)9);
	    sixChar=new String(sixb);
	    tabChar=new String(tsb);
	    
	    fromHtml= new String(s);
    }

	
	private String replacer(String big,String small,String with,boolean ignoreCase,boolean insImg){
	    String work=new String(big);
	    String upper;
	    String sw;
	    StringBuffer t;
	    if (!ignoreCase) sw=new String(small); else sw=small.toUpperCase();
	    if (!ignoreCase) upper=work; else upper=work.toUpperCase();
	    int i;
	    while ((i=upper.indexOf(sw))!=-1)
	    {
	        t=new StringBuffer();
	        t.append(work.substring(0,i));
	        t.append(with);
	        t.append(work.substring(i+small.length()));
	        work=new String(t);
    	    if (!ignoreCase) upper=work; else upper=work.toUpperCase();
	    }
	    return work;
	}
	
	private String deTager(String big){
	    StringBuffer s=new StringBuffer();
	    boolean isTag=false;
	    for(int i=0;i<big.length();i++)
	    {
	        if (big.charAt(i)=='<'){
	            isTag=true;
	            if (big.charAt(i+1)=='!'){
    	           String myTag= new String(big.substring(i,i+10));
    	           //if (myTag.equalsIgnoreCase("<!** ADDON")){
    	           //    i=big.indexOf("<!** EndAddOn",i+10);
    	           //}
	           }
	            
	         }
	            else
    	        if ((big.charAt(i)=='>')&&(isTag)) isTag=false;
    	        else
    	        if (!isTag) s.append(big.charAt(i));
	    }
	    return new String(s);
	}
	
	private String deStyleTager(String big){
	    StringBuffer s=new StringBuffer();
	    boolean isTag=false;
	    for(int i=0;i<big.length();i++)
	    {
	        if ((big.charAt(i)=='<')&&(big.charAt(i+1)!='!')) isTag=true;
	            else
    	        if ((big.charAt(i)=='>')&&(isTag)) isTag=false;
    	        else
    	        if (!isTag) s.append(big.charAt(i));
	    }
	    return new String(s);
	}
	


	private String deCampTager(String big){
	    StringBuffer s=new StringBuffer();
	    boolean isTag=false;
	    for(int i=0;i<big.length();i++)
	    {
	        if ((big.charAt(i)=='<')&&(big.charAt(i+1)=='!')){
	           isTag=true;
	           String myTag= new String(big.substring(i,i+10));
	           if (myTag.equalsIgnoreCase("<!** IMAGE")) s.append(":");
	           //else if (myTag.equalsIgnoreCase("<!** ADDON")){
	           //    i=big.indexOf("<!** EndAddOn",i+10);
	           //    s.append(":");
	           //}
	       }
	        else if ((big.charAt(i)=='>')&&(isTag)) isTag=false;
    	    else if (!isTag) s.append(big.charAt(i));
	    }
	    return new String(s);
	}

	private int charPosition(String s,int v){
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
	    }
	    return ret;
	}
	
	
	private String simpleTags(String code,String open,String close,int action){
	    String ret=new String(code);
	    CustomAction b=new CustomAction("",action,parent);
	    StringBuffer t;
	    int sp;
	    int cp;
	    while((sp=ret.toUpperCase().indexOf(open.toUpperCase()))!=-1)
	    {
	        cp=ret.toUpperCase().indexOf(close.toUpperCase());
	        //textPane.setCaretPosition(charPosition(ret,sp));
	        textPane.setSelectionStart(charPosition(ret,sp));
	        textPane.setSelectionEnd(charPosition(ret,cp));
    	    b.actionPerformed(new ActionEvent(textPane,charPosition(ret,sp),""));
    	    
    	    
    	    t=new StringBuffer();
    	    t.append(ret.substring(0,sp));
    	    t.append(ret.substring(sp+open.length(),cp));
    	    t.append(ret.substring(cp+close.length()));
    	    ret=new String(t);
	    }
	    return ret;
	}
	
	private String cutString(String s,int start,String toCut){
	    StringBuffer sb=new StringBuffer();
	    int length=toCut.length();
	    sb.append(s.substring(0,start));
	    sb.append(s.substring(start+length));
	    return new String(sb);
	}
	
	private String deJustifyer(String s){
        //parent.isJustified.setState(false);
	    
	    if (s.indexOf("<DIV ALIGN=JUSTIFY>")==0)
	    {
	        //parent.isJustified.setState(true);
	        s=cutString(s,0,"<DIV ALIGN=JUSTIFY>");
	        s=cutString(s,s.length()-6,"</DIV>");
	    }
	    return s;
	}
	
	
	public void parseHtml(){
	    String forText;
	    String forCode;
	    String justCode;
	    String justStyle;
        
        parent.showStatus("parsing html ...");
	    translateUnicode();
		
		// remove enters
		fromHtml=replacer(fromHtml,"\n","",false,false);
		// restore \n from brs
		fromHtml=deJustifyer(fromHtml);
		fromHtml=replacer(fromHtml,"<BR>","\n",true,false);
		
		forText=new String(fromHtml);
		forText=replacer(forText,"<DD>",tabChar,true,false);
//		forText=deCampTager(forText);
		forText=deTager(forText);
		forText=replacer(forText,"&lt;","<",true,false);
		forText=replacer(forText,"&gt;",">",true,false);
		forText=replacer(forText,"&nbsp;","",true,false);
		forText=replacer(forText,"&amp;","&",true,false);

		textPane.setText(forText);

		forCode=new String(fromHtml);
		forCode=replacer(forCode,"<DD>",tabChar,true,false);
		forCode=deStyleTager(forCode);
		forCode=replacer(forCode,"&lt;","?",true,false);
		forCode=replacer(forCode,"&gt;","?",true,false);
		forCode=replacer(forCode,"&nbsp;","",true,false);
//		forCode=replacer(forCode,"&nbsp;",sixChar,true,false);
		forCode=replacer(forCode,"&amp;",";",true,false);

		justCode=new String(forCode);

	    int p=justCode.indexOf("<",0);
	    while (p!=-1){
           if (isTag(justCode, "<!** IMAGE", p)){
    	       justCode=CampBroker.getImage().parseHtml(justCode);
    	   }else if (isTag(justCode, "<!** CLASS", p)){
    	       justCode=CampBroker.getKeyword().parseHtml(justCode);
    	   }else if (isTag(justCode, "<!** LINK EXTERNAL", p)){
    	      justCode=CampBroker.getExternalLink().parseHtml(justCode);
    	   }else if (isTag(justCode, "<!** LINK INTERNAL", p)){
              justCode=CampBroker.getInternalLink().parseHtml(justCode);
    	   //}else if (isTag(justCode, "<!** LINK AUDIO", p)){
           //   justCode=CampBroker.getAudioLink().parseHtml(justCode);
    	   //}else if (isTag(justCode, "<!** LINK VIDEO", p)){
           //   justCode=CampBroker.getVideoLink().parseHtml(justCode);
    	   }else if (isTag(justCode, "<!** TITLE", p)){
    	       justCode=CampBroker.getSubhead().parseHtml(justCode);
    	   //}else if (isTag(justCode, "<!** ADDON", p)){
    	   //    justCode=AddOnBroker.parseHtml(justCode);
    	   }else{
    	       //parent.debug("e nasli smo pocetak taga");
    	   }
           p=justCode.indexOf("<",p+1);
        }

		justStyle=new String(fromHtml);
		justStyle=replacer(justStyle,"<DD>",tabChar,true,false);
		justStyle=deCampTager(justStyle);
		justStyle=replacer(justStyle,"&lt;","?",true,false);
		justStyle=replacer(justStyle,"&gt;","?",true,false);
		justStyle=replacer(justStyle,"&nbsp;","",true,false);
//		justStyle=replacer(justStyle,"&nbsp;",sixChar,true,false);
		justStyle=replacer(justStyle,"&amp;",";",true,false);

//		parent.debug(justStyle);
		

	    p=justStyle.indexOf("<",0);
	    while (p!=-1){
    	   if (isTag(justStyle, "<FONT", p))
		      justStyle=CampBroker.getFont().parseHtml(justStyle);
    	   else if (isTag(justStyle, "<B", p))
		      justStyle=simpleTags(justStyle,"<B>","</B>",CustomAction.BOLD);
    	   else if (isTag(justStyle, "<I", p))
		      justStyle=simpleTags(justStyle,"<I>","</I>",CustomAction.ITALIC);
    	   else if (isTag(justStyle, "<U", p))
		      justStyle=simpleTags(justStyle,"<U>","</U>",CustomAction.UNDERLINE);
    	   else if (isTag(justStyle, "<CENTER", p))
		      justStyle=simpleTags(justStyle,"<CENTER>","</CENTER>",CustomAction.CENTER);
    	   else if (isTag(justStyle, "<DIV ALIGN=RIGHT", p))
		      justStyle=simpleTags(justStyle,"<DIV ALIGN=RIGHT>","</DIV>",CustomAction.RIGHT);
    	   else
    	       justStyle="";
	       p=justStyle.indexOf("<",0);
	   }




//		parent.debug(justStyle);
		textPane.setSelectionStart(0);
		textPane.setSelectionEnd(0);
		
//        textPane.setEnabled(true);
        parent.showStatus(CampResources.get("Status.Ready"));


	}


	private void translateUnicode(){
	   
        Font f = new Font(null, Font.PLAIN, 12);
//        Font f = new Font("Dialog", Font.PLAIN, 12);
        textPane.setFont(f);
	   
	   try{
    	   fromHtml= new String (fromHtml.getBytes("UTF-8"), "UTF-8");
	   }catch(Exception e){}
	}


	private boolean isTag(String s,String tag,int si){
	    int idx=-1;
	    String upp=s.toUpperCase();
	    idx=upp.indexOf(tag,si);
	    if (idx==si)
	       return true;
	    else
	       return false;
	}

}


