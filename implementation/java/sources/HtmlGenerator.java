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
     * for links, images, keywords...)
     * Because the Document tree is behaving strange in some cases (ex: if you enter the 
     * word "go", you set the color of the letter "g" to red, than you set the color of
     * the letter "o" to red again, the document tree will contain something like:
     * <FONT COLOR=red>g</FONT><FONT color=red>o</FONT>, which is redundant),
     * there is an HtmlProperties helper class, which behaves as a buffer for the possible
     * properties. The main idea is that when you generate the resulting html, the redundant
     * closing and opening tags for the same propertie will eliminate each other.
     */
import javax.swing.text.*;
import javax.swing.event.*;
import java.awt.*;
import java.util.*;
import java.lang.reflect.Array;

class HtmlGenerator{
    
    private StyledDocument doc;
    private Campfire parent;
    private boolean brline;
    private String elem=null;
    private StringBuffer newHtml;
    private Vector tagList= new Vector();
    private int inserted=0;
    private int docLength=0;
    
    public HtmlGenerator(StyledDocument d,Campfire p,boolean parse){
        doc=d;
        newHtml=new StringBuffer();
        parent=p;
    }
    
    public StringBuffer generate(){
        
        Element[] el=doc.getRootElements();
        try{
            String sDoc= new String(doc.getText(0, doc.getLength()));
            newHtml= new StringBuffer(sDoc);
            docLength= doc.getLength();
        }catch (BadLocationException e){parent.showError(e.toString());}
        
        for(int i=0;i<el.length;i++){
    		recurseElement(el[i]);
    	}

        //parent.debug(newHtml.toString());
        //parent.debug(Integer.toString(docLength));
        placeTags();

//        if (parent.isJustified.getState()) {
//            newHtml.insert(0, "<DIV ALIGN=JUSTIFY>");
//            newHtml.append("</DIV>");
//        }

//        newHtml= toUnicode(breaked(newHtml.toString()));
        newHtml= new StringBuffer(breaked(newHtml.toString()));
        //parent.debug(newHtml.toString());
        
        return newHtml;
            
    }

    private void addTag(int i, String t, String o, String s){
        String transTag= new String(s);
        
        transTag=replacer(transTag, "<", "@l@", true);
        transTag=replacer(transTag, ">", "@r@", true);
        transTag=replacer(transTag, "&", "@a@", true);
        inserted++;
        tagList.addElement( new CampTag(i, t, o, transTag, inserted));
    }


    private void recurseElement(Element e){
        int i=0;
        while(e.getElement(i)!=null){
            Element my=e.getElement(i);
            if (!my.isLeaf()){
                getTags(my);
                recurseElement(my);
            }else{
                ///         Leaf element
                getTags(my);
            }
            i++;
        }
    }

