/*
 * @(#)HtmlProperties.java
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
     * HtmlProperties is a helper class for a workaround regarding the generation
     * of the resulting html.For more details take a look at theHtmlGenerator.
     */

import java.awt.*;

class HtmlProperties{
    boolean properties[];
    int length=0;
    boolean br;
    Color color=null;
    boolean word;
    String myImage=null;
    String mySpaces=null;
    String myLink=null;
    
    public HtmlProperties(int n){
        properties=new boolean[n];
        length=n;
        br=false;
        word=false;
        myImage=null;
        mySpaces=null;
        myLink=null;
        color=null;
    }
    
    public HtmlProperties(HtmlProperties c){
        //System.out.println("copy constructor");
        this.properties=new boolean[c.length];
        this.length=c.length;
        for(int i=0;i<c.length;i++)
            setPropertie(i,c.getPropertie(i));
        this.br=c.br;
        if (c.color==null) this.color=null;
            else this.color=new Color(c.color.getRGB());
        this.word=c.word;    
        if (c.myImage==null) this.myImage=null;
            else this.myImage=new String(c.myImage);
        if (c.mySpaces==null) this.mySpaces=null;
            else this.mySpaces=new String(c.mySpaces);
        if (c.myLink==null) this.myLink=null;
            else this.myLink=new String(c.myLink);
    }
    
    public void setPropertie(int n, boolean b){
        if (n>=properties.length) {System.out.println("Index out of range : "+n);return;}
        properties[n]=b;
    }
    
    public boolean getPropertie(int n){
        if (n>=properties.length) {System.out.println("Index out of range : "+n);return false;}
        return properties[n];
    }
    
    public void setBreakLine(){
        br=true;
        //System.out.println("breaked");
    }

    public boolean getBreakLine(){
        return br;
    }
    
    public void setColor(Color c){
        color=c;
    }
    
    public void setWord(boolean v){
        word=v;
    }
    
    public void setImage(String s){
        myImage=new String(s);
    }
    public void setSpaces(String s){
        //System.out.println("sss");
        mySpaces=new String(s);
    }
    public void setLink(String s){
        myLink=new String(s);
    }
    
    public String toString(){
        return "<"+properties[0]+" "+properties[1]+" "+properties[2]+">";
    }
}