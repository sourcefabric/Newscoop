/*
 * @(#)HtmlGenerator.java
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
     * HtmlGenerator parses the Document tree of the main text area,
     * and will generate the corresponding html-like document (with custom tags
     * for links, images, keywords)
     * Because the Document tree is behaving strange in some cases (ex: if you enter the 
     * word "go", you set the color of the letter "g" to red, than you set the color of
     * the letter "o" to red again, the document tree will contain something like:
     * <FONT COLOR=red>g</FONT><FONT color=red>o</FONT>, which is redundant),
     * there is an HtmlProperties helper class, which behaves as a buffer for the possible
     * properties. The main idea is that when you generate the resulting html, the redundant
     * closing and opening tags for the same propertie will eliminate each other.
     */
import com.sun.java.swing.text.*;
import com.sun.java.swing.event.*;
import java.awt.*;
import java.util.*;

class HtmlGenerator{
    
    Document doc;
    StringBuffer toHtml;
    Test parent;
    boolean brline;
    private String elem=null;
    HtmlProperties nowOpened,toClose,toOpen;
    HtmlProperties bnowOpened,btoClose,btoOpen;
    boolean first;
    boolean parseVisible;
    LinkControl mylink;
//    boolean buffparagraph;

    
    public HtmlGenerator(Document d,StringBuffer s,Test p,boolean parse){
        doc=d;
        toHtml=s;
        parent=p;
        parseVisible=parse;
    }
    
    public void generate(){
        if (parent.isJustified.getState()) 
                toHtml.append("<DIV ALIGN=JUSTIFY>");
        
        Element[] el=doc.getRootElements();
        first=true;
        nowOpened=new HtmlProperties(parent.knownProperties);
        toClose=new HtmlProperties(parent.knownProperties);
        toOpen=new HtmlProperties(parent.knownProperties);
        for(int i=0;i<el.length;i++)
    		recurseElement(el[i]);
        toHtml.setLength(toHtml.length()-5);
        tryOpenElem(true,new HtmlProperties(parent.knownProperties));
        if (parent.isJustified.getState()) 
            {
                toHtml.append("</DIV>");
            }
    }

    boolean tr(String v){
        if (v.equals("true")) return true; else return false;
    }
    
    int order(String v){
        for(int i=0;i<parent.pseutags.length;i++)
            if (v.equals(parent.pseutags[i])) return i;
        return parent.pseutags.length-1;    
    }
    private void tryOpenElem(boolean paragraph,HtmlProperties hp){
        /*if (paragraph)
        {
            //openElem(paragraph,hp);
        }
        else
        {*/
        if (first)
        {
            if (paragraph)
            {
            openElem(paragraph,hp);
            first=false;
            bnowOpened=new HtmlProperties(hp);
            }
            else
            {
            openElem(paragraph,hp);
            first=false;
            nowOpened=new HtmlProperties(hp);
            }
        }
        else
        {
            if (paragraph)
            {
            btoClose=difference(bnowOpened,hp);
            closeElem(paragraph,btoClose);
            bnowOpened=substr(bnowOpened,btoClose);
            btoOpen=difference(hp,bnowOpened);
            openElem(paragraph,btoOpen);
            bnowOpened=addition(bnowOpened,btoOpen);
            }
            else
            {
            toClose=difference(nowOpened,hp);
            //System.out.print(nowOpened);
            closeElem(paragraph,toClose);
            nowOpened=substr(nowOpened,toClose);
            //System.out.print(nowOpened);
            //if(hp.getBreakLine()) System.out.println("dfsd");   
            //if(hp.getBreakLine()&&paragraph) toHtml.append("<BR>\n");   
            
            toOpen=difference(hp,nowOpened);
            openElem(paragraph,toOpen);
            nowOpened=addition(nowOpened,toOpen);
            //System.out.print(nowOpened);
            /*swap=new HtmlProperties(hp);    
            substract(hp,buff);
            closeElem(paragraph,buff);
            openElem(paragraph,hp);
            hp=new HtmlProperties(swap);*/
            }
        }
        //}
    }
    private void tryCloseElem(boolean paragraph,HtmlProperties hp){
        if(hp.getBreakLine()&&paragraph) toHtml.append("<BR>\n");   
        /*if (paragraph)
        {
            //closeElem(paragraph,hp);
        }
        else
        {
        System.out.println("tryClose");
        buff=new HtmlProperties(hp);
        //buffparagraph=paragraph;
        //closeElem(paragraph,hp);
        }*/
    }
    
