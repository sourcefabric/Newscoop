/*
 * @(#)Campfire.java
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
     * Campfire is the main class.
     */


import java.io.File;
import java.applet.*;
import java.awt.*;
import java.awt.event.*;
import java.util.*;
import java.io.FileReader;
import java.net.URL;
import java.net.*;
import java.lang.reflect.Array;
import javax.swing.text.*;
import javax.swing.event.*;
import javax.swing.undo.*;
import javax.swing.*;
import javax.swing.border.*;
//import com.incors.plaf.*;
//import com.incors.plaf.kunststoff.*;

public class Campfire extends JApplet{
//********************************************************************************
//********************************************************************************
//****                       Main variables                                   ****
//********************************************************************************
//********************************************************************************
    
    JTextPane textPane=new JTextPane();
    private JMenuBar menubar=new JMenuBar();
    private JToolBar toolbar=new JToolBar();
    private JScrollPane scrollPane;
    private Hashtable actionTable = new Hashtable();

    
    private JPanel statusArea = new JPanel();
    private JPanel holderArea = new JPanel();
    private JLabel status = new JLabel(" ");

    private boolean stopping,dialogShow;
    private boolean debugVer=false;
    
    private StyledDocument doc,unDoc;
    
    private DumperFrame dumpFrame;
    //JCheckBoxMenuItem isJustified;
    
    private String artindex="";
    private Container contentPane;
    
    private int dot=0,mark=0;
    HtmlEditorKit htmleditorkit;
    int nrofDictionaryWords=0;
    private StringBuffer toHtml;
    private String dictionary[];
    private boolean modified=false;
    
    String linkscript;
	private int nrofFields=8;
	private String retFields[]=new String[nrofFields];
	
    private String[] fieldNames=new String[]{
                            "UserId",
                            "UserKey",
                            "IdPublication",
                            "NrIssue",
                            "NrSection",
                            "NrArticle",
                            "IdLanguage",
                            "Field"   };


    
    private Color backColor=Color.white;
    private Color foreColor=Color.black;
    Color dictColor=Color.red;
    
    
    private String contentString;
    
    private int defaultport=CampConstants.DEFAULT_PORT;
    private int port=defaultport;
    private URL imagepath=null;  
    private boolean firsttime=true;
    private Vector vectorOfImages,vectorOfImagePseudos;
    private Vector vectorOfAudios,vectorOfAudioPseudos;
    private Vector vectorOfVideos,vectorOfVideoPseudos;
	private String scriptString=CampConstants.SCRIPT_PATH;
	
   

    private boolean newLine=false;
    private String succesfully;
    private boolean upOK=false;
    
//    UndoManager undoManager=new UndoManager();
//    UndoAction undoAction =new UndoAction(this);
//    RedoAction redoAction =new RedoAction(this);
    
    UndoManager undoManager;
    UndoAction undoAction ;
    RedoAction redoAction ;
    
//********************************************************************************
//********************************************************************************
//****                       Constructor                                      ****
//********************************************************************************
//********************************************************************************

    public Campfire(){
        contentPane=getContentPane();
    }

    
//********************************************************************************
//********************************************************************************
//****                       populate                                         ****
//********************************************************************************
//********************************************************************************

