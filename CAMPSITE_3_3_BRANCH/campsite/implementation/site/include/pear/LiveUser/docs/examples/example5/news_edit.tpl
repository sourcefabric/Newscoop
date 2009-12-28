<form action="admin.php" method="POST">
    <input type="hidden" name="id" value="{ID}" />
    <table>
    <tr>
        <td>Title: <input type="text" size="20" maxlength="75" name="title" value="{TITLE}" /></td>
        <td>Date: {DATE}</td>
    </tr>
    <tr>
        <td colspan="2">Content:</td>
    </tr>
    <tr>
        <td colspan="2"><textarea name="newscontent" cols="40" rows="20">{CONTENT}</textarea></td>
    </tr>
    <tr>
        <td colspan="2"><input type="submit" name="news" value="Update/Insert news" /></td>
    </tr>
    </table>
</form>
