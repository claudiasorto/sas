<form method="post" name="frmCv" id="frmCv" enctype="multipart/form-data" target="frUP" action="mtto/upAttachNS.php" >
<table cellpadding="0" cellspacing="0" align="center"> 
<tr><td height="10"></td></tr>
  <tr><td height="1" bgcolor="#CCCCCC"></td></tr>
  <tr><td height="10"></td></tr>
  <tr><td style="padding-left:5px;">    
	   <table cellpadding="0" cellspacing="0" class="tab_on" width="400">
	     <tr><td height="3" colspan="2"></td></tr>
		 <tr><td colspan="2" align="left" class="tblHead">Upload file</td>
		 </tr>
		 <tr><td height="8" colspan="2"></td></tr>
		 <tr><td class="itemForm">File: </td>
		   <td align="left"><input type="file" size="30" id="flDoc" name="flDoc" class="txtPag" /></td>
		 </tr>		 
		 <tr><td height="13" colspan="2"></td></tr>
		 <tr><td colspan="2" align="right" style="padding-right:10px;"><input type="button" id="btnUp" class="btnSel" onclick="upFileNS();" value=" upload file " />&nbsp;&nbsp;<input type="button" class="btnSel" id="btnCancel" onClick="cancelDoc();" value="Cancel"></td></tr>
		 <tr><td height="4" colspan="2"></td></tr>
         <tr>
           <td colspan="2" align="center"><div class="loadP" id="lyMsgCv" style="display:none;">Uploading file ...</div></td></tr>
	   </table>
  </td></tr>
  </tr>
  <tr><td height="1"><iframe id="frUP" height="1" name="frUP" src="no.php" style="visibility:hidden;"></iframe></td></tr>    
</table>
<input type="hidden" name="acc" id="acc" value="1" />
<input type="hidden" name="idM" id="idM" value="<!--IdM-->" />
</form>