    private void populate(){
        String[] cutCopyPasteActionNames=new String[]{
            DefaultEditorKit.cutAction,"Cut",CampConstants.TB_ICON_CUT,
            DefaultEditorKit.copyAction,"Copy",CampConstants.TB_ICON_COPY,
            DefaultEditorKit.pasteAction,"Paste",CampConstants.TB_ICON_PASTE,
            "select-all","Select All",CampConstants.TB_ICON_SELALL
        };
    
        String[] styleActionNames=new String[]{
            "font-bold",CampResources.get("FontStyleMenu.Bold"),CampConstants.TB_ICON_BOLD,
            "font-italic",CampResources.get("FontStyleMenu.Italic"),CampConstants.TB_ICON_ITALIC,
            "font-underline",CampResources.get("FontStyleMenu.Underline"),CampConstants.TB_ICON_UNDERLINE
        };
    
        String[] alignActionNames=new String[]{
            "left-justify",CampResources.get("AlignMenu.Left"),CampConstants.TB_ICON_LEFT,
            "center-justify",CampResources.get("AlignMenu.Center"),CampConstants.TB_ICON_CENTER,
            "right-justify",CampResources.get("AlignMenu.Right"),CampConstants.TB_ICON_RIGHT
        };


        JMenu fileMenu=new JMenu(CampResources.get("MainMenu.File")),
              editMenu=new JMenu(CampResources.get("MainMenu.Edit")),
              styleMenu=new JMenu(CampResources.get("FormatMenu.FontStyle")),
              sizeMenu=new JMenu(CampResources.get("FormatMenu.FontSize")),
              alignMenu=new JMenu(CampResources.get("FormatMenu.Align")),
              insertMenu=new JMenu(CampResources.get("MainMenu.Insert")),
              formatMenu=new JMenu(CampResources.get("MainMenu.Format")),
              createMenu=new JMenu(CampResources.get("MainMenu.Create")),
              helpMenu=new JMenu(CampResources.get("MainMenu.Help"));
        
        
        if (CampResources.isRightToLeft()){
            fileMenu.setComponentOrientation(ComponentOrientation.RIGHT_TO_LEFT);
            editMenu.setComponentOrientation(ComponentOrientation.RIGHT_TO_LEFT);
            styleMenu.setComponentOrientation(ComponentOrientation.RIGHT_TO_LEFT);
            sizeMenu.setComponentOrientation(ComponentOrientation.RIGHT_TO_LEFT);
            alignMenu.setComponentOrientation(ComponentOrientation.RIGHT_TO_LEFT);
            insertMenu.setComponentOrientation(ComponentOrientation.RIGHT_TO_LEFT);
            formatMenu.setComponentOrientation(ComponentOrientation.RIGHT_TO_LEFT);
            createMenu.setComponentOrientation(ComponentOrientation.RIGHT_TO_LEFT);
            helpMenu.setComponentOrientation(ComponentOrientation.RIGHT_TO_LEFT);
        }

        toolbar.setFloatable(false);
        
        addCommand(new CustomAction(CampResources.get("FileMenu.New"),CustomAction.NEWFILE,this),fileMenu,CampConstants.TB_ICON_NEW,CampResources.get("ToolTip.New"));
        addCommand(new CustomAction(CampResources.get("FileMenu.Save"),CustomAction.UPLOAD,this),fileMenu,CampConstants.TB_ICON_UPLOAD,CampResources.get("ToolTip.Save"));
        fileMenu.addSeparator();
        addBarSeparator();
        addCommand(new CustomAction(CampResources.get("FileMenu.ArticlePreview"),CustomAction.PREVIEW,this), fileMenu);
        if (debugVer) {
            fileMenu.addSeparator();
	        addCommand(new CustomAction(CampResources.get("FileMenu.Dump"),CustomAction.DUMP,this),fileMenu,CampConstants.TB_ICON_DUMP,CampResources.get("ToolTip.Dump"));
	        addCommand(new CustomAction(CampResources.get("FileMenu.Regenerate"),CustomAction.SETHTML,this),fileMenu,CampConstants.TB_ICON_HTML,CampResources.get("ToolTip.Regenerate"));
            addBarSeparator();
        }
		
        addCommand(undoAction, editMenu);
        addCommand(redoAction, editMenu);
        editMenu.addSeparator();

       	addCommand(getAction(DefaultEditorKit.cutAction),editMenu,CampResources.get("EditMenu.Cut"),CampConstants.TB_ICON_CUT,CampResources.get("ToolTip.Cut"));
       	addCommand(getAction(DefaultEditorKit.copyAction),editMenu,CampResources.get("EditMenu.Copy"),CampConstants.TB_ICON_COPY,CampResources.get("ToolTip.Copy"));
       	addCommand(getAction(DefaultEditorKit.pasteAction),editMenu,CampResources.get("EditMenu.Paste"),CampConstants.TB_ICON_PASTE,CampResources.get("ToolTip.Paste"));

        addToolbarCommand(undoAction,CampConstants.TB_ICON_UNDO,CampResources.get("EditMenu.Undo"));
        addToolbarCommand(redoAction,CampConstants.TB_ICON_REDO,CampResources.get("EditMenu.Redo"));
        editMenu.addSeparator();
       	addCommand(getAction("select-all"),editMenu,CampResources.get("EditMenu.SelectAll"));

        addCommand(new FontSizeStyleAction("1",textPane,"1"), sizeMenu);
        addCommand(new FontSizeStyleAction("2",textPane,"2"), sizeMenu);
        addCommand(new FontSizeStyleAction("3",textPane,"3"), sizeMenu);
        addCommand(new FontSizeStyleAction("4",textPane,"4"), sizeMenu);
        addCommand(new FontSizeStyleAction("5",textPane,"5"), sizeMenu);
        addCommand(new FontSizeStyleAction("6",textPane,"6"), sizeMenu);
        addCommand(new FontSizeStyleAction("7",textPane,"7"), sizeMenu);
        formatMenu.add( sizeMenu);				

        addBarSeparator();
        
        for(int i=0; i<styleActionNames.length; ++i) {
            Action action=getAction(styleActionNames[i]);
            if (action!=null) {

                JButton button=toolbar.add(action);
                JMenuItem item=styleMenu.add(action);
                if (CampResources.isRightToLeft()) item.setComponentOrientation(ComponentOrientation.RIGHT_TO_LEFT);
                item.setText(styleActionNames[++i]);
                
                button.setText(null);
                button.setToolTipText(styleActionNames[i]);
                button.setIcon(new CampToolbarIcon(styleActionNames[++i],this));
                button.setRequestFocusEnabled(false);
                button.setMargin(new Insets(1,1,1,1));
                button.setBorderPainted(false);
                
            }
        }

        formatMenu.add( styleMenu);				
        addCommand(new CustomAction(CampResources.get("FormatMenu.FontColor"),CustomAction.COLOR,this), formatMenu);
        addBarSeparator();

        if (CampResources.isRightToLeft()){
           	addCommand(getAction("right-justify"),alignMenu,CampResources.get("AlignMenu.Right"),CampConstants.TB_ICON_RIGHT,CampResources.get("ToolTip.Right"));
           	addCommand(getAction("center-justify"),alignMenu,CampResources.get("AlignMenu.Center"),CampConstants.TB_ICON_CENTER,CampResources.get("ToolTip.Center"));
           	addCommand(getAction("left-justify"),alignMenu,CampResources.get("AlignMenu.Left"),CampConstants.TB_ICON_LEFT,CampResources.get("ToolTip.Left"));
        }else{
           	addCommand(getAction("left-justify"),alignMenu,CampResources.get("AlignMenu.Left"),CampConstants.TB_ICON_LEFT,CampResources.get("ToolTip.Left"));
           	addCommand(getAction("center-justify"),alignMenu,CampResources.get("AlignMenu.Center"),CampConstants.TB_ICON_CENTER,CampResources.get("ToolTip.Center"));
           	addCommand(getAction("right-justify"),alignMenu,CampResources.get("AlignMenu.Right"),CampConstants.TB_ICON_RIGHT,CampResources.get("ToolTip.Right"));
        }

        addBarSeparator();
        
        formatMenu.addSeparator();
        formatMenu.add( alignMenu);				
        formatMenu.addSeparator();
        addCommand(new CustomAction(CampResources.get("FormatMenu.ClearAllAttributes"),CustomAction.CLEAR,this), formatMenu);
        
        addCommand(new CustomAction(CampResources.get("InsertMenu.Image"),CustomAction.IMAGE,this),insertMenu,CampConstants.TB_ICON_IMAGE,CampResources.get("ToolTip.Image"));
        //addCommand(new CustomAction("Table",CustomAction.TABLE,this),insertMenu,CampConstants.TB_ICON_TABLE,"Insert Table");
        //insertMenu.addSeparator();
        //addCommand(new CustomAction("Add-On",CustomAction.ADDON,this),insertMenu,CampConstants.TB_ICON_ADDON,"Insert Add-On");
//        addBarSeparator();
        
        addCommand(new CustomAction(CampResources.get("CreateMenu.Subhead"),CustomAction.SUBHEAD,this),createMenu,CampConstants.TB_ICON_SUBHEAD,CampResources.get("ToolTip.Subhead"));
        createMenu.addSeparator();
//        addCommand(new CustomAction("Keyword Link",CustomAction.WORD,this),createMenu,CampConstants.TB_ICON_KEYWORD,"Create Keyword Link");
        addCommand(new CustomAction(CampResources.get("CreateMenu.KeywordLink"),CustomAction.WORD,this), createMenu);
        addCommand(new CustomAction(CampResources.get("CreateMenu.InternalLink"),CustomAction.INTLINK,this),createMenu,CampConstants.TB_ICON_INTLINK,CampResources.get("ToolTip.InternalLink"));
        //addCommand(new CustomAction("Audio Link",CustomAction.AUDIO,this),createMenu,CampConstants.TB_ICON_AUDIO,"Create Audio Link");
        //addCommand(new CustomAction("Video Link",CustomAction.VIDEO,this),createMenu,CampConstants.TB_ICON_VIDEO,"Create Video Link");
        addCommand(new CustomAction(CampResources.get("CreateMenu.ExternalLink"),CustomAction.EXTLINK,this),createMenu,CampConstants.TB_ICON_EXTLINK,CampResources.get("ToolTip.ExternalLink"));

        addCommand(new CustomAction(CampResources.get("HelpMenu.OurHomePage"),CustomAction.HOMEPAGE,this), helpMenu);
        addCommand(new CustomAction(CampResources.get("HelpMenu.BugsReport"),CustomAction.BUGS,this), helpMenu);
        addCommand(new CustomAction(CampResources.get("HelpMenu.InstallCertificate"),CustomAction.CERTIF,this), helpMenu);
        helpMenu.addSeparator();
        addCommand(new CustomAction(CampResources.get("HelpMenu.About"),CustomAction.ABOUT,this), helpMenu);
        
        menubar.add(fileMenu);
        menubar.add(editMenu);
        menubar.add(insertMenu);
        menubar.add(formatMenu);
        menubar.add(createMenu);
        menubar.add(helpMenu);
        
        
        //if (getParameter("FontSizeMenu")!=null)
            //if (getParameter("FontSizeMenu").equals("disabled")) sizeMenu.setEnabled(false);
    }

    
//********************************************************************************
//********************************************************************************
//****                       load action table                                ****
//********************************************************************************
//********************************************************************************
    private void loadActionTable(){
        Action[] actions =textPane.getActions();
        
        for(int i=0;i<actions.length;++i) {
            actionTable.put(actions[i].getValue(Action.NAME),actions[i]);
        }
    }
    
//********************************************************************************
//********************************************************************************
//****                       get Action                                       ****
//********************************************************************************
//********************************************************************************
    public Action getAction(String name) {
        return (Action)actionTable.get(name);
    }
    
//********************************************************************************
//********************************************************************************
//****                       Init                                             ****
//********************************************************************************
//********************************************************************************
    public void init(String args[]){

//        out("initializing....");

//      try {
//          UIManager.setLookAndFeel(UIManager.getCrossPlatformLookAndFeelClassName());
//          }
//      catch (Exception exc) {
//          System.err.println("Error loading L&F: " + exc);
//          }
    }
    
    
//********************************************************************************
//********************************************************************************
//****                       start                                            ****
//********************************************************************************
//********************************************************************************
    public void start(){

        
        boolean stopping=false;
        boolean dialogShow=false;
        boolean modified=false;    
        boolean sysLook=false;    
      
        if (firsttime) {

            //if (getCampParameter(CampConstants.PARAM_SYSLOOK)!=null) sysLook=true; else sysLook=false;

            //if (sysLook){
            //    try {
            //       UIManager.setLookAndFeel(UIManager.getSystemLookAndFeelClassName());
            //    }catch(Exception e) {
            //        e.printStackTrace();
            //    }
            //}else{

                try {
                   com.incors.plaf.kunststoff.KunststoffLookAndFeel kunststoffLnF
                        = new com.incors.plaf.kunststoff.KunststoffLookAndFeel();
                   kunststoffLnF.setCurrentTheme(new com.incors.plaf.kunststoff.KunststoffTheme());
                   UIManager.setLookAndFeel(kunststoffLnF);
                }catch(Exception e) {
                    e.printStackTrace();
                }
                UIManager.getLookAndFeelDefaults().put("ClassLoader", getClass().getClassLoader());

            //}

            if (getCampParameter(CampConstants.PARAM_DEBUG)!=null) debugVer=true; else debugVer=false;
            artindex=getCampParameter(CampConstants.PARAM_IDX);
            linkscript=getCampParameter(CampConstants.PARAM_LINKSCRIPT);    
            String po=getCampParameter(CampConstants.PARAM_PORT);

            if (getParameter("LangCode")!=null)
                CampResources.init(getParameter("LangCode"));
            else
                CampResources.init("en");
            
            if (po==null) port=defaultport;
                else
                {
                    try{
                        port=new Integer(po).intValue();
                    }
                    catch(Exception e){
                        port=defaultport;
                    }
                    if (port==0) port=defaultport;
                }
                
            try{
                imagepath=new URL(getCodeBase(),CampConstants.IMAGE_PATH);
            }
            catch(Exception e){}
            
            //textPane.setDoubleBuffered( true);
            scrollPane=new JScrollPane(textPane);
    
            contentPane.add(toolbar, BorderLayout.NORTH);
            contentPane.add(scrollPane,BorderLayout.CENTER);
            contentPane.add(holderArea,BorderLayout.SOUTH);
            holderArea.setLayout(new BorderLayout());
            holderArea.add(statusArea,BorderLayout.SOUTH);
            statusArea.setLayout(new BorderLayout());
            statusArea.add(status,BorderLayout.CENTER);
            
            readDictionary();
            readImages();
            //readAudios();
            //readVideos();
            readColors();
        	readFields();
    
            htmleditorkit=new HtmlEditorKit(this);
            textPane.setEditorKit(htmleditorkit);
            htmleditorkit.install(textPane);
//            textPane.setSelectionColor(Color.blue.darker());
            
            undoManager=new UndoManager();
            undoAction =new UndoAction(this);
            redoAction =new RedoAction(this);

            loadActionTable();
            populate();

            setJMenuBar(menubar);
            
                
            CampBroker.getImage().init(this,imagepath,vectorOfImages,vectorOfImagePseudos);
            CampBroker.getInternalLink().init(this);
            CampBroker.getExternalLink().init(this);
            //CampBroker.getAudioLink().init(this,vectorOfAudios,vectorOfAudioPseudos);
            //CampBroker.getVideoLink().init(this,vectorOfVideos,vectorOfVideoPseudos);
            CampBroker.getKeyword().init(this, dictionary);
            CampBroker.getSubhead().init(this);
            CampBroker.getFont().init(this,imagepath);
            //AddOnBroker.init(this);

            firsttime=false;

            SwingUtilities.updateComponentTreeUI(getParentFrame());
             
            if ((contentString!=null)&&(contentString.length()!=0))
                try{
              	    regen();
                }
                catch(Exception e){
                }

            unDoc=textPane.getStyledDocument();
            unDoc.addUndoableEditListener(new UndoableEditListener(){
                public void undoableEditHappened(UndoableEditEvent e){
                    undoManager.addEdit(e.getEdit());
                    undoAction.update();
                    redoAction.update();
                }
                });
                
            textPane.getDocument().addDocumentListener(new DocumentListener(){
                public void insertUpdate(DocumentEvent e){
                    setModified();
                    }
                public void changedUpdate(DocumentEvent e){
                    setModified();
                    }
                public void removeUpdate(DocumentEvent e){
                    setModified();
                    }
                });

            SwingUtilities.updateComponentTreeUI(getParentFrame());
            if (CampResources.isRightToLeft()){
                this.getParentFrame().applyResourceBundle(CampResources.getBundle());
            }

            textPane.requestFocus();
        }    
        showStatus(CampResources.get("Status.Ready"));   
    }
    
