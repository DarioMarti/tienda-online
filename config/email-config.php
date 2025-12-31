<?php
/**
 * Configuración de Email para Formulario de Contacto
 * 
 * IMPORTANTE: Configura estos valores según tu servidor de correo
 */

return [
    // Email donde se recibirán los mensajes del formulario
    'to_email' => 'boladetiza@gmail.com',
    'to_name' => 'Aetheria Studio',

    // Email de respuesta (reply-to)
    'from_email' => 'boladetiza@gmail.com',
    'from_name' => 'Formulario de Contacto - Aetheria',

    // Asunto del email
    'subject_prefix' => '[Contacto Web]',

    // Configuración adicional
    'enable_html' => true,
    'charset' => 'UTF-8'
];
