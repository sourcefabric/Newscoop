/*
 * @(#)Test.java
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
     * Test is the main class.
     */


import java.io.File;
import java.applet.*;
import java.awt.*;
import java.awt.event.*;
import java.util.*;
import java.io.FileReader;
import java.net.URL;
import java.net.*;
import com.sun.java.swing.text.*;
import com.sun.java.swing.event.*;
import com.sun.java.swing.undo.*;
import com.sun.java.swing.*;
import tol.unicoded.*;


public class Test extends JApplet{
//********************************************************************************
//********************************************************************************
//****                       Main variables                                   ****
//********************************************************************************
//********************************************************************************
    
    //String segg=new String();
    
    JTextPane textPane=new JTextPane();
    
    UrlChooser urler;
    
    private JScrollPane scrollPane;
    
    private JMenuBar menubar=new JMenuBar();
    
    private JToolBar toolbar=new JToolBar();
    
    private Hashtable actionTable = new Hashtable();
    
     Document doc,unDoc;
    
    private JPanel statusArea = new JPanel();

    private JPanel infoArea = new JPanel();
    
    private JPanel holderArea = new JPanel();

    private JLabel status = new JLabel("html editor running");
    
    private JLabel info = new JLabel("info label");
    
    String previewURL;
    
    DumperFrame dumpFrame,clipFrame;
    
    JCheckBoxMenuItem isJustified;
    
    String artindex="";
    

    
    Container contentPane;
    
    int dot=0,mark=0;
    
//    JButton mod;
    
    TolHtmlEditorKit htmleditorkit;
    
    boolean center=false,right=false;
    
    int nrofDictionaryWords=0;
    
    StringBuffer toHtml;
    
    int knownProperties=13;
    
    String dictionary[];
    
    int tagcounter;
    
    boolean visibleImages[];
    boolean visibleBLinks[];
    boolean visibleELinks[];
    int idxBLinks[];
    int idxELinks[];
    LinkControl LinkControlListB[];
    LinkControl LinkControlListE[];
    
    boolean modified=false;
    
    String pseutags[];
    String otags[];
    String ctags[];
    
    String tolescape="#";
    
    String linkscript;

	int nrofFields=8;
	String retFields[]=new String[nrofFields];
	
	table0 table0;
	table1 table1;
	table2 table2;
	table3 table3;
	table4 table4;
	table5 table5;
	table6 table6;
	table7 table7;
	table8 table8;
	table9 table9;
	tableA tableA;
	tableB tableB;
	tableC tableC;
	tableD tableD;
	tableE tableE;
	tableF tableF;
	
String[] fieldNames=new String[]{
"UserId",
"UserKey",
"IdPublication",
"NrIssue",
"NrSection",
"NrArticle",
"IdLanguage",
"Field"   };
    
    Color backColor=Color.white;
    Color foreColor=Color.black;
    Color dictColor=Color.red;
    
    
    MediaTracker tracker;
    Image bigim;
    
    String contentString;
    
    boolean stopping,dialogShow;
    Vector imageList;
    Vector titleList;
    Vector linkBList,linkEList;
    int linknr;
    
    //InputFrame iform;
    
    int defaultport=80;
    
    int port=defaultport;
    
    URL imagepath=null;  
    
    boolean firsttime=true;
    
    Vector vectorOfImages,vectorOfImagePseudos;
    
    boolean debugVer=false,clipVer=false;
    
    FontColorChooser colorChooser;

	String scriptString="/bin/script";
	
	WordFrame wordframe;
	InternalLinkFrame ilframe;
	ImageFrame imframe;
	
	boolean debugworking=false;
	

    //int foundImage;
   // CustomAction clearAttr;
   
   
    private String[] cutCopyPasteActionNames=new String[]{
        DefaultEditorKit.cutAction,"Cut","cut.gif",
        DefaultEditorKit.copyAction,"Copy","copy.gif",
        DefaultEditorKit.pasteAction,"Paste","paste.gif",
        "select-all","Select All","selall.gif"
    };

    String[] styleActionNames=new String[]{
        "font-italic","Italic","italic.gif",
        "font-bold","Bold","bold.gif",
        "font-underline","Underline","underline.gif"
    };

    private String[] alignActionNames=new String[]{
        "left-justify","Left","left.gif",
        "center-justify","Center","center.gif",
        "right-justify","Right","right.gif",
        "dump-model","Dump model to System.out","model.gif"
    };

    boolean newLine=false;
    
    //JMenu dictionaryMenu1,dictionaryMenu2,dictionaryMenu3;
    
    //String sessionID="invalid sessionID";
    String succesfully;
    boolean upOK=false;
    
