<div class="procesos menu">
    <ul>
        <li>
            <?php 
            $class = ($params['estado'] == '1')?'seleccionado':'';
            echo $this->Html->link('[1] Recepcionados<span>Dir. Finanzas</span>', array('action' => 'index', 'mayor' => $params['mayor'], 'estado' => 1), array('escape' => false, 'class' => 'no-habilitado ' . $class)); 
            ?>
        </li>
        <li>
            <?php 
            echo $this->Html->link('[2] Análisis y Proceso', 'javascript:void(0)', array('escape' => false, 'class' => 'no-habilitado')); 
            ?>
            <ul>
                <li>
                    <?php 
                    $class = ($params['estado'] == '2')?'seleccionado':'';
                    echo $this->Html->link('[2.1] Recepción<span>Contabilidad</span>', array('action' => 'index', 'mayor' => $params['mayor'], 'estado' => 2), array('escape' => false, 'class' => 'no-habilitado ' . $class)); 
                    ?>
                </li>
                <li>
                    <?php 
                    $class = ($params['estado'] == '3')?'seleccionado':'';
                    echo $this->Html->link('[2.2] Revisión<span>Analistas</span>', array('action' => 'index', 'mayor' => $params['mayor'], 'estado' => 3), array('escape' => false, 'class' => 'no-habilitado ' . $class)); 
                    ?>
                </li>
                <li>
                    <?php 
                    $class = ($params['estado'] == '4')?'seleccionado':'';
                    echo $this->Html->link('[2.3] Con observación<span>Administradores</span>', array('action' => 'index', 'mayor' => $params['mayor'], 'estado' => 4), array('escape' => false, 'class' => 'no-habilitado ' . $class)); 
                    ?>
                </li>
                <li>
                    <?php 
                    $class = ($params['estado'] == '5')?'seleccionado':'';
                    echo $this->Html->link('[2.4] Sin observación<span>Contabilidad</span>', array('action' => 'index', 'mayor' => $params['mayor'], 'estado' => 5), array('escape' => false, 'class' => 'no-habilitado ' . $class));
                    ?>
                </li>
            </ul>
        </li>
        <li>
            <?php 
            echo $this->Html->link('[3] Elaboración de Cheque', 'javascript:void(0)', array('escape' => false, 'class' => 'no-habilitado'));
            ?>
            <ul>
                <li>
                    <?php 
                    $class = ($params['estado'] == '6')?'seleccionado':'';
                    echo $this->Html->link('[3.1] Recepción<span>Tesorería</span>', array('action' => 'index', 'mayor' => $params['mayor'], 'estado' => 6), array('escape' => false, 'class' => 'no-habilitado ' . $class));
                    ?>
                </li>
                <li>
                    <?php 
                    $class = ($params['estado'] == '7')?'seleccionado':'';
                    echo $this->Html->link('[3.2] Impresión<span>Tesorería</span>', array('action' => 'index', 'mayor' => $params['mayor'], 'estado' => 7), array('escape' => false, 'class' => 'no-habilitado ' . $class));
                    ?>
                </li>
            </ul>
        </li>
    <?php if ($params['mayor'] == 'si') : ?>
        <li>
            <?php 
            $class = ($params['estado'] == '9')?'seleccionado':'';
            echo $this->Html->link('[4] Firma de Cheque<span>Dir. Finanzas</span>', array('action' => 'index', 'mayor' => $params['mayor'], 'estado' => 9), array('escape' => false, 'class' => 'no-habilitado ' . $class)); 
            ?>
        </li>
        <li>
            <?php 
            $class = ($params['estado'] == '10')?'seleccionado':'';
            echo $this->Html->link('[5] Firma de Cheque<span>Stria. Economía</span>', array('action' => 'index', 'mayor' => $params['mayor'], 'estado' => 10), array('escape' => false, 'class' => 'no-habilitado ' . $class)); 
            ?>
        </li>
        <li>
            <?php 
            $class = ($params['estado'] == '11')?'seleccionado':'';
            echo $this->Html->link('[6] Cheque concluído<span>Dir. Finanzas</span>', array('action' => 'index', 'mayor' => $params['mayor'], 'estado' => 11), array('escape' => false, 'class' => 'no-habilitado ' . $class)); 
            ?>
        </li>
        <li>
            <?php 
            $class = ($params['estado'] == '12')?'seleccionado':'';
            echo $this->Html->link('[7] Entrega de Cheque (' . $pendientes['12'] . ')<span>Caja</span>', array('action' => 'pendientes', 'mayor' => $params['mayor'], 'estado' => 12), array('escape' => false, 'class' => 'habilitado ' . $class)); 
            ?>
        </li>
        <li>
            <?php 
            $class = ($params['estado'] == '13')?'seleccionado':'';
            echo $this->Html->link('[8] Archivo<span>Archivo</span>', array('action' => 'pendientes', 'mayor' => $params['mayor'], 'estado' => 13), array('escape' => false, 'class' => 'no-habilitado ' . $class));
            ?>
        </li>
    <?php else : ?>
        <li>
            <?php 
            $class = ($params['estado'] == '8')?'seleccionado':'';
            echo $this->Html->link('[4] Firma de Cheque<span>Contabilidad</span>', array('action' => 'index', 'mayor' => $params['mayor'], 'estado' => 8), array('escape' => false, 'class' => 'no-habilitado ' . $class)); 
            ?>
        </li>
        <li>
            <?php 
            $class = ($params['estado'] == '12')?'seleccionado':'';
            echo $this->Html->link('[5] Entrega de Cheque (' . $pendientes['12'] . ')<span>Caja</span>', array('action' => 'pendientes', 'mayor' => $params['mayor'], 'estado' => 12), array('escape' => false, 'class' => 'habilitado ' . $class)); 
            ?>
        </li>
        <li>
            <?php 
            $class = ($params['estado'] == '13')?'seleccionado':'';
            echo $this->Html->link('[6] Archivo<span>Archivo</span>', array('action' => 'index', 'mayor' => $params['mayor'], 'estado' => 13), array('escape' => false, 'class' => 'no-habilitado ' . $class));
            ?>
        </li>
    </ul>
    <?php endif; ?>
</div>