    public void setModified(){
        modified=true;
    }

//********************************************************************************
//********************************************************************************
//****                       read Dictionary and colors                       ****
//********************************************************************************
//********************************************************************************
    private void readDictionary(){
        int ord=0;
        String s;
        final String dict="tol#";
        while((s=getCampParameter(dict+ord))!=null){
            ord++;
        }
        nrofDictionaryWords=ord;
        ord=0;
        dictionary=new String[nrofDictionaryWords];
        while((s=getCampParameter(dict+ord))!=null){
            dictionary[ord]=new String(s);
            ord++;
        }
    }
    
    private void readColors(){
        String s;
        if ((s=getCampParameter(CampConstants.PARAM_BACKGROUND))!=null)
        {
        ColorConverter a=new ColorConverter(s);
        backColor=a.getColor();
        }
        /*if ((s=getCampParameter(CampConstants.PARAM_FOREGROUND))!=null)
        {
        ColorConverter a=new ColorConverter(s);
        foreColor=a.getColor();
        }*/
        if ((s=getCampParameter(CampConstants.PARAM_WORDCOLOR))!=null)
        {
        ColorConverter a=new ColorConverter(s);
        dictColor=a.getColor();
        }
    }
    

    
//********************************************************************************
//********************************************************************************
//****                       upload                                           ****
//********************************************************************************
//********************************************************************************
    
    
    public void upload(){

        toHtml=new StringBuffer("");
        doc=textPane.getStyledDocument();
        HtmlGenerator hg=new HtmlGenerator(doc,this,false);
        toHtml= hg.generate();
        //debug(toHtml.toString());

		
        showStatus(CampResources.get("Status.Saving"));
		Communicator comm=new Communicator(this,port);

		if (comm.connect())	{
    		StringBuffer header=new StringBuffer();
    		header.append("POST "+scriptString+" HTTP/1.0\r\n");
    		header.append("Host: "+getCodeBase().getHost()+":"+port+"\r\n");
    		header.append("Pragma: no-cache\r\n");
    		header.append("Cache-control: no-cache\r\n");
    		header.append("User-Agent: TOLCommunicator\r\n");
    		header.append("Content-type: application/x-www-form-urlencoded\r\n");

        	StringBuffer fields=new StringBuffer();
        	int len=0;
        
        	for (int k=0;k<nrofFields;k++){
        		StringBuffer tempfields=new StringBuffer();
        		tempfields.append(fieldNames[k]);
        		tempfields.append("=");
        		tempfields.append(new String(URLEncoder.encode(retFields[k])));
        		String returnal=new String(tempfields);
        		fields.append(returnal+"&");
        		len+=(returnal.length()+1);
        	}
        
    		StringBuffer body=new StringBuffer();
    		body.append("Content=");
//    		body.append(URLEncoder.encode(new String(toHtml)));
    		body.append(new CampURLDecoder().encode(new String(toHtml)));
//    		body.append(new String(toHtml));
    		String bodyString=new String(body);
    
//    		len+=bodyString.length();
            try{
        		len+=Array.getLength(bodyString.getBytes("UTF-8"));
            }catch(Exception e){}
    		
    		header.append("Content-length: "+len);
		
    		comm.write(new String(header));
    		comm.write("\r\n\r\n");
    		comm.write(new String(fields)+bodyString);
    		
    		succesfully=comm.read();
    		comm.close();
    		out(succesfully);
    		if (comm.okTrans) upOK=true; else upOK=false;
    		
    		if (stopping) dialogShow=true;
    		if (upOK) {
    		    showStatus(CampResources.get("Status.ArticleFieldSaved"));
    		    modified=false;
    		}else{
    		  if (!comm.locked)
    		      showError(CampResources.get("Error.UploadUnsuccesful"));
    		  else{
    		    stopping=true;
    		    dialogShow=true;
    		    showInfo(CampResources.get("Info.ArticleLocked"));
    		    stopping=false;
    		    dialogShow=false;
    		  }
    		}
    	}else{
		    if (stopping) dialogShow=true;
		    showError(CampResources.get("Error.CantConnectToServer"));
    	}
		    
   }
    
