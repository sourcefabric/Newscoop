/*
 * @(#)TolHtmlEditorKit.java
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
     * TolHtmlEditorKit extends StyledEditorKit and introduces some new behaviours, as
     * the existence of dictionary words, that are represented using another font and
     * color, and are present in the html as special links.
     */


import com.sun.java.swing.text.*;
import com.sun.java.swing.event.*;
import com.sun.java.swing.*;
import java.awt.event.*;
import java.awt.*;

class TolHtmlEditorKit extends StyledEditorKit{
    private CaretListener caretListener;
    private static TolHtmlStyleContext context;
    Test parent=null;
    Color dictionaryColor;
    
    String tolkey;
    String titleKey;

    public TolHtmlEditorKit(Test parent,String s[],String partolkey){
        this.parent=parent;
        dictionaryColor=parent.dictColor;
        tolkey=partolkey;
        titleKey=new String("#title");
        buildDictionary(s);
        context=new TolHtmlStyleContext(this);
        buildDictionaryActions(s);
        caretListener=new Listener(parent);
    }
    
    
    public  Action[] dictionaryActions=new Action[]{};
    String dictionaryWords[];
    
    public void buildDictionary(String s[]){
        dictionaryWords=new String[s.length+1];
        for(int i=0;i<s.length;i++)
        {
            dictionaryWords[i]=new String(s[i]);
        }
        dictionaryWords[s.length]=titleKey;
    }
    
    
    
    
    public void buildDictionaryActions(String s[]){
        dictionaryActions=new Action[s.length+1];
        for(int i=0;i<s.length+1;i++)
        {
            dictionaryActions[i]=new CharStyleAction(tolkey+dictionaryWords[i],
                context.getStyle(tolkey+dictionaryWords[i]));
        }
    }
    
    public Action returnWordAction(String s){
        Action ret=null;
        for (int i=0;i<parent.nrofDictionaryWords+1;i++)//+1=title
            if (dictionaryWords[i].equals(s)) ret=dictionaryActions[i];
        return ret;
    }
    
    
    public Action[] getActions(){
        return TextAction.augmentList(super.getActions(),dictionaryActions);
    }
    
    public void install(JTextPane tp){
        tp.addCaretListener(caretListener);
    }
    
    public void deinstall(JTextPane tp){
        tp.removeCaretListener(caretListener);
    }
    
    static class Listener implements CaretListener{
        Test parent;
        
        Listener(Test parent){
            this.parent=parent;
        }
        public void caretUpdate(CaretEvent e){
            int dot=e.getDot(),mark=e.getMark();
            parent.updateDots(dot,mark);
            
            if(dot==mark){
                JTextComponent c=(JTextComponent) e.getSource();
                StyledDocument document=(StyledDocument) c.getDocument();
                Element elem=document.getCharacterElement(dot);
                AttributeSet set=elem.getAttributes();
                String name=(String) set.getAttribute(StyleConstants.NameAttribute);
                if (name.charAt(0)!='#') 
                        parent.showStatus(" ");
                        else 
                        {
                            if (name.charAt(1)=='#')
                                parent.showStatus("SubHead");
                                else
                                parent.showStatus("Keyword : "+name.substring(1));
                        }
                parent.showInfo("info label");
//                System.out.println(""+dot+" "+mark+" "+name);
               // parent.clearAttr.setEnabled(false);                
            }
            //else 
              //  parent.clearAttr.setEnabled(true);
        }
    }
    
    static class CharStyleAction extends StyledEditorKit.StyledTextAction{
        private Style style;

        public CharStyleAction(String nm, Style style){
            super(nm);
            this.style=style;
        }
        
        public void actionPerformed(ActionEvent e){
            setCharacterAttributes(getEditor(e),style,false);
        }
    }
}


class TolHtmlStyleContext extends StyleContext{
    TolHtmlEditorKit parent;
   
        
    public TolHtmlStyleContext(TolHtmlEditorKit parent){
        Style root=getStyle(DEFAULT_STYLE);
        this.parent=parent;
        
        for(int i=0;i<parent.dictionaryWords.length;i++){
            String name=new String(parent.tolkey+parent.dictionaryWords[i]);
            Style s=addStyle(name,root);
            //StyleConstants.setUnderline(s,true);
            StyleConstants.setForeground(s,parent.dictionaryColor);
            StyleConstants.setFontFamily(s,"Courier");
            //System.out.println(name);
        }
            String name=new String(parent.tolkey+parent.titleKey);
            Style s=addStyle(name,root);
            //StyleConstants.setUnderline(s,true);
            StyleConstants.setForeground(s,Color.blue);
            StyleConstants.setFontFamily(s,"Courier");
    }
}
