@extends('layouts.app')

@section('header-title')
    <h1>Buscador de Cultivos</h1>
    <p>Encuentra el historial completo de tus cultivos</p>
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="table-container" data-aos="fade-up">
                    <div class="text-center mb-4">
                        <i class="fas fa-search fa-3x text-success mb-3"></i>
                        <h3>¿Qué cultivo quieres buscar?</h3>
                        <p class="text-muted">Ejemplo: Rábano, Lechuga, Espinaca, Cilantro, etc.</p>
                    </div>

                    <form action="{{ route('buscar.resultados') }}" method="GET" id="buscarForm">
                        <div class="input-group mb-3">
                            <input type="text"
                                   name="cultivo"
                                   id="cultivoInput"
                                   class="form-control form-control-lg rounded-pill"
                                   placeholder="Escribe el nombre del cultivo..."
                                   autocomplete="off"
                                   required>
                            <button type="submit" class="btn-naranja rounded-pill ms-2 px-4">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                    </form>

                    <div class="row mt-4">
                        <div class="col-md-4 text-center">
                            <div class="stat-card">
                                <div class="stat-info">
                                    <h3 id="totalCultivos">--</h3>
                                    <p>Cultivos disponibles</p>
                                </div>
                                <div class="stat-icon"><i class="fas fa-seedling"></i></div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="stat-card">
                                <div class="stat-info">
                                    <h3 id="cultivosPropios">--</h3>
                                    <p>Mis cultivos</p>
                                </div>
                                <div class="stat-icon"><i class="fas fa-leaf"></i></div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="stat-card">
                                <div class="stat-info">
                                    <h3 id="siembrasActivas">--</h3>
                                    <p>Siembras activas</p>
                                </div>
                                <div class="stat-icon"><i class="fas fa-sprout"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        // Autocompletado
        const input = document.getElementById('cultivoInput');
        let timeout = null;

        input.addEventListener('input', function() {
            clearTimeout(timeout);
            const query = this.value;

            if (query.length < 2) return;

            timeout = setTimeout(() => {
                axios.get('{{ route("buscar.autocomplete") }}', { params: { q: query } })
                    .then(response => {
                        // Aquí se puede implementar un datalist o sugerencias
                        console.log(response.data);
                    });
            }, 300);
        });

        // Cargar estadísticas iniciales
        axios.get('{{ route("buscar.stats") }}')
            .then(response => {
                document.getElementById('totalCultivos').innerText = response.data.total_cultivos;
                document.getElementById('cultivosPropios').innerText = response.data.mis_cultivos;
                document.getElementById('siembrasActivas').innerText = response.data.siembras_activas;
            });
    </script>
@endsection