    public void dump(){
        toHtml=new StringBuffer("");
        doc=textPane.getStyledDocument();
        
        HtmlGenerator hg=new HtmlGenerator(doc,this,false);
        toHtml=hg.generate();
		debug(new String(toHtml));
   }


    public void debug(String s){

        if (dumpFrame==null){
            dumpFrame=new DumperFrame(this);
		    dumpFrame.setVisible(true);
		  }
        else
		    dumpFrame.setVisible(true);
		
		dumpFrame.t.append("\n");
		dumpFrame.t.append(new String(s));
   }

   
    private void readImages(){
        int ord=0;
        String s;
        final String im="image";
        vectorOfImages=new Vector();
        vectorOfImagePseudos=new Vector();
        while((s=getCampParameter(im+ord))!=null){
            vectorOfImagePseudos.addElement(s.substring(0,s.indexOf(",")));
            vectorOfImages.addElement(s.substring(s.indexOf(",")+1));
            ord++;
        }
    }

/*    
    private void readAudios(){
        int ord=0;
        String s;
        final String im="aud";
        vectorOfAudios=new Vector();
        vectorOfAudioPseudos=new Vector();
        while((s=getCampParameter(im+ord))!=null){
            vectorOfAudioPseudos.addElement(s.substring(0,s.indexOf(",")));
            vectorOfAudios.addElement(s.substring(s.indexOf(",")+1));
            ord++;
        }
    }

    private void readVideos(){
        int ord=0;
        String s;
        final String im="vid";
        vectorOfVideos=new Vector();
        vectorOfVideoPseudos=new Vector();
        while((s=getCampParameter(im+ord))!=null){
            vectorOfVideoPseudos.addElement(s.substring(0,s.indexOf(",")));
            vectorOfVideos.addElement(s.substring(s.indexOf(",")+1));
            ord++;
        }
    }
*/
    public void newFile(boolean ask){
        if (ask)
        {
            JOptionPane op=new JOptionPane();
            int selV=op.showConfirmDialog(this,CampResources.get("Message.CreateNewDocument"),CampResources.get("Message.CreateNewDocument.Title"),JOptionPane.YES_NO_OPTION);
            if (selV==JOptionPane.NO_OPTION) return;
        }    
        textPane.setText("");
        //isJustified.setState(false);

        CampBroker.getSubhead().reset();
        CampBroker.getImage().reset();
        CampBroker.getInternalLink().reset();
        CampBroker.getExternalLink().reset();
        //CampBroker.getAudioLink().reset();
        //CampBroker.getVideoLink().reset();
        CampBroker.getFont().reset();
        CampBroker.getKeyword().reset();
        //AddOnBroker.reset();

    }

