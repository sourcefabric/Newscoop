/*
 * @(#)TolHtmlParser.java
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
     * TolHtmlParser parses the content from the html(transfered as a parameter) 
     * and regenerates the content of the textarea. It is not tolerant to errors.
     * If the debu parameter is present, it will appear a debug-tool button:
     * the Regenerate HTML . 
     * By pressing this button, the applet will generate the HTML, and will regenerate
     * the content. By this, you can check the correctness of the content without
     * uploading to the server, and restarting the applet.
     */



import com.sun.java.swing.text.*;
import com.sun.java.swing.*;
import java.awt.event.*;
import tol.unicoded.*;

class TolHtmlParser{
    
    Test parent;
    JTextPane textPane;
  	String fromHtml=new String();
    StringBuffer sixb=new StringBuffer();
    StringBuffer tsb=new StringBuffer();
    String sixChar,tabChar;
    StringBuffer fromTempHtml=new StringBuffer();
    String urlString;
    LinkControl beginL,endL;

    
    public TolHtmlParser(JTextPane t,Test p,String s){
        parent=p;
        textPane=t;
	    sixb.append((char)6);
	    tsb.append((char)9);
	    sixChar=new String(sixb);
	    tabChar=new String(tsb);
	    
	    fromTempHtml.append(s);
    }

	
	public String replacer(String big,String small,String with,boolean ignoreCase,boolean insImg){
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
    	    if (insImg) insImage(i,small);
	    }
	    return work;
	}
	
	public String deTager(String big){
	    StringBuffer s=new StringBuffer();
	    boolean isTag=false;
	    for(int i=0;i<big.length();i++)
	    {
	        if (big.charAt(i)=='<') isTag=true;
	            else
    	        if ((big.charAt(i)=='>')&&(isTag)) isTag=false;
    	        else
    	        if (!isTag) s.append(big.charAt(i));
	    }
	    return new String(s);
	}
	
	public int firstImage(String s){
	    int idx=-1;
	    String upp=s.toUpperCase();
	    //parent.foundImage=-1;
	    idx=upp.indexOf("<!** IMAGE");
/*	    int fn=upp.indexOf("<IMAGE NOT SELECTED>");
	    idx=fn;
	    for(int i=0;i<parent.vectorOfImagePseudos.size();i++)
	    {
    	    fn=upp.indexOf("<"+((String)parent.vectorOfImagePseudos.elementAt(i)).toUpperCase());
    	    if (fn!=-1)
    	    {
    	        if (idx!=-1)
    	        {
    	            if (fn<idx) {parent.foundImage=i;idx=fn;}
    	        }
    	        else {parent.foundImage=i;idx=fn;}
    	    }
	    }*/
	    return idx;
	}
	
	public String deImager(String s){
	    String my=new String(s);
	    StringBuffer t;
	    int v;
	    while ((v=firstImage(my))!=-1)
	    {
	        //System.out.println(""+v+my);
	        t=new StringBuffer();
	        int p=my.indexOf(">",v);
	        t.append(my.substring(0,v));
	        t.append(":");
	        t.append(my.substring(p+1));
	        insImage(charPosition(my,v),my.substring(v,p));
	        my=t.toString();
	        //System.out.println(""+v+my);
	    }
	    return my;
	}
	
	public void insImage(int i,String s){
	    int d=i;
	    //System.out.println(""+d);
//	    textPane.setSelectionStart(d);
	    textPane.setCaretPosition(d);
	    //textPane.setSelectionEnd(d);
	    ImageControl im=parent.insertImage(false);
	    im.setAlign(s);
	    im.setAlt(s);
	    //System.out.println(s);
	    im.setImage(parent,s.substring(11));
	}
	public int firstLink(String s,String kind,int si){
	    int idx=-1;
	    String upp=s.toUpperCase();
	    idx=upp.indexOf("<!** LINK "+kind.toUpperCase(),si);
	    return idx;
	}

	
	public String deLinkerExt(String s){
	        //System.out.println("parserdeLinker");
	    String my=new String(s);
	    StringBuffer t;
	    int v;
	    while ((v=firstLink(my,"EXTERNAL",0))!=-1)
	    {
	        t=new StringBuffer();
	        int p=my.indexOf(">",v);
	        t.append(my.substring(0,v));
	        t.append(":");
	        t.append(my.substring(p+1));
	        int tar=my.indexOf("TARGET \"",v);
	        String targets="";
	        if ((tar!=-1)&&(tar<p)){
	            targets=my.substring(tar+(new String("TARGET \"")).length(),p-1);
	            p=tar-1;
	        }
	        int quo=my.indexOf("\"",v);
	        //System.out.println(p);
	        //System.out.println(quo);
	        String url=my.substring(quo+1,p-1);
	        //System.out.println("parserinslink");
	        //insLink(charPosition(my,v),my.substring(v,p),LinkControl.EXT,LinkControl.BEGIN,parent.linknr);
	        int begin=charPosition(my,v);
	        my=t.toString();
	        //System.out.println("$>"+targets+"<$");
	        //System.out.println("$>"+url+"<$");
	        
	        //the closing tag
	        v=my.indexOf("<!** EndLink",v);
	        //System.out.println("parser+endlink"+v);
	        t=new StringBuffer();
	         p=my.indexOf(">",v);
	        t.append(my.substring(0,v));
	        t.append(":");
	        t.append(my.substring(p+1));
	        //insLink(charPosition(my,v),my.substring(v,p),LinkControl.EXT,LinkControl.END,parent.linknr);
	        int end=charPosition(my,v);
	        //
	        //parent.linknr++;
	        //
	        //System.out.println(beginL);
	        //System.out.println(endL);
	        //beginL.setPair(endL);
	        //endL.setPair(beginL);
	        parent.textPane.setCaretPosition(begin);
	        parent.textPane.setSelectionStart(begin);
	        parent.textPane.setSelectionEnd(end-1);
	        parent.insertLink(LinkControl.EXT,url,targets,false);
		//System.out.println(">"+targets+"<");
	        my=t.toString();
	        //System.out.println(""+v+my);
	    }
	    return my;
	}

	public String deLinkerInt(String s){
	        //System.out.println("parserdeLinker");
	    String my=new String(s);
	    StringBuffer t;
	    int v;
	    while ((v=firstLink(my,"INTERNAL",0))!=-1)
	    {
	        t=new StringBuffer();
	        int p=my.indexOf(">",v);
	        t.append(my.substring(0,v));
	        t.append(":");
	        t.append(my.substring(p+1));
	        int tar=my.indexOf("TARGET \"",v);
	        String targets="";
	        if ((tar!=-1)&&(tar<p)){
	            targets=my.substring(tar+(new String("TARGET \"")).length(),p-1);
	            p=tar-1;
	        }
	        
	        int quo=v+(new String("<!** Link Internal ")).length();
	        //System.out.println(p);
	        //System.out.println(quo);
	        String url=my.substring(quo,p);
	        //System.out.println("parserinslink");
	        //insLink(charPosition(my,v),my.substring(v,p),LinkControl.EXT,LinkControl.BEGIN,parent.linknr);
	        int begin=charPosition(my,v);
	        my=t.toString();
	        
	        //the closing tag
	        v=my.indexOf("<!** EndLink",v);
	        //System.out.println("parser+endlink"+v);
	        t=new StringBuffer();
	         p=my.indexOf(">",v);
	        t.append(my.substring(0,v));
	        t.append(":");
	        t.append(my.substring(p+1));
	        //insLink(charPosition(my,v),my.substring(v,p),LinkControl.EXT,LinkControl.END,parent.linknr);
	        int end=charPosition(my,v);
	        //
	        //parent.linknr++;
	        //
	        //System.out.println(beginL);
	        //System.out.println(endL);
	        //beginL.setPair(endL);
	        //endL.setPair(beginL);
	        parent.textPane.setCaretPosition(begin);
	        parent.textPane.setSelectionStart(begin);
	        parent.textPane.setSelectionEnd(end-1);
	        parent.insertLink(LinkControl.INT,url,targets,false);
	        my=t.toString();
	        //System.out.println(""+v+my);
	    }
	    return my;
	}
	
