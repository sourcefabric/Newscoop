/*
 * @(#)FontColorChooser.java
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
     * FontColorChooser is a frame containing three JTextFields (RGB values)
     * a JPanel (for the sample color), an image representing a simple color scale,
     * and an array of swatches.
     * The color of the sample can be changed in three ways:
     * 1) By clicking, or dragging the image representing the color scale. In this case
     *     the RGB values are calculated based on some formula.
     * 2) By clicking the swatches: the calculations are done based on a simple formula
     * 3) By entering the decimal color codes into the textfields. The textfileds has
     *     associated Documents, in orer to check the values, and update the sample.
     */


import com.sun.java.swing.*;
//import java.applet.*;
import java.awt.*;
import java.awt.event.*;
import com.sun.java.swing.event.*;
import java.net.*;

class FontColorChooser extends JFrame/* implements Runnable*/{
    JTextField rl,gl,bl;
    JTextPane tp;
    Color bc;
    JButton ok, cancel;
    //JButton none;
    boolean returnable;
    Panel sample;
    Test parent;
    ImageCanvas c;
	SwatchCanvas sc;
    //Thread mainTh;
    int xs=10;
    int ys=30;
    int sp=10;
    int ew=50;
    int eh=30;
    int bh=30;
    int bw=80;
    int bo=200;
    int ch=30;
    int ared=0,agreen=0,ablue=0;
    float red=0,green=0,blue=0;
    boolean nonef=false;
    Container cp;
	int offset=100;
    
      

    public FontColorChooser(String s,Test par,URL im){
        super(s);
        cp=getContentPane();
        parent=par;
        setVisible(false);
        c=new ImageCanvas(im,256,384,/*(java.applet.Applet)*/parent,this);
        sc=new SwatchCanvas(72,360,parent,this);
        c.repaint();
		sc.repaint();
        
        setSize(500,560);
        cp.setLayout(null);
        rl=new JTextField(3);
        gl=new JTextField(3);
        bl=new JTextField(3);
        ok=new JButton("Ok");
        sample=new Panel();
        cancel=new JButton("Cancel");
//        none=new JButton("None");
        cp.add(c);
		cp.add(sc);
        c.setBounds(xs,ys,c.getSize().width,c.getSize().height);
        sc.setBounds(350,ys,sc.getSize().width,sc.getSize().height);
        cp.add(sample);
        sample.setBounds(xs,ys+c.getSize().height+sp,100,100);
        cp.add(rl);
        rl.setBounds(xs+c.getSize().width+sp,ys,ew,eh);
        cp.add(gl);
        gl.setBounds(xs+c.getSize().width+sp,ys+eh+sp,ew,eh);
        cp.add(bl);
        bl.setBounds(xs+c.getSize().width+sp,ys+2*eh+2*sp,ew,eh);
        cp.add(ok);
        ok.setBounds(xs+bo+offset,ys+c.getSize().height+sp,bw,bh);
       // cp.add(none);
//        none.setBounds(xs+bo+offset,ys+c.getSize().height+2*sp+bh,bw,bh);
        cp.add(cancel);
        cancel.setBounds(xs+bo+offset,ys+c.getSize().height+2*sp+bh,bw,bh);
        rl.setDocument(new ColorFieldDocument(rl,this,1));
        gl.setDocument(new ColorFieldDocument(gl,this,2));
        bl.setDocument(new ColorFieldDocument(bl,this,3));
        updater();
        
        
        ok.addActionListener(new ActionListener(){
            public void actionPerformed(ActionEvent e){
                updater();
                setVisible(false);
                aPerf();
            }
            });
/*        none.addActionListener(new ActionListener(){
            public void actionPerformed(ActionEvent e){
                nonef=true;
                updater();
                setVisible(false);
                aPerf();
            }
            });*/
        cancel.addActionListener(new ActionListener(){
            public void actionPerformed(ActionEvent e){
                bc=null;
                setVisible(false);
            }
            });
            
    }
    public void aPerf(){
        FontColorStyleAction a=new FontColorStyleAction("ACTION COMMAND,cmd=null",tp,this,"");
        a.actionPerformed(new ActionEvent(parent.textPane,1,""));
          // parent.textPane.;
    }
    
    public void updater(){
            //System.out.println("update");
        
            rl.setText(""+ared);
            gl.setText(""+agreen);
            bl.setText(""+ablue);
            bc=new Color(ared,agreen,ablue);
            //if (nonef) bc=null;
            //System.out.println(bc);
            sample.setBackground(bc);
            sample.repaint();
    }
    public void updaterKeyb(){
            //System.out.println("update");
        
            bc=new Color(ared,agreen,ablue);
            //if (nonef) bc=null;
            //System.out.println(bc);
            sample.setBackground(bc);
            sample.repaint();
    }
    
    
    public Color getColor(){
        if (nonef) return null; else
        return bc;
    }
    
    public void setTP(JTextPane t){
        tp=t;
    }
    
    public void rgbgen(int x,int nr){
        red=0;
        blue=0;
        green=0;
if (dom(nr,0,63))
	{
	red=255;
	blue=nr*4;
	}
if (dom(nr,64,127))
	{
	red=255-(nr-64)*4;
	blue=255;
	}
if (dom(nr,128,191))
	{
	green=(nr-128)*4;
	blue=255;
	}
if (dom(nr,192,255))
	{
	green=255;
	blue=255-(nr-192)*4;
	}
if (dom(nr,256,319))
	{
	red=(nr-256)*4;
	green=255;
	}
if (dom(nr,320,383))
	{
	red=255;
	green=255-(nr-320)*4;
	}
if (x<=127)
	{
	red=(float)(red*x/128.0);
	green=(float)(green*x/128.0);
	blue=(float)(blue*x/128.0);
	}	
if (x>127)
	{
	red=(float)(red+(x-128)*(255-red)/128.0);
	green=(float)(green+(x-128)*(255-green)/128.0);
	blue=(float)(blue+(x-128)*(255-blue)/128.0);
	}	
	
ared=(int)Math.floor(red);	
agreen=(int)Math.floor(green);	
ablue=(int)Math.floor(blue);	
    }
    
public boolean dom(int val,int min,int max){
    boolean ret=false;
    if ((val>=min)&&(val<=max)) ret=true;
    return ret;
}

    public void click(int x,int y){
        //System.out.println(""+y);
        rgbgen(x,y);
        updater();
    }

    public void clickSwatch(int x,int y){
	int d=10;
	int v=51;
	ablue=(x/d)*v;
	ared=y/(d*6)*v;
	agreen=(y%(d*6))/d*v;
        updater();
    }

    
}