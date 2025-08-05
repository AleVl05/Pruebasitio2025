let paso = 1;
let pasoInicial = 1;
let pasoFinal = 3;

const cita = {
    id: '',
    nombre: '',
    fecha: '',
    hora: '',
    servicios: []
}



document.addEventListener('DOMContentLoaded', function() {
    iniciarApp();
});


function iniciarApp(){

    mostrarSeccion(); //muestra y oculta las funciones
    tabs(); //camba la seccion cuando se presionen los tabs
    botonesPaginador(); //agrega o quita botones abajo
    paginaSiguiente();
    paginaAnterior();

    //API

    consularAPI();

    idCliente();
    nombreCliente();
    seleccionarFecha();
    seleccionarHora();

    mostrarResumen();
}


function mostrarSeccion(){

    //ocultar la seccion con la clase mostrar

    const seccionAnterior = document.querySelector('.mostrar');
    if(seccionAnterior){
        seccionAnterior.classList.remove('mostrar');
    }
    

    //seleccionar la seccion con el paso
    const seccion = document.querySelector(`#paso-${paso}`);
    seccion.classList.add('mostrar');

    //quita la clase actual

    const tabAnterior = document.querySelector('.actual');
    if(tabAnterior){
        tabAnterior.classList.remove('actual');
    }


    //resalta la etapa actual

    const tab = document.querySelector(`[data-paso="${paso}"]`);
    tab.classList.add('actual');

}


function tabs(){

    const botones = document.querySelectorAll('.tabs button');

    botones.forEach( boton => {
        boton.addEventListener('click', function(e){
            paso = (parseInt(e.target.dataset.paso));

            mostrarSeccion();
            botonesPaginador();
            
        })  
    })
    

}


function botonesPaginador(){
    //# es para id
    const paginaAnterior = document.querySelector('#anterior') 
    const paginaSiguiente = document.querySelector('#siguiente') 

    if(paso === 1) {
        paginaAnterior.classList.add('ocultar')
        paginaSiguiente.classList.remove('ocultar')
    }else if(paso === 3) {
        paginaAnterior.classList.remove('ocultar')
        paginaSiguiente.classList.add('ocultar')

        mostrarResumen();
    } else {
        paginaAnterior.classList.remove('ocultar')
        paginaSiguiente.classList.remove('ocultar')
    }

    mostrarSeccion();


    
}


function paginaAnterior(){

    

    const paginaAnterior2 = document.querySelector('#anterior')
    paginaAnterior2.addEventListener('click', function(){

        if (paso <= pasoInicial) return
    
        paso--;

        botonesPaginador();

    })
    

}


function paginaSiguiente(){
    

    const paginaSiguiente2 = document.querySelector('#siguiente');
    paginaSiguiente2.addEventListener('click', function(){
        
        if (paso >= pasoFinal) return
    
        paso++;

        botonesPaginador();

    })
    

}


async function consularAPI(){ //si una funcion puede tardar, mejor

    try {
        const url = `location.origin/api/servicios'`
        const resultado = await fetch(url);
        const servicios = await resultado.json();
        mostrarServicios(servicios);
        
    } catch (error) {
        console.log(error);
        
    }
}


function mostrarServicios(servicios) {
    servicios.forEach(servicio => {
        const {id, nombre, precio} = servicio

        const nombreServicio = document.createElement('P');
        nombreServicio.classList.add('nombre-servicio');
        nombreServicio.textContent = nombre;

        const precioServicio = document.createElement('P');
        precioServicio.classList.add('precio-servicio');
        precioServicio.textContent = `$${precio}`;

         const servicioDiv = document.createElement('DIV');
        servicioDiv.classList.add('servicio');
        servicioDiv.dataset.idServicio = id;
        servicioDiv.onclick = function () {
            seleccionarServicio(servicio)
        }

        servicioDiv.appendChild(nombreServicio); 
        servicioDiv.appendChild(precioServicio); 

        document.querySelector('#servicios').appendChild(servicioDiv)

    })
}


function seleccionarServicio(servicio){

    const { id } = servicio;
    const { servicios } = cita;

    const divServicio = document.querySelector(`[data-id-servicio="${id}"]`);


    // Comprovar

    if ( servicios.some(agregado => agregado.id === id)) {

        // Eliminarlo
        cita.servicios = servicios.filter(agregado => agregado.id !== id);
        divServicio.classList.remove('seleccionado')


        
    }else{

        //agrehgarlo

        cita.servicios = [...servicios, servicio]
        divServicio.classList.add('seleccionado')
    }


    

    

    
}

function idCliente() {
    cita.id = document.querySelector('#id').value;
}

function nombreCliente() {
    cita.nombre = document.querySelector('#nombre').value;
}

function seleccionarFecha() {
   
    const inputFecha = document.querySelector('#fecha');
    inputFecha.addEventListener('input', function(e){

        const dia = new Date(e.target.value).getUTCDay();

        if ([6, 0].includes(dia)) {
            e.target.value = '';
            mostrarAlerta('finales de semana no son permitidos', 'error', '.formulario');
        }else{
            cita.fecha = e.target.value;
        }
        

        cita.fecha = inputFecha.value
    })
}

