/*
 * @(#)ImageCanvas.java
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
     * ImageCanvas represents the colorscale from the FontColorFrame frame.
     * It forwards the mouse actions (Click, Drag) to the colorchooser.
     */

import java.awt.*;
import java.awt.event.*;
import java.net.*;
//import java.applet.*;

class ImageCanvas extends Canvas implements MouseListener,MouseMotionListener{
    Image i;
    int width,height;
    FontColorFrame parent;
    public ImageCanvas(URL u,int w,int h,Campfire p,FontColorFrame pa){
        i=p.getImage(u);
        width=w;
        height=h;
        setSize(width,height);
        parent=pa;
        addMouseListener(this);
        addMouseMotionListener(this);
    }
    
    public void paint(Graphics g){
        if (i!=null) g.drawImage(i,0,0,this);
        g.setColor(Color.black);
        g.drawRect(0,0,width-1,height-1);
    }
    
    public void mouseClicked(MouseEvent e){}
    public void mousePressed(MouseEvent e){
        //System.out.println("dfdds");
        parent.click(e.getX(),e.getY());
        }
    public void mouseReleased(MouseEvent e){}
    public void mouseEntered(MouseEvent e){}
    public void mouseExited(MouseEvent e){}
    public void mouseDragged(MouseEvent e){
        if (new Rectangle(0,0,width,height).contains(e.getX(),e.getY()))
        mousePressed(e);
        }
    public void mouseMoved(MouseEvent e){}
}