    public void updateDots(int d, int m){
        mark=m;
        dot=d;
    }
    
    public void insertT(String s){
        try
        {
            int dmm=dot-mark;
            if (dmm<0)
            {
                dmm=-dmm;
                int sw=dot;
                dot=mark;
                mark=sw;
            }
        textPane.getDocument().remove(mark,dmm);
        textPane.getDocument().insertString(dot,s,null);
        }
        catch(Exception e)
        {
            out("ex: "+e);
        }
    }

	private void readFields(){

    	String d;
    	if ((d=getCampParameter(CampConstants.PARAM_SCRIPT))!=null) scriptString=d;
    	String s=new String();
    	for (int i=0;i<nrofFields;i++)
    	{
        	s=getCampParameter(fieldNames[i]);
        	if (s==null) retFields[i]=new String(""); else retFields[i]=new String(s);
    	}
    	contentString=getCampParameter(CampConstants.PARAM_CONTENT);
	}
	
	public void setHtml(){
	    
	    StringBuffer fromHtml=new StringBuffer();
        HtmlGenerator hg=new HtmlGenerator(textPane.getStyledDocument(),this,false);
        fromHtml=hg.generate();
        
	    HtmlParser pars=new HtmlParser(textPane,this,new String(fromHtml));
	    pars.parseHtml();
        
	}

	
    public void stop(){
   }