    private void openElem(boolean paragraph,HtmlProperties hp){
//        if (hp.word) hp.setColor(null);
        if (hp.color!=null) toHtml.append("<FONT COLOR=#"+new ColorConverter(hp.color).getHex()+">");
        for(int i=0;i<parent.knownProperties;i++)
            if (hp.getPropertie(i)) toHtml.append(parent.otags[i]);
        if (hp.myImage!=null) {toHtml.append("<!** Image "+hp.myImage+">");return;}
        if (hp.mySpaces!=null) {toHtml.append(hp.mySpaces);return;}
        if (hp.myLink!=null) {toHtml.append(hp.myLink);return;}
    }
    private void closeElem(boolean paragraph,HtmlProperties hp){
        if (hp.myImage!=null) return;
        if (hp.mySpaces!=null) return;
        if (hp.myLink!=null) return;
        for(int i=parent.knownProperties-1;i>=0;i--)
            if (hp.getPropertie(i)) toHtml.append(parent.ctags[i]);
        if(hp.getBreakLine()&&paragraph) toHtml.append("<BR>\n");   
        if (hp.color!=null) toHtml.append("</FONT>");
    }
/*
    private void substract(HtmlProperties h1,HtmlProperties h2){
            //System.out.println(" "+h1.length+" "+h2.length);
        for (int i=0; i<h1.length;i++)
        {
            if (h1.getPropertie(i)==h2.getPropertie(i))
            {
                h1.setPropertie(i,false);
                h2.setPropertie(i,false);
                //System.out.print(i);
            }
        }
        if ((h1.color!=null)&&(h2.color!=null))
            if (h1.color.equals(h2.color))
            {
                h1.color=null;
                h2.color=null;
            }
    }
  */  
    private HtmlProperties difference(HtmlProperties big,HtmlProperties small){
        HtmlProperties temp=new HtmlProperties(big);
        // ezeket kell majd bezarni.
        // ami benne van a regiben, de nincs benne az ujban azt kell visszaadni
        //ezert a regiekbol kivesszuk azokat, amikaz ujban bennevannak.
        for(int i=0;i<big.length;i++)
           if (small.getPropertie(i)) temp.setPropertie(i,false);
        if (small.word) temp.word=false;
        //if (small.br) temp.br=false;
        //if (small.color==null) ugy kell hagyni
        if (small.color!=null) 
        {
            if (big.color!=null)
                if (big.color.equals(small.color)) temp.color=null;
        }
                
            //temp.color=null;
        //if (small.myImage!=null) temp.myImage=null;
        return temp;    
    }
    /*
    private HtmlProperties sum(HtmlProperties old,HtmlProperties newer){
        HtmlProperties temp=new HtmlProperties(old.length);
        // ezeket kell majd bezarni.
        // ami benne van a regiben, de nincs benne az ujban azt kell visszaadni
        //ezert a regiekbol kivesszuk azokat, amikaz ujban bennevannak.
        for(int i=0;i<old.length;i++)
           if (newer.getPropertie(i)||old.getPropertie(i)) temp.setPropertie(i,true);
        return temp;    
    }*/
    
    private HtmlProperties substr(HtmlProperties big,HtmlProperties small){
        HtmlProperties temp=new HtmlProperties(big);
        for(int i=0;i<big.length;i++)
           if (small.getPropertie(i)) temp.setPropertie(i,false);
        if (small.word) temp.word=false;
        //if (small.br) temp.br=false;
        if (small.color!=null) temp.color=null;
        //if (small.myImage!=null) temp.myImage=null;
        return temp;    
    }
    private HtmlProperties addition(HtmlProperties big,HtmlProperties small){
        HtmlProperties temp=new HtmlProperties(big);
        for(int i=0;i<big.length;i++)
           if (small.getPropertie(i)) temp.setPropertie(i,true);
        if (small.word) temp.word=true;   
        //if (small.br) temp.br=true;
        if (small.color!=null) temp.color=new Color(small.color.getRGB());
        //if (small.myImage!=null) temp.myImage=new String(small.myImage);
        return temp;    
    }
    
    
    
    private String breaked(String s,HtmlProperties hp){
        //return s;
        StringBuffer sb=new StringBuffer("");
        for(int i=0;i<s.length();i++)
        {
            if (s.charAt(i)=='\n') /*sb.append("<br>\n");*/hp.setBreakLine();
                else 
                {
                if (s.charAt(i)=='<') sb.append("&lt;");
                    else 
                    if (s.charAt(i)=='>') sb.append("&gt;");
                        else 
                        
                        if (s.charAt(i)=='&') sb.append("&amp;");
                        else
                            if (s.charAt(i)==(char)9) sb.append("<DD>");
                            else sb.append(s.charAt(i));
                }
        }
            
        //if (s.charAt(s.length()-1)=='\n') hp.setBreakLine();
        //System.out.println(new String(sb));
        return new String(sb);
    }

    
    
