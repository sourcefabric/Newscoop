/*
 * @(#)CampURLDecoder.java
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
     * URLDecoder : decodes URLEncoded strings .
     */


public class CampURLDecoder{
    private String ord="123456789ABCDEF";
    
    public CampURLDecoder(){
    }
    
    public String decode(String s){
        StringBuffer t=new StringBuffer();
        int i=0;
        while (i<s.length()){
            if (s.charAt(i)=='%'){
                t.append(deHexer(s.substring(i+1,i+3)));
                i+=3;
            }else{
                if (s.charAt(i)=='+'){
                    t.append(' ');
                    i++;
                }else{
                    t.append(s.charAt(i));
                    i++;
                }
            }
        }
        return new String(t);
    }
    
    private char deHexer(String s){
        char c=0;
        s=s.toUpperCase();
        c+=(ord.indexOf(s.charAt(0))+1)*16;
        c+=(ord.indexOf(s.charAt(1))+1);
        return (char)c;
    }

    public String encode(String s){
        StringBuffer t=new StringBuffer();
        int i=0;
        while (i<s.length()){
/*
            if (s.charAt(i)=='\n'){
                t.append("%0A");
                i++;
            }else if (s.charAt(i)=='\r'){
                t.append("%0D");
                i++;
            }else if (s.charAt(i)=='\b'){
                t.append("%08");
                i++;
            }else if (s.charAt(i)=='\t'){
                t.append("%09");
                i++;
            }else if (s.charAt(i)=='\"'){
                t.append("%22");
                i++;
            }else if (s.charAt(i)=='\''){
                t.append("%2C");
                i++;
            }else if (s.charAt(i)=='\\'){
                t.append("%5C");
                i++;
            }else if (s.charAt(i)=='&'){
                t.append("%22");
                i++;
*/
            if (s.charAt(i)=='&'){
                t.append("%26");
                i++;
            }else if (s.charAt(i)==';'){
                t.append("%3B");
                i++;
            }else if (s.charAt(i)=='%'){
                t.append("%25");
                i++;
            }else{
                t.append(s.charAt(i));
                i++;
            }
        }
        return new String(t);
    }

}