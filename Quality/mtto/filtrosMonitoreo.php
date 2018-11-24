<table class="tblRepQA" width="900px" align="center" cellpadding="2" cellspacing="2">
<tr><th align="center" colspan="2"><b>Monitoring Form</th></tr>
<tr><td width="25%" align="right" bgcolor="#FFFFFF"><b>QA: </td><td width="75%"><!--evaluador--></td></tr>
<tr><td align="right" bgcolor="#FFFFFF"><b>Agent Name: </td><td><select id="lsAgent" class="txtPag" onchange="getSuperv(this.value)"><!--optEmp--></select></td></tr>
<tr><td align="right" bgcolor="#FFFFFF"><b>Evaluation type: </td><td><select id="lsTpEval" class="txtPag">
<option value="0">SELECT A EVALUATION TYPE</option>
<option value="1">CUSTOMER SERVICE</option>
<option value="2">SALES</option>
<option value="3">NEW SERVICES</option>
<option value="4">CHAT</option>
</select></td></tr>
<tr><td align="right" bgcolor="#FFFFFF"><b>Skill: </b></td><td><select id="lsTpSkill" class="txtPag"><!--optSkill--></select></td></tr>
<tr><td align="right" bgcolor="#FFFFFF"><b>Date: </td><td><!--fechaActual--></td></tr>
<tr><td align="right" bgcolor="#FFFFFF"><b>Supervisor: </td><td><input type="text" id="txtSuperv" class="txtPag" size="35" disabled="disabled" /></td></tr>
<tr><td align="right" bgcolor="#FFFFFF"><b>form will be used to averages? :</b></td><td><select id="lsQuery" class="txtPag"><option value="1">Yes</option><option value="0">No</option></select></td></tr>
<tr><td align="center" colspan="2"><input type="button" value="Load form" onclick="loadForm()" class="btn" /></td></tr>
</table>
<br /><br />
<div id="lyform"></div>