    private void getTags(Element e){
        
		AttributeSet   as = e.getAttributes().copyAttributes();
		String         asString;

		if(as != null) {		    
            int i=0;
            Integer myId= new Integer(0);
            String myTag=new String();

            if (as.containsAttribute(StyleConstants.NameAttribute, "Keyword")){
              myId= (Integer) as.getAttribute("ID");
              myTag=CampBroker.getKeyword().getFirstTag(myId);
              i= e.getStartOffset();
              addTag(i, "Keyword", "O", myTag);
              i= e.getEndOffset();
              addTag(i, "Keyword", "C", "<!** EndClass>");

            }else if (as.containsAttribute(StyleConstants.NameAttribute, "ExternalLink")){
              myId= (Integer) as.getAttribute("ID");
              myTag=CampBroker.getExternalLink().getFirstTag(myId);
              i= e.getStartOffset();
              addTag(i, "ExternalLink", "O", myTag);
              i= e.getEndOffset();
              addTag(i, "ExternalLink", "C", "<!** EndLink>");

            }else if (as.containsAttribute(StyleConstants.NameAttribute, "InternalLink")){
              myId= (Integer) as.getAttribute("ID");
              myTag=CampBroker.getInternalLink().getFirstTag(myId);
              i= e.getStartOffset();
              addTag(i, "InternalLink", "O", myTag);
              i= e.getEndOffset();
              addTag(i, "InternalLink", "C", "<!** EndLink>");

            //}else if (as.containsAttribute(StyleConstants.NameAttribute, "AudioLink")){
            //  myId= (Integer) as.getAttribute("ID");
            //  myTag=CampBroker.getAudioLink().getFirstTag(myId);
            //  i= e.getStartOffset();
            //  addTag(i, "AudioLink", "O", myTag);
            //  i= e.getEndOffset();
            //  addTag(i, "AudioLink", "C", "<!** EndLink>");

            //}else if (as.containsAttribute(StyleConstants.NameAttribute, "VideoLink")){
            //  myId= (Integer) as.getAttribute("ID");
            //  myTag=CampBroker.getVideoLink().getFirstTag(myId);
            //  i= e.getStartOffset();
            //  addTag(i, "VideoLink", "O", myTag);
            //  i= e.getEndOffset();
            //  addTag(i, "VideoLink", "C", "<!** EndLink>");

            }else if (as.containsAttribute(StyleConstants.NameAttribute, "Subhead")){
              i= e.getStartOffset();
              addTag(i, "Subhead", "O", "<!** Title>");
              i= e.getEndOffset();
              addTag(i, "Subhead", "C", "<!** EndTitle>");
            }else {

    		    Enumeration names = as.getAttributeNames();
    		    while(names.hasMoreElements()) {
    		        Object nextName = names.nextElement();
    		        if(nextName != StyleConstants.ResolveAttribute) {	
    		            String prop=nextName.toString();
    		            String value=as.getAttribute(nextName).toString();
    		            if (e.isLeaf()) {
                            		              
    //    		            if (tr(value)) htmlp.setProperty(order(prop),true);
                            if (prop.equals("component")){
    		                    if (as.getAttribute(nextName) instanceof ImageControl){
            		              i= e.getStartOffset();
            		              addTag(i, "Image", "N", value);
                                //}else if (as.getAttribute(nextName) instanceof CampAddOnControl){
            		            //  i= e.getStartOffset();
            		            //  addTag(i, "AddOn", "N", value);
            		            }
        		            }else if (prop.equals("size")){
                                String fontsize= new String();
                                
                                if (value.equals("8")) fontsize="1";
                                else if (value.equals("10")) fontsize="2";
                                else if (value.equals("12")) fontsize="3";
                                else if (value.equals("14")) fontsize="4";
                                else if (value.equals("18")) fontsize="5";
                                else if (value.equals("24")) fontsize="6";
                                else if (value.equals("36")) fontsize="7";
                                
                                myTag= "<FONT SIZE="+fontsize+">";
                                i= e.getStartOffset();
                                addTag(i, "FontSize", "O", myTag);
                                i= e.getEndOffset();
                                addTag(i, "FontSize", "C", "</FONT>");
        		            }else if (prop.equals("foreground")){
                                Color myColor= (Color)as.getAttribute(nextName);
                                myTag= "<FONT COLOR=#"+new ColorConverter(myColor).getHex()+">";
                                i= e.getStartOffset();
                                addTag(i, "FontColor", "O", myTag);
                                i= e.getEndOffset();
                                addTag(i, "FontColor", "C", "</FONT>");
        		            }else if (prop.equals("bold")){
                                i= e.getStartOffset();
                                addTag(i, "FontBold", "O", "<B>");
                                i= e.getEndOffset();
                                addTag(i, "FontBold", "C", "</B>");
        		            }else if (prop.equals("italic")){
                                i= e.getStartOffset();
                                addTag(i, "FontItalic", "O", "<I>");
                                i= e.getEndOffset();
                                addTag(i, "FontItalic", "C", "</I>");
        		            }else if (prop.equals("underline")){
                                i= e.getStartOffset();
                                addTag(i, "FontUnderline", "O", "<U>");
                                i= e.getEndOffset();
                                addTag(i, "FontUnderline", "C", "</U>");
        		            }
        		        }else{
            		        if (prop.equals("Alignment")){
                                if (value.equals("1")){
                                    i= e.getStartOffset();
                                    addTag(i, "FontCenter", "O", "<CENTER>");
                                    i= e.getEndOffset();
                                    addTag(i, "FontCenter", "C", "</CENTER>");
                                }else if(value.equals("2")){
                                    i= e.getStartOffset();
                                    addTag(i, "FontRight", "O", "<DIV ALIGN=RIGHT>");
                                    i= e.getEndOffset();
                                    addTag(i, "FontRight", "C", "</DIV>");
                                }
            		        }
        		        }
    		        }
    		    }
    		    }		   
		  }		

    }

    private void placeTags(){
        CampTag myTag;
        sortTags();
        removeDuplicates();
                
        for (int i=tagList.size()-1;i>=0;i--){
            myTag= (CampTag)tagList.get(i);
            int mypos=myTag.tagPosition.intValue();
            if (mypos>docLength) mypos=docLength;
            if (myTag.tagType.equals("Image")){
                newHtml.replace(mypos, mypos+1, myTag.tagText);
            }else{
                newHtml.insert(mypos, myTag.tagText);
            }
            //parent.debug(Integer.toString(mypos) + " " +myTag.tagText);
        }

    }

