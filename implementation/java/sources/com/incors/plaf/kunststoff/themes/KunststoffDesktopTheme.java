/*
 * KunststoffDesktopTheme.java
 *
 * Created on 17. Oktober 2001, 22:40
 */

package com.incors.plaf.kunststoff.themes;

import java.awt.Font;
import javax.swing.plaf.ColorUIResource;
import javax.swing.plaf.FontUIResource;
import javax.swing.UIDefaults;
import javax.swing.UIManager;

/**
 *
 * @author  christophw
 * @version
 */
public class KunststoffDesktopTheme extends com.incors.plaf.kunststoff.KunststoffTheme {
    private FontUIResource controlFont;
    private FontUIResource menuFont;
    private FontUIResource windowTitleFont;
    private FontUIResource monospacedFont;

    /**
     * Crates this Theme
     */
    public KunststoffDesktopTheme()
    {
        menuFont = new FontUIResource(null,Font.PLAIN, 12);
        controlFont = new FontUIResource(null,Font.PLAIN, 11);
        windowTitleFont =  new FontUIResource(null, Font.BOLD, 12);
        monospacedFont = new FontUIResource("Monospaced", Font.PLAIN, 11);
    }

    public String getName()
    {
        return "Desktop";
    }

    /**
     * The Font of Labels in many cases
     */
    public FontUIResource getControlTextFont()
    {
        return controlFont;
    }

    /**
     * The Font of Menus and MenuItems
     */
    public FontUIResource getMenuTextFont()
    {
        return menuFont;
    }

    /**
     * The Font of Nodes in JTrees
     */
    public FontUIResource getSystemTextFont()
    {
        return controlFont;
    }

    /**
     * The Font in TextFields, EditorPanes, etc.
     */
    public FontUIResource getUserTextFont()
    {
        return controlFont;
    }

    /**
     * The Font of the Title of JInternalFrames
     */
    public FontUIResource getWindowTitleFont()
    {
        return windowTitleFont;
    }

    public void addCustomEntriesToTable(UIDefaults table)
    {
        super.addCustomEntriesToTable(table);
        UIManager.getDefaults().put("PasswordField.font", monospacedFont);
        UIManager.getDefaults().put("TextArea.font", monospacedFont);
        UIManager.getDefaults().put("TextPane.font", monospacedFont);
        UIManager.getDefaults().put("EditorPane.font", monospacedFont);
    }
}
