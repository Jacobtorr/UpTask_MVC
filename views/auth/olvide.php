<div class="contenedor olvide">

<?php include_once __DIR__ . '/../templates/nombre-sitio.php'; ?>

<div class="contenedor-sm">
    <p class="descripcion-pagina">Recupera tu Acceso UpTask</p>

    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>
    
        <form action="/olvide" class="formulario" method="POST" novalidate>
            <div class="campo">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Tu Email">
            </div>

            <input type="submit" value="Enviar" class="boton">
        </form>
        <div class="acciones">
            <a href="/">¿Ya tienes una cuenta? Iniciar Sesion</a>
            <a href="/crear">¿Aun no tienes una cuenta? Crea una</a>
        </div>
    </div> <!--Contenedor-sm-->  
</div>
