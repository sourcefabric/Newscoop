/*
 * @(#)CampLayout.java	1.02 2 Jan 2002
 *
 * Copyright John Redmond (John.Redmond@mq.edu.au).
 * 
 * This software is freely available for commercial and non-commercial purposes.
 * Acknowledgement of its source would be appreciated, but is not required.
 * 
 */

import java.awt.*;

public class CampLayout implements LayoutManager, java.io.Serializable {
  static public int LEFT = 0;
  static public int CENTER = 1;
  static public int RIGHT = 2;
  static public int FILL = 4;
  static public int TOP = 8;
  static public int BOTTOM = 16;

  protected int rows, cols, vgap, hgap;
  protected int topBorder = 0, leftBorder = 0, bottomBorder = 0, rightBorder = 0;
  protected int minW = 10, minH = 10;  // to handle JTextField sensibly
  protected double[] rowScale, columnScale;
  protected int hAlignment = FILL, vAlignment = FILL;
  protected int[][] hAlignments, vAlignments;
  protected int[] rowSizes, columnSizes;

    /**
     * Creates a default (2 x 2) layout with the specified number of rows and
     * columns.
     * <p>
     * horizontal and vertical gaps are set to 0 and
     * X- and Y-alignments are set to FILL.
     */
  public CampLayout() {
    this(2, 2, FILL, FILL, 0, 0);
  }

  /**
   * Creates a layout with the specified number of rows and columns.
   * <p>
   * horizontal and vertical gaps are set to 0 and
   * X- and Y-alignments are set to FILL.
   * @param     rows   the rows.
   * @param     cols   the columns.
   */
   
   public CampLayout(int rows, int cols) {
    this(rows, cols, FILL, FILL, 0, 0);
  }

  /**
   * Creates a layout with the specified number of rows and columns
   * and specified gaps.
   * <p>
   * horizontal and vertical gaps are set to 0 and
   * X- and Y-alignments are set to FILL.
   * @param     rows   the rows.
   * @param     cols   the columns.
   * @param     hgap   the horizontal gap, in pixels.
   * @param     vgap   the vertical gap, in pixels.
   */
   
   public CampLayout(int rows, int cols, int hgap, int vgap) {
    this(rows, cols, FILL, FILL, hgap, vgap);
  }

  /**
   * Creates a layout with the specified number of rows and columns
   * and specified gaps and alignments.
   * <p>
   * horizontal and vertical gaps are set to 0 and
   * X- and Y-alignments are set to FILL.
   * @param     rows   the rows.
   * @param     cols   the columns.
   * @param     hAlignment the X-alignment.
   * @param     vAlignment the Y-alignment.
   * @param     hgap   the horizontal gap, in pixels.
   * @param     vgap   the vertical gap, in pixels.
   */
   
   public CampLayout(int rows, int cols,
                          int hAlignment, int vAlignment,
                          int hgap, int vgap) {
    this.hgap = hgap;
    this.vgap = vgap;
    this.hAlignment = hAlignment;
    this.vAlignment = vAlignment;

    setDimensions(rows, cols);
  }

  private void setScaleValues() {
    rowScale = new double[rows];
    columnScale = new double[cols];
    for (int i = 0; i < rows; i++) rowScale[i] = 1.0;
    for (int j = 0; j < cols; j++) columnScale[j] = 1.0;
  }

  private void setAlignments() {
    hAlignments = new int[rows][cols];
    vAlignments = new int[rows][cols];
    for (int i = 0; i < rows; i++) {
      for (int j = 0; j < cols; j++) {
        hAlignments[i][j] = hAlignment;
        vAlignments[i][j] = vAlignment;
      }
    }
  }

  /**
   * Set up scale values and alignments for the whole layout.
   * <p>
   * @param     rows   the rows.
   * @param     cols   the columns.
   */
   
   private void setDimensions(int rows, int cols) {
    this.rows = rows;
    this.cols = cols;

    setScaleValues();
    setAlignments();
  }

  /**
   * Set up scale values and alignments for the whole layout.
   * <p>
   * @param     topBorder the top border (in pixels).
   * @param     leftBorder the left border (in pixels).
   * @param     bottomBorder the bottom border (in pixels).
   * @param     rightBorder the right border (in pixels).
   */
   
   public void setMargins(int topBorder, int leftBorder,
                          int bottomBorder, int rightBorder) {
    this.topBorder = topBorder;
    this.leftBorder = leftBorder;
    this.bottomBorder = bottomBorder;
    this.rightBorder = rightBorder;
  }

  /**
   * Set up scale value for a specific row.
   * <p>
   * @param     index the row number.
   * @param     prop  the scale value for the row.
   */
   
