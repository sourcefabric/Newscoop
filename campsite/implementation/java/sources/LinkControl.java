/*
 * @(#)LinkControl.java
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
     * LinkControl is the class that represent a link in the textarea.
     * It also stores the relevant data of a link
     * The links can be:
     * External Link: has a url, a target window or a frame name
     * Internal Link: has a list of IDs for "Language","Publication","Issue","Section","Article"
     * A link is represented in the textarea as two small rectangles: the beginning(opening)
     * and the ending(closing) one. 
     * At "serialization" (toString), only the beginning one will output the
     * related data, the ending one will close the link tag.
     * When a link is updated by clicking one of the rectangles, the other is updated
     * through the variable pair.
     *
     * Limitation:
     *    When deleting a link, you delete one of the two rectangles, and the other is deleted 
     *    by the applet. But in the Undo history of the textarea the programatically 
     *    deleted components are not registered. So, when you make an undo, only the 
     *    half of the link will appear.
     */

import com.sun.java.swing.*;
import java.awt.*;
import java.awt.event.*;
import java.util.*;

class LinkControl extends JPanel{
    
    Test parent;
    int kind,location;
    int index=-1;
    String ids[];
    //Vector idV[];
    //Vector nameV[];
    String target="unspecified";
    String frame="";
    
    final static int NULL=0;
    final static int INT=1;
    final static int EXT=2;
    final static int END=3;
    final static int BEGIN=4;
    Color externalBeginColor,externalEndColor,internalEndColor,internalBeginColor,voidColor;
    LinkControl pair;
    public String myurl;
    
    public LinkControl(Test p,int k,int l,int idx){
        myurl=new String("");
        ids=new String[5];
        setPreferredSize(new Dimension(10,10));
        setBorder(BorderFactory.createEtchedBorder());    
        parent=p;
        kind=k;
        location=l;
        index=idx;
        externalBeginColor=new Color(255,0,0);
        externalEndColor=new Color(200,0,0);
        internalBeginColor=new Color(0,0,255);
        internalEndColor=new Color(0,0,200);
        voidColor=new Color(0,255,0);
        setBackground(voidColor);
        
        addMouseListener(new MouseListener(){
            public void mouseClicked(MouseEvent e){
                
                showUrler();
            }
            public void mousePressed(MouseEvent e){}
            public void mouseReleased(MouseEvent e){}
            public void mouseEntered(MouseEvent e){}
            public void mouseExited(MouseEvent e){}
        }
        );
    }
    
    public void setPair(LinkControl pa){
        pair=pa;
    }
    
    public void setUrl(String s,boolean pair){
        myurl=s;
        if ((s==null)||(s.equals(""))) {setBackground(voidColor);}
        else
        {
                if (location==BEGIN) setBackground(externalBeginColor); 
                    else setBackground(externalEndColor); 
                }
        parent.textPane.revalidate();        
        if (pair) this.pair.setUrl(s,false);
    }
    
    public void gotIds(boolean pair){
       if (location==BEGIN) setBackground(internalBeginColor); 
          else setBackground(internalEndColor); 
        parent.textPane.revalidate();        
        if (pair) this.pair.gotIds(false);
    }
    
    public void setIDS(String s){
  //      System.out.println(s);
        for(int i=0;i<5;i++)
        {
            int begin=s.indexOf(InternalLinkFrame.listofIds[i]);
            int eq=s.indexOf("=",begin);
            int amp=s.indexOf("&",eq);
            if (amp==-1) amp=s.length();
            String id=s.substring(eq+1,amp);
            ids[i]=id;
            pair.ids[i]=id;
            gotIds(true);
            //parent.ilframe.links[0].valid=true;
//            System.out.println(id);
        }
    }
    
    
    public String getUrl(){
        return myurl;
    }
    
    private void showUrler(){
    //System.out.println("szai"+kind);
        
        if (kind==EXT)
            parent.showUrler(this);
        if (kind==INT)
            parent.showIntLink(this);
    }
    
    public String toString(){
        if (kind==NULL) return "";
        
        StringBuffer sb=new StringBuffer("");
        sb.append("<!** Link ");
        if (kind==INT)
        {
            sb.append("internal ");
            if (location==BEGIN)
            {
                for(int i=0;i<5;i++)
                {
                    if (i>0)sb.append("&");
                    sb.append(InternalLinkFrame.listofIds[i]);
                    sb.append("=");
                    sb.append(ids[i]);
                }
                sb.append(myTarget());
                sb.append(">");
                return sb.toString();
            }
        }
        if (kind==EXT)
        {
            sb.append("external ");
            if (location==BEGIN)
            {
                sb.append("\""+myurl+"\""+myTarget()+">");
                return sb.toString();
            }
        }
        if (location==END)
        {
            return "<!** EndLink>";
        }
        return "<Link ?>";
        
    }
    
    public void dontUse(){
        setPreferredSize(new Dimension(0,0));
        revalidate();
        kind=NULL;
    }
    
        
    public void setFrame(String s){
        frame=s;
    }
    
    public void setTarget(String s){
        String t="";
        if (s.equals("default")) t="UNSPECIFIED";
        if (s.equals("new window")) t="_blank";
        if (s.equals("full window")) t="_top";
        if (s.equals("frame named")) t=frame;
        target=t;
        //System.out.println("settarget"+s);
    }

    public void setTrueTarget(String s){
	if ((s==null)||(s.equals(""))) target="UNSPECIFIED";
		else target=s;
    }


    public String myTarget(){
        //System.out.println("arget"+target);
        if ((target==null)||(target.toUpperCase().equals("UNSPECIFIED"))||(target.equals(""))) return "";
        else return " "+"TARGET \""+target+"\"";
    }
    
}