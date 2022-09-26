<div id="footer">
    <div id="powered">
        <?php
        echo "&nbsp;";
        echo $this->Html->link(
                $this->Html->image('cake.power.gif', array('alt' => __('CakePHP: the rapid development php framework'), 'border' => '0')), 'http://www.cakephp.org/', array('target' => '_blank', 'escape' => false)
        );
        ?>
    </div>
    <div id="credits">
        &copy;2022 Gobierno Aut&oacute;nomo de Chuquisaca - Todos los derechos reservados<br />
        Desarrollado por Servicios Inform&aacute;ticos <a href="mailto:marceloquispeortega@gmail.com" target="_blank">RootCode</a>. Versi&oacute;n: <?php echo Configure::read('App.version'); ?>. Ultima actualizaci&oacute;n: <?php echo Configure::read('App.update'); ?>
    </div>
    <div class="clear"></div>
</div>