function seleccionarHora() {
    const inputHora = document.querySelector('#hora')
    inputHora.addEventListener('input', function(e){

        const horaCita = e.target.value;
        const hora = horaCita.split(":")[0]; //para separar una string y guardarlo en un array
        if (hora < 10 || hora > 18) {
            e.target.value = '';
            mostrarAlerta('Hora no valida', 'error', '.formulario')
        }else{
            cita.hora = e.target.value;
        }
    })
}

function mostrarAlerta(mensaje, tipo, elemento, desaparece = true) {

    //previene que se cree mas de una alerta
    const alertaPrevia = document.querySelector('.alerta')
    if(alertaPrevia) {
        alertaPrevia.remove();
    }


    const alerta = document.createElement('DIV')
    alerta.textContent = mensaje;
    alerta.classList.add('alerta');
    alerta.classList.add(tipo);

    //mostrar en pantalla

    const referencia = document.querySelector(elemento)
    referencia.appendChild(alerta)

    //eliminar la alerta

    if (desaparece) {
        setTimeout(() => {
            alerta.remove();
        }, 3000);
    }
}


function mostrarResumen() {
    const resumen = document.querySelector('.contenido-resumen')


    //limpiar contenido de resumen

    while(resumen.firstChild){
        resumen.removeChild(resumen.firstChild)
    }

    if (Object.values(cita).includes('') || cita.servicios.length === 0) { // si el array tiene un campo vacio
        mostrarAlerta('faltan datos de servicios, Fecha u Hora', 'erro', '.contenido-resumen', false)

        return;
        

    } 


    const {nombre, fecha, hora, servicios } = cita;


    //heading

    const headingServicios = document.createElement('H3')
    headingServicios.textContent = 'Resumen de servicios';
    resumen.appendChild(headingServicios)

    //iterar

    servicios.forEach(servicio => {

        const {id, precio, nombre} = servicio;

        const contenedorServicio = document.createElement('DIV')
        contenedorServicio.classList.add('contenedor-servicio');

        const textoServicio = document.createElement('P')
        textoServicio.textContent = nombre

        const precioServicio = document.createElement('P')
        precioServicio.innerHTML = `<span>Precio:</span> ${precio}`

        contenedorServicio.appendChild(textoServicio)
        contenedorServicio.appendChild(precioServicio)

        resumen.appendChild(contenedorServicio)
    })

    const headingcita = document.createElement('H3')
    headingcita.textContent = 'Resumen de cita';
    resumen.appendChild(headingcita)

    const nombreCliente = document.createElement('P')
    nombreCliente.innerHTML = `<span>Nombre:</span> ${nombre}`

    //formatear la fecha en español

    const fechaObj = new Date(fecha)
    const mes = fechaObj.getMonth()
    const dia = fechaObj.getDate() + 2;
    const year = fechaObj.getFullYear();

    const fechaUTC = new Date( Date.UTC(year, mes, dia));

    const opciones = {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'}
    const fechaFormateada = fechaUTC.toLocaleDateString('es-MX', opciones)

    
    const fechaCita = document.createElement('P')
    fechaCita.innerHTML = `<span>Fecha:</span> ${fechaFormateada}`

    const horaCita = document.createElement('P')
    horaCita.innerHTML = `<span>Hora:</span> ${hora} Horas`

    //boton para crear la cita

    const botonReservar = document.createElement('BUTTON')
    botonReservar.classList.add('boton')
    botonReservar.textContent = 'Reservar Cita';
    botonReservar.onclick = reservarCita;

    resumen.appendChild(nombreCliente)
    resumen.appendChild(fechaCita)
    resumen.appendChild(horaCita)

    console.log(nombreCliente)

    resumen.appendChild(botonReservar);
    
}


async function reservarCita() {
    
    const { nombre, fecha, hora, servicios, id } = cita;

    const idServicios = servicios.map( servicio => servicio.id );
    // console.log(idServicios);

    const datos = new FormData();
    
    datos.append('fecha', fecha);
    datos.append('hora', hora );
    datos.append('usuarioId', id);
    datos.append('servicios', idServicios);

    // console.log([...datos]); // sirve para hacer log a datos
    //peticion

    try {

        const url = `${location.origin}/api/citas`

    const respuesta = await fetch(url, { //fetch es para conectar
        method: 'POST',
        body: datos //obligatorio cuando es post
    })

    const resultado = await respuesta.json();

    if (resultado.resultado) {
        Swal.fire({
            icon: "success",
            title: "Cita Creada",
            text: "Tu cita fue creada correctamente",
            button: 'OK'
        }).then( () => {
            window.location.reload();
        })
    }
        
    } catch (error) {

        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Hubo un error al guardar la cita",
            button: 'OK'
        });
        
    }


}