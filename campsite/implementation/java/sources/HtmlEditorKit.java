/*
 * @(#)HtmlEditorKit.java
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
     * HtmlEditorKit extends StyledEditorKit and introduces some new behaviours, as
     * the existence of dictionary words, that are represented using another font and
     * color, and are present in the html as special links.
     */


import javax.swing.text.*;
import javax.swing.event.*;
import javax.swing.*;
import java.awt.event.*;
import java.awt.*;

class HtmlEditorKit extends StyledEditorKit{
    private CaretListener caretListener;
    private static HtmlStyleContext context;
    Campfire parent=null;
    Color dictionaryColor;
    Integer objId= new Integer(0);
    String objName= new String();
    
    public HtmlEditorKit(Campfire parent){
        this.parent=parent;
        dictionaryColor=parent.dictColor;
        context=new HtmlStyleContext(this);
        caretListener=new Listener(parent);
        
        parent.textPane.addMouseListener(new MouseListener(){
            public void mouseClicked(MouseEvent e){
                if (e.getClickCount()==2 && SwingUtilities.isLeftMouseButton(e))
                    editProperties();
            }
            public void mousePressed(MouseEvent e){}
            public void mouseReleased(MouseEvent e){}
            public void mouseEntered(MouseEvent e){}
            public void mouseExited(MouseEvent e){}
        }
        );
        
    }
    
    public void setObjName( String s){
        objName= s;
    }

    public void setObjId( Integer n){
        objId= n;
    }

    private void editProperties(){
        parent.textPane.setCursor(Cursor.getPredefinedCursor(Cursor.WAIT_CURSOR));

        if (objName.equalsIgnoreCase("ExternalLink"))
            CampBroker.getExternalLink().edit(objId);
        else if (objName.equalsIgnoreCase("Keyword"))
            CampBroker.getKeyword().edit(objId);
        else if (objName.equalsIgnoreCase("InternalLink"))
            CampBroker.getInternalLink().edit(objId);
        //else if (objName.equalsIgnoreCase("AudioLink"))
        //    CampBroker.getAudioLink().edit(objId);
        //else if (objName.equalsIgnoreCase("VideoLink"))
        //    CampBroker.getVideoLink().edit(objId);
        
        parent.textPane.setCursor(Cursor.getPredefinedCursor(Cursor.TEXT_CURSOR));
    }
 
    public void createPresentation(String s, Integer n){
 
        Style myStyle;
        
        myStyle= context.getStyle(s);
        myStyle.addAttribute("ID", n);
        Action a=new CharStyleAction(s, myStyle);
        if (a!=null){
            a.actionPerformed(new ActionEvent(parent.textPane,1,""));
            parent.setModified();
        }
    }
    
    public void createPresentation(String s){
 
        Style myStyle;
        
        myStyle= context.getStyle(s);
        Action a=new CharStyleAction(s, myStyle);
        if (a!=null){
            a.actionPerformed(new ActionEvent(parent.textPane,1,""));
            //parent.textPane.revalidate();
            parent.setModified();
        }
    }
    

    public void clearMyAttributes(){
 
        Style myStyle;
        
        myStyle= context.getStyle(context.DEFAULT_STYLE);
        Action a=new CharStyleAction(context.DEFAULT_STYLE, myStyle);
        if (a!=null){
            a.actionPerformed(new ActionEvent(parent.textPane,1,""));
            parent.setModified();
        }
    }

    
    public void install(JTextPane tp){
        tp.addCaretListener(caretListener);
    }
    
    public void deinstall(JTextPane tp){
        tp.removeCaretListener(caretListener);
    }
    
    static class Listener implements CaretListener{
        Campfire parent;
        Integer objId= new Integer(0);
        String objName= new String();
        
        Listener(Campfire parent){
            this.parent=parent;
        }
        
        public String getName(){
            return objName;
        }
        
