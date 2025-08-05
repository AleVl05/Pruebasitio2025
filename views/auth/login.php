<h1 class="nombre-pagina">Login</h1>
<p class="descripcion-pagina">Inicia sesión con tus datos</p>


<?php 

include_once __DIR__ . "/../templates/alertas.php"

?>


<form method="POST" action="/" class="formulario">
    
    <div class="campo">
        <label for="email">E-mail</label>
        <input 
            type="email"
            id="email"    
            placeholder="Tu email"
            name="email"
            value="<?php echo $auth->email; ?>" 
        />
    </div>

     <div class="campo">
        <label for="password">Password</label>
        <input 
            type="password"
            id="password"    
            placeholder="Tu password"
            name="password"
        />
    </div>

    <input type="submit" class="boton" value="Iniciar sesión">
</form>

<div class="acciones">
    
    <a href="/crear-cuenta">Aun no tienes una cuenta?</a>
    <a href="/olvide">olvidaste tu password?</a>

</div>

