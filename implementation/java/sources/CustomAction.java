/*
 * @(#)CustomAction.java
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
     * CustomAction extends StyledEditorKit.StyledTextAction, and is a class for the actions 
     * bounded to menu points, or to the buttons on the toolbar.
     * It will either set some CharacterAttributes/ParagraphAttributes,
     * or it will execute a method from the main class.
     */

import javax.swing.*;
import javax.swing.text.*;
import java.awt.event.*;
import java.awt.*;
import java.applet.*;
import java.net.*;

class CustomAction extends StyledEditorKit.StyledTextAction{
//    final static int FORM=0;
    final static int PREVIEW=1;
    final static int DUMP=2;
    final static int IMAGE=3;
    final static int NEWFILE=4;
    final static int UPLOAD=6;
    final static int CLIP=7;
    final static int COLOR=8;
    final static int CLEAR=9;
    final static int SETHTML=10;
    final static int BOLD=11;
    final static int ITALIC=12;
    final static int UNDERLINE=13;
    final static int CENTER=14;
    final static int RIGHT=15;
    final static int SUBHEAD=16;
    final static int EXTLINK=17;
    final static int WORD=18;
    final static int INTLINK=19;
    final static int RE=20;
    final static int EXIT=21;
    final static int HELP=22;
    final static int ABOUT=23;
    final static int VIDEO=24;
    final static int AUDIO=25;
    final static int FLASH=26;
    final static int JAPPLET=27;
    final static int CHTML=28;
    final static int TABLE=29;
    final static int ADDON=30;
    final static int UPIMAGE=31;
    final static int UPVIDEO=32;
    final static int UPAUDIO=33;
    final static int UPFLASH=34;
    final static int UPKEYWORD=35;
    final static int BUGS=36;
    final static int HOMEPAGE=37;
    final static int CERTIF=38;
    private int source;
    private Campfire parent;

    public CustomAction(String action, int src,Campfire p){
        super(action);
        source=src;
        parent=p;
    }
    
    public void actionPerformed(ActionEvent e){
        
        /*if(source==FORM)
            parent.iform.setVisible(true);*/
        if(source==PREVIEW)
            parent.preview();
        else if(source==DUMP)
            parent.dump();
        else if(source==IMAGE)
            CampBroker.getImage().insert();
        //if(source==TABLE)
            //CampBroker.getTable().insert();
//        else if(source==ADDON)
//            AddOnBroker.chooseAddOn();
        else if(source==NEWFILE)
            parent.newFile(true);
        else if(source==EXTLINK)
            CampBroker.getExternalLink().create();
        else if(source==INTLINK)
            CampBroker.getInternalLink().create();
//        else if(source==AUDIO)
//            CampBroker.getAudioLink().create();
//        else if(source==VIDEO)
//            CampBroker.getVideoLink().create();
        else if(source==UPLOAD)
            parent.upload();
        else if(source==COLOR)
            CampBroker.getFont().setColor();
        else if(source==CLEAR){
            CampBroker.getFont().clear();
            parent.htmleditorkit.clearMyAttributes();
        }
        else if(source==SETHTML)
            parent.setHtml();
        else if(source==SUBHEAD)
            CampBroker.getSubhead().create();
        else if(source==WORD)
            CampBroker.getKeyword().create();
        else if(source==RE)
            parent.regen();
        else if (source==BOLD){
            AttributeSet attr=parent.textPane.getCharacterAttributes();
            MutableAttributeSet ats=new SimpleAttributeSet();
            StyleConstants.setBold(ats,true);
            setCharacterAttributes(parent.textPane,ats,false);
            parent.setModified();
        }else if (source==ITALIC){
            AttributeSet attr=parent.textPane.getCharacterAttributes();
            MutableAttributeSet ats=new SimpleAttributeSet();
            StyleConstants.setItalic(ats,true);
            setCharacterAttributes(parent.textPane,ats,false);
            //false=append, not owerwrite
            parent.setModified();
        }else if (source==UNDERLINE){
            AttributeSet attr=parent.textPane.getCharacterAttributes();
            MutableAttributeSet ats=new SimpleAttributeSet();
            StyleConstants.setUnderline(ats,true);
            setCharacterAttributes(parent.textPane,ats,false);
            parent.setModified();
        }else if (source==CENTER){
            AttributeSet attr=parent.textPane.getCharacterAttributes();
            MutableAttributeSet ats=new SimpleAttributeSet();
            StyleConstants.setAlignment(ats,StyleConstants.ALIGN_CENTER);
            setParagraphAttributes(parent.textPane,ats,false);
            parent.setModified();
        }else if (source==RIGHT){
            AttributeSet attr=parent.textPane.getCharacterAttributes();
            MutableAttributeSet ats=new SimpleAttributeSet();
            StyleConstants.setAlignment(ats,StyleConstants.ALIGN_RIGHT);
            setParagraphAttributes(parent.textPane,ats,false);
            parent.setModified();
        }else if(source==HELP){
            URL userUrl;
            try{
                userUrl = new URL(CampConstants.URL_HELP); 
                parent.getAppletContext().showDocument(userUrl,"_blank");             
            } catch (Exception exc){
                System.out.println("Not valid URL");
            }
        }else if(source==BUGS){
            URL userUrl;
            try{
                userUrl = new URL(CampConstants.URL_BUGS); 
                parent.getAppletContext().showDocument(userUrl,"_blank");             
            } catch (Exception exc){
                System.out.println("Not valid URL");
            }
        }else if(source==HOMEPAGE){
            URL userUrl;
            try{
                userUrl = new URL(CampConstants.URL_HOMEPAGE); 
                parent.getAppletContext().showDocument(userUrl,"_blank");             
            } catch (Exception exc){
                System.out.println("Not valid URL");
            }
        }else if(source==CERTIF){
            URL userUrl;
            try{
                userUrl = new URL(parent.getCodeBase(),CampConstants.CERTIF_PATH); 
                parent.getAppletContext().showDocument(userUrl,"_blank");             
            } catch (Exception exc){
                System.out.println("Not valid URL");
            }
        }else if(source==ABOUT){
            parent.about();
        }else if(source==EXIT){
            parent.exitapp();
        }
    }
}