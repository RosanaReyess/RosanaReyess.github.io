// assets/js/calculo_experiencia.js

// Función para calcular el total de meses de experiencia del aspirante
function calcularTotalMeses() {
    // Obtener los valores de los campos de años y meses
    const anios = parseInt(document.getElementById('anios_experiencia').value) || 0;
    const meses = parseInt(document.getElementById('meses_experiencia').value) || 0;

    // Calcular el total de meses (1 año = 12 meses)
    const totalMeses = (anios * 12) + meses;

    // Mostrar el resultado en el campo de total de meses
    document.getElementById('total_meses').value = totalMeses;
}

// Función para calcular el total de meses requeridos de la vacante
function calcularTotalMesesVacante() {
    // Obtener los valores de los campos de años y meses requeridos
    const aniosRequeridos = parseInt(document.getElementById('anios_requeridos').value) || 0;
    const mesesRequeridos = parseInt(document.getElementById('meses_requeridos').value) || 0;

    // Calcular el total de meses requeridos (1 año = 12 meses)
    const totalMesesRequeridos = (aniosRequeridos * 12) + mesesRequeridos;

    // Mostrar el resultado en el campo de total de meses requeridos
    document.getElementById('total_meses_vacante').value = totalMesesRequeridos;
}