    public void beforeunload(){
        stopping=true;
        if (modified){
//            JOptionPane op=new JOptionPane();
            
//            int selV=op.showConfirmDialog(this,"Do you want to upload before leaving the editor?","Campfire - "+retFields[7],JOptionPane.YES_NO_OPTION);
//            if (selV==JOptionPane.YES_OPTION) upload();
            upload();
        }
        stopping=false;
        dialogShow=false;
    }
	
    public int ismodified(){
        if (modified) return 1;
        else return 0;
    }
    
	public void regen(){

        HtmlParser localParser=new HtmlParser(textPane,this,contentString);
        if (contentString!=null){
            //textPane.setEnabled(false);
            localParser.parseHtml();
            //textPane.setEnabled(true); 
        }
        modified=false;
	}


    private String getCampParameter(String myParam){
        String retParam= new String();
        
        retParam=getParameter(myParam);
        if (retParam!=null){
            retParam= new CampURLDecoder().decode(retParam);
            try{    
                retParam= new String( retParam.getBytes("UTF-8"), "UTF-8");
    	   }catch(Exception e){}
        }
   	    return retParam;
    }

//********************************************************************************
//********************************************************************************
//****                       Menu related                                     ****
//********************************************************************************
//********************************************************************************

    private void addCommand(Action a,JMenu menu,String img,String tt){
        JButton button=toolbar.add(a);
        JMenuItem menuitem=menu.add(a);
        if (CampResources.isRightToLeft()) menuitem.setComponentOrientation(ComponentOrientation.RIGHT_TO_LEFT);
        button.setText(null);
        button.setIcon(new CampToolbarIcon(img,this));
        button.setRequestFocusEnabled(false);
        button.setMargin(new Insets(0,0,0,0));
        button.setBorderPainted(false);
        button.setToolTipText(tt);

    }
    