    UndoManager undoManager=new UndoManager();
    UndoAction undoAction =new UndoAction(this);
    RedoAction redoAction =new RedoAction(this);
    
    
     //Action centerAction;
    

    
//	boolean properties[]=new boolean[knownProperties];

    
    
//********************************************************************************
//********************************************************************************
//****                       Constructor                                      ****
//********************************************************************************
//********************************************************************************
    public Test(){
        contentPane=getContentPane();
        //populate();
        //readFile();
        //setJMenuBar(menubar);
        
        //textPane.setEditorKit(new TolHtmlEditorKit());
    }
//********************************************************************************
//********************************************************************************
//****                       Read File                                        ****
//********************************************************************************
//********************************************************************************
    private void readFile(){
        try{
            textPane.getEditorKit().read(new FileReader("text.html"),textPane.getDocument(),0);
//            String url="file:"+System.getProperty("user.dir")+System.getProperty("file.separator")+"text.html";
            //String url="file:///c:/text.html";
            //String url="file://text.html";
            //System.out.println(url);
//            textPane.setPage(url);
        }
        catch(Exception ex) {ex.printStackTrace();}
    }
    
//********************************************************************************
//********************************************************************************
//****                       populate                                         ****
//********************************************************************************
//********************************************************************************
    private void populate(){
        JMenu editMenu=new JMenu("Edit"),
              styleMenu=new JMenu("Style"),
              alignMenu=new JMenu("Align"),
              //formMenu=new JMenu("Form"),
              imageMenu=new JMenu("Insert"),
              //\u0351\u0355\u0369\u1086\u1086\u1087\u1075\u1096\u1097
              fontSizeMenu=new JMenu("Font Size"),
              netMenu=new JMenu("Net"),
              fileMenu=new JMenu("File"),
              optMenu=new JMenu("Other Options");
              //fontSizeMenu.setFont(new Font("Monospaced",Font.PLAIN,12));
              //clearMenu=new JMenu("Clear"),
              //fontColorMenu=new JMenu("Color"),
              //justifyMenu=new JMenu("Justify");
/*        try{
        imagepath=new URL(getCodeBase(),"resources/");
        //imagepath=new URL(getCodeBase(),"");
        }
        catch(Exception e){}*/
//        System.out.println(imagepath);
        //imagepath="resources"+System.getProperty("file.separator");

        
        
        addCommand(new CustomAction("New",CustomAction.NEW,this),fileMenu,"new.gif","New");
        //addCommand(new CustomAction("Revert",CustomAction.RE,this),fileMenu,"re.gif","Revert to starting version");
        toolbar.addSeparator();
		
	int startidx=0;
	if (clipVer) startidx=9;

        for(int i=startidx; i<cutCopyPasteActionNames.length; ++i) {
            Action action=getAction(cutCopyPasteActionNames[i]);
            if (action!=null) {
                JButton button=toolbar.add(action);
                JMenuItem item=editMenu.add(action);
                item.setText(cutCopyPasteActionNames[++i]);
                
                button.setText(null);
                button.setToolTipText(cutCopyPasteActionNames[i]);
                button.setIcon(new TolImageIcon(cutCopyPasteActionNames[++i],bigim,this));
                //button.setIcon(new ImageIcon(buildURL(cutCopyPasteActionNames[++i])));
/*                try{
                button.setIcon(new TolImageIcon(new URL(cutCopyPasteActionNames[++i])));
                }
                catch (Exception e){}*/
                button.setRequestFocusEnabled(false);
                button.setMargin(new Insets(1,1,1,1));
            }
        }
        
        editMenu.addSeparator();
        //editMenu.add(undoAction);
        //editMenu.add(redoAction);
        toolbar.addSeparator();
        addCommand(undoAction,editMenu,"undo.gif","Undo");
        addCommand(redoAction,editMenu,"redo.gif","Redo");
        toolbar.addSeparator();

        if (clipVer)
        {
        addCommand(new CustomAction("Clipboard",CustomAction.CLIP,this),editMenu,"clip.gif","Clipboard operations");
        }

        toolbar.addSeparator();
        
        for(int i=0; i<styleActionNames.length; ++i) {
            Action action=getAction(styleActionNames[i]);
            if (action!=null) {
                JButton button=toolbar.add(action);
                JMenuItem item=styleMenu.add(action);
                item.setText(styleActionNames[++i]);
                
                button.setText(null);
                button.setToolTipText(styleActionNames[i]);
                button.setIcon(new TolImageIcon(styleActionNames[++i],bigim,this));
                //button.setIcon(new ImageIcon(buildURL(imagepath+styleActionNames[++i])));
                button.setRequestFocusEnabled(false);
                button.setMargin(new Insets(1,1,1,1));
                
            }
        }

        toolbar.addSeparator();
        int alignCount=alignActionNames.length;
        if (!debugVer) alignCount-=3;
  //      System.out.println(alignCount);
        for(int i=0; i<alignCount; ++i) {
            Action action=getAction(alignActionNames[i]);
            if (action!=null) {
                JButton button=toolbar.add(action);
                JMenuItem item=alignMenu.add(action);
                item.setText(alignActionNames[++i]);
                
                button.setText(null);
                button.setToolTipText(alignActionNames[i]);
                button.setIcon(new TolImageIcon(alignActionNames[++i],bigim,this));
                //button.setIcon(new ImageIcon(buildURL(imagepath+alignActionNames[++i])));
                button.setRequestFocusEnabled(false);
                button.setMargin(new Insets(1,1,1,1));
            }
        
        }
        
        toolbar.addSeparator();
        
        //addCommand(new FontColorStyleAction("font color ...",textPane),fontColorMenu,buildURL("color.gif"),"Font Color Chooser");
        addCommand(new CustomAction("Select font color",CustomAction.COLOR,this),optMenu,"color.gif","Font Color Chooser");
        toolbar.addSeparator();
        //clearAttr=new CustomAction("all attributes",CustomAction.CLEAR,this);
        addCommand(new CustomAction("Clear all attributes",CustomAction.CLEAR,this),optMenu,"clearall.gif","Clear attributes");
        toolbar.addSeparator();
        
        addCommand(new CustomAction("Create Keyword",CustomAction.WORD,this),imageMenu,"keyword.gif","Create Keyword");
        addCommand(new CustomAction("Create Subhead",CustomAction.TITLE,this),imageMenu,"title.gif","Create Subhead");
        addCommand(new CustomAction("Insert Image",CustomAction.IMAGE,this),imageMenu,"image.gif","Insert New Image");
        addCommand(new CustomAction("Create External Link",CustomAction.EXTLINK,this),imageMenu,"link.gif","Create External Link");
        addCommand(new CustomAction("Create Internal Link",CustomAction.INTLINK,this),imageMenu,"intlink.gif","Create Internal Link");
        //addCommand(new CustomAction("Spaces",CustomAction.SPACE,this),imageMenu,"space.gif","Insert Spaces");
//        imageMenu.add(new CustomAction("Spaces",CustomAction.SPACE,this));
       // toolbar.addSeparator();
       // addCommand(new CustomAction("View Form",CustomAction.FORM,this),formMenu,buildURL("form.gif"),"View Form");
        toolbar.addSeparator();
        
//        addCommand(new CustomAction("Preview",CustomAction.PREVIEW,this),netMenu,buildURL("preview.gif"),"Preview");
        addCommand(new CustomAction("Upload",CustomAction.UPLOAD,this),netMenu,"upload.gif","Upload");
        if (debugVer)
        {
        toolbar.addSeparator();
        addCommand(new CustomAction("to System.out",CustomAction.DUMP,this),netMenu,"dump.gif","Dump to editbox");
        addCommand(new CustomAction("regenerate Html",CustomAction.SETHTML,this),netMenu,"html.gif","Regenerate HTML");
        }
         
        

        
//        dictionaryMenu1=new JMenu("A - I");
//        dictionaryMenu2=new JMenu("J - R");
//        dictionaryMenu3=new JMenu("S - Z");
/*
        for(char a='A';a<='Z';a++)
        {
            StringBuffer sb=new StringBuffer();
            sb.append(a);
            JMenu tjm=new JMenu(new String(sb));
        for(int i=0;i<nrofDictionaryWords+1;i++)
        {
            if (((htmleditorkit.dictionaryWords[i]).toUpperCase()).charAt(0)==a)
            {
            /*JComponent v=*///tjm.add(htmleditorkit.dictionaryActions[i]);
            //v.setFont(new Font("Arial",Font.PLAIN,10));
            //System.out.println(htmleditorkit.dictionaryWords[i]);
            /*
            }
        }   
            int o=a-65;
            if (o<9) dictionaryMenu1.add(tjm);
                else 
                if (o<18) dictionaryMenu2.add(tjm);
                    else dictionaryMenu3.add(tjm);
        }
  */      
        fontSizeMenu.add(new FontSizeStyleAction("font size 1",textPane,"1"));
        fontSizeMenu.add(new FontSizeStyleAction("font size 2",textPane,"2"));
        fontSizeMenu.add(new FontSizeStyleAction("font size 3",textPane,"3"));
        fontSizeMenu.add(new FontSizeStyleAction("font size 4",textPane,"4"));
        fontSizeMenu.add(new FontSizeStyleAction("font size 5",textPane,"5"));
        fontSizeMenu.add(new FontSizeStyleAction("font size 6",textPane,"6"));
        fontSizeMenu.add(new FontSizeStyleAction("font size 7",textPane,"7"));
        
        isJustified=new JCheckBoxMenuItem("Justify all");
        optMenu.add(isJustified);
        

        
        menubar.add(editMenu);
        menubar.add(alignMenu);
        //menubar.add(justifyMenu);
        menubar.add(styleMenu);
        menubar.add(fontSizeMenu);
        //menubar.add(fontColorMenu);
        menubar.add(optMenu);
        menubar.add(imageMenu);
        //menubar.add(formMenu);
        menubar.add(netMenu);
/*        menubar.add(dictionaryMenu1);
        menubar.add(dictionaryMenu2);
        menubar.add(dictionaryMenu3);
        */
        
        
        
    }

