/*
 * @(#)ColorConverter.java
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
     * ColorConverter converts colors from decimal R,G,B values, Color class, or html color code 
     * (with or without starting #) into Color, or html color code (without starting #)
     */

import java.awt.*;

class ColorConverter{
    private int r=0;
    private int g=0;
    private int b=0;
    
    public ColorConverter(){
    }
    public ColorConverter(int x, int y, int z){
        if ((x>=0)&&(x<256)) r=x;
        if ((y>=0)&&(y<256)) g=y;
        if ((z>=0)&&(z<256)) b=z;
    }

    public ColorConverter(Color c){
        int x=c.getRed();
        int y=c.getGreen();
        int z=c.getBlue();
        if ((x>=0)&&(x<256)) r=x;
        if ((y>=0)&&(y<256)) g=y;
        if ((z>=0)&&(z<256)) b=z;
    }

    public ColorConverter(String s){
        if (s.length()==7) s=s.substring(1);
        int x=toDecimal(s,0);
        int y=toDecimal(s,1);
        int z=toDecimal(s,2);
        if ((x>=0)&&(x<256)) r=x;
        if ((y>=0)&&(y<256)) g=y;
        if ((z>=0)&&(z<256)) b=z;
    }
    
    public Color getColor(){
        return new Color(r,g,b);
    }
    
    public String getHex(){
        return (toHex(r)+toHex(g)+toHex(b));
    }
    
    private int toDecimal(String s,int o){
        String v=s.substring(o*2,o*2+2).toUpperCase();
        int h1=toDec(v.charAt(0));
        int h2=toDec(v.charAt(1));
        return h1*16+h2;
    }
    
    private int toDec(char a){
        if (a=='0') return 0;
        if ((a>='1')&&(a<='9')) return a-'1'+1;
        if ((a>='A')&&(a<='F')) return a-'A'+10;
        return 0;
    }
    
    public String toHex(int v){
        int h1=v/16;
        int h2=v%16;
        return (toH(h1)+toH(h2));
    }
    
    private String toH(int v){
        StringBuffer sb=new StringBuffer();
        if (v==0) sb.append("0");
        if ((v>=1)&&(v<=9)) sb.append((char)(v-1+'1'));
        if ((v>=10)&&(v<=15)) sb.append((char)(v-10+'A'));
        return new String(sb);
    }
}