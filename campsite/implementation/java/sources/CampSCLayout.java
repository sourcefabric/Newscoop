/*
 * @(#)CampSCLayout.java	1.02 2 Jan 2002
 *
 * Copyright John Redmond (John.Redmond@mq.edu.au).
 * 
 * This software is freely available for commercial and non-commercial purposes.
 * Acknowledgement of its source would be appreciated, but is not required.
 * 
 */

public class CampSCLayout extends CampLayout {

  /**
   * Creates a layout with the specified number of rows
   * and default alignments and gaps.
   * <p>
   * vertical gaps are set to 0 and
   * X- and Y-alignments are set to FILL.
   * @param     rows   the rows.
   */
   public CampSCLayout(int rows) {
    super(rows, 1);
  }

  /**
   * Creates a layout with the specified number of rows
   * and specified gap.
   * <p>
   * vertical gaps are set to 0 and
   * X- and Y-alignments are set to FILL.
    * @param     rows   the rows.
    * @param     gap   the vertical gap, in pixels.
    */
    public CampSCLayout(int rows, int gap) {
    super(rows, 1, FILL, FILL, gap, 0);
  }

  /**
   * Creates a layout with the specified number of rows
   * and specified alignments and gaps.
   * <p>
   * vertical gaps are set to 0 and
   * X- and Y-alignments are set to FILL.
   * @param     rows   the rows.
   * @param     hAlignment the X-alignment.
   * @param     vAlignment the Y-alignment.
   * @param     gap   the vertical gap, in pixels.
   */
   public CampSCLayout(int rows, int hAlignment, int vAlignment, int gap) {
    super(rows, 1, hAlignment, vAlignment, 0, gap);
  }

  /**
   * Set up alignment for a specific cell.
   * <p>
   * @param     index the cell number.
   * @param     hAlignment  the X-alignment for the cell.
   * @param     vAlignment  the Y-alignment for the cell.
   */
   public void setAlignment(int index, int hAlignment, int vAlignment) {
    setRowAlignment(index, hAlignment, vAlignment);
  }

  /**
   * Set up scale value for a specific cell.
   * <p>
   * @param     index the column number.
   * @param     scale  the scale value for the column.
   */
   public void setScale(int cell, double scale) {
    setRowScale(cell, scale);
  }
} 
