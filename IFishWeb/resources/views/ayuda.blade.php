@extends('layouts.app')

@section('title', 'Ayuda y Soporte - iFish')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold"><i class="bi bi-question-circle-fill me-2"></i> Centro de Ayuda</h2>
    </div>

    <p class="fs-5 text-muted">Bienvenido al centro de ayuda de iFish. Aquí encontrarás respuestas a las preguntas más frecuentes sobre el uso de la plataforma.</p>

    <div class="accordion mt-4" id="faqAccordion">


        @guest
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingGuestOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseGuestOne" aria-expanded="true" aria-controls="collapseGuestOne">
                        <strong><i class="bi bi-info-circle-fill me-2"></i>¿Qué es iFish y para qué sirve?</strong>
                    </button>
                </h2>
                <div id="collapseGuestOne" class="accordion-collapse collapse show" aria-labelledby="headingGuestOne" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        iFish es una plataforma de **Acuicultura Inteligente** diseñada para modernizar y automatizar la gestión de criaderos de peces. Nuestro sistema te permite controlar y monitorear tus dispensadores de alimento de forma remota, optimizar la alimentación basándote en datos reales y llevar un registro detallado de toda tu operación para tomar mejores decisiones de negocio.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingGuestTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseGuestTwo" aria-expanded="false" aria-controls="collapseGuestTwo">
                        <strong><i class="bi bi-diagram-3-fill me-2"></i>¿Cómo funciona el sistema?</strong>
                    </button>
                </h2>
                <div id="collapseGuestTwo" class="accordion-collapse collapse" aria-labelledby="headingGuestTwo" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        El sistema iFish es una solución de **Internet de las Cosas (IoT)** que une el software con el hardware:
                        <ol>
                            <li><strong>Hardware Inteligente:</strong> Un dispositivo (basado en ESP8266) equipado con sensores y un motor se instala en tu dispensador de alimento.</li>
                            <li><strong>Conexión a la Nube:</strong> El dispositivo se conecta a internet a través de tu red WiFi y se comunica constantemente con nuestra plataforma en la nube.</li>
                            <li><strong>Plataforma Web:</strong> Desde esta página web, puedes programar horarios, activar alimentaciones manuales y ver estadísticas. Tus órdenes se envían al dispositivo en tiempo real.</li>
                            <li><strong>Reportes y Automatización:</strong> El dispositivo envía datos de vuelta (como el nivel de comida), y nuestro sistema ejecuta los planes de alimentación de forma 100% automática.</li>
                        </ol>
                    </div>
                </div>
            </div>
        @endguest



        @auth
            @if (Auth::user()->rol === 'Dueño')
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOwnerOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOwnerOne" aria-expanded="true" aria-controls="collapseOwnerOne">
                            <strong><i class="bi bi-water me-2"></i>¿Cómo gestiono mis Estanques y Dispensadores?</strong>
                        </button>
                    </h2>
                    <div id="collapseOwnerOne" class="accordion-collapse collapse show" aria-labelledby="headingOwnerOne" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p><strong>Estanques:</strong> En la sección "Estanques", puedes crear, editar y eliminar los diferentes estanques de tu criadero.</p>
                            <p><strong>Dispensadores:</strong> En la sección "Dispensadores", verás los equipos que el Super Admin te ha asignado. No puedes crearlos, pero sí gestionarlos: haz clic en "Editar" (✏️) para asignarlos a un estanque o cambiar su estado (ej: "Inactivo" para mantenimiento).</p>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOwnerTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOwnerTwo" aria-expanded="false" aria-controls="collapseOwnerTwo">
                            <strong><i class="bi bi-clock-history me-2"></i>¿Cómo programo la alimentación?</strong>
                        </button>
                    </h2>
                    <div id="collapseOwnerTwo" class="accordion-collapse collapse" aria-labelledby="headingOwnerTwo" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>Ve a <strong>"Horarios"</strong> y haz clic en "Nuevo Horario". Tienes dos modos:</p>
                            <ul>
                                <li><strong>Modo Manual:</strong> Para añadir una única alimentación a una hora específica.</li>
                                <li><strong>Modo Automático:</strong> Le dices al sistema cuántas veces al día y en qué rango de horas quieres alimentar, y él calcula y crea los horarios por ti.</li>
                            </ul>
                            Recuerda que solo puedes programar horarios para dispensadores que ya hayas asignado a un estanque.
                        </div>
                    </div>
                </div>
            @endif

            @if (Auth::user()->rol === 'Admin')
                 <div class="accordion-item">
                    <h2 class="accordion-header" id="headingAdminOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAdminOne" aria-expanded="true" aria-controls="collapseAdminOne">
                            <strong><i class="bi bi-building me-2"></i>¿Cómo creo un nuevo cliente (Criadero)?</strong>
                        </button>
                    </h2>
                    <div id="collapseAdminOne" class="accordion-collapse collapse show" aria-labelledby="headingAdminOne" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            El flujo correcto es crear el criadero y su dueño al mismo tiempo:
                            <ol>
                                <li>Ve a <strong>"Gestionar Criaderos"</strong> en tu menú de Super Admin.</li>
                                <li>Haz clic en <strong>"Nuevo Criadero"</strong>.</li>
                                <li>Rellena los datos del criadero y también los de la nueva cuenta para el usuario <strong>Dueño</strong>.</li>
                            </ol>
                            Al guardar, el sistema creará y vinculará ambos automáticamente.
                        </div>
                    </div>
                </div>
                 <div class="accordion-item">
                    <h2 class="accordion-header" id="headingAdminTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAdminTwo" aria-expanded="false" aria-controls="collapseAdminTwo">
                            <strong><i class="bi bi-box-seam-fill me-2"></i>¿Cómo funciona el Inventario de Dispensadores?</strong>
                        </button>
                    </h2>
                    <div id="collapseAdminTwo" class="accordion-collapse collapse" aria-labelledby="headingAdminTwo" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            La sección <strong>"Inventario Dispensadores"</strong> es tu panel central para gestionar todo el hardware.
                            <ul>
                                <li><strong>Añadir:</strong> Usa el botón "Añadir Dispensador" para registrar nuevos dispositivos en el sistema. Aparecerán como "Disponibles".</li>
                                <li><strong>Asignar:</strong> Para darle un equipo a un cliente, ve a "Gestionar Criaderos" y usa el botón "Asignar Disp." en el criadero correspondiente.</li>
                                <li><strong>Archivados:</strong> Si archivas un criadero, sus dispensadores se desactivarán y aparecerán en la sección "Dispensadores Archivados", desde donde podrás reasignarlos.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
        @endauth
    </div>

    <div class="mt-5 text-center">
        <hr>
        <h4 class="mt-4">¿Aún necesitas ayuda?</h4>
        <p class="text-muted">Contacta con nuestro soporte técnico directamente.</p>
        <div class="d-flex justify-content-center gap-3 mt-3">
            <a href="https://wa.me/59167408921" class="btn btn-success btn-lg" target="_blank">
                <i class="bi bi-whatsapp me-2"></i>Chatear por WhatsApp
            </a>
            <a href="tel:+59167408921" class="btn btn-primary btn-lg">
                <i class="bi bi-telephone-fill me-2"></i>Llamar a Soporte
            </a>
        </div>
    </div>
@endsection