    void recurseElement(Element e){
        int i=0;
        while(e.getElement(i)!=null){
            Element my=e.getElement(i);
            if (!my.isLeaf())
            {
                ///      Paragraph
                HtmlProperties hp=new HtmlProperties(parent.knownProperties);
                String s=analyzeElement(my,hp);
                //System.out.print("<para>");
                tryOpenElem(true,hp);
                brline=false;
//                System.out.println("<C>"+center+"<R>"+right);
                recurseElement(my);
                //System.out.println("</para>");
                if (brline) hp.setBreakLine();
                tryCloseElem(true,hp);
//                System.out.println("nana");
            }
            else
            {
                ///         Leaf element
                HtmlProperties hp=new HtmlProperties(parent.knownProperties);
                String s=analyzeElement(my,hp);
                //System.out.print(s);
                tryOpenElem(false,hp);
                //System.out.print(elem);
                toHtml.append(htmlUnicoded(breaked(elem,hp)));
                tryCloseElem(false,hp);
                if (hp.getBreakLine()) brline=true;
            }
            i++;
        }
    }

//********************************************************************************
//********************************************************************************
//****                       analyze Element                                  ****
//********************************************************************************
//********************************************************************************
    String analyzeElement(Element e,HtmlProperties htmlp){
        //System.out.println("Ae");
		AttributeSet   as = e.getAttributes().copyAttributes();
		String         asString;
		boolean isComponent=false;
		if(as != null) {		    
		    StringBuffer       retBuffer = new StringBuffer("[");
		    Enumeration        names = as.getAttributeNames();
		    while(names.hasMoreElements()) {
		        Object        nextName = names.nextElement();
		        if(nextName != StyleConstants.ResolveAttribute) {	
		            String prop=nextName.toString();
		            String value=as.getAttribute(nextName).toString();
		            if (e.isLeaf())
		            {
		            if (tr(value)) htmlp.setPropertie(order(prop),true);
		            if (prop.equals("name")) {htmlp.setPropertie(order(prop+value),true);htmlp.setWord(true);}
		            if (prop.equals("size")) htmlp.setPropertie(order(prop+value),true);
		            if (prop.equals("foreground")) htmlp.setColor((Color)as.getAttribute(nextName));
		            if (prop.equals("component"))
		                {
		                    if (parseVisible)
		                    {
		                        /*
		                    int position=parent.imageList.indexOf(nextName);
		                    if (position!=-1) parent.setImageVisible(position);
		                    */
		                        //System.out.println("parsol");
		                    if (as.getAttribute(nextName) instanceof LinkControl) {
		                        //System.out.println("lk"+((LinkControl)as.getAttribute(nextName)).index+(LinkControl)as.getAttribute(nextName));
		                        mylink=(LinkControl)as.getAttribute(nextName);
		                        int idd=mylink.index;
		                        if (mylink.location==LinkControl.BEGIN)
		                        {
		                            //System.out.println();
		                            parent.visibleBLinks[idd]=true;
		                            parent.idxBLinks[idd]=e.getStartOffset();
		                            parent.LinkControlListB[idd]=mylink;
		                            //System.out.println("parse" +idd+mylink);
		                        }
		                        if (mylink.location==LinkControl.END)
		                        {
		                            parent.visibleELinks[idd]=true;
		                            parent.idxELinks[idd]=e.getStartOffset();
		                            parent.LinkControlListE[idd]=mylink;
		                        }
		                            
		                    }
		                    }
		                    
		                    isComponent=true;
		                    if (as.getAttribute(nextName) instanceof ImageControl) htmlp.setImage(value);
		                    if (as.getAttribute(nextName) instanceof SpaceControl) htmlp.setSpaces(value);
		                    if (as.getAttribute(nextName) instanceof LinkControl) htmlp.setLink(value);
		                }
		            }
		            else
		            {
		            if (prop.equals("Alignment")) htmlp.setPropertie(order(prop+value),true);
		            }
		            /*retBuffer.append(" ");		
		            retBuffer.append(prop);	
		            retBuffer.append("=");		
		            retBuffer.append(value);	*/
		            }		 
		        }		   
		    retBuffer.append(" ]");		  
		    asString = retBuffer.toString();
		    // word decolorizer;
		    if (htmlp.word) htmlp.setColor(null);
		  }		
		  else
		  asString = "[ ]";
		  //System.out.print("<"+asString+">");
		  if (e.isLeaf())
		  {
		  try{
		    if (isComponent) elem="";
		    else
            elem=new String(doc.getText(e.getStartOffset(),e.getEndOffset()-e.getStartOffset()));  
		  }
		  catch(Exception ex){System.out.println(ex);}
		  }
            return asString;
    }
    
    public String htmlUnicoded(String s){
        StringBuffer sb=new StringBuffer("");
        for(int i=0;i<s.length();i++)
        {
            int c=s.charAt(i);
            if (c<256) {sb.append(s.charAt(i));}
                else
                {
                    sb.append("&#"+c+";");
                }
        }
        return sb.toString();
    }

}