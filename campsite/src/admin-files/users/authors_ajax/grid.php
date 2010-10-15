<?php
ini_set("display_errors","2");
error_reporting(E_ALL);
function l_getType($types)
{
    if (!is_array($types)) return;
    $t = "";
    foreach ($types as $type){
        if (strlen($t)>0) $t .= ", ";
        $t .= $type['type'];
    }
    return $t;
}
?>
<a href="#" class="addButtonText" onclick="getRow(0)">Add new Author</a>
<table  id="gridx" style="width:100%; margin:0;">
    <thead >
        <tr>
            <th valign="top">Author</th>
            <th valign="top">Type</th>
            <th valign="top">Delete</th>
        </tr>
    </thead>
  <tbody>
      <?php
      $authors = Author::GetAuthors();
      $i=0;
    foreach($authors as $author) {
        $class="list_row_even";
        if ($i%2==0)
        {
            $class="list_row_odd";
        }
?>
        <tr class="<?php echo $class ?>" onclick="getRow(<?php echo $author->getId()?>)"  onmouseover="setPointer(this, 0, 'over');" onmouseout="setPointer(this, 0, 'out');" style="cursor:pointer">
            <td><?php echo $author->getName();  ?></td>
            <td><?php echo l_getType($author->getTypeWithNames()); ?></td>
            <td align="right" class="last" id="row_0"><a href="?del_id=<?php echo $author->getId() ?>" onclick="return confirm('<?php echo getGS('Are you sure you want to delete this author;')?>')"><img src="../../css/delete.png" border="0" alt="Delete author" title="Delete author" /></a></td>
        </tr>
        <?php
        $i++;

        }?>
  </tbody>
</table>
