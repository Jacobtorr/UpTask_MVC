<div class="contenedor reestablecer">

<?php include_once __DIR__ . '/../templates/nombre-sitio.php'; ?>

<div class="contenedor-sm">
    <p class="descripcion-pagina">Coloca tu Nuevo Password</p>

    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <?php if ($mostrar) {  ?>
    
        <form class="formulario" method="POST">
            <div class="campo">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Tu Password">
            </div>

            <input type="submit" value="Guardar Password" class="boton">
        </form>

        <?php } ?>
        <div class="acciones">
            <a href="/crear">¿Aun no tienes una cuenta? Crea una</a>
            <a href="/olvide">¿Olvidaste tu Password?</a>
        </div>
    </div> <!--Contenedor-sm-->  
</div>
