/*
 * @(#)FontColorFrame.java
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
     * FontColorFrame is a frame containing three JTextFields (RGB values)
     * a JPanel (for the sample color), an image representing a simple color scale,
     * and an array of swatches.
     * The color of the sample can be changed in three ways:
     * 1) By clicking, or dragging the image representing the color scale. In this case
     *     the RGB values are calculated based on some formula.
     * 2) By clicking the swatches: the calculations are done based on a simple formula
     * 3) By entering the decimal color codes into the textfields. The textfileds has
     *     associated Documents, in orer to check the values, and update the sample.
     */


import javax.swing.*;
//import java.applet.*;
import java.awt.*;
import java.awt.event.*;
import javax.swing.event.*;
import java.net.*;

class FontColorFrame extends CampDialog{
    private JTextField rl,gl,bl;
    private JTextPane tp;
    private Color bc;
    //JButton none;
    private boolean returnable;
    private JPanel sample;
    private ImageCanvas c;
	private SwatchCanvas sc;
    //Thread mainTh;
    private int xs=10;
    private int ys=30;
    private int sp=10;
    private int ew=50;
    private int eh=30;
    private int bh=30;
    private int bw=80;
    private int bo=200;
    private int ch=30;
    int ared=0,agreen=0,ablue=0;
    private float red=0,green=0,blue=0;
    boolean nonef=false;
	private int offset=100;
    protected JPanel numPanel = new JPanel();
    protected CampSCLayout numLayout = new CampSCLayout(4, CampSCLayout.FILL, CampSCLayout.FILL, 3);
    
      

    public FontColorFrame(Campfire p, String title, URL im){
        //super(p, title, 440, 560);
        super(p, title, 1, 3, CampLayout.FILL, CampLayout.TOP);

        numPanel.setLayout(numLayout);
        c=new ImageCanvas(im,256,384,parent,this);
        sc=new SwatchCanvas(72,360,parent,this);
        c.repaint();
		sc.repaint();
        
        rl=new JTextField(3);
        gl=new JTextField(3);
        bl=new JTextField(3);
        sample=new JPanel();
        
        numPanel.add(rl);
        numPanel.add(gl);
        numPanel.add(bl);
        numPanel.add(sample);

        rl.setDocument(new ColorFieldDocument(rl,this,1));
        gl.setDocument(new ColorFieldDocument(gl,this,2));
        bl.setDocument(new ColorFieldDocument(bl,this,3));

        addCompo(c);
        addCompo(numPanel);
		addCompo(sc);
        displayLayout.setColumnScale(1, (double)72/256);
        displayLayout.setColumnScale(2, (double)72/256);
        
        addButtons(ok, cancel);
        finishDialog();
        updater();
        
        
        ok.addActionListener(new ActionListener(){
            public void actionPerformed(ActionEvent e){
                updater();
                setVisible(false);
                aPerf();
            }
            });

        cancel.addActionListener(new ActionListener(){
            public void actionPerformed(ActionEvent e){
                bc=null;
                setVisible(false);
            }
            });
            
    }

    public void aPerf(){
        FontColorStyleAction a=new FontColorStyleAction("ACTION COMMAND,cmd=null",tp,getColor(),"");
        a.actionPerformed(new ActionEvent(parent.textPane,1,""));
    }
    
    public void updater(){
            rl.setText(""+ared);
            gl.setText(""+agreen);
            bl.setText(""+ablue);
            bc=new Color(ared,agreen,ablue);
            sample.setBackground(bc);
            sample.repaint();
    }

    public void updaterKeyb(){
            bc=new Color(ared,agreen,ablue);
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