   public void setRowScale(int index, double prop) {
    if (index >= 0 && index < rows) {
      rowScale[index] = prop;
    }
  }

  /**
   * Set up scale value for a specific column.
   * <p>
   * @param     index the column number.
   * @param     prop  the scale value for the column.
   */
   
   public void setColumnScale(int index, double prop) {
    if (index >= 0 && index < cols) {
      columnScale[index] = prop;
    }
  }

   public int getColMaxSize(Container parent, int colindex) {
    int prefWidth = minW;
    if (colindex >= 0 && colindex < cols) {

        for (int i = 0 ; i < rows ; i++) {
            // get max preferred width for a row
                int index = i * cols + colindex;
                //if (index >= ncomponents) continue;
                        
                Component comp = parent.getComponent(index);
                Dimension d = comp.getPreferredSize();
                if (d.width > prefWidth) prefWidth = d.width;
       }
    }
    return prefWidth;
  }


/*
  public void setAlignment(int h, int v) {
    hAlignment = h;
    vAlignment = v;
    setAlignments();
  }
*/

  /**
   * Set up alignment for a specific cell.
   * <p>
   * @param     row the row number.
   * @param     column the column number.
   * @param     h  the X-alignment.
   * @param     v  the Y-alignment.
   */
   
   public void setAlignment(int row, int column, int h, int v) {
    if (row < rows && column < cols) {
      hAlignments[row][column] = h;
      vAlignments[row][column] = v;
    }
  }

  /**
   * Set up alignment for a specific row.
   * <p>
   * @param     row the row number.
   * @param     h  the X-alignment.
   * @param     v  the Y-alignment.
   */
   
   public void setRowAlignment(int row, int h, int v) {
    if (row < rows) {
      for (int column = 0; column < cols; column++) {
        hAlignments[row][column] = h;
        vAlignments[row][column] = v;
      }
    }
  }

  /**
   * Set up alignment for a specific column.
   * <p>
   * @param     column the column number.
   * @param     h  the X-alignment.
   * @param     v  the Y-alignment.
   */
   
   public void setColumnAlignment(int column, int h, int v) {
    if (column < cols) {
      for (int row = 0; row < rows; row++) {
        hAlignments[row][column] = h;
        vAlignments[row][column] = v;
      }
    }
  }

  public void addLayoutComponent(String name, Component comp) {
  }
  
  public void removeLayoutComponent(Component comp) {
    }
    
    /**
    * Determines the preferred size of the container argument using
    * this grid layout.
    * <p>
    * The preferred width is the width of the largest row of children,
    * which is the largest sum of preferred widths.
    * <p>
    * The preferred height is the sum of the the largest heights of
    * the rows, which is the largest preferred height in each row.
    *
    * @param     target   the container in which to do the layout.
    * @return    the preferred dimensions to lay out the
    *                      subcomponents of the specified container.
    */
    
