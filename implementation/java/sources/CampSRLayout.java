/*
 * @(#)CampSRLayout.java	1.02 2 Jan 2002
 *
 * Copyright John Redmond (John.Redmond@mq.edu.au).
 * 
 * This software is freely available for commercial and non-commercial purposes.
 * Acknowledgement of its source would be appreciated, but is not required.
 *
 */

//import java.util.*;
import java.awt.*;

public class CampSRLayout extends CampLayout {

  /**
   * Creates a layout with the specified number of columns
   * and default alignments and gaps.
   * <p>
   * horizontal gaps are set to 0 and
   * X- and Y-alignments are set to FILL.
   * @param     cols   the columns.
   */
   public CampSRLayout(int columns) {
    super(1, columns);
  }

  /**
   * Creates a layout with the specified number of columns
   * and specified gap.
   * <p>
   * horizontal and vertical gaps are set to 0 and
   * X- and Y-alignments are set to FILL.
   * @param     cols   the columns.
   * @param     gap   the horizontal gap, in pixels.
   */
   public CampSRLayout(int columns, int gap) {
    super(1, columns, FILL, FILL, gap, 0);
  }

  /**
   * Creates a layout with the specified number of columns
   * and specified alignments and gaps.
   * <p>
   * horizontal and vertical gaps are set to 0 and
   * X- and Y-alignments are set to FILL.
   * @param     cols   the columns.
   * @param     gap   the horizontal gap, in pixels.
   */
   public CampSRLayout(int columns, int hAlignment, int vAlignment, int gap) {
    super(1, columns, hAlignment, vAlignment, gap, 0);
  }

  /**
   * Set up alignment for a specific cell.
   * <p>
   * @param     index the cell number.
   * @param     hAlignment  the X-alignment for the cell.
   * @param     vAlignment  the Y-alignment for the cell.
   */
   public void setAlignment(int column, int hAlignment, int vAlignment) {
    setColumnAlignment(column, hAlignment, vAlignment);
  }

  /**
   * Set up scale value for a specific cell.
   * <p>
   * @param     index the cell number.
   * @param     scale  the scale value for the column.
   */
   public void setScale(int column, double scale) {
    setColumnScale(column, scale);
  }
}