        public void caretUpdate(CaretEvent e){
            int dot=e.getDot(),mark=e.getMark();
            parent.updateDots(dot,mark);
            
            if(dot==mark){
                JTextComponent c=(JTextComponent) e.getSource();
                StyledDocument document=(StyledDocument) c.getDocument();
                Element elem=document.getCharacterElement(dot);
                AttributeSet set=elem.getAttributes();
                String objName=(String) set.getAttribute(StyleConstants.NameAttribute);
                parent.htmleditorkit.setObjName(objName);
                
                if (objName.equalsIgnoreCase("Keyword")){
                     objId= (Integer)set.getAttribute("ID");
                     parent.htmleditorkit.setObjId(objId);
                     parent.showStatus(CampResources.get("Status.KeywordLink"));
                  }
                else if (objName.equalsIgnoreCase("InternalLink")){
                     objId= (Integer)set.getAttribute("ID");
                     parent.htmleditorkit.setObjId(objId);
                     parent.showStatus(CampResources.get("Status.InternalLink"));
                  }
                else if (objName.equalsIgnoreCase("ExternalLink")){
                     objId= (Integer)set.getAttribute("ID");
                     parent.htmleditorkit.setObjId(objId);
                     parent.showStatus(CampResources.get("Status.ExternalLink"));
                  }
                //else if (objName.equalsIgnoreCase("AudioLink")){
                //     objId= (Integer)set.getAttribute("ID");
                //     parent.htmleditorkit.setObjId(objId);
                //     parent.showStatus("Audio link");
                //  }
                //else if (objName.equalsIgnoreCase("VideoLink")){
                //     objId= (Integer)set.getAttribute("ID");
                //     parent.htmleditorkit.setObjId(objId);
                //     parent.showStatus("Video link");
                //  }
                else if (objName.equalsIgnoreCase("Subhead")){
//                     objId= (Integer)set.getAttribute("ID");
                     parent.showStatus(CampResources.get("Status.Subhead"));
                  }
                else parent.showStatus(CampResources.get("Status.Ready"));

            }
            
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


class HtmlStyleContext extends StyleContext{
    HtmlEditorKit parent;
   
        
    public HtmlStyleContext(HtmlEditorKit parent){
        Style root=getStyle(DEFAULT_STYLE);
        this.parent=parent;

        Style s=addStyle(new String("Keyword"),root);
        StyleConstants.setForeground(s,Color.red.darker());
        StyleConstants.setBold(s,true);
        StyleConstants.setUnderline(s,true);

        s=addStyle(new String("ExternalLink"),root);
        StyleConstants.setForeground(s,Color.blue.darker());
        StyleConstants.setBold(s,true);
        StyleConstants.setUnderline(s,true);

        s=addStyle(new String("InternalLink"),root);
        StyleConstants.setForeground(s,Color.green.darker().darker().darker());
        StyleConstants.setBold(s,true);
        StyleConstants.setUnderline(s,true);

        //s=addStyle(new String("AudioLink"),root);
        //StyleConstants.setForeground(s,Color.green.darker().darker().darker());
        //StyleConstants.setBold(s,true);
        //StyleConstants.setUnderline(s,true);

        //s=addStyle(new String("VideoLink"),root);
        //StyleConstants.setForeground(s,Color.green.darker().darker().darker());
        //StyleConstants.setBold(s,true);
        //StyleConstants.setUnderline(s,true);

        s=addStyle(new String("Subhead"),root);
        StyleConstants.setForeground(s,Color.red);
//        StyleConstants.setFontFamily(s,"Courier");
        StyleConstants.setItalic(s,true);
        StyleConstants.setBold(s,true);

        //s=addStyle(new String("Image"),root);
        //StyleConstants.setAlignment(s, StyleConstants.ALIGN_JUSTIFIED);
        //StyleConstants.setIcon(s,new CampToolbarIcon(CampConstants.TB_ICON_IMAGE,parent.parent));
        //StyleConstants.setComponent(s,new ImageControl(new CampToolbarIcon(CampConstants.TB_ICON_IMAGE,parent.parent)));

    }
}