    private void sortTags(){
        CampTag myTags[]=new CampTag[tagList.size()];
        for (int i=0;i<tagList.size();i++){
            myTags[i]=(CampTag)tagList.get(i);
        }

        Arrays.sort(myTags);
        
        tagList= new Vector();
        for (int i=0;i<myTags.length;i++){
            tagList.addElement(myTags[i]);
        }
        
        
    }

    private void removeDuplicates(){

        CampTag myTag;
        boolean go;

        //--- remove the same tags
        for (int i=0; i<tagList.size();i++){
            myTag= (CampTag)tagList.get(i);
            int j= i+1;
            go=true;
            while (j<tagList.size() &&  go){
                CampTag secTag= (CampTag)tagList.get(j);
                if (myTag.tagPosition.equals(secTag.tagPosition)&&myTag.tagType.equals(secTag.tagType)){
                    if (!myTag.tagOrder.equals(secTag.tagOrder)){
                        tagList.remove(j);
                        tagList.remove(i);
                        i--;
                        go=false;
                    }
                }
                j++;
            }
        }

        //--- allow links to be bold, italic, underlined
        for (int i=0; i<tagList.size();i++){
            myTag= (CampTag)tagList.get(i);
            if (myTag.tagType.endsWith("Link")){
                if (i>0&& myTag.tagOrder.equals("O")){
                    CampTag mySecTag= (CampTag)tagList.get(i+1);
                    int j= i+2;
                    if (j<tagList.size()){
                        CampTag firstTag= (CampTag)tagList.get(i-1);
                        CampTag thirdTag= (CampTag)tagList.get(j);
                        if (firstTag.tagType.startsWith("Font")){
                            if (firstTag.tagPosition.equals(myTag.tagPosition)&&thirdTag.tagPosition.equals(mySecTag.tagPosition)){
                                if (firstTag.tagType.equals(thirdTag.tagType)&&firstTag.tagOrder.equals("C")&&thirdTag.tagOrder.equals("O")){
                                    tagList.remove(j);
                                    tagList.remove(i-1);
                                    i=0;
                                }
                            }
                        }
                    }
                }
            }
        }


    }


    private String breaked(String s){

        StringBuffer sb=new StringBuffer("");
        for(int i=0;i<s.length();i++){
            if (s.charAt(i)=='\n') sb.append("<BR>\n");
            else if (s.charAt(i)=='<') sb.append("&lt;");
            else if (s.charAt(i)=='>') sb.append("&gt;");
            else if (s.charAt(i)=='&') sb.append("&amp;");
            else if (s.charAt(i)==(char)9) sb.append("<DD>");
            else sb.append(s.charAt(i));
        }
        String trans= new String();    
        trans= sb.toString();
        trans=replacer(trans, "@l@", "<", true);
        trans=replacer(trans, "@r@", ">", true);
        trans=replacer(trans, "@a@", "&", true);
        
        return trans;
    }

    
	private String replacer(String big,String small,String with,boolean ignoreCase){
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
	
    private StringBuffer toUnicode(String s){
	   byte myBytes[];
	   StringBuffer sb=new StringBuffer("");
	   try{
	       myBytes= s.getBytes();
    	   sb= new StringBuffer(new String (myBytes, "UTF-8"));
	   }catch(Exception e){}

        return sb;
    }
	
	
    
}

class CampTag implements Comparable{
    String tagText;
    String tagType;
    String tagOrder;
    Integer tagPosition;
    int ins=0;
    
    public CampTag(int i, String t, String o, String s, int r){
        tagPosition= new Integer(i);
        tagType= new String(t);
        tagOrder= new String(o);
        tagText= new String(s);
        ins=r;
    }
    
    public int compareTo(Object o){
        CampTag secTag;
        int i=0;
        secTag= (CampTag)o;
        
        if (secTag.tagPosition.intValue()>this.tagPosition.intValue()) i=-1;
        else if (secTag.tagPosition.intValue()==this.tagPosition.intValue()){
            if (secTag.tagOrder.equals("C") && this.tagOrder.equals("C")){
                if (secTag.ins>this.ins) i=1;
                else if (secTag.ins<this.ins) i=-1;
                else i=0;
            }else {
                i=0;
            }
        }
        else if (secTag.tagPosition.intValue()<this.tagPosition.intValue()) i=1;
        
        return i;
    }
}



