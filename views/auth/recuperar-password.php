<h1 class="nombre-pagina">Recuperar Password</h1>

<p class="descripcion-pagina">coloca tu nuevo password a continuacion</p>

<?php 

include_once __DIR__ . "/../templates/alertas.php"

?>

<?php if($error) return; ?>

<form class="formulario" method="POST"> <!--SI LE PONES acition te va a sobreescribir el token-->
    <div class="campo">
        <label for="password">Password</label>
        
        <input 
            type="password"
            id="password"    
            placeholder="Tu password"
            name="password"
        />
    </div>

    <input type="submit" class="boton" value="Guardar Nuevo Password">

</form>




<div class="acciones">
    
    <a href="/">Ya tienes una cuenta? Inicia Sesión</a>
    <a href="/crear-cuenta">Aun no tienes cuenta? Crea una</a>

</div>