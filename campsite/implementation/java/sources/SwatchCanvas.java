/*
 * @(#)SwatchCanvas.java
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
     * SwatchCanvas paints the swatches , and captures the mouse actions
     */



import java.awt.*;
import java.awt.event.*;
import java.net.*;

class SwatchCanvas extends Canvas implements MouseListener,MouseMotionListener{
    int width,height;
    FontColorFrame parent;

    public SwatchCanvas(int w,int h,Campfire p,FontColorFrame pa){
        width=w;
        height=h;
        setSize(width,height);
        parent=pa;
        addMouseListener(this);
        addMouseMotionListener(this);
    }
    
    public void paint(Graphics g){
    	drawColors(g,8,51,2);
    }
    
    public void mouseClicked(MouseEvent e){}
    public void mousePressed(MouseEvent e){
        parent.clickSwatch(e.getX(),e.getY());
        }
    public void mouseReleased(MouseEvent e){}
    public void mouseEntered(MouseEvent e){}
    public void mouseExited(MouseEvent e){}
    public void mouseDragged(MouseEvent e){
        if (new Rectangle(0,0,width,height).contains(e.getX(),e.getY()))
        mousePressed(e);
        }
    public void mouseMoved(MouseEvent e){}

	public void drawColors(Graphics gr,int wi,int d,int sp){
    	for(int r=0;r<=255;r+=d)
    		for(int g=0;g<=255;g+=d)
    			for(int b=0;b<=255;b+=d){
        			gr.setColor(new Color(r,g,b));
        			gr.fillRect(b/d*(wi+sp),(r/d*6+g/d)*(wi+sp),wi,wi);
        			gr.setColor(Color.black);
        			gr.drawRect(b/d*(wi+sp),(r/d*6+g/d)*(wi+sp),wi,wi);
                }
    }
}