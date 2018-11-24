<form method="POST" name="frmPhoto" id="frmPhoto" enctype="multipart/form-data" target="frPhoto" action="mtto/upPhoto.php" >
<tr><td>
<input type="file" size="30" id="flPhoto" name="flPhoto" class="txtPag" />
<input type="button" id="btnUph" class="btnSel" onclick="upPhoto();" value="Cargar foto" />
<div class="loadP" id="lyMsgPhoto" style="display:none;">Uploading file ...</div>
<iframe id="frPhoto" height="1" name="frPhoto" src="no.php" style="visibility:hidden;"></iframe>
<input type="hidden" name="idE" id="idE" value="<!--id_empleado-->" />
</td></tr>
