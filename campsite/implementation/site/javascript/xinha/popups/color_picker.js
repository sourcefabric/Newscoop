
  /**
   * Gogo Internet Services Color Picker Javascript Widget
   * colorPicker for short.
   *
   * @author James Sleeman <james@gogo.co.nz>
   * @date June, 2005
   *
   * The colorPicker class provides access to a color map for selecting
   * colors which will be passed back to a callback (usually such a callback would
   * write the RGB hex value returned into a field, but that's up to you).
   *
   * The color map presented is a standard rectangular pallate with 0->360 degrees of
   * hue on the Y axis and 0->100% saturation on the X axis, the value (brightness) is
   * selectable as a vertical column of grey values.  Also present is a one row of
   * white->grey->black for easy selection of these colors.
   *
   * A checkbox is presented, which when checked will force the palatte into "web safe"
   * mode, only colours in the "web safe palatte" of 216 colors will be shown, the palatte
   * is adjusted so that the normal blend of colours are "rounded" to the nearest web safe
   * one.  It should be noted that "web safe" colours really are a thing of the past,
   * not only can pretty much every body display several million colours, but it's actually
   * been found that of those 216 web safe colours only 20 to 30 are actually going to be
   * displayed equally on the majority of monitors, and those are mostly yellows!
   *
   * =Usage Example=
   * {{{
   *  <!-- Here is the field -->         <!-- And we will use this button to open the picker"
   *  <input type="text" id="myField" /> <input type="button" value="..." id="myButton" />
   *  <script>
   *    // now when the window loads link everything up
   *    window.onload = function()
   *    {
   *
   *      var myField  = document.getElementById('myField');  // Get our field
   *      var myButton = document.getElementById('myButton'); // And the button
   *      var myPicker = new colorPicker                      // Make a picker
   *        (
   *          {
   *              // Cellsize is the width and height of each colour cell
   *            cellsize: '5px',
   *              // Callback is the function to execute when we are done,
   *              // this one puts the color value into the field
   *            callback: function(color){myField.value=color},
   *              // Granularity defines the maximum number of colors per row/column
   *              // more colors (high number) gives a smooth gradient of colors
   *              // but it will take (much) longer to display, while a small number
   *              // displays quickly, but doesn't show as many different colors.
   *              // Experiement with it, 18 seems like a good number.
   *            granularity: 18
   *           }
   *        );
   *
   *      // And now hookup the button to open the picker,
   *      //  the function to do that is myPicker.open()
   *      //  it accepts two parameters, the "anchorage" and the element to anchor to
   *      //  to anchor to.
   *      //
   *      //  anchorage is made up of two of the keywords bottom,top,left and right
   *      //    left:   the left edge of the picker will align to the left edge of the element
   *      // or right:  the right edgeof the picker aligns to the right edge of the element
   *      //    top:    the picker will appear above the element
   *      // or bottom: the picker will appear below the element
   *
   *      myButton.onclick =
   *        function()
   *        {              // anchorage   , element to anchor to
   *          myPicker.open('bottom,right', myPicker)
   *        };
   *    }
   *  </script>
   * }}}
   */

  function colorPicker(params)
  {
    var picker = this;
    this.callback = params.callback?params.callback:function(color){alert('You picked ' + color )};

    this.cellsize = params.cellsize?params.cellsize:'10px';
    this.side     = params.granularity?params.granularity:18;

    this.value = 1;
    this.saved_cells = null;
    this.table = document.createElement('table');
    this.table.cellSpacing = this.table.cellPadding = 0;
    this.tbody = document.createElement('tbody');
    this.table.appendChild(this.tbody);
    this.table.style.border = '1px solid WindowFrame';
    this.table.style.backgroundColor = 'Window';
    // Add a title bar and close button
    var tr = document.createElement('tr');
    var td = document.createElement('td');
    var but = document.createElement('button');
    but.onclick = function() { picker.close(); }
    but.appendChild(document.createTextNode('x'));
    td.appendChild(but);
    td.style.position = 'relative';
    td.style.verticalAlign = 'middle';
    but.style.cssFloat = 'right';
    but.style.styleFloat = 'right';


    td.colSpan = this.side + 3;
    td.style.backgroundColor = 'ActiveCaption';
    td.style.color = 'CaptionText';
    td.style.fontFamily = 'small-caption,caption,sans-serif';
    td.style.fontSize = 'x-small';
    td.appendChild(document.createTextNode('Click a color...'));
    td.style.borderBottom = '1px solid WindowFrame';

    tr.appendChild(td);
    this.tbody.appendChild(tr);
    but = tr = td = null;

    this.constrain_cb = document.createElement('input');
    this.constrain_cb.type = 'checkbox';

    this.chosenColor     = document.createElement('input');
    this.chosenColor.type = 'text';
    this.chosenColor.size = '7';

    this.backSample     = document.createElement('div');
    this.backSample.appendChild(document.createTextNode('\u00A0'));
    this.backSample.style.fontWeight = 'bold';
    this.backSample.style.fontFamily = 'small-caption,caption,sans-serif';
    this.backSample.fontSize = 'x-small';

    this.foreSample     = document.createElement('div');
    this.foreSample.appendChild(document.createTextNode('Sample'));
    this.foreSample.style.fontWeight = 'bold';
    this.foreSample.style.fontFamily = 'small-caption,caption,sans-serif';
    this.foreSample.fontSize = 'x-small';

    /** Convert a decimal number to a two byte hexadecimal representation.
      * Zero-pads if necessary.
      *
      * @param integer dec Integer from 0 -> 255
      * @returns string 2 character hexadecimal (zero padded)
      */
    function toHex(dec)
    {
      var h = dec.toString(16);
      if(h.length < 2) h = '0' + h;
      return h;
    }

    /** Convert a color object {red:x, green:x, blue:x} to an RGB hex triplet
     * @param object tuple {red:0->255, green:0->255, blue:0->255}
     * @returns string hex triplet (#rrggbb)
     */

    function tupleToColor(tuple)
    {
      return '#' + toHex(tuple.red) + toHex(tuple.green) + toHex(tuple.blue);
    }

    /** Determine the nearest power of a number to another number
     * (eg nearest power of 4 to 5 => 4, of 4 to 7 => 8)
     *
     * @usedby rgbToWebsafe
     * @param number num number to round to nearest power of <power>
     * @param number power number to find the nearest power of
     * @returns number Nearest power of <power> to num.
     */

    function nearestPowerOf(num,power)
    {
      return Math.round(Math.round(num / power) * power);
    }

    /** Concatenate the hex representation of dec to itself and return as an integer.
     *  eg dec = 10 -> A -> AA -> 170
     *
     * @usedby rgbToWebsafe
     * @param dec integer
     * @returns integer
     */

    function doubleHexDec(dec)
    {
      return parseInt(dec.toString(16) + dec.toString(16), 16);
    }

    /** Convert a given RGB color to the nearest "Web-Safe" color.  A websafe color only has the values
     *  00, 33, 66, 99, CC and FF for each of the red, green and blue components (thus 6 shades of each
     *  in combination to produce 6 * 6 * 6 = 216 colors).
     *
     * @param    color object {red:0->255, green:0->255, blue:0->255}
     * @returns  object {red:51|102|153|204|255, green:51|102|153|204|255, blue:51|102|153|204|255}
     */
    function rgbToWebsafe(color)
    {
      // For each take the high byte, divide by three, round and multiply by three before rounding again
      color.red   = doubleHexDec(nearestPowerOf(parseInt(toHex(color.red).charAt(0), 16), 3));
      color.blue  = doubleHexDec(nearestPowerOf(parseInt(toHex(color.blue).charAt(0), 16), 3));
      color.green = doubleHexDec(nearestPowerOf(parseInt(toHex(color.green).charAt(0), 16), 3));
      return color;
    }

    /** Convert a combination of hue, saturation and value into an RGB color.
     *  Hue is defined in degrees, saturation and value as a floats between 0 and 1 (0% -> 100%)
     *
     * @param h float angle of hue around color wheel 0->360
     * @param s float saturation of color (no color (grey)) 0->1 (vibrant)
     * @param v float value (brightness) of color (black) 0->1 (bright)
     * @returns object {red:0->255, green:0->255, blue:0->255}
     * @seealso http://en.wikipedia.org/wiki/HSV_color_space
     */
    function hsvToRGB(h,s,v)
    {
      var colors;
      if(s == 0)
      {
        // GREY
        colors = {red:v,green:v,blue:v}
      }
      else
      {
        h /= 60;
        var i = Math.floor(h);
        var f = h - i;
        var p = v * (1 - s);
        var q = v * (1 - s * f);
        var t = v * (1 - s * (1 - f) );
        switch(i)
        {
          case 0: colors =  {red:v, green:t, blue:p}; break;
          case 1: colors =  {red:q, green:v, blue:p}; break;
          case 2: colors =  {red:p, green:v, blue:t}; break;
          case 3: colors =  {red:p, green:q, blue:v}; break;
          case 4: colors =  {red:t, green:p, blue:v}; break;
          case 5:
          default:colors =  {red:v, green:p, blue:q}; break;
        }
      }
      colors.red = Math.ceil(colors.red * 255);
      colors.green = Math.ceil(colors.green * 255);
      colors.blue = Math.ceil(colors.blue * 255);
      return colors;
    }

    /** Open the color picker
     *
     * @param string anchorage pair of sides of element to anchor the picker to
     *   "top,left" "top,right" "bottom,left" or "bottom,right"
     * @param HTML_ELEMENT element the element to anchor the picker to sides of
     *
     * @note The element is just referenced here for positioning (anchoring), it
     * does not automatically get the color copied into it.  See the usage instructions
     * for the class.
     */

    this.open = function(anchorage,element)
    {
      this.table.style.display = '';

      this.pick_color();

      // Find position of the element
      this.table.style.position = 'absolute';
      var e = element;
      var top  = 0;
      var left = 0;
      do
      {
        top += e.offsetTop;
        left += e.offsetLeft;
        e = e.offsetParent;
      }
      while(e)

      var x, y;
      if(/top/.test(anchorage))
      {
        this.table.style.top = (top - this.table.offsetHeight) + 'px';
      }
      else
      {
        this.table.style.top = (top + element.offsetHeight) + 'px';
      }

      if(/left/.test(anchorage))
      {
        this.table.style.left = left + 'px';
      }
      else
      {
        this.table.style.left = (left - (this.table.offsetWidth - element.offsetWidth)) + 'px';
      }
    };

    /** Draw the color picker. */
    this.pick_color = function()
    {
      var rows, cols;
      var picker = this;
      var huestep = 359/this.side;
      var saturstep = 1/this.side;
      var valustep  = 1/this.side;
      var constrain = this.constrain_cb.checked;

      if(this.saved_cells == null)
      {
        this.saved_cells = new Array();

        for(var row = 0; row <= this.side; row++)
        {
          var tr = document.createElement('tr');
          this.saved_cells[row] = new Array();
          for(var col = 0; col <= this.side; col++)
          {
            var td = document.createElement('td');
            if(constrain)
            {
              td.colorCode = tupleToColor(rgbToWebsafe(hsvToRGB(huestep*row, saturstep*col, this.value)));
            }
            else
            {
              td.colorCode = tupleToColor(hsvToRGB(huestep*row, saturstep*col, this.value));
            }
            this.saved_cells[row][col] = td;
            td.style.height = td.style.width = this.cellsize;
            td.style.backgroundColor = td.colorCode;
            td.hue = huestep * row;
            td.saturation = saturstep*col;
            td.onmouseover = function()
            {
              picker.chosenColor.value = this.colorCode;
              picker.backSample.style.backgroundColor = this.colorCode;
              picker.foreSample.style.color = this.colorCode;
              if((this.hue >= 195  && this.saturation > 0.25) || picker.value < 0.75)
              {
                picker.backSample.style.color = 'white';
              }
              else
              {
                picker.backSample.style.color = 'black';
              }
            }
            td.onclick = function() { picker.callback(this.colorCode); picker.close(); }
            td.appendChild(document.createTextNode(' '));
            td.style.cursor = 'pointer';
            tr.appendChild(td);
            td = null;
          }

          // Add a blank and thena value column
          var td = document.createElement('td');
          td.appendChild(document.createTextNode(' '));
          td.style.width = this.cellsize;
          tr.appendChild(td);
          td = null;

          var td = document.createElement('td');
          td.appendChild(document.createTextNode(' '));
          td.style.width  = this.cellsize;
          td.style.height = this.cellsize;
          td.constrainedColorCode  = tupleToColor(rgbToWebsafe(hsvToRGB(0,0,valustep*row)));
          td.style.backgroundColor = td.colorCode = tupleToColor(hsvToRGB(0,0,valustep*row));
          td.hue = huestep * row;
          td.saturation = saturstep*col;
          td.hsv_value = valustep*row;
          td.onclick = function() {
            picker.value = this.hsv_value; picker.pick_color();
            if(picker.constrain_cb.checked)
            {
              picker.chosenColor.value = this.constrainedColorCode;
            }
            else
            {
              picker.chosenColor.value = this.colorCode;
            }
          }
          td.style.cursor = 'pointer';
          tr.appendChild(td);
          td = null;

          this.tbody.appendChild(tr);
          tr = null;
        }

        // Add one row of greys
        var tr = document.createElement('tr');
        this.saved_cells[row] = new Array();
        for(var col = 0; col <= this.side; col++)
        {
          var td = document.createElement('td');
          if(constrain)
          {
            td.colorCode = tupleToColor(rgbToWebsafe(hsvToRGB(0, 0, valustep*(this.side-col))));
          }
          else
          {
            td.colorCode = tupleToColor(hsvToRGB(0, 0, valustep*(this.side-col)));
          }
          this.saved_cells[row][col] = td;
          td.style.height = td.style.width = this.cellsize;
          td.style.backgroundColor = td.colorCode;
          td.hue = 0;
          td.saturation = 0;
          td.onmouseover = function()
          {
            picker.chosenColor.value = this.colorCode;
            picker.backSample.style.backgroundColor = this.colorCode;
            picker.foreSample.style.color = this.colorCode;
            if((this.hue >= 195  && this.saturation > 0.25) || picker.value < 0.75)
            {
              picker.backSample.style.color = 'white';
            }
            else
            {
              picker.backSample.style.color = 'black';
            }
          }
          td.onclick = function() { picker.callback(this.colorCode); picker.close(); }
          td.appendChild(document.createTextNode(' '));
          td.style.cursor = 'pointer';
          tr.appendChild(td);
          td = null;
        }
        this.tbody.appendChild(tr);
        tr = null;


        var tr = document.createElement('tr');
        var td = document.createElement('td');
        tr.appendChild(td);
        td.colSpan = this.side + 3;
        td.style.padding = '3px';

        var div = document.createElement('div');
        var label = document.createElement('label');
        label.appendChild(document.createTextNode('Web Safe: '));

        this.constrain_cb.onclick = function() { picker.pick_color() };
        label.appendChild(this.constrain_cb);
        label.style.fontFamily = 'small-caption,caption,sans-serif';
        label.style.fontSize = 'x-small';
        div.appendChild(label);
        td.appendChild(div);

        var div = document.createElement('div');
        var label = document.createElement('label');
        label.style.fontFamily = 'small-caption,caption,sans-serif';
        label.style.fontSize = 'x-small';
        label.appendChild(document.createTextNode('Color: '));
        label.appendChild(this.chosenColor);
        div.appendChild(label);
        td.appendChild(div);

        var sampleTable = document.createElement('table');
        sampleTable.style.width = '100%';
        var sampleBody = document.createElement('tbody');
        sampleTable.appendChild(sampleBody);
        var sampleRow = document.createElement('tr');
        sampleBody.appendChild(sampleRow);
        var leftSampleCell = document.createElement('td');
        sampleRow.appendChild(leftSampleCell);
        leftSampleCell.appendChild(this.backSample);
        leftSampleCell.style.width = '50%';
        var rightSampleCell = document.createElement('td');
        sampleRow.appendChild(rightSampleCell);
        rightSampleCell.appendChild(this.foreSample);
        rightSampleCell.style.width = '50%';

        td.appendChild(sampleTable);


        this.tbody.appendChild(tr);
        document.body.appendChild(this.table);

      }
      else
      {
        for(var row = 0; row <= this.side; row++)
        {
          for(var col = 0; col <= this.side; col++)
          {
            if(constrain)
            {
              this.saved_cells[row][col].colorCode = tupleToColor(rgbToWebsafe(hsvToRGB(huestep*row, saturstep*col, this.value)));
            }
            else
            {
              this.saved_cells[row][col].colorCode = tupleToColor(hsvToRGB(huestep*row, saturstep*col, this.value));
            }
            this.saved_cells[row][col].style.backgroundColor = this.saved_cells[row][col].colorCode;
          }
        }
      }
    };

    /** Close the color picker */
    this.close = function()
    {
      this.table.style.display = 'none';
    };

  }