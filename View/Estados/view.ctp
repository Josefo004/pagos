<div class="estados view">
<h2><?php  echo __('Estado');?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($estado['Estado']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Nombre'); ?></dt>
		<dd>
			<?php echo h($estado['Estado']['nombre']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Descripcion'); ?></dt>
		<dd>
			<?php echo h($estado['Estado']['descripcion']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($estado['Estado']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($estado['Estado']['modified']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Estado'), array('action' => 'edit', $estado['Estado']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Estado'), array('action' => 'delete', $estado['Estado']['id']), null, __('Are you sure you want to delete # %s?', $estado['Estado']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Estados'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Estado'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Procesos'), array('controller' => 'procesos', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Proceso'), array('controller' => 'procesos', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php echo __('Related Procesos');?></h3>
	<?php if (!empty($estado['Proceso'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Cite'); ?></th>
		<th><?php echo __('Nro Proceso'); ?></th>
		<th><?php echo __('Fecha Emision'); ?></th>
		<th><?php echo __('Fecha Recepcion'); ?></th>
		<th><?php echo __('Referencia'); ?></th>
		<th><?php echo __('Nro Hojas Doc'); ?></th>
		<th><?php echo __('Descripcion Doc'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th><?php echo __('Dependencia Id'); ?></th>
		<th><?php echo __('Servidores Publico Id'); ?></th>
		<th><?php echo __('Motivo Id'); ?></th>
		<th><?php echo __('Beneficiario Id'); ?></th>
		<th><?php echo __('Usuario Id'); ?></th>
		<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($estado['Proceso'] as $proceso): ?>
		<tr>
			<td><?php echo $proceso['id'];?></td>
			<td><?php echo $proceso['cite'];?></td>
			<td><?php echo $proceso['nur'];?></td>
			<td><?php echo $proceso['fecha_emision'];?></td>
			<td><?php echo $proceso['fecha_recepcion'];?></td>
			<td><?php echo $proceso['referencia'];?></td>
			<td><?php echo $proceso['nro_hojas_doc'];?></td>
			<td><?php echo $proceso['descripcion_doc'];?></td>
			<td><?php echo $proceso['created'];?></td>
			<td><?php echo $proceso['modified'];?></td>
			<td><?php echo $proceso['dependencia_id'];?></td>
			<td><?php echo $proceso['servidores_publico_id'];?></td>
			<td><?php echo $proceso['motivo_id'];?></td>
			<td><?php echo $proceso['beneficiario_id'];?></td>
			<td><?php echo $proceso['usuario_id'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'procesos', 'action' => 'view', $proceso['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'procesos', 'action' => 'edit', $proceso['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'procesos', 'action' => 'delete', $proceso['id']), null, __('Are you sure you want to delete # %s?', $proceso['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Proceso'), array('controller' => 'procesos', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>