        private void addCommand(Action a,JMenu menu,String img,String tt){
            JButton button=toolbar.add(a);
            JMenuItem menuitem=menu.add(a);
        button.setText(null);
        button.setIcon(new TolImageIcon(img,bigim,this));
        button.setRequestFocusEnabled(false);
        button.setMargin(new Insets(1,1,1,1));
        button.setToolTipText(tt);
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
//            System.out.println(actions[i].getValue(Action.NAME));
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
	try {	    
	    UIManager.setLookAndFeel(UIManager.getCrossPlatformLookAndFeelClassName());	    
	    } 
	catch (Exception exc) {	    
	    System.err.println("Error loading L&F: " + exc);	
	    }        
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
    public void showInfo(String s){
        if (dialogShow)
        {
    JOptionPane op=new JOptionPane();
    op.showMessageDialog(null,s,"TOL editor - Stop",JOptionPane.ERROR_MESSAGE);
        }
        else
        {
        info.setText(s);
        info.revalidate();
        status.revalidate();
        }

        
        //System.out.println("info: "+s);
    }
    
//********************************************************************************
//********************************************************************************
//****                       read Dictionary                                  ****
//********************************************************************************
//********************************************************************************
    private void readDictionary(){
        int ord=0;
        String s;
        final String dict="tol#";
        //System.out.println("Begin dictionary");
        while((s=getParameter(dict+ord))!=null){
//            System.out.println("<"+s+">");
            ord++;
        }
        nrofDictionaryWords=ord;
        ord=0;
        dictionary=new String[nrofDictionaryWords];
        //System.out.println("Begin dictionary");
        while((s=getParameter(dict+ord))!=null){
            dictionary[ord]=new String(s);
            ord++;
        }
        //System.out.println("Begin dictionary");
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
if (firsttime)
{
    
        //UnicodeTables=new table[15];
        urler=new UrlChooser("Choose URL",this);
        try{
        imagepath=new URL(getCodeBase(),"resources/");
        //imagepath=new URL(getCodeBase(),"");
        }
        catch(Exception e){}
    
        tracker=null;
        tracker=new MediaTracker(this);
        
                    try{
            URL imurl=buildURL("big.gif");
            bigim=null;
            bigim=fetchImage(imurl,0);
                    }
	        catch (Exception e) {
                out("Error getting image:"+e);
    	            }



    //System.out.println(buildURL("big.gif"));


        imageList=new Vector();
        titleList=new Vector();
        linkBList=new Vector();
        linkEList=new Vector();
        linknr=0;
        //textPane.setFont(new Font("Courier",Font.PLAIN,12));
        scrollPane=new JScrollPane(textPane);
        
	    visibleImages=new boolean[imageList.size()];
        
        
        //scrollPane.add(textPane);
        previewURL=new String("voidURL");
        
//        JScrollPane scrollPane=(textPane);
        if (getParameter("debug")!=null) debugVer=true; else debugVer=false;
        if (getParameter("clip")!=null) clipVer=true; else clipVer=false;
        artindex=getParameter("idx");
        
        readDictionary();
        readImages();
        readColors();
    	readFields();
        htmleditorkit=new TolHtmlEditorKit(this,dictionary,tolescape);
        //htmleditorkit.buildDictionaryActions(dictionary);
        textPane.setBackground(backColor);
        textPane.setForeground(foreColor);

        
        constructTranslator();
        
        textPane.setEditorKit(htmleditorkit);
        htmleditorkit.install(textPane);
        loadActionTable();
        
        
        populate();
        colorChooser=new FontColorChooser("Choose a color",this,buildURL("colors.jpg"));
        //readFile();
        setJMenuBar(menubar);
        
//        String tempID=getParameter("sessionid");
//        if (tempID!=null) sessionID=tempID;
//        System.out.println("sID: "+sessionID);
        
        contentPane.add(toolbar, BorderLayout.NORTH);
//        mod=new JButton("model");
//        toolbar.add(mod);
        //contentPane.add(new JScrollPane(textPane),BorderLayout.CENTER);
        contentPane.add(scrollPane,BorderLayout.CENTER);
        
/*        mod.addActionListener(new ActionListener(){
            public void actionPerformed(ActionEvent e){
                dumper();
            }
            });*/
        
        linkscript=getParameter("linkscript");    
            
        String po=getParameter("port");
        
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
            
        contentPane.add(holderArea,BorderLayout.SOUTH);
        holderArea.setLayout(new BorderLayout());
        holderArea.add(statusArea,BorderLayout.NORTH);
        holderArea.add(infoArea,BorderLayout.SOUTH);
        //statusArea.setLayout(new BorderLayout());
        infoArea.setLayout(new BorderLayout());
        statusArea.add(status,BorderLayout.CENTER);
        infoArea.add(info,BorderLayout.CENTER);
        holderArea.setBorder(BorderFactory.createEtchedBorder());    
        textPane.requestFocus();
        
        //openInputForm();
        dumpFrame=new DumperFrame(this);
        clipFrame=new DumperFrame(this);
        firsttime=false;
        unDoc=textPane.getDocument();
                unDoc.addUndoableEditListener(new UndoableEditListener(){
            public void undoableEditHappened(UndoableEditEvent e){
                undoManager.addEdit(e.getEdit());
                undoAction.update();
                redoAction.update();
                //System.out.println("a");
            }
            });
        imframe=new ImageFrame("Image",this,vectorOfImages);
        wordframe=new WordFrame("KeyWords",dictionary,400,300,this);
        ilframe=new InternalLinkFrame("Internal Link",400,550,this);

        ilframe.links[0].setValues(ilframe.contact(0));
        ilframe.links[0].valid=true;

            
        textPane.getDocument().addDocumentListener(new DocumentListener(){
            public void insertUpdate(DocumentEvent e){
               // System.out.println("insert update");
               modify();
                }
            public void changedUpdate(DocumentEvent e){
                //System.out.println("change update");
                }
            public void removeUpdate(DocumentEvent e){
                //System.out.println("remove update");
                modify();
                //if (e.getLength()>1) 
                    //System.out.println();
                    debugSwing();
                }
            });
            
            
/*            
        contentString=new URLDecoder().decode(contentString);
        //textPane.setText(contentString);
        TolHtmlParser localParser=new TolHtmlParser(textPane,this,contentString);
        localParser.JavaUnicoder();    
        if (contentString!=null) localParser.parseHtml();    */
        //if (contentString!=null) textPane.setText("\u0065\u043f\u0065");

        //ilframe.open();
        if ((contentString!=null)&&(contentString.length()!=0))
        try{
        //textPane.getDocument().insertString(0,"You must press the Revert button to start editing !",null);
        textPane.insertComponent(new Starter(this));
	    CustomAction b=new CustomAction("",CustomAction.CENTER,this);
  	    b.actionPerformed(new ActionEvent(textPane,0,""));
        }
        catch(Exception e){
        }
}       
    }
    
    
    private void modify(){
               modified=true;
    }
    
//********************************************************************************
//********************************************************************************
//****                       create speed button                              ****
//********************************************************************************
//********************************************************************************
    private JButton createSpeedButton(String image){
         JButton button=new JButton();
         button.setText(null);
         button.setIcon(new ImageIcon(image));
         button.setRequestFocusEnabled(false);
         button.setMargin(new Insets(1,1,1,1));
         return button;
    }

    private void constructTranslator(){
        knownProperties=nrofDictionaryWords+12+1+1/*title*/;
        
        pseutags=new String[knownProperties];
        otags=new String[knownProperties];
        ctags=new String[knownProperties];
        
        tagcounter=0;
        
        addtr("name"+tolescape+htmleditorkit.titleKey,"<!** Title>","<!** EndTitle>");
        addtr("bold","<B>","</B>");
        addtr("italic","<I>","</I>");
        addtr("underline","<U>","</U>");
        addtr("size8","<FONT SIZE=1>","</FONT>");
        addtr("size10","<FONT SIZE=2>","</FONT>");
        addtr("size12","<FONT SIZE=3>","</FONT>");
        addtr("size14","<FONT SIZE=4>","</FONT>");
        addtr("size18","<FONT SIZE=5>","</FONT>");
        addtr("size24","<FONT SIZE=6>","</FONT>");
        addtr("size36","<FONT SIZE=7>","</FONT>");
        addtr("Alignment1","<CENTER>","</CENTER>");
        addtr("Alignment2","<DIV ALIGN=RIGHT>","</DIV>");
        for(int i=0;i<nrofDictionaryWords;i++) //-1 title
            addtr("name"+tolescape+dictionary[i],"<!** Class \""+dictionary[i]+"\">","<!** EndClass>");
  //      addtr("name"+tolescape+htmleditorkit.titleKey,"<!** Title>","<!** End Title>");
            

//        addtr("","<X>","</X>");
        addtr("","","");
    }
    
    private void addtr(String ps,String op,String cl){
        pseutags[tagcounter]=new String(ps);
        otags[tagcounter]=new String(op);
        ctags[tagcounter]=new String(cl);
        tagcounter++;
    }
    
    private void readColors(){
        String s;
        if ((s=getParameter("background"))!=null)
        {
        ColorConverter a=new ColorConverter(s);
        backColor=a.getColor();
        }
        /*if ((s=getParameter("foreground"))!=null)
        {
        ColorConverter a=new ColorConverter(s);
        foreColor=a.getColor();
        }*/
        if ((s=getParameter("wordcolor"))!=null)
        {
        ColorConverter a=new ColorConverter(s);
        dictColor=a.getColor();
        }
    }
    
    private void openInputForm(){
        /*
        String text=getParameter("inputtext");
        String title=getParameter("inputheader");
        String close=getParameter("inputclose");
        String width=getParameter("inputformwidth");
        String height=getParameter("inputformheight");
        String alltext=getParameter("inputformalerttextfield");
        String allcombo=getParameter("inputformalertcombobox");
        if (width==null) width="500";
        if (height==null) height="400";
        int w=500;
        int h=400;
        
        try{
            w=new Integer(width).intValue();
            h=new Integer(height).intValue();
        }
        catch(Exception e){}
        
        if (title==null) title=new String("Please complete this form");
        if (text==null) text=new String("Form");
        if (close==null) close=new String("OK");
        if (alltext==null) alltext=new String("You must complete the field ");
        if (allcombo==null) allcombo=new String("You must select a value for ");
        iform=new InputFrame(title,text,close,alltext,allcombo,w,h);
        String s="";
        
        int ord=0;
        
       while((s=getParameter("input"+ord))!=null){
            //System.out.println(s);
            iform.addNewInput(s);
            ord++;
        }
        iform.finishForm();
        iform.setVisible(true);*/
    }
   /* 
    public void preview(){
        
 //       if (!iform.parseInputs())
 //           return;
        showInfo("Preview");
        System.out.println("Preview");
        for (int y=1;y<3;y++)
        System.out.println();
        toHtml=new StringBuffer("");
        doc=textPane.getDocument();
        
        HtmlGenerator hg=new HtmlGenerator(doc,toHtml,this);
        hg.generate();
//        for (int i=0;i<iform.fields.size();i++)
//            System.out.println(iform.getValue(i));
		if (debugVer) System.out.println(new String(toHtml));
		Communicator comm=new Communicator(this,port);
		if (comm.connect())
		{
		StringBuffer sb=new StringBuffer();
		sb.append((char)6);
   /*     for (int i=0;i<iform.fields.size();i++)
            {
            String p=iform.getValue(i);
            if (p!=null) comm.write(p+'\n');
                else comm.write("\n");
            }*/
		/*comm.write(new String(sb));//6
		comm.write("P");
		comm.write(new String(sb));//6
		comm.write(new String(toHtml));
		comm.write(new String(sb));//6
		previewURL=comm.read();
		System.out.println(previewURL);
		comm.close();
		showInfo("Preview available at : "+previewURL);
		//openPreview(previewURL);
		}
		else
		    showInfo("Can't connect to server");
   }
   
   */

    
    public void upload(){
/*        if (!iform.parseInputs())
            return;*/
        
        showInfo("Upload");
        //System.out.println("Upload");
        toHtml=new StringBuffer("");
        doc=textPane.getDocument();
        HtmlGenerator hg=new HtmlGenerator(doc,toHtml,this,false);
        hg.generate();
		//if (debugVer) System.out.println(new String(toHtml));
		Communicator comm=new Communicator(this,port);
		if (comm.connect())
		{
		StringBuffer header=new StringBuffer();
		header.append("POST "+scriptString+" HTTP/1.0\r\n");
		header.append("Host: "+getCodeBase().getHost()+":"+port+"\r\n");
		header.append("Pragma: no-cache\r\n");
		header.append("Cache-control: no-cache\r\n");
		header.append("User-Agent: TOLCommunicator\r\n");
		header.append("Content-type: application/x-www-form-urlencoded\r\n");

	
		    
	StringBuffer fields=new StringBuffer();
	int len=0;


	for (int k=0;k<nrofFields;k++)
	{
		StringBuffer tempfields=new StringBuffer();
		tempfields.append(fieldNames[k]);
		tempfields.append("=");
		tempfields.append(new String(URLEncoder.encode(retFields[k])));
		String returnal=new String(tempfields);
		fields.append(returnal+"&");
		len+=(returnal.length()+1);
		//System.out.println(""+len+">"+fields);
	}

		StringBuffer body=new StringBuffer();
		body.append("Content=");
		//body.append(toHtml);

		body.append(URLEncoder.encode(new String(toHtml)));
		String bodyString=new String(body);

		len+=bodyString.length();
		header.append("Content-length: "+len);

		
		comm.write(header);
		comm.write("\r\n\r\n");
		comm.write(new String(fields)+bodyString);
		//System.out.println(header+new String(fields)+bodyString);
		//comm.write("\r\n\r\nend");
		
		succesfully=comm.read();
		comm.close();
		System.out.println(succesfully);
		if (comm.okTrans) upOK=true; else upOK=false;
		//upOK=true;
		
		if (stopping) dialogShow=true;
		//System.out.println("dS"+dialogShow);
		if (upOK) 
		{
		    showInfo("Upload succesful");
		    modified=false;
		}
		 else   
		 {
		  if (!comm.locked)  showInfo("Upload unsuccesful");
		  else 
		  {
		    stopping=true;
		    dialogShow=true;
		    showInfo("You can't upload because the article is locked !");
		    stopping=false;
		    dialogShow=false;
		  }
		 }
		}

		
		else//comm.connect
		{
		    if (stopping) dialogShow=true;
		    showInfo("Can't connect to server");
		}
		    
   }
    
    public void dump(){
       /* System.out.println("Dump");
        for (int y=1;y<3;y++)
        System.out.println();*/
 /*       for (int i=0;i<iform.fields.size();i++)
            System.out.println(iform.getValue(i));*/
        /*for (int y=1;y<3;y++)
        System.out.println();*/
        toHtml=new StringBuffer("");
        doc=textPane.getDocument();
        
        HtmlGenerator hg=new HtmlGenerator(doc,toHtml,this,false);
        hg.generate();
		//System.out.println(new String(toHtml));
		dumpFrame.setVisible(true);
		
		//dumpFrame.setText(new String(toHtml));
		dumpFrame.setText(URLEncoder.encode(new String(toHtml)));
		dumpFrame.t.append("\n");
		dumpFrame.t.append(new String(toHtml));
   }

    public void clip(){
		clipFrame.setVisible(true);
		//clipFrame.setText(new String(toHtml));
   }
   
   
   /*
   private void openPreview(String urls){
    JSObject win=null;
    try{
    win=JSObject.getWindow((Applet)this);
    }
    catch(Exception e)
    {
        System.out.println("Can't create JSObject");
    }
    if (win!=null)
    {
        win.eval("window.open('"+urls+"','pr')");
    }

   }
   */
   public String getPRURL(){
    return previewURL;
   }

   public int getOK(){
    int k=1;
    if (previewURL.equals("voidURL")) k=0;
    return k;
   }
   
   public void previewPressed(){
    showInfo("opening new Window for preview");
    previewURL=new String("voidURL");
   }
 /*  
   public void previewOldURL(){
    JOptionPane op=new JOptionPane();
    showInfo("");
    op.showMessageDialog(this,"You must press the Preview button, then the link");
   }
   */
   public ImageControl insertImage(boolean insertSpace){
    ImageControl im=new ImageControl(this);
    if (insertSpace)
        try{
        textPane.getDocument().insertString(textPane.getCaretPosition()," ",null);
        textPane.setCaretPosition(textPane.getCaretPosition()-1);
        }
        catch (Exception e){
            System.out.println(e);
        }
    insertComponentTo(im);
    if (insertSpace)
        textPane.setCaretPosition(textPane.getCaretPosition()+1);
    im.addIcon(imagepath+"image.gif");
    //im.addRemover(imagepath+"image.gif");
    im.addCombo(vectorOfImages,vectorOfImagePseudos);
    imageList.addElement(im);
    textPane.revalidate();
    return im;
   }

	public void openImageFrame(ImageControl i){
		imframe.open(i);
	}
   
   public SpaceControl insertSpace(){
    SpaceControl im=new SpaceControl(this);
    im.value.setDocument(new SpaceFieldDocument(im.value));
    im.value.setText("1");
    insertComponentTo(im);
    imageList.addElement(im);
    textPane.revalidate();
    return im;
   }

   public void insertLink(int kind,String url,String target,boolean insertSpace){
    //insertSpace();
    //return null;
    //kind=LinkControl.EXT;
    int ss=textPane.getSelectionStart();
    int se=textPane.getSelectionEnd();
    if (ss==se)
    {
        showStatus("You must select something in order to insert a link");
        return;
    }
    if (ss>se)
    {
        int swap=se;
        se=ss;
        ss=se;
    }
    try{
    textPane.setCaretPosition(ss);
    }
    catch(IllegalArgumentException e)
    {return;}
    LinkControl im=new LinkControl(this,kind,LinkControl.BEGIN,linknr);
    insertComponentTo(im);
    try{
    textPane.setCaretPosition(se+1);
    }
    catch(IllegalArgumentException e)
    {im.dontUse();
    return;
    }
    LinkControl im2=new LinkControl(this,kind,LinkControl.END,linknr);
   
    if (insertSpace)
        try{
        textPane.getDocument().insertString(textPane.getCaretPosition()," ",null);
        textPane.setCaretPosition(textPane.getCaretPosition()-1);
        }
        catch (Exception e){
            System.out.println(e);
        }

    
    insertComponentTo(im2);
    if (insertSpace)
        textPane.setCaretPosition(textPane.getCaretPosition()+1);
    
    
    im.setPair(im2);
    im2.setPair(im);
    linkBList.addElement(im);
    linkEList.addElement(im2);
    linknr++;
    if ((target!=null)&&(!target.equals("")))
    {
	if (insertSpace)
	{
        	im.setTarget(target);
	        im2.setTarget(target);
	}
	else
	{
		im.setTrueTarget(target);
		im2.setTrueTarget(target);
	}

    }
    
    if (url!=null)
    {
        if (kind==LinkControl.EXT)
            im.setUrl(url,true);
        else
            im.setIDS(url);
    }
    textPane.revalidate();
    
   }
 
    private void readImages(){
        int ord=0;
        String s;
        final String im="image";
        vectorOfImages=new Vector();
        vectorOfImagePseudos=new Vector();
        while((s=getParameter(im+ord))!=null){
            vectorOfImagePseudos.addElement(s.substring(0,s.indexOf(",")));
            vectorOfImages.addElement(s.substring(s.indexOf(",")+1));
            ord++;
        }
    }
    
    public void newFile(boolean ask){
        //scrollPane.remove(textPane);
        //remove(scrollPane);
        //textPane.setVisible(false);
        //textPane=new JTextPane();
        //textPane.setVisible(true);
        //scrollPane.add(textPane);
        //scrollPane=new JScrollPane(textPane);
        if (ask)
        {
    JOptionPane op=new JOptionPane();
    int selV=op.showConfirmDialog(this,"Do you really want to create a new document by deleting all the content?","New document",JOptionPane.OK_CANCEL_OPTION);
    if (selV==JOptionPane.CANCEL_OPTION) return;
        }    
        textPane.setText("");
        isJustified.setState(false);

        //textPane.removeCharacterAttributes();
        for (int i=0;i<titleList.size();i++)
        {
            if (titleList.elementAt(i) instanceof TitleControl)
                ((TitleControl)titleList.elementAt(i)).setVisible(false);
        }
        for (int i=0;i<imageList.size();i++)
        {
            if (imageList.elementAt(i) instanceof ImageControl)
                ((ImageControl)imageList.elementAt(i)).setVisible(false);
            if (imageList.elementAt(i) instanceof SpaceControl)
                ((SpaceControl)imageList.elementAt(i)).setVisible(false);
        }
        imageList=new Vector();    
        linknr=0;
        linkBList=new Vector();
        linkEList=new Vector();
        
        //contentPane.add(scrollPane,BorderLayout.CENTER);
       //System.out.println("manan");
    }
public URL buildURL(String a)
{
    URL Im=null;
        try{
            Im=new URL(imagepath,a);
        }
        catch(Exception e){}
    return Im;    
}

public void fontColor(){
    colorChooser.setTP(textPane);
    colorChooser.nonef=false;
    colorChooser.setVisible(true);
}

public void fontClear(){
    FontColorStyleAction a=new FontColorStyleAction("NULLOL",textPane,colorChooser,"");
    a.actionPerformed(new ActionEvent(textPane,1,""));
}

public void createTitle(boolean is){
    int ss=textPane.getSelectionStart();
    int se=textPane.getSelectionEnd();
   
    if (ss==se)
    {
        showStatus("You must select something in order to create a subhead");
        return;
    }
    
    if (ss>se)
    {
        int swap=se;
        se=ss;
        ss=se;
    }

    if (is)
   { 
    try{
    textPane.setCaretPosition(ss);
    }
    catch(IllegalArgumentException e)
    {return;}
/*
        try{
        textPane.getDocument().insertString(textPane.getCaretPosition(),"\n\n",null);
        textPane.setCaretPosition(textPane.getCaretPosition()-1);
        }
        catch (Exception e){
            System.out.println(e);
            }
            
            */
   }
    else
    {
    try{
    textPane.setCaretPosition(ss);
    }
    catch(IllegalArgumentException e)
    {return;}
    	}

    TitleControl im=new TitleControl(this);
    insertComponentTo(im);
    titleList.addElement(im);


    try{
    textPane.setCaretPosition(se+1);
    }
    catch(IllegalArgumentException e)
    {return;}
    	
    TitleControl im2=new TitleControl(this);
    insertComponentTo(im2);
    titleList.addElement(im2);

/*
    
    if (is)
   {
    try{
    textPane.setCaretPosition(se+3);
    }
    catch(IllegalArgumentException e)
    {return;}
        try{
        textPane.getDocument().insertString(textPane.getCaretPosition(),"\n\n",null);
        textPane.setCaretPosition(se+3);
        textPane.setSelectionStart(se+3);
        textPane.setSelectionEnd(se+4);
        fontClear();
        textPane.setCaretPosition(se+3);
        }
        catch (Exception e){
            System.out.println(e);
        }
    }    */
	}

public void setTitle(){
    Action tit=htmleditorkit.dictionaryActions[nrofDictionaryWords];
    tit.actionPerformed(new ActionEvent(textPane,1,""));
	createTitle(true);    
    
	}
	
public void setWord(int idx){
    Action tit=htmleditorkit.dictionaryActions[idx];
    tit.actionPerformed(new ActionEvent(textPane,1,""));
	}

public void openWord(){
    int ss=textPane.getSelectionStart();
    int se=textPane.getSelectionEnd();
    if (ss==se)
    {
        showStatus("You must select something in order to create a dictionary word");
        return;
    }
        wordframe.open();
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
        //System.out.println(dot-mark);
        textPane.getDocument().insertString(dot,s,null);
        }
        catch(Exception e)
        {
            System.out.println("ex: "+e
);
        }
    }

