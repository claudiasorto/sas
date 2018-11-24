<form method="POST" name="frmAttach" id="frmAttach" enctype="multipart/form-data" target="frUP" action="mtto/upAttach.php" >
<tr><td>
<input type="file" size="30" id="flDoc" name="flDoc" class="txtPag" />
<input type="button" id="btnUp" class="btnSel" onclick="upFile();" value="Cargar Archivo" />
<div class="loadP" id="lyMsgAttach" style="display:none;">Uploading file ...</div>
<iframe id="frUP" height="1" name="frUP" src="no.php" style="visibility:hidden;"></iframe>
<input type="hidden" name="idE" id="idE" value="<!--id_empleado-->" />
</td></tr>
