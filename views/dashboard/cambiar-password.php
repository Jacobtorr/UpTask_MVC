<?php include_once __DIR__ . '/header-dashboard.php'; ?>

<div class="contenedor-sm">
    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>
    <br>

    <a href="/perfil" class="enlace">&laquo; Volver a Perfil</a>

    <form action="/cambiar-password" class="formulario" method="POST">

        <div class="campo">
            <label for="nombre">Password Actual</label>
            <input type="password" name="password_actual" placeholder="Tu Password Actual">
        </div>
        <div class="campo">
            <label for="nombre">Password Nuevo</label>
            <input type="password" name="password_nuevo" placeholder="Tu Nuevo Password">
        </div>

        <input type="submit" value="Guardar Cambios" class="submit-crear-proyecto">
    </form>
</div>


<?php include_once __DIR__ . '/footer-dashboard.php'; ?>