	public void readFields(){

	String d;
	if ((d=getParameter("script"))!=null) scriptString=d;
	String s=new String();
	for (int i=0;i<nrofFields;i++)
	{
	s=getParameter(fieldNames[i]);
	if (s==null) retFields[i]=new String(""); else retFields[i]=new String(s);
	//System.out.println("loaded "+i+" "+fieldNames[i]+"="+retFields[i]);
	}
	contentString=getParameter("Content");
	//System.out.println("v"+contentString);
	}
	
	public void setHtml(){
        //int r=textPane.getText().charAt(1);
        //System.out.println(r);
	    
	    StringBuffer fromHtml=new StringBuffer();
        HtmlGenerator hg=new HtmlGenerator(textPane.getDocument(),fromHtml,this,false);
        hg.generate();
        //System.out.println(fromHtml);
        
	    TolHtmlParser pars=new TolHtmlParser(textPane,this,new String(fromHtml));
	    pars.JavaUnicoder();
	    pars.parseHtml();
        
	}

	
	public void debugSwing(){
	    //if(debugworking) return;
	    //debugworking=true;
	    //System.out.print("*");
	    visibleImages=new boolean[imageList.size()];
	    visibleBLinks=new boolean[linknr];
	    visibleELinks=new boolean[linknr];
	    idxBLinks=new int[linknr];
	    idxELinks=new int[linknr];
	    LinkControlListB=new LinkControl[linknr];
	    LinkControlListE=new LinkControl[linknr];
	    //System.out.println("de");
	    StringBuffer temper=new StringBuffer();
        HtmlGenerator hg=new HtmlGenerator(textPane.getDocument(),temper,this,true);
        hg.generate();

	    for(int i=0;i<titleList.size();i++)
                ((TitleControl)titleList.elementAt(i)).setVisible(false);




	    for(int i=0;i<imageList.size();i++)
	        if(!visibleImages[i])
	        {
	            
	            //System.out.println("letorol"+i+imageList.elementAt(i));
	            if(imageList.elementAt(i) instanceof ImageControl)
	                ((ImageControl)imageList.elementAt(i)).setVisible(false);
	                /*
	            if(imageList.elementAt(i) instanceof SpaceControl)
	                ((SpaceControl)imageList.elementAt(i)).setVisible(false);
	                */
	        }
	        /*
	    for(int i=0;i<linknr;i++)
            System.out.print(visibleBLinks[i]+" "+visibleELinks[i]+"E");
            */
            
           	    //System.out.println("debug "+linknr);

	    for(int i=0;i<linknr;i++)
	    {
	        //    if (LinkControlListB[i]!=null) {LinkControlListB[i].setSize(new Dimension(30,30));LinkControlListB[i]System.out.println("setinvB"+i+LinkControlListB[i]);}
	        //    if (LinkControlListE[i]!=null) {LinkControlListE[i].setVisible(false);System.out.println("setinvE"+i);}
	         ((LinkControl)(linkBList.elementAt(i))).setVisible(false);
	                ((LinkControl)(linkEList.elementAt(i))).setVisible(false);
	            
	        if((!visibleBLinks[i]&&visibleELinks[i])||(visibleBLinks[i]&&!visibleELinks[i]))
	        {
	            if (LinkControlListB[i]!=null) LinkControlListB[i].dontUse();
	            if (LinkControlListE[i]!=null) LinkControlListE[i].dontUse();
	            //if (LinkControlListB[i]!=null) {LinkControlListB[i].setVisible(false);System.out.println("setinvB"+i);}
	            //if (LinkControlListE[i]!=null) {LinkControlListE[i].setVisible(false);System.out.println("setinvE"+i);}
	        }
	        
	        if((!visibleBLinks[i]&&!visibleELinks[i]))
	        {
	    //System.out.println(LinkControlListB[i]);
	            if (LinkControlListB[i]!=null) {LinkControlListB[i].setVisible(false);System.out.println("setinvB"+i);}
	            if (LinkControlListE[i]!=null) {LinkControlListE[i].setVisible(false);System.out.println("setinvE"+i);}
	        }
	        
	    }

	
	
	//debugworking=false;
	}
	/*
	public void setImageVisible(int idx){
	    visibleImages[idx]=true;
	    System.out.println("sza");
	}
	*/
    public Image fetchImage(URL imageURL, int trackerClass)
    throws InterruptedException {
//	tellLoadingMsg(imageURL);
    out("loading : "+imageURL.toExternalForm());
    Image image = getImage(imageURL);
//        System.out.println(imageURL.toExternalForm());
	tracker.addImage(image, trackerClass);
//        System.out.println("wait");
	tracker.waitForID(trackerClass);
	return image;
    }
    