    private void addCommand(Action a,JMenu menu,String it, String img,String tt){
        JButton button=toolbar.add(a);
        JMenuItem menuitem=menu.add(a);
        if (CampResources.isRightToLeft()) menuitem.setComponentOrientation(ComponentOrientation.RIGHT_TO_LEFT);
        menuitem.setText(it);
        button.setText(null);
        button.setIcon(new CampToolbarIcon(img,this));
        button.setRequestFocusEnabled(false);
        button.setMargin(new Insets(0,0,0,0));
        button.setBorderPainted(false);
        button.setToolTipText(tt);

    }

    private void addCommand(Action a,JMenu menu,String it){
        JMenuItem menuitem=menu.add(a);
        if (CampResources.isRightToLeft()) menuitem.setComponentOrientation(ComponentOrientation.RIGHT_TO_LEFT);
        menuitem.setText(it);

    }

    private void addCommand(Action a,JMenu menu){
        JMenuItem menuitem=menu.add(a);
        if (CampResources.isRightToLeft()) menuitem.setComponentOrientation(ComponentOrientation.RIGHT_TO_LEFT);

    }

    private void addToolbarCommand(Action a,String img,String tt){
        JButton button=toolbar.add(a);
        button.setText(null);
        button.setIcon(new CampToolbarIcon(img,this));
        button.setRequestFocusEnabled(false);
        button.setMargin(new Insets(0,0,0,0));
        button.setBorderPainted(false);
        button.setToolTipText(tt);
    }
    
