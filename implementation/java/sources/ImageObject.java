/*
 * @(#)ImageObject.java
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
     * ImageObject is a object containing all methods concerning images
     * found in HTML document
     */
     
import javax.swing.text.*;
import javax.swing.*;
import java.awt.*;
import java.awt.event.*;
import java.util.*;
import java.net.URL;

public final class ImageObject extends CampHtmlObject {

  	private ImageFrame imframe;
    private Vector vectorOfImages,vectorOfImagePseudos;
    private URL imagepath=null;

    public ImageObject () {

    }

    public void init(Campfire p,URL imgpath,Vector vcOfImages,Vector vcOfImagePseudos){
        init(p);
        imframe=null;
        imagepath= imgpath;
        vectorOfImages=vcOfImages;
        vectorOfImagePseudos=vcOfImagePseudos;
    }
    
 	public String parseHtml(String s){
	    String my=new String(s);
	    StringBuffer t;
	    int fbeg, fend;
	    ImageProperties imgProps;
	    
	    if ((fbeg=firstTag(my, "<!** IMAGE", 0))!=-1)
	    {
	        t=new StringBuffer();
	        imgProps= new ImageProperties();
	        
	        fend=my.indexOf(">",fbeg);
	        
	        t.append(my.substring(0,fbeg));
	        t.append(":");
	        t.append(my.substring(fend+1));
	        
	        imgProps= parseProperties(my.substring(fbeg+11,fend));
    	    imgProps.carPosition=charPosition(my,fbeg);
	        createPresentation(imgProps);
	        my=t.toString();
	    }
	    return my;
	}
	

	public void insert(){
	    ImageProperties imgProps= new ImageProperties();

        imgProps.carPosition=textPane.getCaretPosition();
	    openDialog();
		imframe.open(imgProps);
	}

	public void edit(ImageControl i){
	    openDialog();
		imframe.open(i);
	}
    
    public ImageProperties parseProperties( String s){
        ImageProperties imgProps= new ImageProperties();
        int i, altidx, toidx;
        String alignWay, altText, imageName;
        String toParse;
        
        toParse=s.toUpperCase();
        
        
        // here we find image name
        if (s.equals("?")) return null;
        i=s.indexOf(" ");
        if (i==-1) i=s.length();
        imageName=s.substring(0,i);
		imgProps.imageName=imageName;

        // here we find alignment
        i=toParse.indexOf("ALIGN=");
        if (i==-1){
    		imgProps.alignWay=new String();
    	}else{
    		altidx=toParse.indexOf("ALT=");
    		toidx=toParse.length();
    		if (altidx!=-1) toidx=altidx-1;
    		alignWay=toParse.substring(i+6,toidx);
    		imgProps.alignWay=alignWay;
    	}
		
        // here we find alternative text
        i=toParse.indexOf("ALT=");
        if (i==-1){
            imgProps.altText=new String();
        }else{
            altText=s.substring(i+5,s.indexOf("\"",i+5));
    		imgProps.altText=altText;
    	}
        
        // here we find subtitle
        i=toParse.indexOf("SUB=");
        if (i==-1){
            imgProps.subTitle=new String();
        }else{
            imgProps.subTitle=s.substring(i+5,s.indexOf("\"",i+5));
    	}

        //parent.debug("image "+imgProps.imageName);
        //parent.debug("align "+imgProps.alignWay);
        //parent.debug("alttext "+imgProps.altText);
        
        return imgProps;

    }

	public void createPresentation(ImageProperties props){

//        textPane.setSelectionStart(props.carPosition);
//        textPane.setSelectionEnd(props.carPosition+1);
//        parent.htmleditorkit.createPresentation("Image", new Integer(3));

  	    textPane.setCaretPosition(props.carPosition);
	    ImageControl im=insertControl();
	    im.setProperties( props);
	}


   private ImageControl insertControl(){
        ImageControl im=new ImageControl(new CampToolbarIcon(CampConstants.TB_ICON_IMAGE,parent));
        insertComponentTo(im);
        objList.addElement(im);

//        TableControl tc=new TableControl(2,3);
//        insertComponentTo(tc);

        return im;
   }
    
    private void openDialog(){
        if (imframe==null){
            imframe=new ImageFrame(parent, CampResources.get("ImageFrame.Title"),vectorOfImages, vectorOfImagePseudos);
        }else{
            imframe.reset();
      	}
    }
}