    public Dimension preferredLayoutSize(Container parent) {
        synchronized (parent.getTreeLock()) {
            int ncomponents = parent.getComponentCount();
            int nrows = rows;
            int ncols = cols;
            
            if (nrows > 0) {
                ncols = (ncomponents + nrows - 1) / nrows;
            } else {
                nrows = (ncomponents + ncols - 1) / ncols;
            }
            int totalWidth = 0;
            int totalHeight = 0;
                    
            for (int i = 0 ; i < nrows ; i++) {
                int prefWidth = 0, prefHeight = 0;
                // get max preferred height for a row
                for (int j = 0; j < ncols; j++) {
                    int index = i * ncols + j;
                    if (index >= ncomponents) continue;
                            
                    Component comp = parent.getComponent(index);
                    Dimension d = comp.getPreferredSize();
                    if (d.width < minW) prefWidth += minW; // add minimum width
                    else prefWidth += d.width;  // increment total preferred width
                    if (d.height > prefHeight) prefHeight = d.height;
                }
                if (prefWidth > totalWidth) totalWidth = prefWidth;
                totalHeight += prefHeight;
           }
           return new Dimension(totalWidth + leftBorder + rightBorder + (ncols - 1)*hgap,
           totalHeight + topBorder + bottomBorder + (nrows - 1)*vgap);
      }
  }
                            
/**
* Determines the minimum size of the container argument using
* this grid layout.
* <p>
* The preferred width is the width of the largest row of children,
* which is the largest sum of minimum widths.
* <p>
* The preferred height is the sum of the the largest heights of
* the rows, which is the largest minimum height in each row.
*
* @param     target   the container in which to do the layout.
* @return    the preferred dimensions to lay out the
*                      subcomponents of the specified container.
*/
public Dimension minimumLayoutSize(Container parent) {
    synchronized (parent.getTreeLock()) {
        int ncomponents = parent.getComponentCount();
        int nrows = rows;
        int ncols = cols;
        
        if (nrows > 0) {
            ncols = (ncomponents + nrows - 1) / nrows;
            } else {
                nrows = (ncomponents + ncols - 1) / ncols;
                }
                int totalWidth = 0;
                int totalHeight = 0;
                for (int i = 0 ; i < nrows ; i++) {
                    int minWidth = 0, minHeight = 0;
                    for (int j = 0; j < ncols; j++) {
                        int index = i * ncols + j;
                        if (index >= ncomponents) continue;
                        Component comp = parent.getComponent(index);
                        Dimension d = comp.getMinimumSize();
                        int width = d.width;
                        if (width < minW) width = minW;
                        minWidth += width;
                        if (minHeight > d.height) minHeight = d.height;
                        }
                        if (totalWidth > minWidth) totalWidth = minWidth;
                        if (minHeight < minH) minHeight = minH; // enough room for text?
                        totalHeight += minHeight;
                        }
                        //    return new Dimension(totalWidth + leftBorder + rightBorder,
                        //                          totalHeight + topBorder + bottomBorder);
                        return new Dimension(totalWidth + leftBorder + rightBorder + (ncols - 1)*hgap,
                        totalHeight + topBorder + bottomBorder + (nrows - 1)*vgap);
                        }
                        }
                        
/**
* Lay out the specified container using this layout within the
* calculated grids.
* <p>
* @param      parent the container to be laid out.
*/
public void layoutContainer(Container parent) {
    int maxWidth = parent.getSize().width - leftBorder - rightBorder;
    int maxHeight = parent.getSize().height - topBorder - bottomBorder;
    int nComps = parent.getComponentCount();
    int previousWidth = 0, previousHeight = 0;
    int x, y = topBorder;
    int rowh = 0, start = 0;
    Dimension d = new Dimension(60, 30);

    allocateMaxSizes(parent);

    for (int i = 0; i < rows; i++) {
      x = leftBorder;
      for (int j = 0; j < cols; j++) {
        int componentIndex = i * cols + j;
        if (componentIndex > nComps - 1) continue;

        Component c = parent.getComponent(componentIndex);
        if (c.isVisible())
          setComponentBounds(c, i, j, x, y);
        x += columnSizes[j] + hgap;
      }
      y += rowSizes[i] + vgap;
    }
  }

  /**
   * Set the bounds for a component of specified coordinates.
   * given the cell coordinates and the origin of the cell.
   * <p>
   *
   * @param     row the grid row
   * @param     column  the grid column
   * @param     left the x=coord of the grid origin.
   * @param     top the y-coord of the grid origin.
   */
   
   void setComponentBounds(Component c,
                                  int row, int column, int left, int top) {
    Dimension d = c.getPreferredSize();
    int finalWidth = columnSizes[column];   // max
    int finalHeight = rowSizes[row];     // max

    int xSpace = finalWidth - d.width;
    if (xSpace > 0) {
      int alignment = hAlignments[row][column];
      if (alignment == RIGHT) left += xSpace;
      else if (alignment == CENTER) left += xSpace/2;

      if (alignment != FILL) finalWidth = d.width; // reduce width to preferred val
    }

    int ySpace = finalHeight - d.height;
    if (ySpace > 0) {
      int vAlignment = vAlignments[row][column];
      if (vAlignment == BOTTOM) top += ySpace;
      else if (vAlignment == CENTER) top += ySpace/2;

      if (vAlignment != FILL) finalHeight = d.height; // reduce height to pref val
    }
    c.setBounds(left, top, finalWidth, finalHeight);
  }

  /**
   * Update out the maximum sizes for each of the grid cells
   * using the specified scale values for rows and columns.
   *
   * @param parent the container to be laid out.
   */
   protected void allocateMaxSizes(Container parent) {
    rowSizes = new int[rows];
    columnSizes = new int[cols];
    Dimension thisSize = parent.getSize();
    int width = thisSize.width - leftBorder - rightBorder
                      - (cols - 1) * hgap;
    int height = thisSize.height - topBorder - bottomBorder
                      - (rows - 1) * vgap;

    double totalRowProps = 0.0;
    for (int i = 0; i < rows; i++) {
      totalRowProps += rowScale[i];
    }

    double totalColumnProps = 0.0;
    for (int j = 0; j < cols; j++) {
      totalColumnProps += columnScale[j];
    }

    for (int p = 0; p < rows; p++) {
      rowSizes[p] = (int)(rowScale[p] * height/totalRowProps);
    }
    for (int q = 0; q < cols; q++ ) {
      columnSizes[q] = (int)(columnScale[q] * width/totalColumnProps);
    }
  }
}