    private void addBarSeparator(){
        JSeparator jsept = new JSeparator(SwingConstants.VERTICAL);
        //if (CampResources.isRightToLeft()) jsept.setComponentOrientation(ComponentOrientation.RIGHT_TO_LEFT);
        jsept.setMaximumSize(new Dimension(6, 24));
        toolbar.addSeparator(new Dimension(5, 24));
        toolbar.add(jsept);

    }

    
//********************************************************************************
//********************************************************************************
//****                       show status                                      ****
//********************************************************************************
//********************************************************************************
    public void showStatus(String s){
        status.setText(s);
        status.revalidate();
    }

    public void showError(String s){
        JOptionPane op=new JOptionPane();
        op.showMessageDialog(null,s,CampResources.get("Error.Title"),JOptionPane.ERROR_MESSAGE);
    }

    public void showInfo(String s){
        JOptionPane op=new JOptionPane();
        op.showMessageDialog(null,s,CampResources.get("Info.Title"),JOptionPane.INFORMATION_MESSAGE);
    }
    
    private void out(String s){
        System.out.println(s);
        //showStatus(s);
    }

    private Frame findParentFrame(){ 
        Container c = this; 
        while(c != null){ 
          if (c instanceof Frame) 
            return (Frame)c; 
    
          c = c.getParent(); 
        } 
        return (Frame)null; 
      } 

    public Frame getParentFrame(){ 
        return findParentFrame(); 
      } 


   public void about() {
//        JOptionPane op=new JOptionPane();
//        String s= new String("CAMPFIRE 2.0 UTF-8 RC1, Copyright © 1999-2002 MDLF");
//        s= s + "\n" + "Maintained and distributed under GNU GPL by CAMPWARE";
//        s= s + "\n" + "";
//        s= s + "\n" + "Written by:";
//        s= s + "\n" + "Nenad Pandzic";
//        op.showMessageDialog(this,s,"About",JOptionPane.INFORMATION_MESSAGE);
        AboutFrame af= new AboutFrame(this, CampResources.get("HelpMenu.About"));
        af.setVisible(true);
   }

   public void preview() {
        URL userUrl;
        String s= new String("/priv/pub/issues/sections/articles/preview.php");
        String returnal= new String("?");

        returnal= returnal + "Pub=" +  new String(URLEncoder.encode(retFields[2]));   
        returnal= returnal + "&" + "Issue=" +  new String(URLEncoder.encode(retFields[3]));   
        returnal= returnal + "&" + "Section=" +  new String(URLEncoder.encode(retFields[4]));   
        returnal= returnal + "&" + "Article=" +  new String(URLEncoder.encode(retFields[5]));   
        returnal= returnal + "&" + "Language=" +  new String(URLEncoder.encode(retFields[6]));   
        returnal= returnal + "&" + "sLanguage=" +  new String(URLEncoder.encode(retFields[6]));   

        s= s + returnal;
        try{
            userUrl = new URL(getCodeBase(),s); 
            this.getAppletContext().showDocument(userUrl,"_blank");             
        } catch (Exception exc){
            out("Not valid URL");
        }
   }


   public void exitapp() {
        URL userUrl;
        try{
            userUrl = new URL("javascript:window.close()"); 
            this.getAppletContext().showDocument(userUrl);             
        } catch (Exception exc){
            out("Not valid URL");
        }
   }

}