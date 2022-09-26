<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<h2>Sistema de Procesos de Pago</h2>
<h3>N° de Proceso: <?php echo $proceso['Proceso']['nro_proceso']; ?></h3>
<table>
    <tr>
        <th width="33%">Cite</th>
        <th width="33%">Fecha de Emisi&oacute;n</th>
        <th width="33%">Autorizado por</th>
    </tr>
    <tr>
        <td><?php echo $proceso['Proceso']['cite']; ?></td>
        <td><?php echo $proceso['Proceso']['fecha_emision']; ?></td>
        <td>
            <?php echo $proceso['ServidoresPublico']['nombre']; ?><br />
            <i><b><?php echo $proceso['Dependencia']['nombre']; ?></b></i>
        </td>
    </tr>
    <tr>
        <th>Motivo</th>
        <th>Beneficiario</th>
        <th>Monto</th>
    </tr>
    <tr>
        <td><?php echo $proceso['Motivo']['nombre']; ?></td>
        <td><?php echo $proceso['Beneficiario']['nombre']; ?></td>
        <td><?php echo $proceso['Proceso']['monto']; ?></td>
    </tr>
</table>
<table class="firmas">
    <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
</table>