    public void out(String s){
    System.out.println(s);
    showStatus(s);
}

public void showUrler(LinkControl w){
    urler.changeUrl(w);
    
}

public void showIntLink(LinkControl w){
    //System.out.println("szai");
    ilframe.open(w);
}

public void stop(){
    //System.out.println("szai");
    //if (true) return;
    stopping=true;
    if (modified)
    {
    JOptionPane op=new JOptionPane();
    //op.setBounds(100,100,300,300);
    
    int selV=op.showConfirmDialog(this,"Do you  want to upload before leaving the editor?","TOL Editor - "+retFields[7]+"("+artindex+")",JOptionPane.YES_NO_OPTION);
    if (selV==JOptionPane.NO_OPTION) return;
    upload();
    stopping=false;
    dialogShow=false;
    }
}

public void insertComponentTo(Component c){
	int car=textPane.getCaretPosition();
	//System.out.println("hossz="+textPane.getDocument().getLength());
	try{
		//textPane.setCaretPosition(car);
	//	textPane.getDocument().insertString(car,"  ",null);
		//textPane.setCaretPosition(car+1);
		textPane.insertComponent(c);
		//textPane.getDocument().insertString(car+1,"X",null);
	}
	catch(Exception e){
		System.out.println("at insert "+e);
		}

/*
	try{
		textPane.setCaretPosition(car);
		textPane.getDocument().remove(car,1);
	}
	catch(Exception e){
		System.out.println("az elso remove"+e);
		}

	try{
		textPane.getDocument().remove(car+1,1);
	}
	catch(Exception e){
		System.out.println("a masodik remove"+e);
		}
*/
	}
	
	public void regen(){
    
    /*if (modified){
    JOptionPane op=new JOptionPane();
    int selV=op.showConfirmDialog(this,"Do you really want to revert the data to the data available when loading this page ?","Revert document",JOptionPane.OK_CANCEL_OPTION);
    if (selV==JOptionPane.CANCEL_OPTION) return;
    }
*/
        contentString=new URLDecoder().decode(contentString);
        //textPane.setText(contentString);
        TolHtmlParser localParser=new TolHtmlParser(textPane,this,contentString);
        localParser.JavaUnicoder();    
        if (contentString!=null) localParser.parseHtml(); 
        modified=false;
	}

}