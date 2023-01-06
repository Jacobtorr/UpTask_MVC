<?php include_once __DIR__ . '/header-dashboard.php'; ?>

<?php if(count($proyectos) === 0)  { ?>

    <p class="no-proyectos">No hay proyectos Aun, <a href="/crear-proyecto">Crea Uno</a></p>

<?php } else {  ?>
    <ul class="listado-proyectos">
    <?php foreach($proyectos as $proyecto) { ?>                 
    <div class="caja">
        <form action="/proyecto/eliminar" method="post">
            <input type="hidden" name="id" value="<?php echo $proyecto->id ?>">
            <input type="button" class="btn btn-eliminar" value="Eliminar"/>
        </form>
        <a href="/proyecto?id=<?php echo $proyecto->url; ?>">                    
            <li class="proyecto">
                <?php echo $proyecto->proyecto; ?>
            </li>
        </a>                    
    </div>               
<?php } ?>
    </ul>

<?php } ?>

<?php include_once __DIR__ . '/footer-dashboard.php'; ?>

