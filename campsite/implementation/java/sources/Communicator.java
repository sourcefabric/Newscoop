/*
 * @(#)Communicator.java
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
     * Communicator implements the client of a TCP connection, in order to save the document,
     * by creating a http POST message. 
     * while receiving the acknowledgement, it will look for the three values mentioned
     * in the goForward method, and will stop when one is found.
     */


import java.net.*;
import java.io.*;

class Communicator{
    
    Socket server;
    int port;
    Test parent;
    InputStream netinput;
    OutputStream netoutput;
    boolean ready=true;
	boolean okTrans=true;
	boolean locked=false;
    
    public Communicator(Test par, int por){
        parent=par;
        port=por;
    }
    
    public boolean connect(){
        parent.showInfo("Connecting ...");
	okTrans=true;
        try{
            //System.out.println("sock");
            //System.out.println(parent.getCodeBase().getHost()+port);
            server=new Socket(parent.getCodeBase().getHost(),port);
        }
        catch(IOException e){
            System.out.println("Ex at new Socket:"+e);
            parent.showInfo("Network Error : "+e);
            return false;
        }
        
        try{
            netinput=server.getInputStream();
            netoutput=server.getOutputStream();
        }
        catch(IOException e){
            System.out.println("Ex at ioStreams:"+e);
            parent.showInfo("Network Error : "+e);
            return false;
        }
        //write_net_output(netoutput,parent.sessionID+"\n");
        return true;
    }
    
      void write_net_output(OutputStream output, String string){
        byte byte_array[];
        int length=string.length();
        byte_array=new byte[length];
        for(int i=0;i<string.length();i++)
            byte_array[i]=(byte)(string.charAt(i));
        //string.getBytes(0,length,byte_array,0);
        try{
            output.write(byte_array);
            }
            catch (IOException e){
            parent.showInfo("Network Error : "+e);
            System.out.println("Ex at write :"+e);
                }
      }
      
      void write(String s){
        ready=false;
        write_net_output(netoutput,s);
        ready=true;
      }
      void write(StringBuffer s){
        ready=false;
        write_net_output(netoutput,new String(s));
        ready=true;
      }

      void write_net_output(OutputStream output, char ch){
        try{
            output.write(ch);
        }
        catch (IOException e){}
      }
      
      void close(){
        try{
            server.close();
        }
        catch(Exception e){
        }
        
        parent.showInfo("Connection closed");
    }
    
      String read_net_input_line(InputStream input){
        String line= "";
        String c;
        //System.out.println("read");

        c=read_net_input(input);
        //System.out.println("readelt egy valamit");
        while(goForward(line)){
            line=line+c;
            c=read_net_input(input);
        }

        return line;
      }

	public boolean goForward(String line){	
		//System.out.println(line.length());
		boolean ret=true;
		if((line.toUpperCase()).indexOf("TOLOK")!=-1) {ret=false;okTrans=true;}
		if((line.toUpperCase()).indexOf("TOLERR")!=-1) {ret=false;okTrans=false;}
		if((line.toUpperCase()).indexOf("TOLLOCK")!=-1) {ret=false;okTrans=false;locked=true;}
		return ret;
	}


      String read_net_input(InputStream input){
        byte bytes[];
        int number_of_bytes;

        //System.out.println("readel");
        try{
            bytes=new byte[1];
            number_of_bytes=input.read(bytes,0,1);
            //System.out.println("readelt egy bytot");
            if (number_of_bytes > 0){
                StringBuffer sb=new StringBuffer();
                sb.append((char)bytes[0]);
//                return (new String(bytes,0,0,number_of_bytes));//lajos
                //System.out.println(new String(sb));
                return new String(sb);
            }
            else
            {
                //System.out.println("nem olvasott semmit");
                return null;
            }
        }
        catch (IOException e){
        //System.out.println("a readnetinput");
            return null;
        }
      }
      
      String read(){
        //System.out.println("read");
        return read_net_input_line(netinput);
      }
}