/*	
	
	public LinkControl insLink(int i,String s,int kind,int location,int idx){
	    int d=i;
	    if (location==LinkControl.BEGIN)
	        urlString=s.substring(s.indexOf("\"")+1,s.length()-1);
	    //System.out.println(urlString);
	    //textPane.setSelectionStart(d);
	    textPane.setCaretPosition(d);
        LinkControl lc=new LinkControl(parent,kind,location,idx);
        lc.setUrl(urlString,false);
        textPane.insertComponent(lc);
        if (location==LinkControl.BEGIN) beginL=lc;
        if (location==LinkControl.END) endL=lc;
        return lc;
	    //textPane.setSelectionEnd(d);
//	    Control im=parent.insertImage();
//	    im.setAlign(s);
	    //System.out.println(lc);
//	    im.setImage(parent,s.substring(11));
	}
	*/
	public void regenerateStyle(String s){
	    textPane.setSelectionStart(5);
	    textPane.setSelectionEnd(10);
	    Action b=parent.getAction(parent.styleActionNames[3]);
	    b.actionPerformed(new ActionEvent(textPane,1,""));
	}
	
	public int charPosition(String s,int v){
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
	
	public String deSpacer(String f){
	    String s=new String(f);
	    StringBuffer t;
	    int v;
	    int start;
	    //v=s.indexOf(sixs);
	    //System.out.println(s);
	    while ((v=s.indexOf(sixChar))!=-1)
	    {
	    //System.out.println("itt "+v );
	    //System.out.println("itt "+s.length() );
	        int nr=0;
	        start=v;
	        while ((v<s.length())&&(s.charAt(v)==(char)6)) 
	            {
        	    //System.out.println(" "+v+ " " +nr);
	                
	                v++;
	                nr++;
	            }
	        t=new StringBuffer();
	        t.append(s.substring(0,start));
	        t.append(":");
	        t.append(s.substring(start+nr));
	        s=new String(t);
        //System.out.println(" "+start+ " " +nr);
        textPane.setCaretPosition(charPosition(s,start));
        SpaceControl sp=parent.insertSpace();
        sp.value.setText(""+(nr));
	    }
	    return s;
	}
	
	private void alignLeft(){
	    Action b=parent.getAction("select-all");
	    b.actionPerformed(new ActionEvent(textPane,1,""));
	    b=parent.getAction("left-justify");
	    b.actionPerformed(new ActionEvent(textPane,1,""));
	    textPane.setSelectionStart(0);
	    textPane.setSelectionEnd(0);
	    /*textPane.setCaretPosition(0);
	    textPane.setSelectionStart(0);
	    textPane.setSelectionEnd(textPane.);*/
	}
	
	private String simpleTags(String code,String open,String close,int action){
	    String ret=new String(code);
	    CustomAction b=new CustomAction("",action,parent);
	    StringBuffer t;
	    int sp;
	    int cp;
	    //System.out.println("before "+action+":"+ret);
	    while((sp=ret.toUpperCase().indexOf(open.toUpperCase()))!=-1)
	    {
	        cp=ret.toUpperCase().indexOf(close.toUpperCase());
	        //System.out.println(""+charPosition(ret,sp)+" "+charPosition(ret,cp));
	        textPane.setCaretPosition(charPosition(ret,sp));
	        textPane.setSelectionStart(charPosition(ret,sp));
	        textPane.setSelectionEnd(charPosition(ret,cp));
    	    b.actionPerformed(new ActionEvent(textPane,charPosition(ret,sp),""));
    	    
    	    
    	    t=new StringBuffer();
    	    t.append(ret.substring(0,sp));
    	    t.append(ret.substring(sp+open.length(),cp));
    	    t.append(ret.substring(cp+close.length()));
    	    ret=new String(t);
	    //System.out.println("after "+action+":"+ret);
	    }
	    return ret;
	}
/*
	private String alignTags(String code,String open,String close,int action){
	    String ret=new String(code);
	    CustomAction b=new CustomAction("",action,parent);
	    StringBuffer t;
	    int sp;
	    int cp;
	    //System.out.println("before "+action+":"+ret);
	    while((sp=ret.toUpperCase().indexOf(open.toUpperCase()))!=-1)
	    {
	        cp=ret.toUpperCase().indexOf(close.toUpperCase());
	        //System.out.println(""+charPosition(ret,sp)+" "+charPosition(ret,cp));
	        textPane.setCaretPosition(charPosition(ret,sp));
	        textPane.setSelectionStart(charPosition(ret,sp));
	        textPane.setSelectionEnd(charPosition(ret,cp));
    	    b.actionPerformed(new ActionEvent(textPane,charPosition(ret,sp),""));
    	    
    	    
    	    t=new StringBuffer();
    	    t.append(ret.substring(0,sp));
    	    t.append(ret.substring(sp+open.length(),cp));
    	    t.append(ret.substring(cp+close.length()));
    	    ret=new String(t);
	    //System.out.println("after "+action+":"+ret);
	    }
	    return ret;
	}
	
	*/
	private String deFonter(String s){
	    String ret=new String(s);
	    int end;
	    int color,size,last;
	    int mode;//1 color 2 size
	    while ((end=(ret.toUpperCase()).indexOf("</FONT>"))!=-1)
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
	            textPane.setCaretPosition(charPosition(ret,last));
	            textPane.setSelectionStart(charPosition(ret,last));
	            textPane.setSelectionEnd(charPosition(ret,end));
	            FontSizeStyleAction a=new FontSizeStyleAction("",textPane,ret.substring(ret.indexOf("=",last)+1,ret.indexOf("=",last)+2));
	            a.actionPerformed(new ActionEvent(textPane,1,""));
	            //ret=cutString(ret,last,"<FONT SIZE=1>");
    	        //ret=cutString(ret,end-13,"</FONT>");
    	        ret=cutString(ret,end,"</FONT>");
	            ret=cutString(ret,last,"<FONT SIZE=1>");
	        }
	        if (mode==1)
	        {
	            textPane.setCaretPosition(charPosition(ret,last));
	            textPane.setSelectionStart(charPosition(ret,last));
	            textPane.setSelectionEnd(charPosition(ret,end));
	            FontColorStyleAction a=new FontColorStyleAction("",textPane,parent.colorChooser,ret.substring(ret.indexOf("=",last)+1,ret.indexOf("=",last)+8));
	            a.actionPerformed(new ActionEvent(textPane,1,""));
	            //ret=cutString(ret,last,"<FONT COLOR=#ffffff>");
    	        //ret=cutString(ret,end-20,"</FONT>");
    	        ret=cutString(ret,end,"</FONT>");
	            ret=cutString(ret,last,"<FONT COLOR=#ffffff>");
	        }
	    }
	    
	    return ret;
	}
	/*
	public String deEscape(String s){
	    int a;
	    while((a=s.indexOf("&quot"))!=-1)
	    {
	        //s=cutString(s,a,"&quot;");
	        StringBuffer t=new StringBuffer();
	        t.append(s.substring(0,a));
	        t.append(s.substring(a+6));
	        s=new String(t);
	    }
	    return s;
	}
	*/

	private String deWorder(String s){
	    String ret=new String(s);
	    int beg;
	    int st=0;
	    while ((beg=(ret.toUpperCase()).indexOf("<!** CLASS \""))!=-1)
	    {
	        String word=ret.substring(ret.indexOf("\"",beg)+1,ret.indexOf(">",beg)-1);
	        //System.out.println(word);
   	        ret=cutString(ret,beg,ret.substring(beg,ret.indexOf(">",beg)+1));
	        //System.out.println(ret);
	        int end=(ret.toUpperCase()).indexOf("<!** ENDCLASS>");
	        if (end==-1) end=beg;
	            else ret=cutString(ret,end,"<!** ENDCLASS>");
	        	st=charPosition(ret,beg);
	            textPane.setCaretPosition(charPosition(ret,beg));
	            textPane.setSelectionStart(charPosition(ret,beg));
	            textPane.setSelectionEnd(charPosition(ret,end));
	            Action a=parent.htmleditorkit.returnWordAction(word);
	            //CharStyleAction a=new CharStyleAction("#"+word,context.getStyle("#"+word));
	            //FontSizeStyleAction a=new FontSizeStyleAction("",textPane,ret.substring(ret.indexOf("=",last)+1,ret.indexOf("=",last)+2));
	            if (a!=null)
	                a.actionPerformed(new ActionEvent(textPane,1,""));
	            if (word.equals("#title")) parent.createTitle(false);
	            ret=ret.substring(0,st)+"::"+ret.substring(st);
	        }
	    return ret;
	}
	
	
	public String cutString(String s,int start,String toCut){
	    StringBuffer sb=new StringBuffer();
	    int length=toCut.length();
	    sb.append(s.substring(0,start));
	    sb.append(s.substring(start+length));
	    return new String(sb);
	}
	
	public String deJustifyer(String s){
        parent.isJustified.setState(false);
	    
	    if (s.indexOf("<DIV ALIGN=JUSTIFY>")==0)
	    {
	        parent.isJustified.setState(true);
	        s=cutString(s,0,"<DIV ALIGN=JUSTIFY>");
	        s=cutString(s,s.length()-6,"</DIV>");
	    }
	    return s;
	}
	
	
	public void parseHtml(){
	    String forText;
	    String forCode;
	    String justCode;
	    
        //fromTempHtml=new StringBuffer("");
        //parent.doc=textPane.getDocument();
        
        parent.showInfo("parsing html ...");
		parent.newFile(false);
        textPane.setEnabled(false);

		//System.out.println(new String(fromTempHtml));
		//dumpFrame.setVisible(true);
		// remove enters
		fromHtml=replacer(new String(fromTempHtml),"\n","",false,false);
		// restore \n from brs
		fromHtml=deJustifyer(fromHtml);
		fromHtml=replacer(fromHtml,"<BR>","\n",true,false);
		fromHtml=replacer(fromHtml,"<!** Title>","<!** Class \"#title\">",true,false);
		fromHtml=replacer(fromHtml,"<!** EndTitle>","<!** EndClass>",true,false);
		
		forText=new String(fromHtml);
		forText=replacer(forText,"<DD>",tabChar,true,false);
		forText=deTager(forText);
		forText=replacer(forText,"&lt;","<",true,false);
		forText=replacer(forText,"&gt;",">",true,false);
		forText=replacer(forText,"&nbsp;","",true,false);
		forText=replacer(forText,"&amp;","&",true,false);
		//System.out.println(forText);
		//dumpFrame.setText(forText);
		forCode=new String(fromHtml);
		forCode=replacer(forCode,"<DD>",tabChar,true,false);
		forCode=replacer(forCode,"&lt;","?",true,false);
		forCode=replacer(forCode,"&gt;","?",true,false);
		forCode=replacer(forCode,"&nbsp;",sixChar,true,false);
		forCode=replacer(forCode,"&amp;",";",true,false);
		//System.out.println("forcode");

		textPane.setText(forText);

		justCode=new String(forCode);
		justCode=deSpacer(justCode);
		//System.out.println(justCode);
		justCode=deImager(justCode);
		//System.out.println(justCode);
		//System.out.println("nagyparse");
		justCode=deLinkerExt(justCode);
		justCode=deLinkerInt(justCode);

		alignLeft();
		
		justCode=simpleTags(justCode,"<B>","</B>",CustomAction.BOLD);
		justCode=simpleTags(justCode,"<I>","</I>",CustomAction.ITALIC);
		justCode=simpleTags(justCode,"<U>","</U>",CustomAction.UNDERLINE);
		
		//System.out.println(justCode);
		justCode=deFonter(justCode);
		justCode=deWorder(justCode);

		justCode=simpleTags(justCode,"<CENTER>","</CENTER>",CustomAction.CENTER);
		justCode=simpleTags(justCode,"<DIV ALIGN=RIGHT>","</DIV>",CustomAction.RIGHT);

		textPane.setSelectionStart(0);
		textPane.setSelectionEnd(0);
		
		//System.out.println(justCode);
//		regenerateStyle(justCode);
        textPane.setEnabled(true);
        parent.showInfo("info label");

	}
	
	public void JavaUnicoder(){
	    String s=fromTempHtml.toString();
	    StringBuffer sb=new StringBuffer("");
	    int i=0;
	    while(i<s.length())
	    {
	        if ((s.charAt(i)=='&')&&(i<s.length()-1)&&(s.charAt(i+1)=='#'))
	        {
	            i+=2;
	            StringBuffer nn=new StringBuffer("");
	            while((i<s.length())&&(s.charAt(i)!=';'))
	            {
	                nn.append(s.charAt(i));
	                i++;
	            }
	            i++;
	            //Integer ed=new Integer(-1);
                Integer ed=new Integer(nn.toString());
                int nr=ed.intValue();
                /*
                StringBuffer uni=new StringBuffer("");
                uni.append("\\u");
                uni.append(new ColorConverter().toHex(nr/256));
                uni.append(new ColorConverter().toHex(nr%256));
                */
                //System.out.println("Unicode"+nr);
                /**
                byte ar[]=new byte[2];
                ar[0]=(byte)(nr/256);
                ar[1]=(byte)(nr%256);
                String sss=new String();
                try{
                    System.out.println(""+ar[0]+"hj"+ar[1]);
                sss=new String(ar,"iso8859_2");
                }
                catch (Exception e){System.out.println(e);}
                **/
                if (nr<256) sb.append((char)nr);
                    else
                    {
                        int idx=tabler.getTable(nr);
                        //System.out.println(idx);
                        
                        
                            switch (idx)
                            {
                                case 0:if (parent.table0==null) parent.table0=new table0();sb.append(parent.table0.getChar(nr));break;
                                case 1:if (parent.table1==null) parent.table1=new table1();sb.append(parent.table1.getChar(nr));break;
                                case 2:if (parent.table2==null) parent.table2=new table2();sb.append(parent.table2.getChar(nr));break;
                                case 3:if (parent.table3==null) parent.table3=new table3();sb.append(parent.table3.getChar(nr));break;
                                case 4:if (parent.table4==null) parent.table4=new table4();sb.append(parent.table4.getChar(nr));break;
                                case 5:if (parent.table5==null) parent.table5=new table5();sb.append(parent.table5.getChar(nr));break;
                                case 6:if (parent.table6==null) parent.table6=new table6();sb.append(parent.table6.getChar(nr));break;
                                case 7:if (parent.table7==null) parent.table7=new table7();sb.append(parent.table7.getChar(nr));break;
                                case 8:if (parent.table8==null) parent.table8=new table8();sb.append(parent.table8.getChar(nr));break;
                                case 9:if (parent.table9==null) parent.table9=new table9();sb.append(parent.table9.getChar(nr));break;
                                case 10:if (parent.tableA==null) parent.tableA=new tableA();sb.append(parent.tableA.getChar(nr));break;
                                case 11:if (parent.tableB==null) parent.tableB=new tableB();sb.append(parent.tableB.getChar(nr));break;
                                case 12:if (parent.tableC==null) parent.tableC=new tableC();sb.append(parent.tableC.getChar(nr));break;
                                case 13:if (parent.tableD==null) parent.tableD=new tableD();sb.append(parent.tableD.getChar(nr));break;
                                case 14:if (parent.tableE==null) parent.tableE=new tableE();sb.append(parent.tableE.getChar(nr));break;
                                case 15:if (parent.tableF==null) parent.tableF=new tableF();sb.append(parent.tableF.getChar(nr));break;
                            }
                          
                           
                    }
//                        sb.append(tabler.getUni(nr));
//                        sb.append(tabler.getUni(nr));
                /*
                byte ar[]=new byte[1];
                ar[0]=(byte)(nr%256);
                int hi=nr%256;
                String sss=new String(ar,hi,0,1);
                */
//                sb.append(sss);
	        }
	        else
	        {
	            sb.append(s.charAt(i));
	            i++;
	        }
	        fromTempHtml=sb;
	        //System.out.println(fromTempHtml.toString());
	    }
	    
	}

}