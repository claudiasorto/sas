<table cellpadding="3" cellspacing="0" width="60%" class="tblReport" align="center" bordercolor="#069">
<tr><td>Status </td>
<td><select id="lsStatus" class="txtPag" onChange="getRequest()"><option value="O">Open</option><option value="0">ALL</option><option value="C">Closed</option></select></td>
<td>Type of request </td>
<td><select id="lsTpReq" class="txtPag" onChange="getRequest()"><!--optReq--></select></td></tr> 
<tr><td colspan="4"><input type="button" class="btn" value="New HR Request" onClick="newHrRequest()">
</table>
<br>
<div id="lyForm"></div>
<br>
<div id="lyDetalles"></div>
<br>
<div id="lyData">
<!--tblData-->
</div>