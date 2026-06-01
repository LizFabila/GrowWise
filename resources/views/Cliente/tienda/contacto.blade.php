@extends('cliente.layouts.app')

@section('content')
    <div class="row">
        <div class="col-12 mb-4">
            <h1 class="display-5 fw-bold text-success"><i class="fas fa-envelope"></i> Contacto</h1>
            <p class="text-muted">¿Tienes preguntas? Contáctanos</p>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-map-marker-alt text-success"></i> Dirección</h5>
                    <p>Carretera Federal Monumento-Valle de Bravo Km 30,<br>Ejido San Antonio Laguna, Valle, CP 51200</p>

                    <h5 class="fw-bold mt-4 mb-3"><i class="fas fa-phone text-success"></i> Teléfono</h5>
                    <p>+52 7223752122</p>

                    <h5 class="fw-bold mt-4 mb-3"><i class="fas fa-envelope text-success"></i> Correo electrónico</h5>
                    <p>growwisetesvb@gmail.com</p>

                    <h5 class="fw-bold mt-4 mb-3"><i class="fas fa-clock text-success"></i> Horario de atención</h5>
                    <p>Lunes a Viernes: 9:00 - 18:00<br>Sábados: 10:00 - 14:00</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-paper-plane text-success"></i> Envíanos un mensaje</h5>
                    <form>
                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control rounded-pill">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control rounded-pill">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mensaje</label>
                            <textarea class="form-control rounded-4" rows="4"></textarea>
                        </div>
                        <button type="submit" class="btn-naranja w-100">Enviar mensaje</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
